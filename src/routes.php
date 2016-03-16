<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $a = new Category();
	print_r($a->getPath());
});

