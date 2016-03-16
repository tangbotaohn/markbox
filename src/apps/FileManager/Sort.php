<?php

namespace Markbox\FileManager;

class Sort
{
    private $list = [];

    private $sorttime = [];

    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new Exception('data error');
        }
        $this->list = $data;
        foreach ($data as $k => $v) {
            $this->sorttime[] = $this->getFiletime($v);
        }
    }

    public function getList()
    {
        return $this->list;
    }

    public function getSorttime()
    {
        return $this->sorttime;
    }

    public function orderBy($type = 'desc')
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
            throw new \Exception('order by type error');
        }

        $i = 0;
        foreach ($list as $k => $v) {
            $this->list[$i] = $k;
            $this->sorttime[$i] = $v;
            ++$i;
        }

        return $this;
    }

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
