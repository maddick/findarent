<?php

class Listing_RestController extends Zend_Rest_Controller
{

    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error' ), false, true );
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
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        //get the listing ID parameter
        $id = $this->getRequest()->getParam('id');
        $listingIdCriteria = new Custom_IdCriteria( intval($id) );

        //create and initialize a listing model
        $listingModel = new Listing_Model_Listing();

        //get the listing
        $listing = $listingModel->setListingIdCriteria($listingIdCriteria)->getListing();

        //process and send response
        if ( $listing['result'] === 'success' ){
            if ( empty( $listing['listing'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson( $listing, false, true );
    }

    /**
     * The head action handles HEAD requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function headAction()
    {
        // TODO: Implement headAction() method.
        $this->setHeader();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error' ), false, true );
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error' ), false, true );
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error' ), false, true );
    }
}
