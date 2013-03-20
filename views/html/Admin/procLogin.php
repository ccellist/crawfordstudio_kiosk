<?php
/*
 * Created on Dec 8, 2011
 *
 * author: arturo
 * 
 */

class html_Admin_procLogin extends presenter {
	public function __construct($destUrl,$modName,$error) {
		if (strlen($destUrl)==0){
			$destUrl = "/";
		}
		parent::__construct(__CLASS__, $destUrl, $modName, $error);
	}
	
	/**
	 * Override the parent display() function
	 * to prevent any HTML from being sent.
	 * We're just processing a login and redirecting
	 * appropriately.
	 */
	public function display(){ var_dump($this);
		header("Location: " . $this->data);
		//echo $this->data;
	}
}

