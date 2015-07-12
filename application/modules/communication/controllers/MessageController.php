<?php
class Communication_MessageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function sendEmailToFriendAction()
    {
        $result = array();

        if ( !$this->getRequest()->isPost() ) {
            $result['result'] = 'method error';
            $result['reasons'] = 'Method not allowed';
            $this->getResponse()->setHttpResponseCode(405);
            $this->_helper->json->sendJson($result, false, true);
        }

        if ( $this->getRequest()->getHeader('Content-Type') !== 'application/json' ) {
            $result['result'] = 'error';
            $result['reason'] = 'requests must be json';
            $this->getResponse()->setHttpResponseCode(400);
            $this->_helper->json->sendJson($result, false, true);
        }

        try {
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body, Zend_Json::TYPE_ARRAY);

            $messageModel = new Communication_Model_Message();

            if ( array_key_exists( 'senderName', $data ) ) {
                $messageModel->setSenderName($data['senderName']);
            }

            if ( array_key_exists( 'recipientName', $data ) ) {
                $messageModel->setRecipientName($data['recipientName']);
            }

            if ( array_key_exists( 'recipientAddress', $data ) ) {
                $messageModel->setRecipientAddress($data['recipientAddress']);
            }

            if ( array_key_exists( 'listingTitle', $data ) ) {
                $messageModel->setListingTitle($data['listingTitle']);
            }

            if ( array_key_exists( 'listingURL', $data ) ) {
                $messageModel->setListingURL($data['listingURL']);
            }

            if ( array_key_exists( 'listingNumber', $data ) ) {
                $messageModel->setListingNubmer($data['listingNumber']);
            }

            $result = $messageModel->sendEmail(Communication_Model_Message::TYPE_TO_FRIEND);

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