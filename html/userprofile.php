<?php 
include("accesscontrol.php");
include("header.php");
include_once("functions.php");	
?>
</head>
<body>
<?php
include("navigation.php");
if (isset($_POST['submitok'])):
  // Process signup submission 

  if ($_POST['name']=='' or $_POST['email']=='') {
    error('Name or email missing.\\n'.
      'Please fill them in and try again.');
  }
if ($_POST['name']!=$userfullname or $_POST['email']!=$useremail or $_POST['color']!=$usercolor or $_POST['notes']!=$usernotes) {
  $q ="UPDATE user SET fullname = '${_POST['name']}', email = '${_POST['email']}',
     color = '${_POST['color']}', notes = '${_POST['notes']}' WHERE userid = '$uid'";
  $r = pdo_query($q);
  print $q;
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
header('Location: '.$_SERVER['HTTP_REFERER']);
endif
// Display the user profile form 
?>
<div class="container">
  <div id="title"><h2>User Profile for <?php print $username ?></h2></div>
  <form method="post" action="<?php print $_SERVER['PHP_SELF']?>">
  <div class="form-group">
  <label for="name" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-4">
      <input type="text" name="name" class="form-control" id="name" 
        value="<?php print $userfullname?>" placeholder="Name">
    </div>
  </div>
  <div class="form-group">
  <label for="email" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-4">
      <input type="text" name="email" class="form-control" id="email" placeholder="Email"
        value="<?php print $useremail?>" />
    </div>
  </div>
   <div class="form-group">
      <label for="color" class="col-sm-2 control-label">Default Booking Color</label>
    <div class="col-sm-4">
      <input type="text" name="color" class="form-control" id="color" list="select-list-id"
        value="<?php print $usercolor?>" />
   </div>
   </div>
   <datalist id="select-list-id">
      <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
      <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
      <option style="color:#008000;" value="#008000">&#9724; Green</option>
      <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
      <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
      <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
      <option style="color:#000;" value="#000">&#9724; Black</option>
   </datalist>
  <div class="form-group">
  <label for="oldpw" class="col-sm-2 control-label">Old Password</label>
    <div class="col-sm-4">
      <input type="password" name="oldpw" class="form-control" id="oldpw" placeholder="Old Password">
    </div>
  </div>
  <div class="form-group">
  <label for="newpw" class="col-sm-2 control-label">New Password</label>
    <div class="col-sm-4">
      <input type="text" name="newpw" class="form-control" id="newpw" placeholder="New Password">
    </div>
  </div>
  <div class="form-group">
  <label for="newpwconf" class="col-sm-2 control-label">Confirmation</label>
    <div class="col-sm-4">
      <input type="text" name="newpwconf" class="form-control" id="newpw" placeholder="Confirmation">
    </div>
  </div>
  <div class="form-group">
  <label for="notes" class="col-sm-2 control-label">Notes</label>
    <div class="col-sm-4">
      <textarea rows="5" name="notes" class="form-control" id="notes" placeholder="">
        <?php print $usernotes;?>
      </textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-4">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  <button type="submit" name="submitok" class="btn btn-primary">Save changes</button>
  </div>
  </div>
</form> 
</div>