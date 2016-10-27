<?php
/**
* 类 Storages 提供了文件缓存能力。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Storages.php
*/
namespace Markbox;

class Storages
{
	private $_path;
	private $_expire = 0;
	private $_suffix  = '.cache.php';

	public function __construct($dsn){
		if(!is_dir($dsn['path'])){
			trigger_error("缓存目录 '{$dsn['path']}' 不存在", E_USER_ERROR);
		}
		if(!is_readable($dsn['path'])){
			trigger_error("缓存目录 '{$dsn['path']}' 不可读", E_USER_ERROR);
		}
		if(!is_writable($dsn['path'])){
			trigger_error("缓存目录 '{$dsn['path']}' 不可写", E_USER_ERROR);
		}
		$this->_path = realpath($dsn['path']).'/';
		if( ! empty($dsn['domain'])) $this->_path .= $dsn['domain'].'/';
		if( ! is_dir($this->_path)){
			if( ! mkdir($this->_path)){
				trigger_error("缓存域 '{$dsn['domain']}' 创建失败",E_USER_ERROR);
			}
		}
		if(isset($dsn['expire'])) $this->_expire = intval($dsn['expire']);
	}

	public function set($name, $value, $expire=0){
        if($expire > 0){
            $expire += time();
        }elseif($this->_expire > 0){
            $expire = $this->_expire + time();
        }
		$data = array('time'=>$expire,'data'=>$value);
		$string = "<?php \r\n return " . var_export($data,true).";";
		$filename = $this->_path.$name.$this->_suffix;
		$fp = fopen($filename, 'wb');
		if ($fp) {
			flock($fp, LOCK_EX);
			fwrite($fp, $string);
			flock($fp, LOCK_UN);
			fclose($fp);
			return true;
		} else {
			return false;
		}
	}
	
	public function get($name){
		$filename = $this->_path.$name.$this->_suffix;
		if( ! is_file($filename)) return false;
		$data = include $filename;
		if($data['time'] > 0 && $data['time'] < time()) unlink($filename);
		return $data['data'];
	}
	
	public function del($name){
		$filename = $this->_path.$name.$this->_suffix;
		if(is_file($filename)) unlink($filename);
	}
	
	public function flush(){
		$dh = opendir($this->_path);
		while ($file = readdir($dh)) {
			if($file!='.' && $file!='..' && !is_dir($file)) unlink($this->_path.$file);
		}
	}
	
	public function getCurrent(){
		return $this->_path;
	}

}