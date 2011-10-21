<?php
$submitFunction = "true";
$titleName = "Box";
$mode = $_GET['mode'];
$table = 'boxes';
$formParams = array('table'=>$table, 'mode'=>$mode);
include("formhead.php");
printID($formParams);
printTextField('Box name', 'name', $formParams);
printTextField('Location', 'location', $formParams);
printTextArea('Description', 'description', $formParams);
printSubmitButton($formParams, $button);
if ($mode == 'display'){
	echo "<div class=\"formRow\">";
	echo "<a href=\"list.php?list=listVials&box=$id\">Show vials</a></div>";
}
?>