<?php
/*
 * Created on Dec 9, 2011
 *
 * author: arturo
 * 
 */

class Mod_Admin extends Mod_User {
	
	public function _default(){
		/* Code to load default behavior for a 
		 * web app admin. Overridden in subclasses.
		 */
		$this->data = ADMIN_HOME_PG;
	}
	
	public function procLogin($un="",$pwd="",$url=""){
		/*get userid from username, then user info, check password, if valid 
		 * enter user in the db, then load all session variables.
		 * Also save any destination URL and redirect as needed.
		 */
		$un==""?$un=$_POST['username']:false;
		$pwd==""?$pwd=$_POST['password']:false;
		
		$this->userId = $this->getDBUserId($un);
		$this->password = $this->getDBPassword();
		$this->userType = $this->getDBUserType();
		if ($this->checkPassword($pwd)){
			/*echo $this->password."<br>\n";
			echo $_POST['password']."<br>\n";
			echo crypt($_POST['password'], $this->password)."<br>\n";*/
			$this->loginId = $this->setActiveInDb();
			$this->loadSessionData();
			$url==""?$url=$_POST['destUrl']:false;
			if (!$this->mustChangePassword()){
				$destUrl = $this->setDestUrl($url);
			} else {
				(strlen($url)>0)?$qryStr="&qry=" . urlencode($this->setDestUrl($url)):$qryStr = "";
				$destUrl = "/index.php?module=User&action=chgPwd".$qryStr;
			}
			$this->data = $destUrl;
		} else { 
			$destUrl = urlencode($this->setDestUrl($_POST['destUrl']));
			$this->data = "/index.php?module=User&action=login&qry=$destUrl&e=".INVALID_LOGIN;
		}
	}
	
	private function loadSessionData(){
		$session = SessionTool::getSession();
		$session->auth_level = "admin";
		$session->userId = $this->userId;
		$session->homePage = "/index.php?module=UserMgmt";
		$this->homePage = $session->homePage;
	}
}