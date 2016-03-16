<?php

namespace Markbox\FileManager;

class Folder
{
    private $CURRENT = './';

    private function __construct()
    {
    }

    public static function open($path)
    {
        $path = realpath($path);
        if (!is_dir($path)) {
            throw new \Exception('not found dir');
        }

        $obj = new self();
        $obj->setCURRENT($path);

        return $obj;
    }

    private function setCURRENT($path)
    {
        $this->CURRENT = '/'.trim($path, '/').'/';
    }

    public function getPath()
    {
        return $this->CURRENT;
    }

    public function create($name)
    {
        $name = trim($name, '/');
        if (empty($name)) {
            throw new \Exception('create name error');
        }
        $name = $this->CURRENT.$name.'/';
        if (is_dir($name)) {
            return true;
        }

        return @mkdir($name, 077);
    }

    public function rename($oldname, $newname)
    {
        $oldname = trim($oldname, '/');
        $newname = trim($newname, '/');
        if (empty($oldname)) {
            throw new \Exception('oldname is error');
        }
        if (empty($newname)) {
            throw new \Exception('newname is error');
        }
        $oldname = $this->CURRENT.$oldname.'/';
        $newname = $this->CURRENT.$newname.'/';
        if (!is_dir($oldname)) {
            throw new \Exception('oldname not exists');
        }

        return @rename($oldname, $newname);
    }

    public function remove($name)
    {
        return @rmdir($this->CURRENT.$name);
    }

    public function addFile($name, $body)
    {
        $name = trim($name, '/');
        $name = $this->CURRENT.$name;

        return (bool) file_put_contents($name, $body);
    }

    public function delFile($name)
    {
        $name = trim($name, '/');
        $name = $this->CURRENT.$name;
        if (!file_exists($name)) {
            return true;
        }

        return @unlink($name);
    }

    public function getSubDirectories()
    {
        $files = glob($this->CURRENT.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);

        return $files;
    }

    public function getAllDirectories()
    {
        return $this->scanAllDirs([], $this->CURRENT);
    }

    public function getSubFiles($type = '*')
    {
        if ($type != '*' && !strchr($type, '.')) {
            throw new \Exception('error type');
        }
        $files = glob($this->CURRENT.$type, GLOB_NOSORT);

        return $files;
    }

    public function getAllFiles($type = '*.php')
    {
        return $this->scanAllFiles([], $this->CURRENT, $type);
    }

    private function scanAllDirs(array $directories, string $path)
    {
        $path = realpath($path);
        $path = trim($path, '/');
        $path = '/'.$path.'/';
        if (!is_dir($path)) {
            throw new \Exception('path error');
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

    private function scanAllFiles(array $allfiles, string $path, string $type)
    {
        $path = realpath($path);
        $path = trim($path, '/');
        $path = '/'.$path.'/';
        if (!is_dir($path)) {
            throw new \Exception('path error');
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
