<?php

class Brokers_FeaturedBrokersController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function setHeader()
    {
        $config = new Zend_config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        if ( $this->getRequest()->getHeader('Origin') ) {
            foreach( $config->headers->allowOrigin as $header ) {
                if ( $header === $this->getRequest()->getHeader('Origin') ) {
                    $this->getResponse()->setHeader('Access-Control-Allow-Origin', $header);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getBrokerCitiesByStateAction()
    {
        //get parameters
        $state = $this->getRequest()->getParam('state');

        //create a state criteria
        $stateCriteria = new Custom_StateCriteria( $state );

        //initialize a featured communities model and get a list of cities by state
        $featuredCommunitiesModel = new Brokers_Model_FeaturedBrokers();
        $citiesByState = $featuredCommunitiesModel->setStateCriteria($stateCriteria)->getBrokerCitiesByState();

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
        $this->setHeader();
        $this->_helper->json->sendJson( $citiesByState, false, true );
    }


    /**
     * The search action handles searches against featured brokers and receives a 'city-state' criteria. It returns
     * a json string containing the results of the search.
     */
    public function searchAction()
    {
        //initialize a community model
        $featuredBrokersModel = new Brokers_Model_FeaturedBrokers();

        //get our parameters
        $searchCriteria = $this->getRequest()->getParams();

        //if we have a city-state then set the dependency for the search
        if ( array_key_exists( 'city-state', $searchCriteria ) ) {
            $cityStateCriteria = new Custom_CityStateCriteria( $searchCriteria['city-state'] );
            $featuredBrokersModel->setCityStateCriteria( $cityStateCriteria );
        }

        if ( array_key_exists( 'zip-code', $searchCriteria ) ) {
            $zipCodeCriteria = new Custom_ZipCodeCriteria( $searchCriteria['zip-code'] );
            $featuredBrokersModel->setZipCodeCriteria( $zipCodeCriteria );
        }

        //search the communities
        $searchResults = $featuredBrokersModel->searchFeaturedBrokers();

        //process and send our results
        if ( $searchResults['result'] === 'success' ) {
            if ( empty( $searchResults['brokers'] ) ) {
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
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        $this->setHeader();
        $this->_helper->json->sendJson( $searchResults, false, true );
    }
}