<?php
require '../libraries/Markbox.php';
$app = new Markbox();
if(!$app->installed()){
	header("Location:install.php");
}

if(!empty($_POST)){
	try{
		if($app->login($_POST['username'],$_POST['password'])){
			header("Location:./");
		}
	}catch($e Exception){
		exit($e->getMessage());
	}
}

$settings = $app->config('settings');
require 'themes/login.html';
