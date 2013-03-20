<?php
/*
 * Created on Dec 23, 2011
 *
 * author: arturo
 * 
 */
?>
<div class="login"><form action="/index.php?module=User&action=setPwd" method="post" id="loginForm">
<div class="enterEmail">
<?php 
if ($pgErr != 0){
	print "<span class=\"center error\">";
	if ($pgErr == INVALID_LOGIN){
		print "Password mismatch.<br>Please try again.";
	} else {
		print "Unexpected error. Please contact the webmaster.<br>Error code '$pgErr'.";
	}
	print "<br /></span>";
}
?>
<span style="position:relative;top:-5px;">Please change your password:</span><br />
<!--<input type="hidden" name="reset" value="1" />-->
<table>
<tr>
<td>Current password:</td>
<td><input type="password" name="oldPw" style="width:250px;" /></td></tr>
<tr>
<td>New password:</td><td><input type="password" name="newPw" style="width:250px;" /></td></tr>
<tr>
<td>Password confirm:</td>
<td><input type="password" style="width:250px;" />
<input type="hidden" name="destUrl" value="<?php print $dispData; ?>"</td></tr>
<tr>
<td><input class="button" type="submit" value="Submit" /></td>
<td><input class="button" type="reset" value="Clear" /></td></tr>
</table>
</div>
</form>
</div>
