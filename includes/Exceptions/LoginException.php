<?php
/*
 * Created on Dec 8, 2011
 *
 * Extends Exception class to include the login
 * type that was attempted when exception was thrown.
 *
 * author: arturo Araya
 * 
 */

class LoginException extends Exception {
	private $loginType;
	
	public function __construct($loginType,$msg,$errorCode=0,$previous=NULL){
		parent::__construct($msg,$errorCode,$previous);
		$this->loginType = $loginType;
	}
	
	public function getLoginType() {
		return $this->loginType;
	}
}