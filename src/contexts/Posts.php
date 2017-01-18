<?php namespace Markbox;
class Posts
{
	private $folder;
	private $context;
	public function __construct($context){
		$this->folder = new \Tmkook\Folder;
		$this->folder->open(__BASEPATH__.'/storages/publish/');
		$this->context = $context;
	}
	
	public function markdown($file){
		$path = $this->url2path($file);
		return file_get_contents($path);
	}
	
	public function html($file){
		$path = $this->url2path($file);
		$content = file_get_contents($path);
		$parsedown = new \Parsedown();
		return $parsedown->text($content);
	}
	
	public function get(){
		$files = $this->folder->getSubFiles('*.md');
		foreach($files as $k=>$v){
			$files[$k] = $this->path2url($v);
		}
		return $files;
	}
	
	public function url2path($url){
		$path = str_replace('-','/',$url);
		$path = $this->folder->getDirectory().$path.'.md';
		return $path;
	}
	
	public function path2url($path){
		$url = str_replace($this->folder->getDirectory(),'',$path);
		$url = str_replace('.md','',str_replace('/','-',$url));
		return $url;
	}
	
}