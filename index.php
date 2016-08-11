<?php

use Phalcon\Mvc\Micro;

$app = new Micro();


$app->post('/api/robots', function () {

});


$app->handle();