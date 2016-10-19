<?php
session_start();
if(empty($_SESSION['signtime'])){
	header("Location:login.php");
}

if(!empty($_POST)){
	if(empty($_POST['content'])){
		exit('内容不能为空');
	}else if(empty($_POST['filename'])){
		exit('文件名不能为空');
	}else{
		require 'lib/Folder.php';
		$folder = FileManager\Folder::open('./posts/');
		$folder->addFile($_POST['filename'],$_POST['content']);
		exit('保存成功');
	}
}

if(!empty($_GET['del'])){
	require 'lib/Folder.php';
	$folder = FileManager\Folder::open('./posts/');
	$folder->delFile($_GET['del']);
	exit('删除成功');
}

$filename = '';
$content = '';
if(!empty($_GET['filename'])){
	$content = file_get_contents('./posts/'.$_GET['filename']);
	if(!empty($content)){
		$filename = $_GET['filename'];
	}
}

require 'theme/write.html';
