<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Request;
use Phalcon\Config\Adapter\Ini as IniConfig;

class ContainerBuilder 
{
    public static function Build() 
    {
        $di = new FactoryDefault();

        $di->set('config', function () {
            return new IniConfig("config.ini");
        });
        
        $di->set('request', function () {
            return new Request();
        });
        
        $di->set('client', function () {
            return new Client();
        });
        
        return $di;
    }

}