<?php
/*
 * Created on Dec 8, 2011
 *
 * author: arturo
 * 
 */

 class UI_Login {
 	private $html;
 	private $css;
 	private $js;
 	private $id;
 	private $class;
 	private $errMsg;
 	private $destUrl;
 	
 	public static $newAcctUrl;
 	public static $resetUrl;
 	public static $loginPage;
 	
 	public function __construct($id="",$class="",$htmlOverride="", $errMsg=""){	 	
 		if ($id != ""){
	 		$this->id = $id;
 		} else {
 			$this->id = "login";
 		}
 		if ($class != ""){
	 		$this->class = "class='$class'";
 		} else {
 			$this->class = "class='loginMod'";
 		}
 		if ($htmlOverride != ""){
	 		$this->html = "$htmlOverride";
 		} else {
 			$this->buildHtml();
 		}
 		$this->errMsg = $errMsg;
 		$this->buildJS();
 		self::$newAcctUrl = "/index.php?module=User&action=getNewUserInfo";
	 	self::$resetUrl = "/index.php?module=User&action=getUserName";
	 	self::$loginPage = "/index.php?module=User&action=procLogin";
 	}
 	
 	public function getHtml() {
 		return $this->html;
 	}
 	
 	public function setHtml($html){
 		$this->html = $html;
 	}
 	
 	public function getCSS(){
 		return $this->css;
 	}
 	
 	public function setCSS($css) {
 		$this->css = $css;
 	}
 	
 	public function getJS() {
 		return $this->js;
 	}
 	
 	public function setJS($js,$overwrite=true){
 		if ($overwrite){
 			$this->js = $js;
 		} else {
 			$this->js = preg_replace("/-->/","$js\n-->",$this->js);
 		}
 	}
 	
 	public function __set($key,$val){
 		if ($key == "destUrl"){
 			$this->$key = "<input type='hidden' name='destUrl' value='$val'>";
 			self::$newAcctUrl = "/index.php?module=User&action=getNewUserInfo&qry=".urlencode($val);
 		} else {
 			$this->$key = $val;
 		} 		
 	}
 	
 	public function overrideFormAction($newAction){
 		self::$loginPage = $newAction;
 	}
 	
 	public function reBuildHtml(){
 		$this->buildHtml();
 	}
 	
 	private function buildHtml(){
 		$resetPage = self::$resetUrl;
 		$loginPage = self::$loginPage;
 		$newAcctUrl = self::$newAcctUrl;
 		$destUrl = $this->destUrl;
 		$this->html = <<<HTML
<!-- Begin login -->
<div id="{$this->id}Top"></div>
<div {$this->class} id="{$this->id}"> 
	<span class="center error">{$this->errMsg}<br /></span>
	<span style="position:relative;top:-3px;">Please log in to continue, or <a href="$newAcctUrl">create a new account</a>.</span><br />

	<form action="$loginPage" method="post" id="{$this->id}Form">
	<table id="{$this->id}Box">
	   <tr>
		<td><input type="text" id="username" name="username" value="Enter your email" onfocus='clearField()' style="width:160px"/></td>
		<td><input type="password" id="password" name="password" value="password" onfocus='clearField()' style="width:160px"/></td>
	   </tr>
	   <tr>
		<td><input type="submit" id="submit" value="submit" /></td>
		<td><span class="tiny"><a href="$resetPage">Forgot password?</a></span></td>
$destUrl
	   </tr>
	</table>
	</form>
</div>
<!-- End login -->
 
HTML
 ;
 	}
 	
 	private function buildJS(){
 		$this->js = <<<JS
<script type="text/javascript">
<!--
var cleanSlate = 0;

function clearField() {
	if (cleanSlate == 0) {
		document.getElementById('username').value = "";
		document.getElementById('password').value = "";
		cleanSlate = 1;
	}
}
-->
</script>

JS
;
 	}
 }