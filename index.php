<?php
require 'lib/Markbox.php';
$markbox = new Markbox('./posts/');
$markbox->timeSort();
$show = empty($_GET['page'])? 1 : intval($_GET['page']);
$page = $markbox->makePage($show);
$list = $markbox->getList();
require 'theme/index.html';