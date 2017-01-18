<?php
$router->get('/',function(){
	print_r($this->posts->get());
});

$router->get('/posts/:file?',function(){
	$file = $this->request->getParams();
	$file = $file['file'];
	return $this->posts->html($file);
});

$router->add('/rest/:api',function(){
	$request = $this->request->getParams();
	$request = $request['api'];
	$method = $_SERVER['REQUEST_METHOD'];
	$params = $method == 'GET' || $method == 'DELETE'? $_GET : $_POST;
	require 'models/Api.php';
	$api = new Markbox\Api($this);
	if( ! method_exists($api,$request)){
		throw new Exception('api not found',404);
	}
	if(empty($params)){
		return $api->$request();
	}else{
		return $api->$request($params);
	}
})->via(array('get','post','delete','put'));

