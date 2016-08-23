<?php

require 'vendor/autoload.php';
require 'ContainerBuilder.php';

use Phalcon\Mvc\Micro;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Phalcon\Http\Response;

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
 
    $options = ['json' => $app->request->getJsonRawBody(),  'Authorization' => ['Basic '.$app->config->environment->token] ];
    $promise = $app->client->requestAsync('POST', $app->config->environment->url, $options);
                  
    $data = null;                  

    $promise->then(
        function (ResponseInterface $response) {
            $result = new Response();
            $result->setJsonContent(array('status' => 'OK','data' => $response->getBody()->getContents()));
            $result->setStatusCode(200);
            $result->setContentType('application/json');
            $data = $result;
            var_dump($data);
        },
        function (RequestException  $e) {
            $result = new Response();
            $result->setJsonContent(array('status' => 'ERRO','data' => $e->getResponse()->getBody()->getContents()));
            $result->setStatusCode(500);
            $result->setContentType('application/json');
            $data = $result;     
        });
        
    $promise->wait();

    return $data;
});

$app->get('/', function () {
    echo 'bswebhook';
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
});

$app->handle();