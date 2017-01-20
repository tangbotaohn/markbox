<?php
namespace Markbox;
class View
{
	private $themepath = '';
	private $themeuri = '';
	private $host = '/';
	private $context;
	public function __construct($context){
		$this->context = $context;
		$themepath = $context->config->get('settings/theme');
		$this->host = $context->config->get('settings/host');
		$this->themepath = '/'.trim(realpath($themepath),'/').'/';
		$this->themeuri = '/'.trim(str_replace(__BASEPATH__,'',$themepath),'/').'/';
	}
	
	public function redirect($uri){
		return header("Location:{$this->host}{$uri}");
	}
	
	public function url($uri){
		return "{$this->host}{$uri}";
	}
	
	public function render($tpl,$assign=array()){
		extract((array)$assign);
		require $this->themepath.$tpl;
	}
	
}