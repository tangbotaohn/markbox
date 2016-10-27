<?php
/**
* 类 Markbox 提供了基础数据操作接口
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Folder.php
*/
require 'Vender.php';

class Markbox
{
	private $vender;
	public function __construct(){
		$this->vender = new Vender();
	}

	public function isInstalled(){
		$settings = $this->vender->Config->get('settings');
		$users = $this->vender->Config->get('users');
		return !(empty($settings) || empty($users));
	}
	
}
