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

        return $this->_helper->json->sendJson( $citiesByState, false, true );
    }
}