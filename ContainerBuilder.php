<?php

use Phalcon\Di\FactoryDefault;

class ContainerBuilder 
{
    public static Build () 
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