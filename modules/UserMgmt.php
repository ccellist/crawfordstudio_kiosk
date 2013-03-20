<?php
/*
 * Created on Dec 15, 2011
 *
 * author: arturo
 * 
 */

class Mod_UserMgmt extends AuthAdmin {	
	public function __construct($modName, $qry = ""){
		parent::__construct($modName, $qry);
	}
	
	public function _default(){
		/* Does nothing, but provides vehicle for displaying
		 * the default template which lists various user
		 * management functions.
		 */
	}
	
	public function showUsers(){
		$output = "";
		$sql = "select uid,username,admin_user from users";
		$res = $this->getResult($sql);
		$this->data = $res;
	}
	
	public function getNewUserInfo(){}
	
	public function togAdmin(){
		$sql = "select count(*) as admin_count from users where admin_user = '1'";
		$res = $this->getResult($sql);
		if (($res[0]['admin_count'] > 1)||($_POST['val'] == '1')) {
			$sql = "update users set admin_user = '" . $_POST['val'] . "' where uid = " . $_POST['uid'];
			if ($this->Run($sql) === true){
				$this->data = "Permissions changed.";
			} else {
				$this->data = "Error setting permissions.";
			}
		} else {
			$this->data = "At least one admin user is required. Aborting.";
		}
	}
	
	public function delete(){
		$sql = "delete from users where uid = " . $_POST['uid'];
		if (($res = $this->Run($sql)) === false) {
			$this->data = "General failure deleting user.";
		} else {
			$this->data = "User deleted.";
		}
	}
	
	public function create(){
		$user = new User();
		try {
			$user->create();
			$this->data = "User successfully created. New ID: " . $user->userId;
		} catch (Exception $e) {
			$this->data = "Error creating user. " . $e->getMessage();
		}		
	}
	
	public function resetPwd(){
		$sql = "select uid,username from users where uid = " . $_POST['uid'];
		$res = $this->getResult($sql);
		if($this->num_rows){ 
			$uid = $res[0]['uid'];
			$username = $res[0]['username'];
			$newPwd = substr(md5($username . "_" . $uid . time()),0,8);
			$newCrypt = crypt($newPwd);
			$sql2 = "update users set must_chg_pwd = '1', password = '$newCrypt' where uid = $uid";
			if (($res = $this->Run($sql2)) === false) {
				$this->data = "General failure resetting password.";
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
				/*Display the new password*/
				$this->data = "New password: $newPwd";
			}
		} else {
			$this->data = "Could not reset password. User number " . $this->qryString . "not found.";
		}
	}
}