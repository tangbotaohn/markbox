<?php
require '../libraries/Markbox.php';
function response($data, $code=200){
	exit(json_encode(array('data'=>$data,'code'=>$code)));
}

$app = new Markbox();
if(!$app->installed()){
	response('未安装',1);
}

$login = $app->getLogin();
if(empty($login)){
	response('未登录',2);
}
if(!ini_get('date.timezone')){
    date_default_timezone_set('Asia/Shanghai');
}
if(!empty($_GET['mod'])){
	try{
		$method = $_GET['mod'];
		$param = array_merge((array)$_GET,(array)$_POST);
		$result = $app->{$method}($param);
		response($result);
	}catch(Exception $e){
		response($e->getMessage(),$e->getCode());
	}catch(FolderException $e){
		response($e->getMessage(),$e->getCode());
	}catch(FileListSortException $e){
		response($e->getMessage(),$e->getCode());
	}
}
