<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRestController()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $restRoute = new Zend_Rest_Route($front);
        $router->addRoute('default', $restRoute);
    }

    protected function _initCustomLibraries()
    {
        //register custom classes
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Custom_');
    }

    protected function _initListingResources()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH . 'modules/listing/',
            'namespace' => 'Listing_'
        ));

        $resourceLoader->addResourceType( 'model', 'models/', 'Model');
        return $resourceLoader;
    }
}

