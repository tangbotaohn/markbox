<?php
require '../libraries/Markbox.php';
function response($data, $code=200){
	exit(json_encode(array('data'=>$data,'code'=>$code)));
}

$app = new Markbox();
if(!$app->installed()){
	response(array('msg'=>'未安装'),1);
}

if(empty($app->getLogin())){
	response(array('msg'=>'未登录'),2);
}

if(!empty($_GET['mod'])){
	$method = $_GET['mod'];
	if($method == 'content'){
		$result = $app->content($_GET['type'],$_GET['t']);
	}else if(!empty($_GET['t'])){
		$result = $app->{$method}($_GET['t']);
	}else{
		$result = $app->{$method}();
	}
	response($result);
}
