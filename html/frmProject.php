<?php
$submitFunction = "true";
$titleName = "Project";
$mode = $_GET['mode'];
$table = 'projects';
$formParams = array('table'=>$table, 'mode'=>$mode);
include("formhead.php");
printID($formParams);
printTextField('Project name', 'name', $formParams);
printTextArea('Description', 'description', $formParams);
printSubmitButton($formParams, $button);
?>