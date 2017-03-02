<?php

//环境检查
$users = $context->config->get('users');
$siteinfo = $context->config->get('siteinfo');
$settings = $context->config->get('settings');
if(empty($settings) || empty($users) || empty($siteinfo)){
	exit('please run php markbox.php -m init');
}

if( ! is_writable(dirname(dirname(__FILE__)).'/storages/')){
	exit('please run chmod -R 777 storages');
}
