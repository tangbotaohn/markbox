<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $a = new Category();
	print_r($a->getSubFiles());
});

$app->get('/post', function ($request, $response, $args) {
	$post = new Posts(new Category(),'test.md');
	$post->addText('# test title');
	$post->save();
	
	echo $post->parsedown();
});
