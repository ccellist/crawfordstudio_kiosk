<?php
/*
 * Created on Dec 8, 2011
 *
 * User module. Responsible for handling all actions
 * relating to users, such as logging one in, creating
 * new users, modifying application-specific user 
 * information such as passwords and e-mails if required.
 * 
 * NOTE: This class *must not* extend anything other than 
 * AuthPublic or you will get an authentication loop that 
 * requires you to log in just to log somebody in.
 *
 * author: Arturo Araya
 * 
 */

class Mod_User extends AuthPublic {
	protected $userId;
	protected $loginId;
	protected $username;
	protected $password;
	protected $userType;
	protected $homePage;
	protected $authSessValue;
	
	public function __construct($modName,$qry="",$error=0) {
		parent::__construct($modName,$qry);
		$this->getSessionVars();
		$this->error = $error;
	}
	
	public function _default() {
		/* Default code for private destination goes here.
		 * Might be a modified home page or something else.
		 * Function is designed to be overridden
		 * in subclasses (customer, etc). */
		 $this->data = USER_HOME_PG;
	}
	
	public function goHome() {}
	
	public function login(){
		/* Save the qry string, i.e. the destination URL if there is one
		 * to the data value so we can use it if needed.
		 */
		$this->data = $this->setDestUrl($this->qryString);
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
	
	public function renewLogin() {
		$interval = USER_LOGIN_EXP_TIME;
		$newDate = date("YmdHis", time() + $interval);
		$qry = "update active_users set expire = '$newDate' where instance_id = '" . $this->loginId . "'";		
		$this->Run($qry);
		$this->data = $this->qryString;
	}
	
	public function logout(){
		/* Remove user from active_users table. Destroy
		 * the session variable.
		 */
		 $this->setInactiveInDb();
		 $session = SessionTool::getSession();
		 $session->destroy();
		 $this->loadSessionData("logout");
		 $this->data = "/index.php";
	}
	
	public function getNewUserInfo(){
		if ($this->qryString != ""){
			$this->data = $this->setDestUrl($this->qryString);
		} else {
			$this->data = "";
		}
	}
	
	public function create(){
		if (($this->userId = $this->getDBUserId($_POST['username'])) == 0){
			if (isset($_POST['chkIsAdmin'])){
				if ($_POST['chkIsAdmin'] == "1") {
					$isAdmin = $_POST['chkIsAdmin'];
				} else {
					$isAdmin = "0";
				}
			} else {
				$isAdmin = "0";
			}
			$username = $_POST['username'];
			$password = crypt($_POST['password']);
			$sql = "insert into users (username,password,date_created,admin_user) values ('$username','$password','".date("Y-m-d H:i:s",time())."','$isAdmin')";
			
			if ($res = $this->Run($sql)){
				$sql2 = "select LAST_INSERT_ID() as uid from users";
				$res = $this->getResult($sql2);
				$this->userId = $res[0]['uid'];
				(isset($_POST['destUrl']) && strlen($_POST['destUrl'])>0)?$goUrl = $_POST['destUrl']:$goUrl = "/index.php";
				$this->procLogin($_POST['username'],$_POST['password'],$goUrl);
			} else {
				throw new Exception("Error creating user '$username'.<br>Please try again, or contact the webmaster.");
			}
		} else {
			throw new Exception("User $username exists. Please select another.");
		}
	}
	
	public function getUserName(){}
	
	public function chgPwd(){
		$this->data = $this->qryString;
	}
	
	public function setPwd() {
		$this->password = $this->getDBPassword();
		if ($this->checkPassword($_POST['oldPw'])){
			$newPwd = crypt($_POST['newPw']);
			$sql = "update users set password = '$newPwd', must_chg_pwd = '0' where uid = " . $this->userId;
			if ($this->Run($sql) === true){
				$this->data = $this->setDestUrl($_POST['destUrl']);
			} else {
				$this->data = "error|".$this->setDestUrl($_POST['destUrl']);
			}
		} else {
			$destUrl = urlencode($this->setDestUrl($_POST['destUrl']));
			$this->data = "crederror|/index.php?module=User&action=chgPwd&qry=$destUrl&e=".INVALID_LOGIN;
		}
	}
	
	public function resetPwd(){
		$sql = "select uid,username from users where username = '" . $_POST['username'] . "'";
		$res = $this->getResult($sql);
		if($this->num_rows){ 
			$uid = $res[0]['uid'];
			$username = $res[0]['username'];
			$newPwd = substr(md5($username . "_" . $uid . time()),0,8);
			$newCrypt = crypt($newPwd);
			$sql2 = "update users set password = '$newCrypt', must_chg_pwd = '1' where uid = $uid";
			if (($res = $this->Run($sql2)) === false) {
				$this->data = "General failure resetting password.<br>Please contact the webmaster.";
			} else {
				/*send user an email with their new password*/
				$body = <<<BODY
Hello,

At your request your password has been changed. It is now $newPwd.

You will be asked to change this on your next login.

Thanks,
BODY
. WEBSITE_NAME . "\n";
				Email::SendEmail($username,BULK_EMAIL_RETURN_ADDRESS,$body);
				$this->data = "Password successfully changed.<br>Your new password has been sent to your registered email address.<br>";
				$this->data .= "Redirecting in 5 seconds...";
			}
		} else {
			$this->data = "Could not reset password. User number " . $this->qryString . "not found.";
		}
	}
	
	protected function setDestUrl($url=""){
		if (strlen($url)>0){
			$destUrl = preg_replace("/&amp;/","&",$url);
		} else {
			$destUrl = $this->homePage;
		}
		return $destUrl;
	}
	
	protected function getDBUserId($username){
		$sql = "select uid from users where username = '$username'";
		$res = $this->getResult($sql);
		if($this->num_rows){ 
			return $res[0]['uid'];
		} else {
			return 0;
		}
		
	}
	
	protected function getDBUserType(){
		$sql = "select admin_user from users where uid = '". $this->userId . "'";
		$res = $this->getResult($sql);
		if($this->num_rows){
			if ($res[0]['admin_user'] == "1"){
				return "admin";	
			} else {
				return "user";
			}
		} else {
			/* We are returning 'invalid' here to indicate the
			 * user does not exist.
			 */
			return "user";
		}
	}
	
	protected function getDBPassword(){
		$sql = "select password from users where uid = '". $this->userId . "'";
		$res = $this->getResult($sql);
		if($this->num_rows){
			return $res[0]['password'];
		} else {
			return hash("sha256","password" . mt_rand());
		}
	}
	
	private function loadSessionData($action = "login"){
		$session = SessionTool::getSession();
		if ($action == "logout"){
			$session->auth_level = "public";
			$session->userId = 0;
			$session->loginId = 0;
		} else {
			$session->auth_level = "user";
			$session->userId = $this->userId;
			$session->loginId = $this->loginId;
			$session->homePage = USER_HOME_PG;
			$this->homePage = $session->homePage;
		}		
	}
	
	protected function getSessionVars(){
		$session = SessionTool::getSession();
		$this->userId = $session->userId;
		$this->loginId = $session->loginId;
	}

	protected function setActiveInDb() { 
		$exp = time() + USER_LOGIN_EXP_TIME;
		$instance = md5(mt_rand(0, 100));
		$ip_addr = implode(",",get_ip_list());
		$qryInsert = "INSERT INTO active_users (userid, instance_id, last_login, expire, ip_addr) VALUES ({$this->userId}, '$instance', " . date('YmdHis') . ", " . date('YmdHis', $exp) . ", '$ip_addr')";
		$this->Run($qryInsert);
		return $instance;
	}
	
	public function setInactiveInDb(){
		$sql = "delete from active_users where instance_id = '" . $this->loginId . "'";
		$this->Run($sql);
	}

	protected function checkPassword($inPwd) { /*tested*/		
		if ($this->password == crypt($inPwd, $this->password)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	protected function mustChangePassword(){
		$sql = "select must_chg_pwd from users where uid = " . $this->userId;
		$res = $this->getResult($sql);
		return $res[0]['must_chg_pwd'];
	}

	public static function getRemainingSessionTime($login_id) {
		$sql = "select expire from active_users where instance_id = '$login_id'";
		$res = $this->getResult($sql);
		if (is_array($res)){
			$expTime = $res[0]['expire'];
			$remTime = strtotime($res[0]['expire']) - time();
			if (date("i:s",$remTime) == "00:00") {
				return "60:00";
			}
			else {
				return date("i:s",$remTime);
			}
		}
	}
}