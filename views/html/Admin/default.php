<?php
/*
 * Created on Dec 8, 2011
 *
 * author: arturo
 * 
 */

class html_Admin_default extends presenter {
	public function __construct($data,$modName,$error) {
		parent::__construct(__CLASS__, $data, $modName, $error);
	}
	
	public function display(){
		header("Location: " . $this->data);
	}
}

