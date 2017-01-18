<?php
namespace Markbox;
class Auth
{
	private $context;
	public function __construct($context){
		$this->context = $context;
		session_start();
	}
	
	public function check($level=0){
		if(empty($_SESSION['auth'])){
			exit('{"code":"101","error":"Auth invalid"}');
		}else if($_SESSION['auth']['level'] > $level){
			exit('{"code":"101","error":"Auth invalid"}');
		}
		return true;
	}
	
	public function getSignUser(){
		return $_SESSION['auth'];
	}
	
	public function sign($username,$password){
		$users = $this->context->config->get("users");
		foreach($users as $user){
			if($user['user'] == $username){
				break;
			}
		}
		if(empty($user)){
			return false;
		}
		if($this->makePassword($password) != $user['password']){
			return false;
		}
		
		return $_SESSION['auth'] = $user;
	}
	
	public function makeSalt(){
		return md5(rand(0,999999).uniqid().time());
	}
	
	public function getSalt(){
		return $this->context->config->get('settings/salt');
	}
	
	public function makePassword($password){
		return md5($password.$this->getSalt());
	}
}