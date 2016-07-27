<?php
// Show Routes
$app->get('/', function ($request, $response, $args) {
    $assign = [];
	$category = new Posts();
	$cat = $category->getCategories();
	print_r($cat->orderByTime('desc')->getSorttime());exit;
	//$this->renderer->render($response, 'index.phtml', $assign);
});

//登录
$app->post('/v1/user/sign', function ($request, $response, $args) {
	$post = $request->getParams();
	if(empty($post['username'])){
		throw new Exception("用户名不能为空");
	}
	if(empty($post['password'])){
		throw new Exception("密码不能为空");
	}
	$token = '';
    $users = $this->get('users');
	foreach($users as $user){
		if($user['username'] == $post['username'] && $user['password'] == $post['password']){
			$token = $this->token->build($user);
			unset($user['password']);
			break;
		}
	}
	if(empty($token)){
		throw new Exception("用户名或密码错误",101);
	}
	$user['token'] = $token;
	$this->json->render($user);
});



