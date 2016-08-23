<?php

require 'vendor/autoload.php';
require 'ContainerBuilder.php';

use Phalcon\Mvc\Micro;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

$di = ContainerBuilder::Build();

$app = new Micro();
$app->setDI($di);

$app->before(function () use ($app) {
    
    $header = $app->request->getHeader('HTTP_X_HUB_SIGNATURE');
    $rawBody = $app->request->getRawBody();
    $hashedBody = hash_hmac('sha1', $rawBody, $app->config->environment->secret);

    if(strcmp($header, 'sha1='.$hashedBody) != 0)
    {
        $app->response->setJsonContent(
            array(
                'status' => 'NOT AUTHORIZED',
                'data'   => null
                )
            );
        $app->response->setStatusCode(401);
        $app->response->send();
        
        return false;
    }
    
    return true;
});

$app->post('/bswebhook', function () use ($app) {
    
    $code = "201";
    $status = 'OK';
    $data = NULL;
   
    try {
        $options = ['json' => $app->request->getJsonRawBody(),  'Authorization' => ['Basic '.$app->config->environment->token] ];
        $data = $app->client->request('POST', $app->config->environment->url, $options)->getBody()->getContents();
    } catch (ServerException $e) {
        $code = 500;
        $status = "INTERNAL SERVER ERROR";
        $data =  $e->getResponse()->getBody()->getContents();
    } catch (ClientException $e) {
        $code = 400;
        $status = "BAD REQUEST";
        $data =  $e->getResponse()->getBody()->getContents();
    }
    
    $app->response->setJsonContent(
        array(
            'status' => $status,
            'data'   => $data
        )
    );
    
    $app->response->setStatusCode($code);
    return $app->response;

});

$app->get('/', function () {
    echo 'bswebhook';
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
});

$app->handle();