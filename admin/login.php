<?php
require '../libraries/Markbox.php';
$app = new Markbox();
if(!$app->installed()){
	header("Location:install.php");
}

if(!empty($_POST)){
	if($app->login($_POST['username'],$_POST['password'])){
		header("Location:./");
	}
}

$settings = $app->config('settings');
require 'themes/login.html';
