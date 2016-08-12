<?php

require_once 'vendor/autoload.php';

BoletoSimples::configure(array(
  "environment" => 'sandbox', 
  "access_token" => 'a6e3e7990d39c413862d7fcc126f57c418d7cf6dbf18e2da8eb3dea738a17349'
));

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get('/', function () {
    echo 'bswebhook!';
});

$app->post('/bswebhook/', function ($data) {
    echo $data;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();