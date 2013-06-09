<style>
div.login .enterEmail {
margin-top:15px;	
}
input.button{
margin-top: 7px;
}
</style>
<div class="login"><form action="/index.php?module=User&action=resetPwd" method="post" id="loginForm">
<div class="enterEmail">
<span style="position:relative;top:-5px;">Please enter your email address:</span><br />
<!--<input type="hidden" name="reset" value="1" />-->
<input type="text" name="username" style="width:250px;" /><br />
<input class="button" type="submit" value="Submit" />
<input class="button" type="reset" value="Clear" />
</div>
</form>
</div>

