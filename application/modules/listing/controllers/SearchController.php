<?php

class Listing_SearchController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function setHeader()
    {
        $config = new Zend_config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        if ( $this->getRequest()->getHeader('Origin') and !$config->headers->allowOriginOverride ) {
            foreach( $config->headers->allowOrigin as $header ) {
                if ( $header === $this->getRequest()->getHeader('Origin') ) {
                    $this->getResponse()->setHeader('Access-Control-Allow-Origin', $header);
                }
            }
        } else if ( $config->headers->allowOriginOverride ) {
            $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        }
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
    }

    public function preDispatch()
    {
        $this->setHeader();
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        $this->forward( 'getListings' );
    }

    /**
     * This function gathers search parameters from the url and uses them
     * to perform a search against active listings.
     */
    public function getListingsAction()
    {
        //create a new listing search model
        $search = new Listing_Model_Search();

        //get the search criteria
        $searchCriteria = $this->getRequest()->getParams();

        //set each existing criteria in the params
        if( array_key_exists('zip-code', $searchCriteria) ) {
            $zipCodeCriteria = new Custom_ZipCodeCriteria( $searchCriteria['zip-code'] );
            $search->setZipCriteria($zipCodeCriteria);
        }

        if ( array_key_exists('city-state', $searchCriteria ) ) {
            $cityStateCriteria = new Custom_CityStateCriteria( $searchCriteria['city-state'] );
            $search->setCityStateCriteria($cityStateCriteria);
        }

        if( array_key_exists('radius', $searchCriteria)) {
            $radiusCriteria = new Custom_RadiusCriteria( intval( $searchCriteria['radius'] ) );
            $search->setRadiusCriteria($radiusCriteria);
        }

        if( array_key_exists('number-of-bedrooms', $searchCriteria)) {
            $numberOfBedroomsCriteria = new Custom_NumberOfBedroomsCriteria( intval($searchCriteria['number-of-bedrooms']));
            $search->setNumberOfBedroomsCriteria($numberOfBedroomsCriteria);
        }

        if( array_key_exists('number-of-bathrooms', $searchCriteria)) {
            $numberOfBathroomsCriteria = new Custom_NumberOfBathroomsCriteria( floatval($searchCriteria['number-of-bathrooms']));
            $search->setNumberOfBathroomsCriteria($numberOfBathroomsCriteria);
        }

        if ( array_key_exists('min-rent', $searchCriteria) ) {
            $minRentCriteria = new Custom_RentCriteria(doubleval($searchCriteria['min-rent']));
            $search->setMinRentCriteria($minRentCriteria);
        }

        if ( array_key_exists('max-rent', $searchCriteria) ) {
            $maxRentCriteria = new Custom_RentCriteria(doubleval($searchCriteria['max-rent']));
            $search->setMaxRentCriteria($maxRentCriteria);
        }

        if ( array_key_exists('type', $searchCriteria ) ) {
            $rentalTypeCriteria = new Custom_RentalTypeCriteria($searchCriteria['type']);
            $search->setRentalTypeCriteria($rentalTypeCriteria);
        }

        //search listings
        $searchResults = $search->searchListings();

        //process and send the response
        if ( $searchResults['result'] === 'success' ) {
            if ( empty( $searchResults['listings'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            if ( $searchResults['result'] === 'server error' ) {
                $this->getResponse()->setHttpResponseCode(500);
            } else {
                $this->getResponse()->setHttpResponseCode(400);
            }
        }

        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * This function returns all cities and zip codes.
     */
    public function getAllCitiesAndZipCodesAction()
    {
        $search = new Listing_Model_Search();
        $searchResults = $search->getAllCitiesAndZipCodes();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['cities-and-zip-codes'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * @deprecated
     */
    public function getAutocompleteSuggestionsAction()
    {
        $search = new Listing_Model_Search();
        $autocompleteData = $this->getRequest()->getParam('autocomplete-data');
        $autocompleteCriteria = new Custom_AutocompleteCriteria($autocompleteData);

        $searchResults = $search->setAutocompleteCriteria($autocompleteCriteria)->autoCompleteCityStateOrZip();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['suggestedValues'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    public function getListingsByBrokerIdAction()
    {
        $search = new Listing_Model_Search();
        $brokerId = $this->getRequest()->getParam('broker-id');
        $brokerIdCriteria = new Custom_IdCriteria(intval($brokerId));

        $searchResults = $search->setBrokerIdCriteria($brokerIdCriteria)->getListingsByBrokerId();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['listings'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    public function getListingsByCommunityIdAction()
    {
        $search = new Listing_Model_Search();
        $communityId = $this->getRequest()->getParam('community-id');
        $communityIdCriteria = new Custom_IdCriteria(intval($communityId));

        $searchResults = $search->setCommunityIdCriteria($communityIdCriteria)->getListingsByCommunityId();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['listings'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * This function searches against active listings by a given landlord id.
     */
    public function getListingsByLandlordAction()
    {
        $search = new Listing_Model_Search();
        $landlordId = $this->getRequest()->getParam('landlord-id');
        $landlordIdCriteria = new Custom_IdCriteria(intval($landlordId));

        $searchResults = $search->setLandlordIdCriteria($landlordIdCriteria)->getListingsByLandlord();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['listings'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * This function returns the photos associated to a listing.
     */
    public function getPhotosByListingIdAction()
    {
        $search = new Listing_Model_Search();
        $listingId = $this->getRequest()->getParam('listing-id');
        $listingIdCriteria = new Custom_IdCriteria(intval($listingId));

        $searchResults = $search->setListingIdCriteria($listingIdCriteria)->getListingImages();

        if ( $searchResults['result'] === 'success' ){
            if ( empty( $searchResults['photos'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * This function returns a list of unique cities / states that have valid
     * active listings.
     */
    public function getPopularSearchesAction()
    {
        $search = new Listing_Model_Search();
        $searchResults = $search->getPopularSearches();

        if ( $searchResults['result'] === 'success' ) {
            if ( empty($searchResults['searches'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    /**
     * This function returns the total active listings count
     */
    public function totalActiveListingsAction()
    {
        $search = new Listing_Model_Search();
        $searchResults = $search->getActiveListingsCount();

        if ( $searchResults['result'] === 'server error') {
            $this->getResponse()->setHttpResponseCode(500);
        } else {
            $this->getResponse()->setHttpResponseCode(200);
        }
        $this->_helper->json->sendJson( $searchResults, false, true );
    }
}