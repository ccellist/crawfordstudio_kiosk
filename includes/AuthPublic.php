<?php

class AuthPublic extends Module implements Iface_Auth {	
 	protected $authClassName;
 	
 	public function __construct($modName,$qry=""){
 		parent::__construct($modName,$qry);
 		$this->authClassName = __CLASS__;
 	}
	
	public function authenticate(){
		$session = SessionTool::getSession();
		if(strlen($session->auth_level)==0) {
			$session->auth_level = "public";
		}
		return true;
	}
}
