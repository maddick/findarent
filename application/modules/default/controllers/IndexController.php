<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->getResponse()->setHttpResponseCode(404);
        $this->_helper->json->sendJson(array('result' => 'invalid'),false, true);
    }


}

