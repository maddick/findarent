<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRestController()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $restRoute = new Zend_Rest_Route($front, array(), array(
            'listing' => array( 'rest', ),
            'communities' => array( 'rest' )
        ));
        $router->addRoute('rest', $restRoute);
    }

    protected function _initCustomLibraries()
    {
        //register custom classes
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Custom_');
    }
}

