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
		session_start();
	}

	public function isInstalled(){
		$settings = $this->vender->Config->get('settings');
		$users = $this->vender->Config->get('users');
		return !(empty($settings) || empty($users));
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
			if($value['usrename'] == $username){
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

		return $_SESSION['login'] = $user;
	}

	public function getLogin(){
		return empty($_SESSION['login'])? '' : $_SESSION['login'];
	}

	public function checkLogin(){
		if(empty($_SESSION['login']) || empty($_SESSION['login']['username'])){
			header("Location:index.php?m=admin&c=login");
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
	
	public function getList($show,$order='desc'){
		$page = $this->vender->page;
		$md = $this->vender->folder->get('*.md');
		$md->orderByTime($order);
		$list = $md->getList();
		$total = count($list);
		$page->setPage($total,$show);
		$limit = $page->getLimit();
		$list = array_slice($list,$limit[0],$limit[1]);
		$md = new Markbox\FileListSort($list);
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		$base = $this->vender->folder->getCurrent();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return array('list'=>$data,'prev'=>$page->getPrev(),'next'=>$page->getNext(),'current'=>$page->getShow(),'back'=>$page->getBack(),'going'=>$page->getGoing());
	}
	
	public function getAll($order='desc'){
		$md = $this->vender->folder->get('*.md');
		$md->orderByTime($order);
		$list = $md->getList();
		$title = $md->getTitle();
		$sorttime = $md->getSorttime();
		$data = array();
		$base = $this->vender->folder->getCurrent();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($base,'',$v);
			$data[$k]['time'] = $sorttime[$k];
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
}
