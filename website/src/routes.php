<?php

$router->get('/', function ($context) {
    $context->theme->render('index.html');
});

$router->get('/archive/:path?', function ($context) {
    $assign = $context->request->getParams();
    if (empty($assign['path'])) {
        $assign['path'] = '';
    }
    $context->theme->render('archive.html', $assign);
});

$router->get('/categories/:path?', function ($context) {
    $assign = $context->request->getParams();
    if (empty($assign['path'])) {
        $assign['path'] = '';
    }
    $context->theme->render('categories.html', $assign);
});

$router->get('/posts/:file', function ($context) {
    $assign = $context->request->getParams();
    $context->theme->render('posts.html', $assign);
});

$router->add('/rest/:api', function ($context) {
    $request = $context->request->getParams();
    $request = $request['api'];
    $method = $_SERVER['REQUEST_METHOD'];
    $params = $method == 'GET' || $method == 'DELETE' ? $_GET : $_POST;
    require 'models/Api.php';
    $api = new Markbox\Api($context);
    if (!method_exists($api, $request)) {
        throw new Exception('api not found', 404);
    }
    if (empty($params)) {
        return $api->$request();
    } else {
        return $api->$request($params);
    }
})->via(array('get', 'post', 'delete', 'put'));
