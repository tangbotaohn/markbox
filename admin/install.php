<?php
require '../libraries/Markbox.php';
$app = new Markbox();
if($app->installed()){
	$_GET['step'] = 'success';
}

if(empty($_GET['step']) || $_GET['step'] == 'check'){
	$power['storages/configs']['w'] = is_writable('../storages/configs');
	$power['storages/configs']['r'] = is_readable('../storages/configs');

	$power['storages/images']['w'] = is_writable('../storages/images');
	$power['storages/images']['r'] = is_readable('../storages/images');
	
	$power['storages/mdfiles']['w'] = is_writable('../storages/mdfiles');
	$power['storages/mdfiles']['r'] = is_readable('../storages/mdfiles');

	$power['storages/publish']['w'] = is_writable('../storages/publish');
	$power['storages/publish']['r'] = is_readable('../storages/publish');
	
	$power['storages/recycles']['w'] = is_writable('../storages/recycles');
	$power['storages/recycles']['r'] = is_readable('../storages/recycles');

	$power['storages/themes']['w'] = is_writable('../storages/themes');
	$power['storages/themes']['r'] = is_readable('../storages/themes');
	
	$classname = array(true=>'success',false=>'danger');
	$name = array(true=>'通过',false=>'不通过');
	require 'install/check.html';
}else if($_GET['step'] == 'settings'){
	if(!empty($_POST)){
		$settings = $_POST['settings'];
		$settings['salt'] = md5(time());
		$app->setConfig('settings',$settings);
		$users = $_POST['user'];
		$users['password'] = md5($users['password'].$settings['salt']);
		$app->setConfig('users',array($users));
		header('Location:?step=success');
		exit;
	}
	require 'install/settings.html';
}else{
	require 'install/success.html';
}
