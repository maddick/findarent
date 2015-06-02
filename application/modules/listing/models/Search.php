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
     * @var Custom_NumberOfBathroomsCriteria
     */
    protected $_numberOfBathroomsCriteria;

    /**
     * @var Custom_RentCriteria
     */
    protected $_minRentCriteria;

    /**
     * @var Custom_RentCriteria
     */
    protected $_maxRentCriteria;

    /**
     * @var Custom_IdCriteria
     */
    protected $_landlordIdCriteria;

    /**
     * @var Custom_IdCriteria
     */
    protected $_listingIdCriteria;

    /**
     * @var Custom_AutocompleteCriteria
     */
    protected $_autocompleteCriteria;

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
     * @var bool true if a number of bathrooms is specified false otherwise
     */
    protected $_useNumberOfBathrooms = true;

    /**
     * @var bool true if number of bedrooms is used false otherwise
     */
    protected $_useNumberOfBedrooms = true;

    protected $_useMinRent = true;

    protected $_useMaxRent = true;

    public function searchListings()
    {
        $this->_validateSearch();
        if ( !empty( $this->_validationErrors ) ) {
            $this->results['result'] = 'error';
            $this->results['reasons'] = $this->_validationErrors;
            return $this->results;
        } else {
            try{
                $listings = $this->_searchListings();
                $this->results['result'] = 'success';
                $this->results['listings'] = $listings;
            } catch (Exception $e) {
                $this->results['result'] = 'server error';
                $this->results['message'] = $e->getMessage();
            }
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
            }
        } else {
            $this->_useZipCode = false;
        }

        if ( isset( $this->_cityStateCriteria ) ) {
            if ( !$this->_cityStateCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_cityStateCriteria->getValidationErrors() );
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
            }
        } else {
            $this->_useRadius = false;
        }

        if ( isset( $this->_numberOfBedroomsCriteria ) ) {
            if ( !$this->_numberOfBedroomsCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_numberOfBedroomsCriteria->getValidationErrors() );
            }
        } else {
            $this->_useNumberOfBedrooms = false;
        }

        if ( isset( $this->_numberOfBathroomsCriteria ) ) {
            if (!$this->_numberOfBathroomsCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_numberOfBathroomsCriteria->getValidationErrors() );
            }
        } else {
            $this->_useNumberOfBathrooms = false;
        }

        if ( isset( $this->_minRentCriteria ) ) {
            if ( !$this->_minRentCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_minRentCriteria->getValidationErrors() );
            }
        } else {
            $this->_useMinRent = false;
        }

        if ( isset( $this->_maxRentCriteria ) ) {
            if ( !$this->_maxRentCriteria->isValid() ) {
                $this->_validationErrors = array_merge( $this->_validationErrors, $this->_maxRentCriteria->getValidationErrors() );
            }
        } else {
            $this->_useMaxRent = false;
        }
    }

    /**
     * @return array containing the results of the search
     * @throws Exception when there is a database error
     */
    protected function _searchListings()
    {
        //setup the initial query string
        $listingsSql =
            'SELECT li.*, IFNULL(Views,0) AS Views, IFNULL(ContactRequests,0) AS ContactRequests,
                 IFNULL(ContactEmailsSent,0) AS ContactEmailsSent, l.Premium, l.Rebates, p.ImageURL
            FROM far_landlords l, far_listings_photos p, far_listings li LEFT JOIN

              (SELECT ListingID, SUM(Views) AS Views, SUM(ContactRequests) AS ContactRequests, SUM(ContactEmailsSent) AS ContactEmailsSent,
                  SUM(WebsiteReferrals) AS WebsiteReferrals
                FROM far_listings_tracking GROUP BY ListingID) AS Tracking ON li.ListingID = Tracking.ListingID

            WHERE li.LandlordID = l.LandlordID
            AND p.PhotoID = (SELECT PhotoId
					FROM far_listings_photos
                    WHERE ListingID = li.ListingID
                    AND (Active = 1)
                    AND Deleted = 0
                    ORDER BY `Order`
                    LIMIT 1)
            AND (li.Active = 1)
            AND (
              li.ExpirationDate IS NULL OR
              DATE(li.ExpirationDate) >= DATE(now())
            )
            AND (li.Deleted = 0)
            AND (l.Active = 1 AND l.Deleted = 0 AND DATE(l.ExpirationDate) >= DATE(now()))';

        //initialize an array for variables passed into the query
        $variableArray = array();

        $db = Zend_Db_Table::getDefaultAdapter();
        try {

            //if we use a city/state get find the corresponding zip code
            //otherwise simply use the zip code provided
            if ( $this->_useCityState ) {

                //translate city/state to zip code
                $cityStateToZipSql = 'CALL ZipCodes_GetZipCodeByCityState(:city, :state)';
                $cityStateToZipStmt = $db->prepare($cityStateToZipSql);
                $cityStateToZipStmt->execute( array(
                    'city' => $this->_cityStateCriteria->getCity(),
                    'state' => $this->_cityStateCriteria->getState()
                ));

                //if no zip code is returned the return an empty set
                if ( $cityStateToZipStmt->rowCount() === 0 ) {
                    return array();
                }

                $cityStateToZipResult = $cityStateToZipStmt->fetchAll();
                $cityStateToZipStmt->closeCursor();
                $zipCode = $cityStateToZipResult[0]['zip_code'];
            } else {
                $zipCode = $this->_zipCodeCriteria->getCriteria();
            }

            //if a radius is provided then get all zip codes within the given
            //radius and add them to a sql IN statement
            if ( $this->_useRadius ) {

                //fetch the coordinates of the provided zipCode
                $zipCoordsSql = 'CALL ZipCodes_GetCoordsByZipCode(:zipCode)';
                $zipCoordsStmt = $db->prepare($zipCoordsSql);
                $zipCoordsStmt->execute( array('zipCode' => $zipCode) );

                //if no coordinates are returned return an empty set
                if ( $zipCoordsStmt->rowCount() === 0 ) {
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
                $zipCodesInjector .= ') ';

                //append the zip code injector to the query string
                $listingsSql .= $zipCodesInjector;
            } else {
                $zipCodesInjector = ' AND li.ZipCode IN( ? ) ';
                $listingsSql .= $zipCodesInjector;
                $variableArray[] = $zipCode;
            }

            if ( $this->_useNumberOfBedrooms ) {
                $numberOfBedRoomsInjector = ' AND li.Bedrooms = ? ';
                $variableArray[] = $this->_numberOfBedroomsCriteria->getCriteria();
                $listingsSql .= $numberOfBedRoomsInjector;
            }

            if ( $this->_useNumberOfBathrooms ) {
                $numberOfBathroomsInjector = ' AND li.Bathrooms >= ?';
                $variableArray[] = $this->_numberOfBathroomsCriteria->getCriteria();
                $listingsSql .= $numberOfBathroomsInjector;
            }

            if ( $this->_useMinRent and $this-> _useMaxRent ) {
                $rentInjector = ' AND (li.Rent >= ? AND li.Rent <= ? )';
                $variableArray[] = $this->_minRentCriteria->getCriteria();
                $variableArray[] = $this->_maxRentCriteria->getCriteria();
                $listingsSql .= $rentInjector;
            } else {

                if ( $this->_useMinRent ) {
                    $minRentInjector = ' AND li.Rent >= ?';
                    $variableArray[] = $this->_minRentCriteria->getCriteria();
                    $listingsSql .= $minRentInjector;
                }

                if ( $this->_useMaxRent ) {
                    $maxRentInjector = ' AND li.Rent <= ?';
                    $variableArray[] = $this->_maxRentCriteria->getCriteria();
                    $listingsSql .= $maxRentInjector;
                }
            }

            //prepare the statement and execute the search query
            $listingStmt = $db->prepare($listingsSql);
            $listingStmt->execute($variableArray);
            $listings = $listingStmt->fetchAll();
            $listingStmt->closeCursor();
            return $listings;
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    public function autoCompleteCityStateOrZip()
    {
        if ( !isset( $this->_autocompleteCriteria ) ) {
            throw new Exception('No autocompleteCriteria was set.');
        }

        //validate the autocomplete criteria sent along to the controller
        if ( !$this->_autocompleteCriteria->isValid() ) {
            $this->results['result'] = 'error';
            $this->results['reasons'] = $this->_autocompleteCriteria->getValidationErrors();
        } else {

            $sql = 'CALL ZipCodes_AutoSuggestLocations( :city, :state, :zipCode, 1)';
            $variableArray = array();

            //if we have a zip code
            if ( $this->_autocompleteCriteria->hasZipCode() ) {
                $sql = str_replace( ':city', '\'\'', $sql );
                $sql = str_replace( ':state', '\'\'', $sql );
                $variableArray['zipCode'] = $this->_autocompleteCriteria->getZipCode();
            }

            //if we have a city
            if ( $this->_autocompleteCriteria->hasCity() ) {
                $sql = str_replace( ':zipCode', '\'\'', $sql );
                $variableArray['city'] = $this->_autocompleteCriteria->getCity();
            }

            //include the state if it was detected
            if ( $this->_autocompleteCriteria->hasState() ) {
                $variableArray['state'] = $this->_autocompleteCriteria->getState();
            } else {
                $sql = str_replace( ':state', '\'\'', $sql );
            }

            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $stmt = $db->prepare($sql);
                $stmt->execute($variableArray);
                $suggestedValues = $stmt->fetchAll();
                $stmt->closeCursor();
                $this->results['result'] = 'success';
                $this->results['suggestedValues'] = $suggestedValues;
            } catch ( Exception $e ) {
                $this->results['result'] = 'server error';
                $this->results['reasons'] = $e->getMessage();
            }
        }

        return $this->results;
    }

    /**
     * Returns the total number of active listings
     *
     * @return array containing the total number of active listings
     */
    public function getActiveListingsCount()
    {
        $listingSql =
            'SELECT COUNT(*) AS TotalActiveListings
            FROM far_listings listings,
              (SELECT LandlordID
                 FROM far_landlords
                WHERE far_landlords.Active = 1
                  AND far_landlords.Deleted = 0
                  AND ( DATE(far_landlords.ExpirationDate) >= DATE(now()) OR far_landlords.ExpirationDate IS NULL ) ) landlords
            WHERE listings.LandlordID = landlords.LandlordID
              AND listings.Active = 1
              AND listings.Deleted = 0
              AND ( DATE(listings.ExpirationDate) >= DATE(now()) OR listings.ExpirationDate IS NULL )';

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $stmt = $db->prepare($listingSql);
            $stmt->execute();
            $listingTotal = $stmt->fetchAll();
            $this->results['result'] = 'success';
            $this->results['TotalActiveListings'] = $listingTotal[0]['TotalActiveListings'];
        } catch(Exception $e ) {
            $this->results['result'] = 'server error';
            $this->results['reasons'] = $e->getMessage();
        }

        return $this->results;
    }

    public function getListingsByLandlord()
    {
        if ( !isset( $this->_landlordIdCriteria ) ) {
            throw new Exception('No landlordIdCriteria was set.');
        }

        $listingSql =
          'SELECT
            listings.*,
              ( SELECT COUNT(*) FROM far_ads a WHERE a.ParentID = listings.ListingID AND a.Type = \'Listing\' AND a.Active = 1 ) AS AdsCount,
              ( SELECT COUNT(*) FROM far_listings_notes n WHERE n.ListingID = listings.ListingID AND Note != \'\' ) AS NotesCount,
              IFNULL(Views,0) AS Views, IFNULL(ContactRequests,0) AS ContactRequests, IFNULL(ContactEmailsSent,0) AS ContactEmailsSent
          FROM (
            SELECT * FROM (
               SELECT li.*, l.Premium, l.Rebates
               FROM far_listings li
               INNER JOIN
                 far_landlords l ON li.LandlordID = l.LandlordID
               WHERE li.LandlordID = :landlordId
               AND li.Active = 1
               AND li.Deleted = 0
            ) AS LandlordListings
          ) AS listings


          LEFT JOIN (
            SELECT ListingID, SUM(Views) AS Views, SUM(ContactRequests) AS ContactRequests, SUM(ContactEmailsSent) AS ContactEmailsSent
            FROM far_listings_tracking GROUP BY ListingID
          ) AS Tracking ON listings.ListingID = Tracking.ListingID


          ORDER BY listings.ListingID IN (
            SELECT ListingID
            FROM far_listings_photos p
            WHERE p.Deleted = 0
          ) DESC, AddedDate DESC;';

        if ( !$this->_landlordIdCriteria->isValid() ) {
            $this->results['result'] = 'error';
            $this->results['reasons'] = $this->_landlordIdCriteria->getValidationErrors();
        } else {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $stmt = $db->prepare($listingSql);
                $stmt->execute( array('landlordId' => $this->_landlordIdCriteria->getCriteria() ) );
                $listings = $stmt->fetchAll();
                $this->results['result'] = 'success';
                $this->results['listings'] = $listings;
            } catch(Exception $e ) {
                $this->results['result'] = 'server error';
                $this->results['reasons'] = $e->getMessage();
            }
        }

        return $this->results;
    }

    /**
     * @return array containing the listings photos
     * @throws Exception when listingIdCriteria is not set
     */
    public function getListingImages()
    {
        if ( !isset($this->_listingIdCriteria) ) {
            throw new Exception('No listingIdCriteria was set.');
        }

        if ( !$this->_listingIdCriteria->isValid() ) {
            $this->results['result'] = 'error';
            $this->results['reasons'] = $this->_listingIdCriteria->getValidationErrors();
        } else {
            $id = $this->_listingIdCriteria->getCriteria();
            $db = Zend_Db_Table::getDefaultAdapter();
            try {
                $sql = 'CALL FAR_Listings_GetPhotosByListingID(:id,1)';
                $resultObj = $db->prepare($sql);
                $resultObj->execute(array('id' => $id));
                $this->results['result'] = 'success';
                $this->results['photos'] = $resultObj->fetchAll();
            } catch ( Exception $e ) {
                $this->results['result'] = 'error';
                $this->results['reasons'] = $e->getMessage();
            }
        }

        //return our results
        return $this->results;
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

    /**
     * Sets the dependency for the numberOfBathrooms criteria object
     *
     * @param $numberOfBathrooms
     * @throws Exception when $numberOfBathroomsCriteria is not an instance of Custom_NumberOfBathroomsCriteria
     */
    public function setNumberOfBathroomsCriteria( $numberOfBathrooms ) {
        if ( $numberOfBathrooms instanceof Custom_NumberOfBathroomsCriteria ) {
            $this->_numberOfBathroomsCriteria = $numberOfBathrooms;
        } else {
            throw new Exception('numberOfBathroomsCriteria must be an instance of Custom_NumberOfBathroomsCriteria');
        }
    }

    public function setMinRentCriteria( $minRent ) {
        if ( $minRent instanceof Custom_RentCriteria ) {
            $this->_minRentCriteria = $minRent;
        } else {
            throw new Exception('minRent must be an instance of Custom_RentCriteria');
        }
    }

    public function setMaxRentCriteria( $maxRent ) {
        if ( $maxRent instanceof Custom_RentCriteria ) {
            $this->_maxRentCriteria = $maxRent;
        } else {
            throw new Exception('maxRent must be an instance of Custom_RentCriteria');
        }
    }

    public function setLandlordIdCriteria( $landlordId ) {
        if ( $landlordId instanceof Custom_IdCriteria ) {
            $this->_landlordIdCriteria = $landlordId;
        } else {
            throw new Exception('$landlordId must be an instance of Custom_IdCriteria');
        }

        return $this;
    }

    public function setListingIdCriteria( $listingId ) {
        if ( $listingId instanceof Custom_IdCriteria ) {
            $this->_listingIdCriteria = $listingId;
        } else {
            throw new Exception('$listingId must be an instance of Custom_IdCriteria');
        }

        return $this;
    }

    public function setAutocompleteCriteria( $autocompleteCriteria )
    {
        if ( $autocompleteCriteria instanceof Custom_AutocompleteCriteria ) {
            $this->_autocompleteCriteria = $autocompleteCriteria;
        } else {
            throw new Exception('$autocompleteCriteria must be an instance of Custom_AutocompleteCriteria');
        }

        return $this;
    }
}