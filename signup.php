<?php 
include("header.php");
include("functions.php");	
if (!isset($_POST['submitok'])):
   // Display the user signup form 
   ?>

	<h3>New User Registration Form</h3>
	<p><font color="orangered" size="+1"><tt><b>*</b></tt></font>
	  indicates a required field</p>
	<form method="post" action="<?php print $_SERVER['PHP_SELF']?>">
	<table border="0" cellpadding="0" cellspacing="5">
	   <tr>
	       <td align="right">
	           <p>User ID</p>
	       </td>
	       <td>
	           <input name="newid" type="text" maxlength="100" size="25" />
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>Full Name</p>
	       </td>
	       <td>
	           <input name="newname" type="text" maxlength="100" size="25" />
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>E-Mail Address</p>
	       </td>
	       <td>
	           <input name="newemail" type="text" maxlength="100" size="25" />
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr>
	       <td align="right">
	           <p>Group</p>
	       </td>
	       <td>
	           <select name="group"/>
		   <option value ="0">please choose:</option>
		   <?php
		      $gq = "SELECT id, fullname FROM user WHERE groupType='1'";
		      $groups = pdo_query($gq);
		      foreach($groups as $g){
	           	print "<option value =\"${g['id']}\">${g['fullname']}</option>";
		      }
	           ?>
	           </select>
	           <font color="orangered" size="+1"><tt><b>*</b></tt></font>
	       </td>
	   </tr>
	   <tr valign="top">
	       <td align="right">
	           <p>Other Notes</p>
	       </td>
	       <td>
	           <textarea wrap="soft" name="newnotes" rows="5" cols="30"></textarea>
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
<?php 
else:
   // Process signup submission 

if ($_POST['newid']=='' or $_POST['newname']==''
     or $_POST['newemail']=='' or $_POST['group']==0) {
       error('One or more required fields were left blank.\\n'.
             'Please fill them in and try again.');
   }

// Check for existing user with the new id
$sql = "SELECT COUNT(*) FROM user WHERE userid = '$_POST[newid]'";
$result = pdo_query($sql);
if (!$result) {
error('A database error occurred in processing your '.
     'submission.\\nIf this error persists, please '.
     'contact schalch@cshl.edu.');
}
if ($result[0]['COUNT(*)']) {
error('A user already exists with your chosen userid.\\n'.
     'Please try another.');
}

$newpass = substr(md5(time()),0,6);

$sql = "INSERT INTO user SET
     userid = '$_POST[newid]',
     password = MD5('$newpass'),
     fullname = '$_POST[newname]',
     email = '$_POST[newemail]',
     notes = '$_POST[newnotes]',
     groupType = 0";
$newid = pdo_query($sql);
if (!$newid)
	error('1A database error occurred in processing your '.
	     'submission.\\nIf this error persists, please '.
	     'contact schalch@cshl.edu.');
    // Email the new password to the person.
    
# setup groups
$sql = "INSERT INTO groups SET
     userid = '$newid',
     belongsToGroup = '$newid',
     defaultPermissions = 2";
if (!pdo_query($sql))
	error('2A database error occurred in processing your '.
	     'submission.\\nIf this error persists, please '.
	     'contact schalch@cshl.edu.');

$sql = "INSERT INTO groups SET
     userid = '$newid',
     belongsToGroup = '$_POST[group]',
     defaultPermissions = 0";
if (!pdo_query($sql))
	error('3A database error occurred in processing your '.
	     'submission.\\nIf this error persists, please '.
	     'contact schalch@cshl.edu.');
    // Email the new password to the person.


$message = "Hi!

Your personal account for the Project Web Site
has been created! To log in, proceed to the
following address:

http://nanda.cshl.org/labdb

Your personal login ID and password are as
follows:

userid: $_POST[newid]
password: $newpass

You aren't stuck with this password! Your can
change it at any time after you have logged in.

If you have any problems, feel free to contact me at
<schalch@cshl.edu>.

Thomas
";

mail($_POST['newemail'],"Your Password for labdb",
	$message, "From:Thomas Schalch <schalch@cshl.edu>");

mail("schalch@cshl.edu","New labdb user created",
	"user $_POST[newid] has signed up to labdb.",
        "From:Thomas Schalch <schalch@cshl.edu>");


?>
   <p><strong>User registration successful!</strong></p>
   <p>Your userid and password have been emailed to
      <strong><?=$_POST[newemail]?></strong>, the email address
      you just provided in your registration form. To log in,
      click <a href="index.php">here</a> to return to the login
      page, and enter your new personal userid and password.</p>
<?php
endif;

include("footer.php"); ?>