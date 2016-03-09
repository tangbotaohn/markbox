<?php

class Posts
{
    const BASE = __DIR__.'/../../storages/posts/';
    private $files = [];
    private $directories = [];
    public function __construct()
    {
    }

    private function getFiles($path)
    {
        if (!is_dir($path)) {
            throw new Exception('Path error', 101);
        }
        $files = glob($path.'*.md', GLOB_NOSORT);
        $result = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $result[] = $file;
            } else {
                continue;
            }
        }

        return $result;
    }

    private function getDirectories($path)
    {
        if (!is_dir($path)) {
            throw new Exception('Path error', 101);
        }
        $files = glob($path.'*', GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT);
        $result = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                $result[] = $file;
            } else {
                continue;
            }
        }

        return $result;
    }

    public function initBase($path = self::BASE)
    {
        $files = $this->getFiles($path);
        if (!empty($files)) {
            $this->files = array_merge($this->files, $files);
        }

        $directories = $this->getDirectories($path);
        if (!empty($directories)) {
            $this->directories = array_merge($this->directories, $directories);
            foreach ($directories as $dir) {
                $this->initBase($dir);
            }
        }
    }

    // 序列化文件
    public function serialize()
    {
        $base = glob(self::BASE.'*');
        foreach ($base as $key => $val) {
            if (is_dir($val)) {
                $this->category[] = $val;
            } elseif (is_file($val)) {
                $this->artiles[] = $val;
            } else {
                throw new Exception('unknow file type', 101);
            }
        }
    }
}
