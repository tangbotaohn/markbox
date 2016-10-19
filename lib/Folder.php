<?php
/**
* 类 Folder 提供了 Markbox 目录文件及基本操作。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Folder.php
*/

namespace FileManager;

class Folder
{
    private $CURRENT = './';

    private function __construct()
    {
    }

    //工厂方法 - 获取操作对象
    public static function open($path)
    {
        $path = realpath($path);
        if (!is_dir($path)) {
            throw new FolderException("not found dir {$path}");
        }

        $obj = new self();
        $obj->setCURRENT($path);

        return $obj;
    }

    private function setCURRENT($path)
    {
        $this->CURRENT = '/'.trim($path, '/').'/';
    }

    //获取当前对象的路径
    public function getPath()
    {
        return $this->CURRENT;
    }

    //在当前对象路径下创建目录
    public function create($name)
    {
        $name = trim($name, '/');
        if (empty($name)) {
            throw new FolderException('create name error');
        }
        $name = $this->CURRENT.$name.'/';
        if (is_dir($name)) {
            return true;
        }

        return @mkdir($name, 0777);
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
    public function getFile($name)
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

    //获取当前目录下的子目录
    public function getSubDirectories()
    {
        $files = glob($this->CURRENT.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);

        return $files;
    }

    //获取当前目录下的所有目录
    public function getAllDirectories()
    {
        return $this->scanAllDirs(array(), $this->CURRENT);
    }

    //获取当前目录下的子文件
    public function getSubFiles($type = '*')
    {
        if ($type != '*' && !strchr($type, '.')) {
            throw new FolderException('error type');
        }
        $files = glob($this->CURRENT.$type, GLOB_NOSORT);

        return $files;
    }

    //获取当前目录下的所有文件
    public function getAllFiles($type = '*.php')
    {
        return $this->scanAllFiles(array(), $this->CURRENT, $type);
    }

    //递归获取目录
    private function scanAllDirs(array $directories, string $path)
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
    private function scanAllFiles(array $allfiles, string $path, string $type)
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

//异常类
class FolderException extends \Exception
{
}
