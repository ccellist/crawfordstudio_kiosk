<?php

class SessionTool {
	protected static $instance;
	public static $sessionId;
	
	private function __construct() {
		Session::setHandler();
		session_start();
		self::$sessionId = session_id();
	}
	
	public static function getSession(){
		if (!isset(self::$instance)){
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	public function destroy(){
		setcookie("PHPSESSID",session_id(),time()-3600);
		$_SESSION = array();
		session_destroy();
		$class = __CLASS__;
		self::$instance = new $class();
	}
	
	public function __clone(){
		throw new Exception("Permission denied.");
	}
	
	public static function isAuthType($type){
		return ($type == $_SESSION['auth_level']);
	}
	
	public static function dumpSession(){
		print <<<HTML
<div class="debug" style="float:right;width:35%;height:100px;background:yellow">
<div class="dbgInner" style="margin:5px">
HTML
;
		print "session id = " . session_id()."<br>";
		foreach ($_SESSION as $key=>$val){
			print $key . " = " . $val . "<br>\n";
		}
		//var_dump(self::$instance);
		print "</div></div>";
	}
	
	public function __set($key,$val){
		return ($_SESSION[$key] = $val);
	}
	
	public function __get($val) {
            if (($val == "id") || ($val == "session_id")){
                return session_id();
            } else {
		if (isset($_SESSION[$val])) {
			return $_SESSION[$val];
		} else {
			return false;
		}
            }
	}
	
	public function __destruct(){
		session_write_close();
	}
}
