<div id='compassdiv'></div><div id='warningdiv'></div>
<?php
$template = <<<THEVERYENDOFYOU
<form action="users.php?do=resend" method="post">
<table width="80%">
<tr><td colspan="2">Thank you for registering. If you haven't got your Verification Code email yet, please type in your email below, and hopefully it will be sent to you.<p></td></tr>
<tr><td width="20%">Email Address:</td><td><input type="text" name="email" size="30" maxlength="100" /></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" /></td></tr>
</table>
</form>
THEVERYENDOFYOU;
?>