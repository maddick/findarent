<?php

class Communities_Model_FeaturedCommunitySearch
{
    /**
     * @var Custom_CityStateCriteria
     */
    protected $_cityStateCriteria;

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
              AND State LIKE :state
              AND City LIKE :city
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

        $variableArray = array(
            'state' => $this->_cityStateCriteria->getState(),
            'city'  => $this->_cityStateCriteria->getCity()
        );

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
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
     * Sets the dependency for city/state criteria
     *
     * @param $cityStateCriteria
     * @throws Exception when $cityStateCriteria is not an instance of Custom_CityStateCriteria
     */
    public function setCityStateCriteria($cityStateCriteria)
    {
        if ( $cityStateCriteria instanceof Custom_CityStateCriteria ) {
            $this->_cityStateCriteria = $cityStateCriteria;
        } else {
            throw new Exception('cityStateCriteria must be an instance of Custom_CityStateCriteria');
        }
    }
}