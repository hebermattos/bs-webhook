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

$di->set('request', function () {
    return new Request();
});

$app = new Micro();
$app->setDI($di);

$app->get('/', function ()  {
    echo 'bswebhook';
});

$app->post('/bswebhook', function () use ($app) {
    
    $header = $app->request->getHeader('HTTP_X_HUB_SIGNATURE');
    $rawBody = $app->request->getRawBody();
    $hashedBody = hash_hmac('sha1', $rawBody, $app->config->environment->secret);
    
    $response = new Response();
    $response->setContentType('application/json');
    $code = "200";
    $status = 'OK';
    $data = NULL;
    
    if(strcmp($header, 'sha1='.$hashedBody) == 0)
    {
        try {
            $client = new Client();
            $options = ['json' => $app->request->getJsonRawBody(),  'Authorization' => ['Basic '.$app->config->environment->token] ];
            $data = $client->request('POST', $app->config->environment->url, $options)->getBody()->getContents();
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
            'data'   => $data
        )
    );
    $response->setStatusCode($code);
    return $response;

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();