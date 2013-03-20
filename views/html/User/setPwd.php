<?php

class html_User_setPwd extends presenter {
	public function __construct($data,$modName,$error) {
		parent::__construct(__CLASS__, $data, $modName, $error);
	}
	
	public function display(){
		$delay=5;
		$newurl = $this->data;
		 if (strpos($newurl,"crederror|") !== false){
			$newurl = preg_replace("/^crederror\|(.+)/","$1",$newurl);
			header("Location: $newurl");
		} elseif (strpos($newurl,"error|") !== false){
			$message = "Sorry, but your password could not be changed at this time.<br>" .
					"Please contact the website administrator for assistance.<br>" .
					"Redirecting in $delay seconds...";
			$newurl = preg_replace("/^error\|(.+)/","$1",$newurl);
		} else {
			$message = <<<MSG
			Password successfully changed.<br>
			Redirecting in $delay seconds...
MSG
;
		}
		$output = "<html><head>\n";
		$output .= "<meta http-equiv=\"refresh\" content=\"$delay;url=$newurl\" />";
		$output .= "</head><body>\n";
		$output .=  $message . "\n";
		$output .= "</body>";
		$output .= "</html>";
		
		print $output;
	}
}
