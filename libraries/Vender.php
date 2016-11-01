<?php
/**
* 类 Vender 提供了依赖的模块对象
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Folder.php
*/
class Vender
{
	public function __get($vendername){
		$vendername = ucfirst($vendername);
		if(!isset($this->$vendername)){
			if(!method_exists($this,'make'.$vendername)){
				throw new Exception("not found \"{$vendername}\" vender");
			}else{
				$method = 'make'.$vendername;
				$this->{$vendername} = $this->$method();
			}
		}
		
		return $this->{$vendername};
	}
	
	private function makeConfig(){
		require 'vender/Config.php';
		return new Markbox\Config(dirname(dirname(__FILE__)).'/storages/configs/');
	}
	
	private function makeFolder(){
		require 'vender/Folder.php';
		$folder = new Markbox\Folder();
		$folder->setPath(dirname(dirname(__FILE__)).'/storages/mdfiles/');
		return $folder;
	}
	
	private function makePage(){
		require 'vender/Page.php';
		return new Markbox\Page();
	}
	
	private function makeParsedown(){
		require 'vender/Parsedown.php';
		return new Parsedown();
	}
	
	private function makeZip(){
		require 'vender/Zip.php';
		return new Zip();
	}
	
	private function makeStorages(){
		require 'vender/Storages.php';
		return new Markbox\Storages(array('path'=>dirname(dirname(__FILE__)).'/storages/caches/','expire'=>3600));
	}
	
}