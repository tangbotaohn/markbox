<?php
require '../libraries/Markbox.php';
$app = new Markbox();
if(!$app->installed()){
	header("Location:install.php");
}
$app->checkLogin();

$settings = $app->config('settings');
require 'themes/index.html';
