<?php

class Posts
{
	//post dir
    const BASE = __DIR__.'/../../storages/posts';
	
	//current handle dir
	private $CURDIR = '';
	
	//all children files
    private $files = [];
	
	//all children dirs
	private $dirs = [];
	
	private $allfiles = [];
	
	//construct
    public function __construct()
    {
		$this->entry('/');
    }
	
	//entry current dir
	public function entry($dirname){
		$dir = self::BASE.'/'.trim($dirname,'/').'/';
		if(!is_dir($dir)){
			throw new Exception("not found dir",101);
		}
		$this->CURDIR = $dir;
		$this->init($dir);
	}
	
	//init current dir files
	private function init($path)
    {
        $files = $this->scanFiles($path);
        if (!empty($files)) {
            $this->files = $files;
        }

        $directories = $this->scanDirs($path);
        if (!empty($directories)) {
            $this->dirs = $directories;
        }
		$this->allfiles = [];
		$this->initAllFIles($path);
    }
	
	private function initAllFiles($path){
		$files = $this->scanFiles($path);
        if (!empty($files)) {
            $this->allfiles = array_merge($this->allfiles,$files);
        }

        $directories = $this->scanDirs($path);
        if (!empty($directories)) {
            foreach($directories as $dir){
				$this->initAllFiles(self::BASE.'/'.$dir.'/');
			}
        }
	}

	//scan current dir files
    private function scanFiles($path)
    {
        if (!is_dir($path)) {
            throw new Exception('Path error', 101);
        }
        $files = glob($path.'*.md', GLOB_NOSORT);
        $result = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $result[] = trim(str_replace(self::BASE,'',$file),'/');
            } else {
                continue;
            }
        }

        return $result;
    }

	//scan current dir dirs
    private function scanDirs($path)
    {
        if (!is_dir($path)) {
            throw new Exception('Path error', 101);
        }
        $files = glob($path.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);
        $result = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                $result[] = trim(str_replace(self::BASE,'',$file),'/');
            } else {
                continue;
            }
        }

        return $result;
    }
	
	//return files path
	public function getFiles(){
		return $this->files;
	}
	
	//return dirs path
	public function getDirs(){
		return $this->dirs;
	}
	
	//return files info
	public function getFilesInfo(){
		$files = [];
		foreach($this->files as $k=>$v){
			$file = $this->fileInfo($v);
			$files[] = $file;
		}
		
		$files = $this->sortFiles($files);
		return $files;
	}
	
	//return all files info
	public function getAllFilesInfo(){
		$files = [];
		foreach($this->allfiles as $k=>$v){
			$file = $this->fileInfo($v);
			$files[] = $file;
		}
		
		$files = $this->sortFiles($files);
		return $files;
	}
	
	//return dirs info
	public function getDirsInfo(){
		$dirs = [];
		foreach($this->dirs as $k=>$v){
			//file time
			$filename = self::BASE.'/'.$v;
			$mtime = filemtime($filename);
			if(empty($mtime)){
				touch($filename);
				$mtime = filemtime($filename);
				if(empty($mtime)){
					$mtime = filectime($filename);
					if(empty($mtime)){
						$mtime = time() + $k;
					}
				}
			}
			
			$dir = ['name'=>$v,'mtime'=>$mtime];
			$dirs[] = $dir;
		}
		return $dirs;
	}
	
	
	//return files info
	private function fileInfo($file){
		//file time
		$filename = self::BASE.'/'.$file;
		$mtime = filemtime($filename);
		if(empty($mtime)){
			touch($filename);
			$mtime = filemtime($filename);
			if(empty($mtime)){
				$mtime = filectime($filename);
				if(empty($mtime)){
					$mtime = time() + rand(0,100);
				}
			}
		}
		
		//title
		$main = file_get_contents($filename);
		if(empty($main)){
			$main = '';
		}
		preg_match("|\# .*|",$main,$content);
		if( ! isset($content[0])){
			$content[0] = '# 无标题';
		}
		$title = str_replace('# ','',$content[0]);

		//author
		$author = basename($file,'.md');
		if(strchr($author,".")){
			$tmp = explode('.',$author);
			$author = end($tmp);
		}else{
			$author = 'anonymous';
		}
		
		$file = ['title'=>$title,'link'=>$file,'author'=>$author,'mtime'=>$mtime];
		return $file;
	}
	
	private function sortFiles($files){
		//krsort array
		$tmpfiles = [];
		foreach($files as $k=>$v){
			if(empty($tmpfiles[$v['mtime']])){
				$tmpfiles[$v['mtime']] = $v;
			}else{
				$time = $v['mtime'] + $k;
				if(isset($tmpfiles[$time])){
					$time = $time.'_'.rand(0,600);
				}
				$tmpfiles[$time] = $v;
			}
		}
		krsort($tmpfiles);
		$files = array_values($tmpfiles);
		return $files;
	}
	
	//return parsedown html string
	public function parsedownFile($filename){
		$file = self::BASE.'/'.trim($filename,'/');
		$md = file_get_contents($file);
		$parse = new Parsedown();
		return $markdown = $parse->text($md);
	}

}
