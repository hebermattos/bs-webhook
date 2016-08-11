<?php

require_once 'vendor/autoload.php';

BoletoSimples::configure(array(
  "environment" => 'production', // default: 'sandbox'
  "access_token" => 'access-token'
));

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get('/', function () {
    echo 'home';
});

$app->get('/api/test/{data}', function ($data) {
    echo $data;
});

$app->post('/bswebhook/', function ($data) {
    echo $data;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});

$app->handle();