<?php 
include("accesscontrol.php");
include("header.php");
include_once("functions.php");	
if (isset($_POST['submitok'])):
   // Process signup submission 

if ($_POST['name']=='' or $_POST['email']=='') {
       error('One or more required fields were left blank.\\n'.
             'Please fill them in and try again.');
   }
if ($_POST['name']!=$userfullname or $_POST['email']!=$useremail or $_POST['notes']!=$usernotes) {
	$q ="UPDATE user SET fullname = '${_POST['name']}', email = '${_POST['email']}',
	     notes = '${_POST['notes']}' WHERE userid = '$uid'";
	$r = pdo_query($q);
	print "<strong>Profile change successful!</strong><br/>";
	include("accesscontrol.php");
}

#update password   
if ($_POST['newpwdconf']!=$_POST['newpwd'] or ($_POST['oldpwd']!='' and $_POST['oldpwd']!=$pwd)){
	error('Passwords don\'t match.\nPlease try again.');
}elseif ($_POST['newpwd']!=''and $_POST['newpwdconf']==$_POST['newpwd'] and $_POST['oldpwd']==$pwd){
	$pwd = $_POST['newpwd'];
	$q ="UPDATE user SET password = MD5('$pwd') WHERE userid = '$uid'";
	$r = pdo_query($q);
	$_SESSION['pwd'] = $pwd;
	print "<strong>Password change successful!</strong>";
}
endif
   // Display the user profile form 
?>
	<h3>User Profile for <?php print $username ?></h3>
	<p><font color="orangered" size="+1"><tt><b>*</b></tt></font>
	  indicates a required field</p>
	<form method="post" action="<?php print $_SERVER['PHP_SELF']?>">
	<table border="0" cellpadding="0" cellspacing="5">
	   <tr>
	       <td align="right">
	           <p>Full Name</p>
	       </td>
	       <td>
	           <input name="name" type="text" maxlength="100" size="25" 
	                  value="<?php print $userfullname?>"/>
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>E-Mail Address</p>
	       </td>
	       <td>
	           <input name="email" type="text" maxlength="100" size="25" 
	                  value="<?php print $useremail?>" />
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>Old password</p>
	       </td>
	       <td>
	           <input name="oldpwd" type="password" maxlength="100" size="25"/>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>New password</p>
	       </td>
	       <td>
	           <input name="newpwd" type="password" maxlength="100" size="25"/>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>Confirm new password</p>
	       </td>
	       <td>
	           <input name="newpwdconf" type="password" maxlength="100" size="25"/>
	       </td>
	   </tr>
	   <tr valign="top">
	       <td align="right">
	           <p>Other Notes</p>
	       </td>
	       <td>
	           <textarea wrap="soft" name="notes" rows="5" cols="30"><?php print $usernotes;?></textarea>
	       </td>
	   </tr>
	   <tr>
	       <td align="right" colspan="2">
	           <hr noshade="noshade" />
	           <input type="reset" value="Reset Form" />
	           <input type="submit" name="submitok" value="   OK   " />
	       </td>
	   </tr>
	</table>
	</form> 