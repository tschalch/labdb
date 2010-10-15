<?php
$titleName = "Locations";
$submitFunction = "true";
$mode = $_GET['mode'];
$table = 'locations';
$formParams = array('table'=>$table, 'mode'=>$mode);
include("formhead.php");
printID($formParams);
printTextField('Location', 'name', $formParams);
printTextArea('Description', 'description', $formParams);
printSubmitButton($formParams, $button);
?>