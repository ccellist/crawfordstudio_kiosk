<?php
/*
 * Created on Dec 28, 2011
 *
 * author: arturo
 * 
 */

class Mod_Member extends AuthUser {
	public function __construct($modName, $qry = ""){
		parent::__construct($modName, $qry);
	}
	
	public function _default(){
		
	}
}