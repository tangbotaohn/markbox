<?php
$settings = $app->config('settings');
$page = empty($_GET['page'])? 1 : intval($_GET['page']);
$list = $app->getList($page);
print_r($settings);
print_r($list);