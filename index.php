<?php
require 'libraries/Markbox.php';
$markbox = new Markbox();
$list = $markbox->mdfiles(array('t'=>'publish','order'=>'time'));
$show = empty($_GET['page'])? 1 : intval($_GET['page']);
$data = $markbox->makePage($show,$list);
$list = $data['data'];
$page = $data['page'];
require 'storages/themes/default/index.html';
