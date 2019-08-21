<?php
session_start();
include_once("functions.php");

#print "uid: $uid, session: ${_SESSION['uid']}<br/>";

if (isset($_POST['uid']) & isset($_POST['pwd'])){
   $uid = $_POST['uid'];
   $pwd = $_POST['pwd'];
   $_SESSION['uid'] = $uid;
   $_SESSION['pwd'] = $pwd;
} elseif(isset($_SESSION['uid']) & isset($_SESSION['pwd'])) {
   $uid = $_SESSION['uid'];
   $pwd = $_SESSION['pwd'];
}


if(!isset($uid)) {
unset($_SESSION['currUser']);
include_once("header.php");
print "</head><body>";
include_once("title_bar.php");
 ?>
 <h1> Login</h1>
 <p>You must log in to access this area of the site. If you are
    not a registered user, <a href="signup.php">click here</a>
    to sign up for instant access!</p>
 <form method="post" action="
 	<?php print "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
 	?>">
   <div class="login">User ID: <input style="width: 20em;" type="text" name="uid"/></div>
   <div class="login">Password:<input style="width: 20em;" type="password" name="pwd"/></div>
   <div class="login"><input type="submit" value="Log in" /></div>
 </form>
 <?php
 include("footer.php");
 exit;
}

$sql = "SELECT * FROM user WHERE userid = '$uid' AND password = MD5('$pwd')";
#print $sql;
$accresult = pdo_query($sql);
if (!$accresult) {
 unset($_SESSION['uid']);
 unset($_SESSION['pwd']);
 include("header.php");
 ?>
 <h1> Access Denied </h1>
 <p>Your user ID or password is incorrect, or you are not a
    registered user on this site. To try logging in again, click
    <a href="<?php print $_SERVER['PHP_SELF']?>">here</a>. To register for instant
    access, click <a href="signup.php">here</a>.</p>
 <?php
 include("footer.php");
 exit;
}

$userfullname = $accresult[0]['fullname'];
$username = $accresult[0]['userid'];
#$groups = "(".$accresult[0]['groups'].")";
#$groupArray = split (',',$accresult[0]['groups']);
#$mainGroup = split(',', $accresult[0]['groups']);
#$mainGroup = $mainGroup[0];
$userid = $accresult[0]['ID'];
$useremail = $accresult[0]['email'];
$usernotes = $accresult[0]['notes'];
if (!$_SESSION['currUser']) $_SESSION['currUser'] = -1;
?>
