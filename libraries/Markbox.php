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
	
	public function getList($page,$order='desc'){
		$page = $this->vender->page;
		$md = $this->vender->folder->get('*.md');
		$md->orderByTime($order);
		$list = $md->getList();
		$total = count($list);
		$page->setPage($total,$page);
		$limit = $page->getLimit();
		$list = array_slice($list,$limit[0],$limit[1]);
		$md = new FileListSort($list);
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
