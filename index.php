<?php

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

$app = new Micro();

$app->get('/', function () {
    echo 'bswebhook!';
});

$app->get('/bswebhook/{data}', function ($data) {
    echo 'bswebhook: '.$data;
});

$app->post('/bswebhook', function () use ($app) {
    
    $header = $app->request->getHeader('HTTP_X_HUB_SIGNATURE');

    $body = $app->request->getJsonRawBody();

    $hashedBody = hash_hmac('sha1', $body, 'a6e3e7990d39c413862d7fcc126f57c418d7cf6dbf18e2da8eb3dea738a17349');
    
    $response = new Response();
    
    echo $header;
    echo $body;

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();