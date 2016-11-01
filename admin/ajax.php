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

if($_GET['mod'] == 'file'){
	$list = $app->getFiles();
	response($list);
}else if($_GET['mod'] == 'fold'){
	$list = $app->getFolds();
	response($list);
}else if($_GET['mod'] == 'read'){
	$list = $app->getContent($_GET['file']);
	response($list);
}
