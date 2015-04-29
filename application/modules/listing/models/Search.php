<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/21/15
 * Time: 8:23 PM
 */

class Listing_Model_Search
{

    /**
     * @var array
     */
    protected $_validationErrors = array();

    /**
     * @var Custom_RadiusCriteria
     */
    protected $_radiusCriteria;

    /**
     * @var Custom_ZipCodeCriteria
     */
    protected $_zipCodeCriteria;

    /**
     * @var Custom_CityStateCriteria
     */
    protected $_cityStateCriteria;

    /**
     * @var Custom_NumberOfBedroomsCriteria
     */
    protected $_numberOfBedroomsCriteria;

    /**
     * @var array
     */
    public $results = array();

    /**
     * @var bool true if zip code is used false otherwise
     */
    protected $_useZipCode = true;

    /**
     * @var bool true if city/state is used false otherwise
     */
    protected $_useCityState = true;

    /**
     * @var bool true if radius is used false otherwise
     */
    protected $_useRadius = true;

    /**
     * @var bool true if number of bedrooms is used false otherwise
     */
    protected $_useNumberOfBedrooms = true;

    public function searchListings()
    {
        $this->_validateSearch();
        if ( !empty( $this->_validationErrors ) ) {
            $this->results['result'] = 'error';
            $this->results['reasons'] = $this->_validationErrors;
            return $this->results;
        } else {
            $listings = $this->_searchListings();
            $this->results['result'] = 'success';
            $this->results['listings'] = $listings;
            return $this->results;
        }
    }

    /**
     * This function houses the business logic for validating that a search has what it needs before
     * actually performing any search at all. The validation of each separate search criteria is
     * handled by that criteria's implementation but their errors are gathered here if any exist.
     */
    protected function _validateSearch()
    {
        if ( isset( $this->_zipCodeCriteria ) ) {
            if ( !$this->_zipCodeCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_zipCodeCriteria->getValidationErrors() );
            } else {
                //$this->results['zipCode'] = $this->_zipCodeCriteria->getCriteria();
            }
        } else {
            $this->_useZipCode = false;
        }

        if ( isset( $this->_cityStateCriteria ) ) {
            if ( !$this->_cityStateCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_cityStateCriteria->getValidationErrors() );
            } else {
                $this->results['cityState'] = $this->_cityStateCriteria->getCriteria();
            }
        } else {
            $this->_useCityState = false;
        }

        if ( !$this->_useZipCode and !$this->_useCityState ) {
            $this->_validationErrors[] = 'you must have at least a zip code or a city/state to perform a search';
        } elseif ( !( $this->_useZipCode xor $this->_useCityState ) ) {
            $this->_validationErrors[] = 'you must have either a zip code or a city/state, but not both';
        }

        if ( isset( $this->_radiusCriteria ) ) {
            if ( !$this->_radiusCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_radiusCriteria->getValidationErrors() );
            } else {
                $this->results['radius'] = $this->_radiusCriteria->getCriteria();
            }
        } else {
            $this->_useRadius = false;
        }

        if ( isset( $this->_numberOfBedroomsCriteria ) ) {
            if ( !$this->_numberOfBedroomsCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_numberOfBedroomsCriteria->getValidationErrors() );
            } else {
                $this->results['numberOfBedrooms'] = $this->_numberOfBedroomsCriteria->getCriteria();
            }
        } else {
            $this->_useNumberOfBedrooms = false;
        }
    }

    protected function _searchListings()
    {
        //setup the initial query string
        $listingsSql =
            'SELECT li.*, IFNULL(Views,0) AS Views, IFNULL(ContactRequests,0) AS ContactRequests,
                 IFNULL(ContactEmailsSent,0) AS ContactEmailsSent, l.Premium, l.Rebates
            FROM far_landlords l, far_listings li LEFT JOIN

              (SELECT ListingID, SUM(Views) AS Views, SUM(ContactRequests) AS ContactRequests, SUM(ContactEmailsSent) AS ContactEmailsSent,
                  SUM(WebsiteReferrals) AS WebsiteReferrals
                FROM far_listings_tracking GROUP BY ListingID) AS Tracking ON li.ListingID = Tracking.ListingID

            WHERE li.LandlordID = l.LandlordID
            AND (1 IS NULL OR li.Active = 1)
            AND (
              1 IS NULL OR (
                1 = 1 AND (
                  li.ExpirationDate IS NULL OR
                  DATE(li.ExpirationDate) >= DATE(now())
                )
              )
            )
            AND (0 IS NULL OR li.Deleted = 0)
            AND (1 = 0 OR (1 = 1 AND l.Active = 1 AND l.Deleted = 0 AND DATE(l.ExpirationDate) >= DATE(now())))';

        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            if ( $this->_useCityState ) {
                //TODO: translate the city/state to a zip code
                return array( 'specialMessage' => 'Not Yet Implemented');
            }

            if ( $this->_useNumberOfBedrooms ) {
                $numberOfBedRoomsInjector = 'AND li.Bedrooms = ? ';
            }

            if ( $this->_useRadius ) {

                //fetch the coordinates of the provided zipCode
                $zipCoordsSql = 'CALL ZipCodes_GetCoordsByZipCode(:zipCode)';
                $zipCoordsStmt = $db->prepare($zipCoordsSql);
                $zipCoordsStmt->execute( array('zipCode' => $this->_zipCodeCriteria->getCriteria()) );

                //if no coordinates are returned return an empty set
                if ( $zipCoordsStmt->rowCount() == 0 ) {
                    return array();
                }

                $zipCoords = $zipCoordsStmt->fetchAll();
                $zipCoordsStmt->closeCursor();

                //fetch all zip codes in the radius of the give zipCode
                $zipCodesInRadiusSql = 'call mysql_76313_far.ZipCodes_GetZipsInRadius(:lat, :lon, :rad)';
                $zipCodesInRadiusStmt = $db->prepare($zipCodesInRadiusSql);
                $zipCodesInRadiusStmt->execute(array(
                    'lat' => $zipCoords[0]['lat'],
                    'lon' => $zipCoords[0]['lon'],
                    'rad' => $this->_radiusCriteria->getCriteria()
                ));

                $zipCodesInRadius = $zipCodesInRadiusStmt->fetchAll();
                $zipCodesInRadiusStmt->closeCursor();

                //extract the zip codes into a variable array
                $variableArray = array();
                foreach ( $zipCodesInRadius as $row ) {
                    if ( !in_array($row['zip_code'], $variableArray ) ) {
                        $variableArray[] = $row['zip_code'];
                    }
                }

                //create an IN clause injector with a number of ?'s equal to the number of zip codes
                $zipCodesInjector = ' AND li.ZipCode IN(';
                for ( $index = 0; $index < count($variableArray); $index++ ) {
                    $zipCodesInjector .= ( $index === ( count($variableArray) - 1 ) ) ? '?' : '?,';
                }
                $zipCodesInjector .= ')';

                //append the zip code injector to the query string
                $listingsSql .= $zipCodesInjector;

                //if used, add the bedroom variable to the variable array
                if ( isset( $numberOfBedRoomsInjector ) ) {
                    $variableArray[] = $this->_numberOfBedroomsCriteria->getCriteria();
                    $listingsSql .= $numberOfBedRoomsInjector;
                }

                //prepare the statement and execute the search query
                $listingStmt = $db->prepare($listingsSql);
                $listingStmt->execute($variableArray);
                $listings = $listingStmt->fetchAll();
                $listingStmt->closeCursor();
                return $listings;
            } else {

            }
        } catch ( Exception $e ) {
            echo($e->getMessage());
        }
    }

    /**
     * Sets the dependency for zip Criteria object
     *
     * @param $zipCodeCriteria
     * @throws Exception when $zipCodeCriteria is not an instance of Custom_ZipCodeCriteria
     */
    public function setZipCriteria( $zipCodeCriteria )
    {
        if ( $zipCodeCriteria instanceof Custom_ZipCodeCriteria ) {
            $this->_zipCodeCriteria = $zipCodeCriteria;
        } else {
            throw new Exception('zipCodeCriteria must be an instance of Custom_ZipCodeCriteria');
        }
    }

    /**
     * Sets the dependency for city state criteria object
     *
     * @param $cityStateCriteria
     * @throws Exception when $cityStateCriteria is not an instance of Custom_CityStateCriteria
     */
    public function setCityStateCriteria( $cityStateCriteria )
    {
        if ( $cityStateCriteria instanceof Custom_CityStateCriteria ) {
            $this->_cityStateCriteria = $cityStateCriteria;
        } else {
            throw new Exception('cityStateCriteria must be an instance of Custom_CityStateCriteria');
        }
    }

    /**
     * Sets the dependency for the radius criteria object
     *
     * @param $radiusCriteria
     * @throws Exception when $radiusCriteria is not an instance of Custom_RadiusCriteria
     */
    public function setRadiusCriteria( $radiusCriteria )
    {
        if ( $radiusCriteria instanceof Custom_RadiusCriteria ) {
            $this->_radiusCriteria = $radiusCriteria;
        } else {
            throw new Exception('radiusCriteria must be an instance of Custom_RadiusCriteria');
        }
    }

    /**
     * Sets the dependency for the numberOfBedrooms criteria object
     *
     * @param $numberOfBedroomsCriteria
     * @throws Exception when $numberOfBedroomsCriteria is not an instance of Custom_NumberOfBedroomsCriteria
     */
    public function setNumberOfBedroomsCriteria( $numberOfBedroomsCriteria )
    {
        if ( $numberOfBedroomsCriteria instanceof Custom_NumberOfBedroomsCriteria ) {
            $this->_numberOfBedroomsCriteria = $numberOfBedroomsCriteria;
        } else {
            throw new Exception('numberOfBedroomsCriteria must be an instance of Custom_NumberOfBedroomsCriteria');
        }
    }
}