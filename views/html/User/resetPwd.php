<?php

class html_User_resetPwd extends presenter {
	public function __construct($data,$modName,$error) {
		parent::__construct(__CLASS__, $data, $modName, $error);
	}
	
	public function display(){
		$newurl = "/index.php";
		$output = "<html><head>\n";
		$output .= "<meta http-equiv=\"refresh\" content=\"5;url=$newurl\" />";
		$output .= "</head><body>\n";
		$output .= $this->data . "\n";
		$output .= "</body>";
		$output .= "</html>";
		
		print $output;
	}
}
