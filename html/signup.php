<?php 
include("header.php");
include("functions.php");
include('config.php');
    
if (!isset($_POST['submitok'])):
   // Display the user signup form 
   ?>
<link href="css/signin.css" rel="stylesheet">
<head><body>
<div class="container text-center">
 <h1 class="h3 mb-3 font-weight-normal">New User Registration Form</h1>
        <form method="post" class="form-signin" action="<?php print $_SERVER['PHP_SELF']?>">

   <div class="form-group">
    <label class="sr-only" for="userid">User ID</label>
    <input placeholder="Username" class="form-control" id="userid" type="text" name="newid" required autofocus/>
   </div>
   <div class="form-group">
    <label class="sr-only" for="fullname">User ID</label>
    <input placeholder="Full Name" class="form-control" id="fullname" type="text" name="newname" required autofocus/>
   </div>
   <div class="form-group">
    <label class="sr-only" for="email">User ID</label>
    <input placeholder="E-mail Address" class="form-control" id="email" type="text" name="newemail" required autofocus/>
   </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
    <button class="btn btn-lg btn-primary btn-block" type="reset">Reset</button>
        </form>

	</form> 
<?php 
else:
   // Process signup submission 

if ($_POST['newid']=='' or $_POST['newname']==''
     or $_POST['newemail']=='' or $_POST['group']==0) {
       error('One or more required fields were left blank.\\n'.
             'Please fill them in and try again.');
   }

$newpass = substr(md5(time()),0,6);
$newid = 'username';

// Check for existing user with the new id
$sql = "SELECT COUNT(*) FROM user WHERE userid = '$_POST[newid]'";
$result = pdo_query($sql);
if (!$result) {
error('A database error occurred in processing your '.
     'submission.\\nIf this error persists, please '.
     'contact $adminEmail.');
}

if ($result[0]['COUNT(*)']) {
error('A user already exists with your chosen userid.\\n'.
     'Please try another.');
}


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
	     "contact $adminEmail.");
    // Email the new password to the person.
//
# setup groups
$sql = "INSERT INTO groups SET
     userid = '$newid',
     belongsToGroup = '$newid',
     defaultPermissions = 2";
if (!pdo_query($sql))
	error('2A database error occurred in processing your '.
	     'submission.\\nIf this error persists, please '.
	     "contact $adminEmail.");

$sql = "INSERT INTO groups SET
     userid = '$newid',
     belongsToGroup = '$_POST[group]',
     defaultPermissions = 1;";

if (!pdo_query($sql))
	error('3A database error occurred in processing your '.
	     'submission.\\nIf this error persists, please '.
	     "contact $adminEmail.");
    // Email the new password to the person.


$message = "Hi!

Your personal account for the Project Web Site
has been created! To log in, proceed to the
following address:

$labdbUrl

Your personal login ID and password are as
follows:

userid: $_POST[newid]
password: $newpass

You aren't stuck with this password! Your can
change it at any time after you have logged in.

If you have any problems, feel free to contact me at
<$adminEmail>.

Thomas
";

$sendmailparams = "-r $adminEmail";

$headers = array(
    'From' => "From: $adminEmail",
    'Reply-To' => "Reply-To: $adminEmail",
    'X-Mailer' => 'X-Mail: PHP/' . phpversion()
);

mail($_POST['newemail'],"Your Password for labdb",
	$message, implode("\n", $headers), $sendmailparams);

mail("$adminEmail","New labdb user created",
	"user $_POST[newid] has signed up to labdb.", 
	implode("\n", $headers), $sendmailparams);

#print implode("\n", $headers);
?>
   <p><strong>User registration successful!</strong></p>
   <p>Your userid and password have been emailed to
      <strong><?=$_POST['newemail']?></strong>, the email address
      you just provided in your registration form. To log in,
      click <a href="index.php">here</a> to return to the login
      page, and enter your new personal userid and password.</p>
<?php
endif;

include("footer.php"); ?>
