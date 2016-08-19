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

    if(strcmp($header, 'sha1='.$hashedBody) != 0);
        return 'mantega';
        //throw new Exception("NOT AUTHORIZED");
});

$app->get('/', function ()  {
    echo 'bswebhook';
});

$app->post('/bswebhook', function () use ($app) {
    
    $code = "200";
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

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();