<?php
/*
 * Created on Dec 8, 2011
 *
 * Class that provides authentication User restrictions
 * to children classes.
 * 
 * author: Arturo Araya
 */
 class AuthUser extends Module implements Iface_Auth {
 	protected $authClassName;
 	
 	public function __construct($modName,$qry=""){
 		parent::__construct($modName,$qry);
 		$this->authClassName = __CLASS__;
 	}
 	
 	public function authenticate() {
 		$session = SessionTool::getSession();
 		if (($session->auth_level == "user") || ($session->auth_level == "admin")){
 			return true;
 		} else {
 			return false;
 		}
 	}
 }

