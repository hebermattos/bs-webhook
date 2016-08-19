<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Request;
use Phalcon\Config\Adapter\Ini as IniConfig;
use GuzzleHttp\Client;

class ContainerBuilder 
{
    public static function build() 
    {
        $diFactory = new FactoryDefault();

        $diFactory->set('config', function () {
            return new IniConfig("config.ini");
        });
        
        $diFactory->set('request', function () {
            return new Request();
        });
        
        $diFactory->set('client', function () {
            return new Client();
        });
        
        return $diFactory;
    }

}