<?php

class Brokers_RestController extends Zend_Rest_Controller
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
        // TODO: Implement indexAction() method.
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $id = $this->getRequest()->getParam("id");
        $brokerIdCriteria = new Custom_IdCriteria( intval( $id ) );

        $brokerModel = new Brokers_Model_Broker( $brokerIdCriteria );
        $broker = $brokerModel->getBroker();

        if ( $broker['result'] === 'success' ) {
            if ( empty( $broker['brokers'] ) ) {
                $this->getResponse()->setHttpResponseCode(404);
            } else {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } else {
            if ( $broker['result'] === 'server error' ) {
                $this->getResponse()->setHttpResponseCode(500);
            } else {
                $this->getResponse()->setHttpResponseCode(400);
            }
        }

        return $this->_helper->json->sendJson( $broker, false, true );
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
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error', false, true ));
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error', false, true ));
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
        $this->_helper->json->sendJson( array( 'result' => 'error', false, true ));
    }
}