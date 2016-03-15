<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $a = new Posts();
	$a->entry('/test');
	print_r($a->getFilesInfo());
});

