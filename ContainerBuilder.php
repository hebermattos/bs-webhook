<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Config\Adapter\Ini as IniConfig;
use GuzzleHttp\Client;

class ContainerBuilder 
{
    public static function build() 
    {
        public static $config = new IniConfig("config.ini");
        
        $diFactory = new FactoryDefault();

        $diFactory->set('config', function () {
            return $config;
        });
        
        $diFactory->set('request', function () {
            return new Request();
        });
        
        $diFactory->set('client', function () {
            return new Client();
        });
        
         $diFactory->set('response', function () {
            $response =  new response();
            $response->setContentType('application/json');
            return $response;
        });

        return $diFactory;
    }

}