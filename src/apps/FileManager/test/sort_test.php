<?php
require '../Sort.php';
$path = [realpath('./dir_test.php'),realpath('./sort_test.php')];

$dir = new Markbox\FileManager\Sort($path);

print_r($dir->getList());

$dir->orderBy();

print_r($dir->getList());

print_r($dir->getSorttime());
