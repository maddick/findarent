<?php
class Communication_MessageController extends Zend_Controller_Action
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

        $this->getResponse()->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
    }

    public function preDispatch()
    {
        $this->setHeader();
    }

    public function sendEmailToFriendAction()
    {
        $result = array();

        //handle pre-flight requests
        if ( $this->getRequest()->isOptions() ) {
            return;
        }

        //only accept post requests
        if ( !$this->getRequest()->isPost() ) {
            $result['result'] = 'method error';
            $result['reasons'] = 'Method not allowed';
            $this->getResponse()->setHttpResponseCode(405);
            $this->_helper->json->sendJson($result, false, true);
        }

        //ensure data type as json in request headers
        if ( $this->getRequest()->getHeader('Content-Type') !== 'application/json' ) {
            $result['result'] = 'error';
            $result['reason'] = 'requests must be json';
            $this->getResponse()->setHttpResponseCode(400);
            $this->_helper->json->sendJson($result, false, true);
        }

        //and away we go...
        try {
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body, Zend_Json::TYPE_ARRAY);

            $messageModel = new Communication_Model_SendListingToFriendMessage();

            if ( array_key_exists( 'senderName', $data ) ) {
                $messageModel->setSenderName($data['senderName']);
            }

            if ( array_key_exists( 'recipientName', $data ) ) {
                $messageModel->setRecipientName($data['recipientName']);
            }

            if ( array_key_exists( 'recipientAddress', $data ) ) {
                $recipientAddress = new Custom_EmailCriteria($data['recipientAddress']);
                $messageModel->setRecipientAddress($recipientAddress);
            }

            if ( array_key_exists( 'listingTitle', $data ) ) {
                $messageModel->setListingTitle($data['listingTitle']);
            }

            if ( array_key_exists( 'listingURL', $data ) ) {
                $messageModel->setListingURL($data['listingURL']);
            }

            if ( array_key_exists( 'listingNumber', $data ) ) {
                $messageModel->setListingNumber($data['listingNumber']);
            }

            $result = $messageModel->sendMessage();

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