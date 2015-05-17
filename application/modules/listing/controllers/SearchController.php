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
}