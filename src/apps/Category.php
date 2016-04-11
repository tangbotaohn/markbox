<?php
require 'FileManager/Folder.php';
class Category
{
    //post dir
    private $base = __DIR__.'/../../storages/posts/';
	private $folder;
	
    //construct
    public function __construct($base = '')
    {
		$this->base .= $base;
		$this->base = realpath($this->base);
		$this->folder = FileManager\Folder::open($this->base);
    }
	
	public function getFolder(){
		return $this->folder;
	}
	
	public function create($name){
		return $this->folder->create($name);
	}
	
	public function get(){
		$subdir =  $this->folder->getSubDirectories();
		return $this->stripBase($subdir);
	}
	
	public function remove($name){
		return $this->folder->remove($name);
	}
	
	public function rename($name){
		return $this->folder->rename($name);
	}
	
	public function getSubFiles(){
		$files = $this->folder->getSubFiles('*.md');
		return $this->stripBase($files);
	}
	
	private function stripBase(array $paths){
		foreach($paths as $k=>$item){
			$paths[$k] = trim(str_replace($this->base,'',$item),'/');
		}
		return $paths;
	}
	
}
