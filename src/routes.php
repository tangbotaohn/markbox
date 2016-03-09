<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $a = new Posts();
});

// Author Routes
$app->group('/author', function () {
    $this->get('/login', function ($request, $response, $args) {
        return $this->renderer->render($response, 'index.phtml');
    });
});
