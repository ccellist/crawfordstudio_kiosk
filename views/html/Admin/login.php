<?php
/*
 * Created on Dec 8, 2011
 *
 * author: arturo
 * 
 */

class html_Admin_login extends presenter {
	public function __construct($data,$modName,$error) {
		parent::__construct(__CLASS__, $data, $modName, $error);
	}        
	
	public function display(){
		$dispData = $this->data;
		$pgErr = $this->error;
		ob_start();
		include(self::$templates . $this->templateFile);
		$mainContents = ob_get_contents();
		ob_clean();
		include(self::$mainTemplPath . "/mainNoSidebarsTempl.php");		
	}
}

