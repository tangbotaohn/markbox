<?php
namespace Markbox;
class View
{
	private $theme = '';
	private $uri = '';
	public function __construct($path){
		$this->theme = '/'.trim(realpath($path),'/').'/';
		$this->uri = '/'.trim(str_replace(__BASEPATH__,'',$path),'').'/';
	}
	
	public function redirect($uri){
		return header("Location:{$this->uri}{$uri}");
	}
	
	public function url($uri){
		return "{$this->uri}{$uri}";
	}
	
	public function render($tpl,$assign=array()){
		extract((array)$assign);
		require $this->theme.$tpl;
	}
	
}