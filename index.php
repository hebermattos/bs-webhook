<?php

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get('/', function () {
    echo 'bswebhook!';
});

$app->get('/bswebhook', function ($data) {
    echo 'bswebhook: '.$data;
});

$app->post('/bswebhook', function ($data) use ($app) {
    
    echo $app->request->getHeader('HTTP_X_HUB_SIGNATURE');
    
    echo $data;
    
    echo hash_hmac('sha1', $data, 'a6e3e7990d39c413862d7fcc126f57c418d7cf6dbf18e2da8eb3dea738a17349');
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();