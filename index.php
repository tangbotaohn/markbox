<?php
require 'libraries/Markbox.php';
$app = new Markbox();
if(!$app->isInstalled()){
	require 'apps/install/index.php';
}else{
	$m = empty($_GET['m'])? 'home' : $_GET['m'];
	$c = empty($_GET['c'])? 'index' : $_GET['c'];
	require "apps/{$m}/{$c}.php";
}
