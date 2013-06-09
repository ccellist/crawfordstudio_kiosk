<?php
/*
 * Created on Dec 8, 2011
 *
 * author: arturo
 * 
 */

$login = new UI_Login();
$login->overrideFormAction("/index.php?module=Admin&action=procLogin");
$login->destUrl = $dispData;
if ($pgErr != 0){
	if ($pgErr == INVALID_LOGIN){
		$login->errMsg = "That username/password combination was not found.<br>Please try again.";
	} else {
		$login->errMsg = "Unexpected error. Please contact the webmaster.<br>Error code '$pgErr'.";
	}
}
$login->reBuildHtml();
print $login->getJS();
print $login->getHtml();