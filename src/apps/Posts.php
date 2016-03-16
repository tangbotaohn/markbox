<?php

class Posts
{
    //post dir
    const BASE = __DIR__.'/../../storages/posts/';

    //construct
    public function __construct()
    {
    }

    //return parsedown html string
    public function parsedownFile($filename)
    {
        $file = self::BASE.'/'.trim($filename, '/');
        $md = file_get_contents($file);
        $parse = new Parsedown();

        return $markdown = $parse->text($md);
    }
}
