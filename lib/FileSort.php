<?php
/**
* 类 Sort 提供了 Markbox 目录文件的排序操作。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Sort.php
*/

namespace FileManager;

class FileSort
{
    private $list = [];

    private $sorttime = [];

    //初始化
    public function __construct(array $data)
    {
        $this->list = $data;
        foreach ($data as $k => $v) {
            $this->sorttime[$k] = $this->getFiletime($v);
        }
    }

    //获取列表
    public function getList()
    {
        return $this->list;
    }

    //获取排序时间
    public function getSorttime()
    {
        return $this->sorttime;
    }

    // 时间排序
    // asc  升序
    // desc 降序
    public function orderByTime($type = 'desc')
    {
        $list = [];
        foreach ($this->list as $k => $v) {
            $list[$v] = $this->sorttime[$k];
        }

        if ($type == 'desc') {
            arsort($list);
        } elseif ($type == 'asc') {
            asort($list);
        } else {
            throw new FileSortException('order by type error');
        }

        $i = 0;
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
        $list = [];
        foreach ($this->list as $k => $v) {
			$item = explode('/',trim($v,'/'));
            $list[$v] = end($item);
        }

        if ($type == 'desc') {
            arsort($list);
        } elseif ($type == 'asc') {
            asort($list);
        } else {
            throw new FileSortException('order by type error');
        }

        $i = 0;
        foreach ($list as $k => $v) {
            $this->list[$i] = $k;
            $this->sorttime[$i] = $v;
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

class FileSortException extends \Exception
{
}
