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
                $stmt->execute( array( 'state' => $this->_stateCriteria->getCriteria() ) );
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
     * performs validation on the search criteria. Validations for each criteria is handled at the criteria level.
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


          FROM (
            SELECT c.*
            FROM far_communities c
            JOIN (
              SELECT LandlordID
              FROM far_landlords l
              JOIN my_aspnet_membership m
                ON l.UserId = m.userId
              WHERE IsApproved = 1
              AND Active = 1
              AND Deleted = 0
              AND DATE(ExpirationDate) >= DATE(NOW())
            ) accounts ON c.LandlordID = accounts.LandlordID


            JOIN (
              SELECT CommunityID
              FROM far_listings l
              WHERE Active = 1
              AND Deleted = 0
              AND (ExpirationDate IS NULL OR DATE(ExpirationDate) >= DATE(NOW()))
              :state
              :city
              GROUP BY CommunityID
            ) listings ON c.CommunityID = listings.CommunityID
            WHERE Active = 1
            AND c.Deleted = 0
          ) AS communities

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

            //if we use city / state, then alter the query with LIKE statements
            //otherwise inject an empty string
            if ( $this->_useCityState ) {
                $stateReplace = 'AND State LIKE :state';
                $cityReplace = 'AND City LIKE :city';

                //prepare the variable array with the values needed
                $variableArray['state'] = $this->_cityStateCriteria->getState();
                $variableArray['city'] = $this->_cityStateCriteria->getCity();
            } else {
                $stateReplace = '';
                $cityReplace = '';
            }

            $searchSql = str_replace( ':state', $stateReplace, $searchSql );
            $searchSql = str_replace( ':city', $cityReplace, $searchSql );

            $stmt = $db->prepare( $searchSql );
            $stmt->execute( $variableArray );
            $searchResults = $stmt->fetchAll();
            $stmt->closeCursor();
            return $searchResults;
        } catch ( Exception $e ) {
            throw $e;
        }
    }

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
     * @throws Exception when $cityStateCriteria is not an instance of Custom_CityStateCriteria
     * @return $this
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
}