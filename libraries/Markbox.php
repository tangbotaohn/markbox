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
	
	public function mdfiles($dir=''){
		$base = $this->vender->mdfiles->getPath();
		if(empty($dir)){
			$md = $this->vender->mdfiles->scan('*.md');
		}else{
			$path = $this->vender->mdfiles->getPath();
			$next = $path.trim($dir,'/');
			$this->vender->mdfiles->setPath($next);
			$md = $this->vender->mdfiles->get('*.md');
		}
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function mdfolds(){
		$base = $this->vender->mdfiles->getPath();
		$md = $this->vender->mdfiles->get('*','dir');
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file']  = trim(str_replace($base,'',$v),'/');
			$data[$k]['time']  = $sorttime[$k];
			$data[$k]['title'] = $data[$k]['file'];
		}
		return $data;
	}

	public function publish(){
		$base = $this->vender->publish->getPath();
		$md = $this->vender->publish->get('*.md');
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function recycles(){
		$base = $this->vender->recycles->getPath();
		$md = $this->vender->recycles->get('*.md');
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function content($type,$file){
		$path = dirname($this->vender->mdfiles->getPath()).'/';
		$content = file_get_contents($path.$type.'/'.$file);
		return $this->vender->parsedown->text($content);
	}

}
