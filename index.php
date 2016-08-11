<?php

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get('/', function () {
    echo 'home';
});

$app->get('/api/test/{data}', function ($data) {
    echo $data;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});

$app->handle();