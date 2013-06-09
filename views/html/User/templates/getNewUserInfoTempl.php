<form action="/index.php?module=User&action=create" method="POST">
Username:&nbsp;<input type="text" id="username" name="username"><br>
Password:&nbsp;<input type="password" id="password" name="password"><br>
<input type="hidden" value="<?php print $dispData; ?>" name="destUrl">
<input type="submit" value="submit">
<input type="reset" value="Clear">
</form>