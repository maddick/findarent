<?php

class Communities_FeaturedCommunitiesController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function getCommunityCitiesByStateAction()
    {
        $state = $this->getRequest()->getParam('state');
        $stateCriteria = new Custom_StateCriteria( $state );
        $featuredCommunitiesModel = new Communities_Model_FeaturedCommunities();
        $featuredCommunitiesModel->setStateCriteria($stateCriteria);
        $citiesByState = $featuredCommunitiesModel->getCommunityCitiesByState();

        if ( $citiesByState['result'] === 'success' ) {
            if ( empty( $citiesByState['cities'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            if ( $citiesByState['result'] === 'server error' ) {
                $this->getResponse()->setHttpResponseCode(500);
            } else {
                $this->getResponse()->setHttpResponseCode(400);
            }
        }

        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        return $this->_helper->json->sendJson( $citiesByState, false, true );
    }

    public function searchAction()
    {
        //initialize a search model
        $searchModel = new Communities_Model_FeaturedCommunitySearch();

        //get our parameters
        $searchCriteria = $this->getRequest()->getParams();

        //if we have a city-state then set the dependency for the search
        if ( array_key_exists( 'city-state', $searchCriteria ) ) {
            $cityStateCriteria = new Custom_CityStateCriteria( $searchCriteria['city-state'] );
            $searchModel->setCityStateCriteria( $cityStateCriteria );
        }

        //search the communities
        $searchResults = $searchModel->searchFeaturedCommunities();

        //process and send our results
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
}