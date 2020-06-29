<div id='compassdiv'></div><div id='warningdiv'></div>
<?php
$template = <<<THEVERYENDOFYOU
<form action="users.php?do=register" method="post">
<table width="80%">
<tr><td colspan="2">Please fill in the information below, and follow the neccessary instructions for a successful registration. Areas marked with <font color=red>*</font> must be filled in.<br/><br /></td></tr>
<tr><td width="20%"><font color=red>*</font>Username:</td><td><input type="text" name="username" size="30" maxlength="30" /><br />Usernames must be 30 alphanumeric characters <b>or less</b> and contain no spaces. (numbers, letters or a combination). The following characters are not allowed: - _ \ / [ ] ^<br /><br /><br /></td></tr>
<tr><td><font color=red>*</font>Password:</td><td><input type="password" name="password1" size="30" maxlength="10" /></td></tr>
<tr><td><font color=red>*</font>Verify Password:</td><td><input type="password" name="password2" size="30" maxlength="10" /><br />Passwords must be 10 alphanumeric characters <b>or less</b> and contain no spaces. (numbers, letters, or a combination).  The following characters are not allowed: - _ \ / [ ] ^<br /><br /><br /></td></tr>
<tr><td><font color=red>*</font>Email Address:</td><td><input type="text" name="email1" size="30" maxlength="100" /><br>Email must be valid.</td></tr>
<tr><td><font color=red>*</font>Character Name:</td><td><input type="text" name="charname" size="30" maxlength="30" /><br />Character Names must be 30 alphanumeric characters <b>or less</b> and contain no spaces. (numbers, letters or a combination). The following characters are not allowed: - _ \ / [ ] ^</td></tr>
<tr><td><font color=red>*</font>Character Class:</td><td><select name="charclass"><option value="1">{{class1name}}</option><option value="2">{{class2name}}</option><option value="3">{{class3name}}</option><option value="4">{{class4name}}</option><option value="5">{{class5name}}</option><option value="6">{{class6name}}</option><option value="7">{{class7name}}</option></select><br>Each class has its own Advantages and Disadvantages.<p><p>Below is optional information which will appear in your Profile. You can modify and update this from within your Player Options while logged in. (optional)<p></td></tr>

<tr><td><p>Name:</td><td><input type="text" name="name" size="25" maxlength="25" /></td></tr>
<tr><td>Gender:</td><td><select name="gender"><option value="Unknown">Unknown</option><option value="Male">Male</option><option value="Female">Female</option></select></td></tr>
<tr><td>Country:</td><td><input type="text" name="country" size="15" maxlength="15" /></td></tr>

<tr><td>MSN Messenger:</td><td><input type="text" name="msn" size="30" maxlength="35" /></td></tr>
<tr><td>AOL Instant Messenger:</td><td><input type="text" name="aim" size="30" maxlength="35" /></td></tr>
<tr><td>Yahoo! Instant Messenger:</td><td><input type="text" name="yim" size="30" maxlength="35" /></td></tr>
<tr><td>ICQ:</td><td><input type="text" name="icq" size="30" maxlength="35" /></td></tr>
<tr><td colspan="2"><b>By clicking Create Account below, you agreeing to obide by the rules and provide a valid email address. <p>Note: When you sign in, if it brings you back to the main login page, this means you need cookies enabled correctly, simply set your privacy bar to low on Internet Explorer.</b><br/><br /></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="Create Account" /> <input type="reset" name="reset" value="Reset" /></td></tr>
</form></table>
THEVERYENDOFYOU;
?>


