<?php

require 'vendor/autoload.php';

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Di\FactoryDefault;
use Phalcon\Config\Adapter\Ini as IniConfig;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7;

$di = new FactoryDefault();

$di->set('config', function () {
    return new IniConfig("config.ini");
});

$app = new Micro();

$app->setDI($di);

$app->get('/', function ()  {
    echo 'bswebhook';
});

$app->post('/bswebhook', function () use ($app) {
    
    $request = new Request();
    
    $header = $request->getHeader('HTTP_X_HUB_SIGNATURE');
    $rawBody = $request->getRawBody();
    $hashedBody = hash_hmac('sha1', $rawBody, $app->config->environment->boletosimpleswebhooksecret);
    
    $response = new Response();
    $response->setContentType('application/json');
    $code = "200";
    $status = 'OK';
    $data = NULL;
    
    if(strcmp($header, 'sha1='.$hashedBody) == 0)
    {
        $client = new Client();
        
        try {
            $data = $client->request('POST', $app->config->environment->billapiurl,  [
                'json' => $request->getJsonRawBody(),
                'Authorization' => ['Basic '.$app->config->environment->billapitoken]
                ]
            );
        } catch (ServerException $e) {
            $code = 500;
            $status = "INTERNAL SERVER ERROR";
            $data =  Psr7\str($e->getResponse());
        } catch (ClientException $e) {
            $code = 400;
            $status = "BAD REQUEST";
            $data =  Psr7\str($e->getResponse());
        }
    }
    else {
        $code = "401";
        $status = "NOT AUTHORIZED";
    }
    
    $response->setJsonContent(
        array(
            'status' => $status,
            'data'   => $data,
        )
    );
    
    $response->setStatusCode($code);
    
    return $request->getHeaders();

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();