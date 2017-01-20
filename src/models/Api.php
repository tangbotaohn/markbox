<?php namespace Markbox;
class Api
{
	private $context;
	private $publish;
	public function __construct($context){
		$this->context = $context;
		$this->publish = __BASEPATH__.'/storages/publish/';
	}
	
	public function state(){
		$check = array(
			'storages/configs',
			'storages/publish',
			'storages/themes',
			'storages/system'
		);
		$power = true;
		foreach($check as $dir){
			if(!is_writable($dir) || !is_readable($dir)){
				$power = false;
				break;
			}
		}
		
		return $power;
	}
	
	public function siteinfo(){
		$website = $this->context->config->get('siteinfo');
		return $website;
	}
	
	public function setSiteinfo($params){
		$users = $this->context->config->get('users');
		if(!empty($users)){
			$this->context->auth->check(0);
		}
		$website = $this->context->config->get('siteinfo');
		$website = array_merge($website,$params);
		$this->context->config->set('siteinfo',$website);
		return $this->context->config->save('siteinfo');
	}
	
	public function sign($params){
		if(empty($params['user']) || empty($params['password'])){
			return false;
		}
		return $this->context->auth->sign($params['user'],$params['password']);
	}
	
	public function users(){
		$this->context->auth->check(0);
		$users = $this->context->config->get('users');
		return $users;
	}
	
	public function addUser($params){
		$users = $this->context->config->get('users');
		if(!empty($users)){
			$this->context->auth->check(0);
		}
		if(empty($params['user'])){
			throw new ApiException('user invalid',101);
		}
		if(empty($params['password'])){
			throw new ApiException('password invalid',102);
		}
		if(!isset($params['level'])){
			$params['level'] = 0;
		}
		$params['password'] = $this->context->auth->makePassword($params['password']);
		$users[] = $params;
		$this->context->config->set('users',$users);
		return $this->context->config->save('users');
	}
	
	public function delUser($params){
		$this->context->auth->check(0);
		if(empty($params['user'])){
			throw new ApiException('user invalid',101);
		}
		$users = $this->context->config->get('users');
		$userid = null;
		foreach($users as $k=>$user){
			if($user['user'] == $params['user']){
				$userid = $k;
				break;
			}
		}
		if($userid !== null && $userid > -1){
			unset($users[$userid]);
		}
		$users = array_values($users);
		$this->context->config->set('users',$users);
		return $this->context->config->save('users');
	}
	
	public function addFolder($params){
		$this->context->auth->check(1);
		$this->context->folder->open($this->publish);
		$path = (array)explode('/',trim($params['path'],'/'));
		foreach($path as $dir){
			$this->context->folder->create($dir);
			$this->context->folder->entry($dir);
		}
		return true;
	}
	
	public function addFile($params){
		$this->context->folder->open($this->publish);
		return $this->context->folder->addFile($params['file'],$params['content']);
	}
}

class ApiException extends \Exception
{
}
