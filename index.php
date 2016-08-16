<?php

require 'vendor/autoload.php';

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException

$app = new Micro();

$app->get('/', function () {
    echo 'bswebhook!';
});

$app->get('/bswebhook/{data}', function ($data) {
    echo 'bswebhook: '.$data;
});

$app->post('/bswebhook', function () use ($app) {
    
    $request = new \Phalcon\Http\Request();
    
    $header = $request->getHeader('HTTP_X_HUB_SIGNATURE');
    $body = $request->getRawBody();
    $hashedBody = hash_hmac('sha1', $body, 'a6e3e7990d39c413862d7fcc126f57c418d7cf6dbf18e2da8eb3dea738a17349');
    
    $response = new Response();
    $response->setContentType('application/json');
    $code = "200";
    $status = 'OK';
    $data = NULL;
    
    if(strcmp($header, 'sha1='.$hashedBody) == 0)
    {
        $client = new Client();
        
        try {
            $data = $client->request('POST', 'http://200.178.195.70:888/v1/boletosimples', ['body' => $body]);
        } catch (ServerException $e) {
            $code = 500;
            $status = "Internal server error";
            if ($e->hasResponse()) {
                $data = $e->getResponse();
            }
        } catch (ClientException $e) {
            $code = 400;
            $status = "badrequest";
            if ($e->hasResponse()) {
                $data = $e->getResponse();
            }
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