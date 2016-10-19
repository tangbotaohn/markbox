<?php
require 'Folder.php';
require 'FileSort.php';
require 'Page.php';
class Markbox
{
	private $files;
	private $filetime;
	private $page;
	
	public function __construct($dir){
		$folder = FileManager\Folder::open($dir);
		$this->files = (array)$folder->getSubFiles('*.md');
	}
	
	public function timeSort($type='desc'){
		$filesort = new FileManager\FileSort($this->files);
		$filesort->orderByTime($type);
		$this->files = $filesort->getList();
		$this->filetime = $filesort->getSorttime();
	}
	
	public function makePage($page){
		$current = empty($page)? 1 : intval($page);
		$total = count($this->files);
		$page = new FileManager\Page($total,$current);
		$limit = $page->getLimit();
		$this->files = array_slice($this->files,$limit[0],$limit[1]);
		if(!empty($this->filetime)){
			$this->filetime = array_slice($this->filetime,$limit[0],$limit[1]);
		}
		return $page;
	}
	
	public function getList(){
		//获取标题
		$lists = array();
		$time = time();
		foreach($this->files as $k=>$file){
			$main = file_get_contents($file);
			preg_match("|\# .*|",$main,$content);
			if(isset($this->filetime[$k])){
				$time = $this->filetime[$k];
			}
			$lists[] = array('title'=>str_replace('# ','',$content[0]),'link'=>basename($file),'updated_at'=>$time);
		}
		return $lists;
	}

}