<?php

use Phalcon\Mvc\Micro;

$app = new Micro();


$app->get('/api/test', function ($data) {
    echo json_encode($data);
});


$app->handle();

?>
