<?php
require 'libraries/Markbox.php';
$markbox = new Markbox();
$file = $_GET['read'];
$data = $markbox->content(array('t'=>'publish/'.$file));
$markdown = $data['html'];
$title = $data['title'];
require 'storages/themes/default/wiki.html';
