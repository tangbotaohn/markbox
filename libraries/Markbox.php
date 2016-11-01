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
	private $baseurl = '/';
	public function __construct(){
		$this->vender = new Vender();
		session_start();
		$basepath = trim(str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname(dirname(__FILE__))),'/');
		if(empty($basepath)){
			$basepath = '';
		}
		$this->baseurl = "http://{$_SERVER['HTTP_HOST']}{$basepath}";
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
	
	public function addDir($name){
		return $this->vender->folder->create($name);
	}
	
	public function delDir(){
		$this->vender->folder->clean();
		return $this->vender->folder->remove($name);
	}
	
	public function addFile($name,$body){
		return $this->vender->folder->addFile($name,$body);
	}
	
	public function delFile(){
		return $this->vender->folder->delFile($name);
	}

	public function folderDown($name){
		$dir = $this->vender->folder->getPath();
		$next = $dir.trim($name,'/');
		$this->vender->folder->setPath($next);
		return true;
	}

	public function folderUp($name){
		$dir = $this->vender->folder->getPath();
		$next = dirname($dir);
		$this->vender->folder->setPath($next);
		return true;
	}
	
	public function getFiles($dir=''){
		if(!empty($dir)){
			$this->folderDown($dir);
		}
		$md = $this->vender->folder->get('*.md');
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		$base = $this->vender->folder->getPath();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function getFolds($dir=''){
		if(!empty($dir)){
			$this->folderDown($dir);
		}
		$md = $this->vender->folder->get('*','dir');
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['title'] = basename($v);
			$data[$k]['file'] = $v;
			$data[$k]['time'] = $sorttime[$k];
		}
		return $data;
	}
	
	public function getContent($file){
		$path = $this->vender->folder->getPath();
		$content = file_get_contents($path.$file);
		return $this->vender->parsedown->text($content);
	}
	

}
