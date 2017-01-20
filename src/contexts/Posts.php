<?php namespace Markbox;
class Posts
{
	private $folder;
	private $context;
	private $publish = '';
	private $page;
	public function __construct($context){
		$this->folder = new \Tmkook\Folder;
		$this->publish = __BASEPATH__.'/storages/publish/';
		$this->folder->open($this->publish);
		$this->context = $context;
	}
	
	public function getSubFiles($params=array()){
		if(empty($params['page'])){
			$params['page'] = 1;
		}
		if(empty($params['field'])){
			$params['field'] = 'mtime';
		}
		if(empty($params['order'])){
			$params['order'] = 'desc';
		}
		if(empty($params['path'])){
			$params['path'] = '';
		}
		$this->folder->entry($params['path']);
		$files = $this->folder->getSubFiles('*.md');
		$sort = new \Tmkook\FolderInfo($files);
		$files = $sort->setOrder($params['field'],$params['order'])->get();
		foreach($files as $k=>$v){
			$files[$k]['uri'] = $this->path2uri($v['path']);
		}
		$obj = new \Context();
		$obj->files = $files;
		$obj->page = new Page(count($files),$params['page']);
		$limit = $obj->page->getLimit();
		$obj->news = array_slice($files,$limit[0],$limit[1]);
		return $obj;
	}
	
	public function getFiles($params=array()){
		if(empty($params['page'])){
			$params['page'] = 1;
		}
		if(empty($params['field'])){
			$params['field'] = 'mtime';
		}
		if(empty($params['order'])){
			$params['order'] = 'desc';
		}
		if(empty($params['path'])){
			$params['path'] = '';
		}
		$this->folder->entry($params['path']);
		$files = $this->folder->getFiles('*.md');
		$sort = new \Tmkook\FolderInfo($files);
		$files = $sort->setOrder($params['field'],$params['order'])->get();
		foreach($files as $k=>$v){
			$files[$k]['uri'] = $this->path2uri($v['path']);
		}
		$obj = new \Context();
		$obj->files = $files;
		$obj->page = new Page(count($files),$params['page']);
		$limit = $obj->page->getLimit();
		$obj->news = array_slice($files,$limit[0],$limit[1]);
		return $obj;
	}
	
	public function getFolders($params=array()){
		if(empty($params['page'])){
			$params['page'] = 1;
		}
		if(empty($params['field'])){
			$params['field'] = 'mtime';
		}
		if(empty($params['order'])){
			$params['order'] = 'desc';
		}
		if(empty($params['path'])){
			$params['path'] = '';
		}
		$this->folder->entry($params['path']);
		$files = $this->folder->getFolders();
		$sort = new \Tmkook\FolderInfo($files);
		$files = $sort->setOrder($params['field'],$params['order'])->get();
		foreach($files as $k=>$v){
			$files[$k]['uri'] = $this->path2uri($v['path']);
		}
		$obj = new \Context();
		$obj->folders = $files;
		$obj->page = new Page(count($files),$params['page']);
		$limit = $obj->page->getLimit();
		$obj->news = array_slice($files,$limit[0],$limit[1]);
		return $obj;
	}
	
	public function getSubFolders($params=array()){
		if(empty($params['page'])){
			$params['page'] = 1;
		}
		if(empty($params['field'])){
			$params['field'] = 'mtime';
		}
		if(empty($params['order'])){
			$params['order'] = 'desc';
		}
		if(empty($params['path'])){
			$params['path'] = '';
		}
		$this->folder->entry($params['path']);
		$files = $this->folder->getSubFolders();
		$sort = new \Tmkook\FolderInfo($files);
		$files = $sort->setOrder($params['field'],$params['order'])->get();
		foreach($files as $k=>$v){
			$files[$k]['uri'] = $this->path2uri($v['path']);
		}
		$obj = new \Context();
		$obj->folders = $files;
		$obj->page = new Page(count($files),$params['page']);
		$limit = $obj->page->getLimit();
		$obj->news = array_slice($files,$limit[0],$limit[1]);
		return $obj;
	}
	
	public function getMarkdown($file){
		$file = urldecode($file);
		$file = $this->uri2path($file).'.md';
		$info = new \Tmkook\FolderInfo(array($file));
		$file = $info->get();
		$file = $file[0];
		$file['content'] = file_get_contents($file['path']);
		return $file;
	}
	
	public function getHtml($file){
		$file = urldecode($file);
		$file = $this->uri2path($file).'.md';
		$info = new \Tmkook\FolderInfo(array($file));
		$file = $info->get();
		$file = $file[0];
		$file['content'] = $this->parsedown($file['path']);
		return $file;
	}
	
	private function parsedown($path){
		$parsedown = new \Parsedown();
		$html = $parsedown->text(file_get_contents($path));
		$basedir = trim(str_replace($this->publish,'',dirname($path)),'/');
		preg_match_all('|<img src="([\w\/]*\.\w+)"|',$html,$match);
		$match = $match[1];
		foreach($match as $url){
			$html = str_replace($url,"/storages/publish/{$basedir}/".trim($url,'/'),$html);
		}
		return $html;
	}
	
	private function uri2path($url){
		$path = str_replace('-','/',$url);
		$path = $this->publish.$path;
		return $path;
	}
	
	private function path2uri($path){
		$url = trim(str_replace($this->publish,'',$path),'/');
		$url = str_replace('.md','',str_replace('/','-',$url));
		return $url;
	}
}