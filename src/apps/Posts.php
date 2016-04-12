<?php
/**
* 类 Posts 提供了对 storages/posts 目录下的文件管理操作。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Posts.php
*/
require 'FileManager/Folder.php';
require 'FileManager/Sort.php';
require 'FileManager/Page.php';
class Posts
{
    //post dir
    private $base = __DIR__ .'/../../storages/posts/';
	private $category;
	private $realpath;
    private $folder;

    //construct
    public function __construct($category = '')
    {
		$this->base = realpath($this->base);
        $this->category = trim($category,'/');
        $this->realpath = realpath($this->base.'/'.$this->category);
        $this->folder = FileManager\Folder::open($this->realpath);
    }
	
	public function getCategory(){
		return $this->category;
	}

    public function getFolder()
    {
        return $this->folder;
    }

    public function getCategories()
    {
        $subdir = $this->folder->getSubDirectories();
		$sort = new FileManager\Sort($subdir);
		return $sort;
    }

    public function getSubFiles($curent_page)
    {
        $files = $this->folder->getSubFiles('*.md');
		
        return $this->stripBase($files);
    }

    private function stripBase(array $paths)
    {
        foreach ($paths as $k => $item) {
            $paths[$k] = trim(str_replace($this->base, '', $item), '/');
        }

        return $paths;
    }
}
