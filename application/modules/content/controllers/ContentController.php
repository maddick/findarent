<?php

class Content_ContentController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function setHeader()
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
        $this->getResponse()->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
    }

    public function preDispatch()
    {
        $this->setHeader();
    }

    public function getContentAction()
    {
        $result = array();
        //handle pre-flight requests
        if ( $this->getRequest()->isOptions() ) {
            return;
        }

        try {
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body, Zend_Json::TYPE_ARRAY);

            $contentModel = new Content_Model_Content();

            if ( !is_null( $data ) and !empty($data) ) {

                if ( array_key_exists('content', $data ) ) {
                    $contentCriteria = new Custom_ContentCriteria($data['content']);
                    $contentModel->setContentCriteria($contentCriteria);
                }
            }

            $result = $contentModel->getContent();

        } catch ( Zend_Json_Exception $json_e) {
            $result['result'] = 'json error';
            $result['reasons'] = $json_e->getMessage();
        } catch ( Exception $e ) {
            $result['result'] = 'server error';
            $result['reasons'] = $e->getMessage();
        }

        if ( $result['result'] === 'success' ) {
            $this->getResponse()->setHttpResponseCode(200);
        } elseif ( $result['result'] === 'error' or $result['result'] === 'json error' ) {
            $this->getResponse()->setHttpResponseCode(400);
        } else {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_helper->json->sendJson($result, false, true);
    }
}