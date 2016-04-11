<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $assign = [];
	$category = new Category();
	$assign['categories'] = $category->get();
	$assign['posts'] = $category->getSubFiles();
	$this->renderer->render($response, 'index.phtml', $assign);
});

$app->get('/post/{category}/{name}', function ($request, $response, $args) {
	$post = new Posts(new Category(),$args['name'].'.md');
	echo $post->parsedown();
});


