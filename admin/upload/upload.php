<?php
header("Content-Type:text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");
$name = 'editormd-image-file';
if (isset($_FILES[$name]))
{
	$rst = move_uploaded_file($_FILES[$name]["tmp_name"],"../../storages/images/{$_FILES[$name]["name"]}");
	if($rst){
		exit(json_encode(array('success'=>1,'url'=>'/storages/images/'.$_FILES[$name]["name"],'message'=>'success')));
	}else{
		exit(json_encode(array('success'=>0,'message'=>"上传失败")));
	}
}
