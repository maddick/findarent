<?php

class Communities_Model_FeaturedCommunities
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
     * Returns a list of featured community cities by state
     *
     * @return array containing the cities by state for featured communities
     * @throws Exception when _stateCriteria is not set
     */
    public function getCommunityCitiesByState()
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
                $sql = 'CALL FAR_Accounts_GetCommunityCitiesByState( :state )';
                $stmt = $db->prepare( $sql );
                $stmt->execute( array( 'state' => $this->_stateCriteria->getCriteriaValue() ) );
                $communityCitiesByState = $stmt->fetchAll();
                $this->_results['result'] = 'success';
                $this->_results['cities'] = $communityCitiesByState;
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
    public function searchFeaturedCommunities()
    {
        $this->_validateSearch();
        if ( !empty( $this->_validationErrors ) ) {
            $this->_results['result'] = 'error';
            $this->_results['reasons'] = $this->_validationErrors;
            return $this->_results;
        } else {
            try {
                $communities = $this->_searchFeaturedCommunities();
                $this->_results['result'] = 'success';
                $this->_results['communities'] = $communities;
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

        if ( isset( $this->_zipCodeCriteria ) ) {
            if ( !$this->_zipCodeCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_zipCodeCriteria->getValidationErrors() );
            }
        } else {
            $this->_useZipCode = false;
        }

        if ( !$this->_useCityState and !$this->_useZipCode ) {
            $this->_validationErrors[] = 'you must provide a city-state or a zip code to perform a search';
        } elseif ( !( $this->_useZipCode xor $this->_useCityState ) ) {
            $this->_validationErrors[] = 'you must have either a zip code or a city/state, but not both';
        }
    }

    /**
     * Searches the database for featured communities
     *
     * @return array containing search results
     * @throws Exception when there is a database/server error
     */
    protected function _searchFeaturedCommunities()
    {
        $searchSql = '
          SELECT
            communities.*,
              IFNULL(Views,0) AS Views,
              IFNULL(ContactRequests,0) AS ContactRequests,
              IFNULL(ContactEmailsSent,0) AS ContactEmailsSent


          FROM ( :communitiesQueryString ) AS communities

          LEFT JOIN (
            SELECT CommunityID, SUM(Views) AS Views, SUM(ContactRequests) AS ContactRequests, SUM(ContactEmailsSent) AS ContactEmailsSent
            FROM far_communities_tracking
            GROUP BY CommunityID
          ) AS tracking ON communities.CommunityID = tracking.CommunityID

          ORDER BY communities.CommunityID IN (
            SELECT c.CommunityID
            FROM far_communities c, far_listings l
            WHERE c.LandlordID = l.LandlordID
            AND l.ListingID IN (
              SELECT ListingID
              FROM far_listings_photos p
              WHERE p.Deleted = 0
            )
          ) DESC, AddedDate DESC;';

        $variableArray = array();
        try {
            $db = Zend_Db_Table::getDefaultAdapter();

            $accounts = $db->select()
                ->from(
                    array('l' => 'far_landlords'),
                    array('LandlordID')
                )
                ->join(
                    array('m' => 'my_aspnet_membership'),
                    'l.UserId = m.userId',
                    array()
                )
                ->where('IsApproved = 1')
                ->where('Active = 1')
                ->where('Deleted = 0')
                ->where('DATE(ExpirationDate) >= DATE(NOW())');

            $listings = $db->select()
                ->from(
                    array('l' => 'far_listings'),
                    array('CommunityID')
                )
                ->where('Active = 1')
                ->where('Deleted = 0')
                ->where('ExpirationDate IS NULL OR DATE(ExpirationDate) >= DATE(NOW())')
                ->group('CommunityID');


            $communities = $db->select()
                ->from(
                    array('c' => 'far_communities'),
                    array('c.*')
                )
                ->join(
                    array( 'accounts' => new Zend_Db_Expr('(' . $accounts . ')') ),
                    'c.LandlordID = accounts.LandlordID',
                    array()
                )
                ->join(
                    array( 'listings' => new Zend_Db_Expr('(' . $listings . ')')),
                    'c.CommunityID = listings.CommunityID',
                    array()
                )
                ->where('Active = 1')
                ->where('c.Deleted = 0');

            //if we use city / state, then we add the where clause with the appropriate
            //variable replacements needed
            if ( $this->_useCityState ) {

                $communities->where('State LIKE :state');
                $communities->where('City LIKE :city');

                //prepare the variable array with the values needed
                $variableArray['state'] = $this->_cityStateCriteria->getState();
                $variableArray['city'] = $this->_cityStateCriteria->getCity();
            }

            if ( $this->_useZipCode ) {
                $communities->where('ZipCode IN(:zipCode)');

                $variableArray['zipCode'] = $this->_zipCodeCriteria->getCriteriaValue();
            }

            //build the entire query by injecting the now appropriately built query
            //string of communities
            $communitiesSql = $communities->__toString();
            $searchSql = str_replace( ':communitiesQueryString', $communitiesSql, $searchSql );

            $stmt = $db->prepare( $searchSql );
            $stmt->execute( $variableArray );
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
     * Sets the dependency for city/state criteria
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