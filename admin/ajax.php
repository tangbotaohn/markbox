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
	if($method=='addfile'){
		$result = $app->addfile($_GET['t'],$_POST['data']);
	}else if($method == 'move'){
		$app->move($_GET['t'],$_POST['mv']);
	}else if($method == 'copy'){
		$app->copy($_GET['t'],$_POST['cp']);
	}else if(!empty($_GET['t'])){
		$result = $app->{$method}($_GET['t']);
	}else{
		$result = $app->{$method}();
	}
	response($result);
}
