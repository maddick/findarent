<?php

class Brokers_Model_FeaturedBrokers
{

    /**
     * @var Custom_CityStateCriteria
     */
    protected $_cityStateCriteria;

    /**
     * @var bool true if city state is used false otherwise
     */
    protected $_useCityState = true;

    /**
     * @var array containing any validation errors
     */
    protected $_validationErrors = array();

    /**
     * @var array containing the end results of a search
     */
    protected $_results = array();

    /**
     * @return array containing the search results
     */
    public function searchFeaturedBrokers()
    {
        $this->_validateSearch();
        if ( !empty( $this->_validationErrors ) ) {
            $this->_results['result'] = 'error';
            $this->_results['reasons'] = $this->_validationErrors;
            return $this->_results;
        } else {
            try {
                $brokers = $this->_searchFeaturedBrokers();
                $this->_results['result'] = 'success';
                $this->_results['brokers'] = $brokers;
            } catch ( Exception $e ) {
                $this->_results['result'] = 'server error';
                $this->_results['reasons'] = $e->getMessage();
            }

            return $this->_results;
        }
    }

    /**
     * performs validation on the search criteria. Currently, the only valid search criteria is a city / state criteria.
     * State alone is not accounted for as of yet. Validations for each criteria is handled at the criteria level.
     */
    protected function _validateSearch()
    {
        if ( isset( $this->_cityStateCriteria ) ) {
            if ( !$this->_cityStateCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_cityStateCriteria->getValidationErrors() );
            }
        } else {
            $this->_useCityState = false;
        }

        if ( !$this->_useCityState ) {
            $this->_validationErrors[] = 'you must provide a city-state to perform a search';
        }
    }

    protected function _searchFeaturedBrokers()
    {
        $trackingSql = '(SELECT BrokerID, SUM(Views) AS Views FROM far_brokers_tracking GROUP BY BrokerID)';

        $variableArray = array();
        try {
            $db = Zend_Db_Table::getDefaultAdapter();

            $brokersSelect =  $db->select()
                ->from(
                    array('b' => 'far_brokers'),
                    array('b.*')
                )
                ->join(
                    array( 'l' => 'far_landlords' ),
                    'b.LandlordID = l.LandlordID',
                    array()
                )
                ->join(
                    array('m' => 'my_aspnet_membership'),
                    'l.UserId = m.userId',
                    array()
                )
                ->where('b.Headshot != \'\'')
                ->where('b.Active = 1 AND b.Deleted = 0')
                ->where('l.Active = 1 AND l.Deleted = 0 AND l.ExpirationDate >= now()')
                ->where('m.IsApproved = 1');

            $select = $db->select()
                ->from(
                    array('brokers' => new Zend_Db_Expr('(' . $brokersSelect . ')' ) ),
                    array('brokers.*', 'Views' => 'IFNULL(Views,0)')
                )
                ->joinLeft(
                    array('tracking' => new Zend_Db_Expr( $trackingSql ) ),
                    'brokers.BrokerID = tracking.BrokerID',
                    array()
                );

            //if we use city / state, then we add the where clause with the appropriate
            //variable replacements needed
            if ( $this->_useCityState ) {

                $brokersIdSelect = $db->select()
                    ->distinct()
                    ->from(
                        array('l' => 'far_listings'),
                        array('l.BrokerID')
                    )
                    ->join(
                        array('b' => 'far_brokers'),
                        'b.BrokerID = l.BrokerID',
                        array()
                    )
                    ->join(
                        array('la' => new Zend_Db_Expr( '(select LandlordID from far_landlords where Active = 1)' )),
                        'la.LandlordID = b.LandlordID',
                        array()
                    )
                    ->where('l.Active = 1 and b.Active = 1')
                    ->where('l.Deleted = 0 and b.Deleted = 0')
                    ->where('l.ExpirationDate IS NULL OR DATE(l.ExpirationDate) >= DATE(NOW())');

                $brokersIdSelect->where('l.State LIKE :state');
                $brokersIdSelect->where('l.City LIKE :city');

                //prepare the variable array with the values needed
                $variableArray['state'] = $this->_cityStateCriteria->getState();
                $variableArray['city'] = $this->_cityStateCriteria->getCity();

                //get broker ID's in request city State
                $brokerIdSql = $brokersIdSelect->__toString();
                $brokerIdStmt = $db->prepare($brokerIdSql);
                $brokerIdStmt->execute($variableArray);
                $brokerIdResults = $brokerIdStmt->fetchAll();
                $brokerIdStmt->closeCursor();

                //create IN clause using ID's found above
                $brokerIdInClause = 'brokers.BrokerID IN(';
                for( $index = 0; $index < count($brokerIdResults); $index++ ) {
                    $brokerIdInClause .= ( $index === ( count($brokerIdResults) - 1 ) ) ? $brokerIdResults[$index]['BrokerID'] : $brokerIdResults[$index]['BrokerID'] . ',';
                }
                $brokerIdInClause .= ')';

                $select->where($brokerIdInClause);
            }

            //build the entire query by injecting the now appropriately built query
            //string of communities
            $brokerSql = $select->__toString();

            $stmt = $db->prepare( $brokerSql );
            $stmt->execute( $variableArray );
            $searchResults = $stmt->fetchAll();
            $stmt->closeCursor();
            return $searchResults;
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    public function setCityStateCriteria($cityStateCriteria)
    {
        if ( $cityStateCriteria instanceof Custom_CityStateCriteria ) {
            $this->_cityStateCriteria = $cityStateCriteria;
        } else {
            throw new Exception('cityStateCriteria must be an instance of Custom_CityStateCriteria');
        }

        return $this;
    }
}
