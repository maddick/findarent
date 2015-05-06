<?php

class Listing_SearchController extends Zend_Rest_Controller
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
        // TODO: Implement indexAction() method.
        $this->forward( 'get' );
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        // TODO: Implement getAction() method.

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
            $numberOfBedroomsCriteria = new Custom_NumberOfBedroomsCriteria( floatval($searchCriteria['number-of-bedrooms']));
            $search->setNumberOfBedroomsCriteria($numberOfBedroomsCriteria);
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

    /**
     * The head action handles HEAD requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function headAction()
    {
        // TODO: Implement headAction() method.
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        // TODO: Implement postAction() method.
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error'), false, true);
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        // TODO: Implement putAction() method.
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error'), false, true);
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        // TODO: Implement deleteAction() method.
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error'), false, true);
    }
}