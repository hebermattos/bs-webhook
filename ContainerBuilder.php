<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Config\Adapter\Ini as IniConfig;
use GuzzleHttp\Client;

class ContainerBuilder 
{
    public static function build() 
    {
        $diFactory = new FactoryDefault();

        $diFactory->setShared('config', function () {
            return new IniConfig("config.ini");
        });
        
        $diFactory->setShared('client', function () {
            return new Client();
        });

        return $diFactory;
    }

}