<?php
include_once("accesscontrol.php");
#$noUserFilter = True;
$fields = array();
if ((isset($edit) and $edit) or (isset($duplicate) and $duplicate)){
  if(isset($form)){
    $query="SELECT `$table`.*, tracker.*, sampletypes.`table`, sampletypes.`form`, sampletypes.st_name AS stName FROM `$table` LEFT JOIN tracker ON $table.id=tracker.sampleID LEFT JOIN sampletypes ON sampletypes.`table`='$table' WHERE $table.id='$id' AND tracker.sampleType=sampletypes.id AND tracker.owner=$userid ";
    #print $query;
    $rows = pdo_query($query);
    $row = $rows[0];
  } else {
    $row = getRecord($id, $userid);
  }
  #print_r($row);
  if (isset($duplicate) and $duplicate){
    $id = $row['trackID'];
    unset($row['trackID']);
  }
  foreach ($row as $key => $field){
    $fields[$key] = $field;
  }
  if(array_key_exists('trackID',$fields)) $id = $fields['trackID'];
  if ($mode == 'display'){
    $title = "$titleName ${fields['name']}";
  } else {
    $title = "Edit $titleName ${fields['name']}";
  }
}
if (isset($new) and $new){
  $title = "New $titleName Entry";
  $formaction = "insert.php";
  if(isset($_POST['DNASequence'])) $fields['DNASequence'] = fastaseq($_POST['DNASequence'], "\n", 60);
  if(isset($_POST['proteinSequence'])) $fields['proteinSequence'] = fastaseq($_POST['proteinSequence'], "\n", 60);
}
$button = "Save Entry";
include("header.php");
?>
<script src="js/MooTools-More-1.6.0-compat-compressed.js" type="text/javascript"></script>
<link media="all" type="text/css" href="css/MenuMatic.css" rel="stylesheet">
<link rel="stylesheet" href="css/Autocompleter.css" type="text/css" />

<script src="js/Autocompleter.js" type="text/javascript"></script>
<script src="js/Autocompleter.Request.js" type="text/javascript"></script>
<script src="js/Autocompleter.labdb.js" type="text/javascript"></script>
<script src="js/Observer.js" type="text/javascript"></script>
</head>
<body>

<?php 
include("title_bar.php");
include("navigation.php");
?>
<div class="container">
<?php
if (!isset($noUserFilter)) $noUserFilter = Null;
if (!isset($noProjectFilter)) $noProjectFilter = Null;
initProjects($noUserFilter, $noProjectFilter);
$formParams['fields'] = isset($fields) ?  $fields : Null;
//print_r($fields);
?>
<div id='title'><h2><?php echo "$title";?></h2></div>
<?php
if (isset($submitFunction)){
  echo "<form id=\"mainform\" name=\"mainform\" action=\"saveRecord.php?\" method=\"post\">\n";
}
if ($mode == 'modify'):
?>
  <script type="text/javascript">
  var frm_submitted = false;
window.addEvent('domready', function() {
  $('mainform').addEvent('submit', function(e) {
    //Prevents the default submit event from loading a new page.
    e.stop();
    //Empty the log and show the spinning indicator.
    if (!<?php print "$submitFunction" ?>) return;
    var log = $('log_res').empty().addClass('ajax-loading');
    //Set the options of the form's Request handler. 
    //("this" refers to the $('myForm') element).
    this.set('send', {onComplete: function(response) { 
      response = JSON.parse(response);
      log.removeClass('ajax-loading');
      $('id').set('html', response['id']);
      $('inp_id').set('value', response['id']);
      if ($('hexID')) $('hexID').set('html', response['hexid']);
      //$('title').set('html', "<h2>Edit "+$('name').get('value')+"<\/h2>");			    
      //return
      frm_submitted = true;
      if (goBack) {
        history.back();
  } else {
    var loc = "editEntry.php?id=" + response['id'] + "&mode=modify";
    if (loc == location){
      history.go(0);
  } else {
    location.replace(loc);
  }
  }
  goBack = false;
  }
  });
  //Send the form.
  this.send();
  });
  $('submit').addEvent('click', function(e){
    goBack = true;
    $('mainform').fireEvent('submit', e);
    //history.back();
  });
  $('apply').addEvent('click', function(e){
    goBack = false;
    $('mainform').fireEvent('submit', e);
    //history.back();
  });
  $('stop').addEvent('click', function(e){
    history.back();
  });
  });

  window.addEventListener('beforeunload', function(e) {
    var myPageIsDirty = FormChanges('mainform').length; //you implement this logic...
    if (frm_submitted) return;
    if(myPageIsDirty > 0) {
      //following two lines will cause the browser to ask the user if they
      //want to leave. The text of this dialog is controlled by the browser.
      e.preventDefault(); //per the standard
      e.returnValue = ''; //required for Chrome
  }
  //else: user is allowed to leave without a warning dialog
  });

  // from Craig Buttler https://www.sitepoint.com/detect-html-form-changes/
  function FormChanges(form) {
    if (typeof form == "string") form = document.getElementById(form);
    if (!form || !form.nodeName || form.nodeName.toLowerCase() != "form") return null;
    var changed = [], n, c, def, o, ol, opt;
    for (var e = 0, el = form.elements.length; e < el; e++) {
      n = form.elements[e];
      c = false;
      switch (n.nodeName.toLowerCase()) {
        // select boxes
      case "select":
        def = 0;
        for (o = 0, ol = n.options.length; o < ol; o++) {
          opt = n.options[o];
          c = c || (opt.selected != opt.defaultSelected);
          if (opt.defaultSelected) def = o;
  }
  if (c && !n.multiple) c = (def != n.selectedIndex);
  break;
  // input / textarea
case "textarea":
  case "input":

    switch (n.type.toLowerCase()) {
    case "checkbox":
      case "radio":
        // checkbox / radio
        c = (n.checked != n.defaultChecked);
        break;
      default:
        // standard values
        c = (n.value != n.defaultValue);
        break;
  }
  break;
  }
  if (c) changed.push(n);
  }
  return changed;
  }

  <!--
    function validate_form ( )
    {
      valid = true;
      field = $(document.mainform.<?php print "${table}_0_name";?>);
      if ( field.value == "" ){
        field.style.border  = "1px solid #FF6633";
        valid = false;
    }
  for (var i=0; i<fields.length; i++){
    window.fields[i].style.border  = "";
    if (window.fields[i].value.length < 1){
      window.fields[i].style.border  = "1px solid #FF6633";
      valid = false;
    }
    }
  for (var i=0; i<NoFields.length; i++){
    window.NoFields[i].style.border  = "";
    if (isNaN(window.NoFields[i].value) | (window.NoFields[i].value.length < 1)){
      window.NoFields[i].style.border  = "1px solid #FF6633";
      valid = false;
    }
    }

  for (var i=0; i<window.DateFields.length; i++){
    if (!checkDate(window.DateFields[i])){
      valid = false;
    }
    }
  if (!valid) alert ( "Form contains errors. Please correct or complete where marked." );

  if (typeof(MaxLenFields) != "undefined"){
    var msg = '';
    for (var i=0; i<MaxLenFields.length; i++){
      window.MaxLenFields[i]['name'].style.border  = "";
      if ((window.MaxLenFields[i]['name'].value.length > MaxLenFields[i]['maxLen']) | +
        (window.MaxLenFields[i]['name'].value.length < MaxLenFields[i]['minLen']) ) 
      {
        window.MaxLenFields[i]['name'].style.border  = "1px solid #FF6633";
        ( msg += "- " + MaxLenFields[i]['msg'] + "\n" );
        valid = false;
  }
  }
  if (msg!='') alert ( msg );
  }

  return valid;
    }

  //-->
  </script>

<?php
endif;
if (isset($table)){
  print "<input type=\"hidden\" name=\"maintable\" value=\"$table\"/>\n";
}
?>
