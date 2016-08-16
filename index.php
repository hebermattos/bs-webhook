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
    
    $request = new \Phalcon\Http\Request();
    $header = $request->getHeader('HTTP_X_HUB_SIGNATURE');
    
    $body = $request->getRawBody();

    $hashedBody = hash_hmac('sha1', $body, 'a6e3e7990d39c413862d7fcc126f57c418d7cf6dbf18e2da8eb3dea738a17349');
    
    $response = new Response();
    $response->setContentType('application/json');
    
    if(strcmp($header, 'sha1='.$hashedBody) == 0)
    {
        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data'   => array()
            )
        );
    }
    else {
        $response->setJsonContent(
            array(
                'status' => 'NOT AUTHORIZED',
                'data'   => array()
            )
        );
    }
    
    return $response;

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'page not found';
});

$app->handle();