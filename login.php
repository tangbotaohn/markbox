<?php
session_start();
$savepwd = 'bdf7080500d61cc2d2a534bf31da4c6f';
function makePwd($pwd){
	$salt = '632bc84dd3a4b7a642f24c9f9658f43a';
	return md5($pwd.$salt);
}
if(!empty($_POST['password'])){
	if(makePwd($_POST['password']) != $savepwd){
		exit('口令错误');
	}else{
		$_SESSION['signtime'] = time();
		header('Location:write.php');
	}
}
require 'theme/login.html';