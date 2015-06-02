<?php

class Listing_SearchController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
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
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
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

        //search listings
        $searchResults = $search->searchListings();

        //process and send the response
        if ( $searchResults['result'] === 'error' ) {
            $this->getResponse()->setHttpResponseCode(400);
        } elseif ( $searchResults['result'] === 'server error' ) {
            $this->getResponse()->setHttpResponseCode(500);
        } else {
            $this->getResponse()->setHttpResponseCode(200);
        }
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

    public function totalActiveListingsAction()
    {
        $search = new Listing_Model_Search();
        $searchResults = $search->getActiveListingsCount();

        if ( $searchResults['result'] === 'server error') {
            $this->getResponse()->setHttpResponseCode(500);
        } else {
            $this->getResponse()->setHttpResponseCode(200);
        }
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        $this->_helper->json->sendJson( $searchResults, false, true );
    }

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
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        return $this->_helper->json->sendJson( $searchResults, false, true );
    }

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
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        return $this->_helper->json->sendJson( $searchResults, false, true );
    }

    public function getAutocompleteSuggestionsAction()
    {
        $search = new Listing_Model_Search();
        $autocompleteData = $this->getRequest()->getParam('autocomplete-data');
        $autocompleteCriteria = new Custom_AutocompleteCriteria($autocompleteData);

        var_dump($autocompleteCriteria);

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
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        return $this->_helper->json->sendJson( $searchResults, false, true );
    }
}