<?php
/*
 * Created on Dec 8, 2011
 *
 * Class that provides authentication 
 * Administrator restrictions to children classes.
 * 
 * author: Arturo Araya
 */
  class AuthAdmin extends Module implements Iface_Auth {
 	protected $authClassName;
 	
 	public function __construct($modName,$qry=""){
 		parent::__construct($modName,$qry);
 		$this->authClassName = __CLASS__;
 		//$this->dumpToDbErrLog(__FILE__,$this->authClassName);
 	}
 	
 	public function authenticate() {
 		$session = SessionTool::getSession();
 		if ($session->auth_level == "admin"){
 			return true;
 		} else {
 			return false;
 		}
 	}
 }

