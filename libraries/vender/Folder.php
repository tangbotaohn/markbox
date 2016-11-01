<?php
/**
* 类 Folder 提供了目录文件的基本操作。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Folder.php
*/

namespace Markbox;

class Folder
{
    private $CURRENT = './';

    //初始化目录
    public function setPath($path)
    {
        $path = realpath($path);
        if (!is_dir($path)) {
            throw new FolderException("not found dir {$path}");
        }

        $this->CURRENT = '/'.trim($path, '/').'/';
        $this->getModeObjects = $this->scanModeObjects = array();
    }

    public function getCurrent(){
        $dir = dirname($this->CURRENT);
        $name = trim(str_replace($dir,'',$this->CURRENT),'/');
        if(empty($name)){
            return '/';
        }
        return $name;
    }

    //获取当前对象的路径
    public function getPath()
    {
        return $this->CURRENT;
    }

    //在当前对象路径下创建目录
    public function create($name, $chmod=0777)
    {
        $name = trim($name, '/');
        if (empty($name)) {
            throw new FolderException('create name error');
        }
        $name = $this->CURRENT.$name.'/';
        if (is_dir($name)) {
            return true;
        }

        return @mkdir($name, $chmod);
    }

    //重命名当前对象下的目录
    public function rename($oldname, $newname)
    {
        $oldname = trim($oldname, '/');
        $newname = trim($newname, '/');
        if (empty($oldname)) {
            throw new FolderException('oldname is error');
        }
        if (empty($newname)) {
            throw new FolderException('newname is error');
        }
        $oldname = $this->CURRENT.$oldname.'/';
        $newname = $this->CURRENT.$newname.'/';
        if (!is_dir($oldname)) {
            throw new FolderException('oldname not exists');
        }

        return @rename($oldname, $newname);
    }

    //删除当前对象下的目录
    public function remove($name)
    {
        return @rmdir($this->CURRENT.$name);
    }

    //在当前对象目录下创建文件
    public function addFile($name, $body)
    {
        $name = trim($name, '/');
        $name = $this->CURRENT.$name;

        return (bool) file_put_contents($name, $body);
    }

    //获取当前目录下的文件内容
    public function getFileContent($name)
    {
        $name = trim($name, '/');
        $file = $this->CURRENT.$name;
        if (!file_exists($file)) {
            throw new FolderException("file not found '{$name}'");
        }

        return file_get_contents($file);
    }

    //删除当前目录下的文件
    public function delFile($name)
    {
        $name = trim($name, '/');
        $name = $this->CURRENT.$name;
        if (!file_exists($name)) {
            return true;
        }

        return @unlink($name);
    }

    //清空当前目录下的所有文件及目录
    public function clean()
    {
        $files = glob($this->CURRENT.'*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                @rmdir($file);
            } else {
                @unlink($file);
            }
        }

        return true;
    }
	
	private $getModeObjects =  [];
	public function get($file='*', $type=''){
		$key = $file.$type;
		$mod = GLOB_NOSORT;
		if($type == 'dir'){
			$mod = GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT;
		}
		if(empty($this->getModeObjects[$key])){
			$files = glob($this->CURRENT.$file, $mod);
			$this->getModeObjects[$key] = new FileListSort($files);
		}
		
		return $this->getModeObjects[$key];
	}
	
	private $scanModeObjects =  [];
	public function scan($file='*', $type=''){
		$key = $file.$type;
		if($type == 'dir'){
			$files = $this->scanAllDirs(array(),$this->CURRENT);
			$this->scanModeObjects[$key] = new FileListSort($files);
		}else{
			$files = $this->scanAllFiles(array(),$this->CURRENT,$file);
			$this->scanModeObjects[$key] = new FileListSort($files);
		}
		
		return $this->scanModeObjects[$key];
	}

    //递归获取目录
    private function scanAllDirs(array $directories, $path)
    {
        $path = realpath($path);
        $path = trim($path, '/');
        $path = '/'.$path.'/';
        if (!is_dir($path)) {
            throw new FolderException('path error');
        }
        $files = glob($path.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);
        if (!empty($files)) {
            foreach ($files as $k => $v) {
                $directories[] = $v;
                $directories = $this->scanAllDirs($directories, $v);
            }
        }

        return $directories;
    }

    //递归获取文件
    private function scanAllFiles(array $allfiles, $path, $type)
    {
        $path = realpath($path);
        $path = trim($path, '/');
        $path = '/'.$path.'/';
        if (!is_dir($path)) {
            throw new FolderException('path error');
        }
        $files = glob($path.$type, GLOB_NOSORT);
        if (!empty($files)) {
            $allfiles = array_merge($allfiles, $files);
        }
        $dirs = glob($path.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);
        if (!empty($dirs)) {
            foreach ($dirs as $k => $v) {
                $allfiles = $this->scanAllFiles($allfiles, $v, $type);
            }
        }

        return $allfiles;
    }
}


//文件列表排序
class FileListSort
{
    private $list = array();

    private $sorttime = array();
	
	private $title = array();
	
	private $content = array();

    //初始化
    public function __construct(array $data)
    {
        $this->list = $data;
    }

    //获取列表
    public function getList()
    {
        return $this->list;
    }

    //获取排序时间
    public function getSorttime()
    {
		if(empty($this->sorttime)){
			foreach ($this->list as $k => $v) {
				$this->sorttime[$k] = $this->getFiletime($v);
			}
		}
		
        return $this->sorttime;
    }
	
	//获取内容
	public function getContent(){
		if(empty($this->content)){
			foreach($this->list as $k=>$file){
				$main = file_get_contents($file);
				$this->content[$k] = $main;
			}
		}
		
		return $this->content;
	}
	
	//获取标题
	public function getTitle(){
		if(empty($this->title)){
			$content = $this->getContent();
			foreach($content as $k=>$main){
				preg_match("|\# .*|",$main, $content);
				$this->title[$k] = str_replace('# ','',$content[0]);
			}
		}
		
		return $this->title;
	}

    // 时间排序
    // asc  升序
    // desc 降序
    public function orderByTime($type = 'desc')
    {
        $list = array();
		$sorttime = $this->getSorttime();
        foreach ($this->list as $k => $v) {
            $list[$v] = $sorttime[$k];
        }

        if ($type == 'desc') {
            arsort($list);
        } elseif ($type == 'asc') {
            asort($list);
        } else {
            throw new FileListSortException('order by type error');
        }

        $i = 0;
		$this->list = $this->sorttime  = $this->title = $this->content = array();
        foreach ($list as $k => $v) {
            $this->list[$i] = $k;
            $this->sorttime[$i] = $v;
            ++$i;
        }

        return $this;
    }
	
	// 名字排序
    // asc  升序
    // desc 降序
    public function orderByName($type = 'desc')
    {
        $list = array();
        foreach ($this->list as $k => $v) {
			$item = explode('/',trim($v,'/'));
            $list[$v] = end($item);
        }

        if ($type == 'desc') {
            arsort($list);
        } elseif ($type == 'asc') {
            asort($list);
        } else {
            throw new FileListSortException('order by type error');
        }

        $i = 0;
		$this->list = $this->sorttime = $this->title = $this->content = array();
        foreach ($list as $k => $v) {
            $this->list[$i] = $k;
            ++$i;
        }
		
        return $this;
    }

    //获取文件更新时间
    private function getFiletime($file)
    {
        $mtime = filemtime($file);
        if (empty($mtime)) {
            touch($file);
            $mtime = filemtime($file);
            if (empty($mtime)) {
                $mtime = filectime($file);
                if (empty($mtime)) {
                    $mtime = time();
                }
            }
        }

        return $mtime;
    }

}

class FileListSortException extends \Exception
{
}

//异常类
class FolderException extends \Exception
{
}

//test
/*
$folder = new Folder('../');
$files = $folder->scan('*.php');
print_r($files->orderByTime('asc')->getList());
print_r($files->getSorttime());
*/
