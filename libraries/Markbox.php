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
	
	public function mdfiles($param){
		$dir = empty($param['t'])? 'mdfiles' : trim($param['t'],'/');
		$order = empty($param['order'])? '' : $param['order'];
		if($dir == 'mdfiles' || $dir == 'publish'){
			$this->vender->folder->down($dir);
			$md = $this->vender->folder->scan('*.md');
		}else{
			$this->vender->folder->down($dir);
			$md = $this->vender->folder->get('*.md');
		}
		$fold = (array)explode('/',$dir);
		$fold = $fold[0];
		if($order == 'name'){
			$md->orderByName('desc');
		}else if($order == 'time'){
			$md->orderByTime('desc');
		}else{
			$md->orderByName('asc');
		}
		$list = $md->getList();
		$sorttime = $md->getSorttime();
		$title = $md->getTitle();
		$data = array();
		foreach($list as $k=>$v){
			$data[$k]['file'] = str_replace($this->storagedir,'',$v);
			$data[$k]['filename'] = trim(str_replace($fold,'',$data[$k]['file']),'/');
			$data[$k]['time'] = $this->formattime($sorttime[$k]);
			$data[$k]['title'] = $title[$k];
		}
		return $data;
	}
	
	public function mdfolds($param){
		$dir = empty($param['t'])? 'mdfiles' : trim($param['t'],'/');
		$order = empty($param['order'])? '' : $param['order'];
		$this->vender->folder->down($dir);
		$md = $this->vender->folder->get('*','dir');
		if($order == 'name'){
			$md->orderByName('desc');
		}else if($order == 'time'){
			$md->orderByTime('desc');
		}else{
			$md->orderByName('asc');
		}
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
	
	public function content($param){
		$file = empty($param['t'])? '' : $param['t'];
		$path = $this->storagedir.$file;
		if(!file_exists($path)){
			throw new Exception("file not found",101);
		}
		$main = file_get_contents($path);
		preg_match("|\# .*|",$main, $content);
		if(empty($content[0])){
			$title = array_shift(explode("\r\n",$content));
		}else{
			$title = $content[0];
		}
		$title = trim(str_replace('#','',$title));
		$html = $this->vender->parsedown->text($main);
		return array('title'=>$title,'html'=>$html);
	}

	public function mdcontent($param){
		$file = empty($param['t'])? '' : $param['t'];
		$path = $this->storagedir.$file;
		if(!file_exists($path)){
			throw new Exception("file not found",101);
		}
		return file_get_contents($path);
	}

	public function addfile($param){
		$name = empty($param['t'])? '' : $param['t'];
		$body = empty($param['data'])? '' : $param['data'];
		if(empty($body)){
			throw new Exception("文件内容不能为空",101);
		}
		return $this->vender->folder->addFile($name,$body);
	}

	public function delfile($param){
		$name = empty($param['t'])? '' : $param['t'];
		return $this->vender->folder->delFile($name);
	}

	public function addfold($param){
		$name = empty($param['t'])? '' : $param['t'];
		$last = $this->downfolder($name);
		return $this->vender->folder->create($last);
	}

	public function delfold($param){
		$name = empty($param['t'])? '' : $param['t'];
		$last = $this->downfolder($name);
		return $this->vender->folder->remove($last);
	}

	public function clean($param){
		$name = empty($param['t'])? '' : $param['t'];
		$last = $this->downfolder($name);
		return $this->vender->folder->clean($last);
	}

	public function move($param){
		$file = empty($param['t'])? '' : $param['t'];
		$tofold = empty($param['mv'])? '' : $param['mv'];
		$filename = basename($file);
		$tofold = $tofold.'/'.$filename;
		return $this->vender->folder->rename($file,$tofold);
	}
	
	public function rename($param){
		$file = empty($param['t'])? '' : $param['t'];
		$toname = empty($param['newname'])? '' : $param['newname'];
		$last = (array)explode('/',$file);
		$last = end($last);
		$newname = str_replace($last,$toname,$file);
		return $this->vender->folder->rename($file,$newname);
	}
	
	public function copy($param){
		$oldname = empty($param['t'])? '' : $param['t'];
		$newname = empty($param['newname'])? '' : $param['newname'];
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
	
	public function makePage($page,$list){
		$current = empty($page)? 1 : intval($page);
		$total = count($list);
		$this->vender->page->setPage($total,$current);
		$limit = $this->vender->page->getLimit();
		$list = array_slice($list,$limit[0],$limit[1]);
		return array('page'=>$this->vender->page,'data'=>$list);
	}

	private function formattime($time){
		$last = time() - $time;
		if($last < 60){
			return '刚刚';
		}
		$last = intval($last / 60);
		if($last < 60){
			return $last.'分钟前';
		}
		$last = intval($last / 60);
		if($last < 60){
			return $last.'小时前';
		}
		$last = intval($last / 60);
		if($last < 30){
			return $last.'天前';
		}
		if($last < 60){
			return date('M-d H:i',$time);
		}
		return date('Y-M-d',$time);
	}


}
