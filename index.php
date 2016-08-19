<?php

require 'vendor/autoload.php';
require 'ContainerBuilder.php';

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7;

$di = ContainerBuilder::Build();

$app = new Micro();
$app->setDI($di);

$app->before(function () use ($app) {
    
    $header = $app->request->getHeader('HTTP_X_HUB_SIGNATURE');
    $rawBody = $app->request->getRawBody();
    $hashedBody = hash_hmac('sha1', $rawBody, $app->config->environment->secret);
    
    //$code = "401";
    //$status = "NOT AUTHORIZED";
    
    return strcmp($header, 'sha1='.$hashedBody);
});

$app->get('/', function ()  {
    echo 'bswebhook';
});

$app->post('/bswebhook', function () use ($app) {
    
    $response = new Response();
    $response->setContentType('application/json');
    $code = "200";
    $status = 'OK';
    $data = NULL;
   
    try {
        $options = ['json' => $app->request->getJsonRawBody(),  'Authorization' => ['Basic '.$app->config->environment->token] ];
        $data = $app->client->request('POST', $app->config->environment->url, $options)->getBody()->getContents();
    } catch (ServerException $e) {
        $code = 500;
        $status = "INTERNAL SERVER ERROR";
        $data =  Psr7\str($e->getResponse());
    } catch (ClientException $e) {
        $code = 400;
        $status = "BAD REQUEST";
        $data =  Psr7\str($e->getResponse());
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