<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// json view renderer
$container['json'] = function ($c) {
    return new JsonApiView($c);
};

// kv cache service
$container['token'] = function ($c) {
    return new UserToken($c);
};