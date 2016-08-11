<?php

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get('/', function () {
    echo "welcome....";
});

$app->get('/home', function () {
    echo "welcome....";
});

$app->get('/api/test/{data}', function ($data) {
    echo json_encode($data);
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});

$app->handle();


