<?php

namespace Markbox;

class View
{
    private $themepath = '';
    private $themeuri = '';
    private $host = '/';
    private $context;
    public function __construct($context)
    {
        $this->context = $context;
        $theme = $context->config->get('settings/theme');
        $this->host = trim($context->config->get('settings/host'),'/').'/';
        $this->themepath = __BASEPATH__.'/storages/themes/'.$theme.'/';
        $this->themeuri = $this->host.'storages/themes/'.$theme.'/';
    }

    public function redirect($uri)
    {
        return header("Location:{$this->host}{$uri}");
    }

    public function url($uri)
    {
        return "{$this->host}{$uri}";
    }

    public function render($tpl, $assign = array())
    {
        extract((array) $assign);
        require $this->themepath.$tpl;
    }
}
