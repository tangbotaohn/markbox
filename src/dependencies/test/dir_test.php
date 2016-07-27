<?php
require '../Folder.php';

$dir = Markbox\FileManager\Folder::open('./');

var_dump($dir->create('testname'));

var_dump($dir->rename('testname','testrename'));

var_dump($dir->remove('testrename'));

var_dump($dir->getSubDirectories());

var_dump($dir->getAllDirectories());

var_dump($dir->getSubFiles('*.php'));

var_dump($dir->getAllFiles('*.php'));

var_dump($dir->addFile('test.php','test'));

var_dump($dir->delFile('test.php'));