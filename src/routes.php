<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $assign = [];
	$category = new Posts();
	$cat = $category->getCategories();
	print_r($cat->orderByTime('desc')->getSorttime());exit;
	//$this->renderer->render($response, 'index.phtml', $assign);
});

$app->get('/post/{name}', function ($request, $response, $args) {
	$post = new Posts(new Category(),$args['name'].'.md');
	echo $post->parsedown();
});

$app->get('/uploads/img/{name}', function ($request, $response, $args) {
	$img = new FileManager\Image(file_get_contents(__DIR__ .'/../storages/uploads/'.$args['name']));
	$img->display();exit;
});
