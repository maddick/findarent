<?php

class Brokers_Model_FeaturedBrokers
{
    /**
     * @var Custom_StateCriteria
     */
    protected $_stateCriteria;

    /**
     * @var Custom_CityStateCriteria
     */
    protected $_cityStateCriteria;

    /**
     * @var Custom_ZipCodeCriteria
     */
    protected $_zipCodeCriteria;

    /**
     * @var bool true if city state is used false otherwise
     */
    protected $_useCityState = true;

    /**
     * @var bool true if zip code is used false otherwise
     */
    protected $_useZipCode = true;

    /**
     * @var array containing any validation errors
     */
    protected $_validationErrors = array();

    /**
     * @var array containing the end results of a search
     */
    protected $_results = array();

    /**
     * Returns an array of broker cities by state
     *
     * @return array containing broker cities by state
     * @throws Exception when no state criteria is set
     */
    public function getBrokerCitiesByState()
    {
        if ( !isset( $this->_stateCriteria ) ) {
            throw new Exception('No state criteria was set.');
        }

        if ( !$this->_stateCriteria->isValid() ) {
            $this->_results['result'] = 'error';
            $this->_results['reasons'] = $this->_stateCriteria->getValidationErrors();
        } else {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $sql = 'CALL FAR_Accounts_GetBrokerCitiesByState( :state )';
                $stmt = $db->prepare( $sql );
                $stmt->execute( array( 'state' => $this->_stateCriteria->getCriteriaValue() ) );
                $brokerCitiesByState = $stmt->fetchAll();
                $this->_results['result'] = 'success';
                $this->_results['cities'] = $brokerCitiesByState;
            } catch ( Exception $e ) {
                $this->_results['result'] = 'server error';
                $this->_results['reasons'] = $e->getMessage();
            }
        }

        return $this->_results;
    }

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

        if ( isset( $this->_zipCodeCriteria ) ) {
            if ( !$this->_zipCodeCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_zipCodeCriteria->getValidationErrors() );
            }
        } else {
            $this->_useZipCode = false;
        }

        if ( !$this->_useCityState and !$this->_useZipCode ) {
            $this->_validationErrors[] = 'you must provide a city-state or zip code to perform a search';
        } elseif ( !( $this->_useZipCode xor $this->_useCityState ) ) {
            $this->_validationErrors[] = 'you must have either a zip code or a city/state, but not both';
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

            //if we use city / state, then we add the where clause with the appropriate
            //variable replacements needed
            if ( $this->_useCityState ) {

                //add the needed where clause
                $brokersIdSelect->where('l.State LIKE :state');
                $brokersIdSelect->where('l.City LIKE :city');

                //prepare the variable array with the values needed
                $variableArray['state'] = $this->_cityStateCriteria->getState();
                $variableArray['city'] = $this->_cityStateCriteria->getCity();

            } elseif ( $this->_useZipCode ) {

                //add the needed zip code where clause
                $brokersIdSelect->where('l.ZipCode IN(:zipCode)');

                //prepare the variable array with the values needed
                $variableArray['zipCode'] = $this->_zipCodeCriteria->getCriteriaValue();
            }

            //get broker ID's in request city State
            $brokerIdSql = $brokersIdSelect->__toString();
            $brokerIdStmt = $db->prepare($brokerIdSql);
            $brokerIdStmt->execute($variableArray);
            $brokerIdResults = $brokerIdStmt->fetchAll();
            $brokerIdStmt->closeCursor();

            //if no broker ID's are returned for the city / state provided then
            //return an empty array
            if ( empty( $brokerIdResults ) ) {
                return array();
            }

            //create IN clause using ID's found above
            $brokerIdInClause = 'brokers.BrokerID IN(';
            for( $index = 0; $index < count($brokerIdResults); $index++ ) {
                $brokerIdInClause .= ( $index === ( count($brokerIdResults) - 1 ) ) ? $brokerIdResults[$index]['BrokerID'] : $brokerIdResults[$index]['BrokerID'] . ',';
            }
            $brokerIdInClause .= ')';

            $select->where($brokerIdInClause);

            //build the entire query by injecting the now appropriately built query
            //string of communities
            $brokerSql = $select->__toString();
            $stmt = $db->prepare( $brokerSql );
            $stmt->execute();
            $searchResults = $stmt->fetchAll();
            $stmt->closeCursor();
            return $searchResults;
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    /**
     * Sets the dependency for state criteria
     *
     * @param $stateCriteria
     * @return $this
     * @throws Exception when $stateCriteria is not an instance of Custom_StateCriteria
     */
    public function setStateCriteria( $stateCriteria )
    {
        if ( $stateCriteria instanceof Custom_StateCriteria ) {
            $this->_stateCriteria = $stateCriteria;
        } else {
            throw new Exception('stateCriteria must be an instance of Custom_StateCriteria');
        }

        return $this;
    }

    /**
     * Sets the dependency for city / state criteria
     *
     * @param $cityStateCriteria
     * @return $this
     * @throws Exception when $cityStateCriteria is not an instance of Custom_CityStateCriteria
     */
    public function setCityStateCriteria($cityStateCriteria)
    {
        if ( $cityStateCriteria instanceof Custom_CityStateCriteria ) {
            $this->_cityStateCriteria = $cityStateCriteria;
        } else {
            throw new Exception('cityStateCriteria must be an instance of Custom_CityStateCriteria');
        }

        return $this;
    }

    public function setZipCodeCriteria($zipCodeCriteria)
    {
        if ( $zipCodeCriteria instanceof Custom_ZipCodeCriteria ) {
            $this->_zipCodeCriteria = $zipCodeCriteria;
        } else {
            throw new Exception('zipCodeCriteria must be an instance of Custom_ZipCodeCriteria');
        }

        return $this;
    }
}
