<?php
/**
* 类 Markbox 提供了基础数据操作接口
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Markbox.php
*/
require 'Vender.php';

class Markbox
{
	private $vender;
	private $baseurl = '/';
	private $storagedir = '/';
	public function __construct(){
		$this->vender = new Vender();
		session_start();
		$basepath = trim(str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname(dirname(__FILE__))),'/');
		if(empty($basepath)){
			$basepath = '';
		}
		$this->baseurl = "http://{$_SERVER['HTTP_HOST']}{$basepath}";
		$this->storagedir = dirname(dirname(__FILE__)).'/storages/';
		$this->vender->folder->setPath($this->storagedir);
	}
	
	public function installed(){
		$settings = $this->vender->config->get('settings');
		$users = $this->vender->config->get('users');
		if(empty($settings) || empty($users)){
			return false;
		}
		return true;
	}

	public function config($name){
		return $this->vender->config->get($name);
	}

	public function setConfig($name,$value){
		$this->vender->config->set($name,$value);
		return $this->vender->config->save($name);
	}

	public function login($username,$password){
		$users = $this->config('users');
		$user = array();
		foreach ($users as $k => $value) {
			if($value['username'] == $username){
				$user = $value;
				break;
			}
		}
		if(empty($user)){
			throw new Exception("用户名或密码错误");
		}
		$settings = $this->config('settings');
		$password = md5($password.$settings['salt']);
		if($password != $user['password']){
			throw new Exception("用户名或密码错误");
		}
		
		unset($user['password']);
		return $_SESSION['login'] = $user;
	}

	public function getLogin(){
		return empty($_SESSION['login'])? '' : $_SESSION['login'];
	}

	public function checkLogin(){
		if(empty($_SESSION['login']) || empty($_SESSION['login']['username'])){
			header("Location:{$this->baseurl}/admin/login.php");
			exit;
		}
	}
	
	public function mdfiles($dir=''){
		$dir = trim($dir,'/');
		if($dir == 'mdfiles') $dir = '';
		if(empty($dir)){
			$this->vender->folder->down('mdfiles');
			$md = $this->vender->folder->scan('*.md');
		}else{
			$this->vender->folder->down($dir);
			$md = $this->vender->folder->get('*.md');
		}
		$md->orderByTime();
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($this->storagedir,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function mdfolds($dir=''){
		$dir = trim($dir,'/');
		if(empty($dir)){
			$dir = 'mdfiles';
		}
		$this->vender->folder->down($dir);
		$md = $this->vender->folder->get('*','dir');
		$md->orderByTime();
		$list = $md->getList();

		$sorttime = $md->getSorttime();
		$data = array();
		$base = $this->vender->folder->getPath();
		foreach($list as $k=>$v){
			$data[$k]['file']  = trim(str_replace($this->storagedir,'',$v),'/');
			$data[$k]['time']  = $sorttime[$k];
			$data[$k]['title'] = trim(str_replace($base,'',$v),'/');
		}
		return $data;
	}
	
	public function content($file){
		$path = $this->storagedir.$file;
		if(!file_exists($path)){
			throw new Exception("file not found",101);
		}
		$content = file_get_contents($path);
		return $this->vender->parsedown->text($content);
	}

	public function mdcontent($file){
		$path = $this->storagedir.$file;
		if(!file_exists($path)){
			throw new Exception("file not found",101);
		}
		return file_get_contents($path);
	}

	public function addfile($name,$body){
		return $this->vender->folder->addFile($name,$body);
	}

	public function delfile($name){
		return $this->vender->folder->delFile($name);
	}

	public function addfold($name){
		$last = $this->downfolder($name);
		return $this->vender->folder->create($last);
	}

	public function delfold($name){
		$last = $this->downfolder($name);
		return $this->vender->folder->remove($last);
	}

	public function clean($name){
		$last = $this->downfolder($name);
		return $this->vender->folder->clean($last);
	}

	public function move($oldname,$newname){
		return $this->vender->folder->rename($oldname,$newname);
	}

	public function copy($oldname,$newname){
		$path = $this->vender->folder->getPath();
		$file = $path.$oldname;
		if(!file_exists($file)){
			throw new Exception("file not found",101);
		}
		$body = file_get_contents($file);
		return $this->vender->publish->addFile($newname,$body);
	}
	
	private function downfolder($name){
		$name = trim($name,'/');
		$path = (array)explode('/',$name);
		$len = count($path)-1;
		$createname = $path[$len];
		unset($path[$len]);
		foreach($path as $v){
			$this->vender->folder->down($v);
		}
		
		return $createname;
	}
}
