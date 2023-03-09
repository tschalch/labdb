<?php
$submitFunction = "validate_form()";
$titleName = "Project";
$mode = $_GET['mode'];
$table = 'projects';
$formParams = ['table'=>$table, 'mode'=>$mode];
include("formhead.php");
?>
<script type="text/javascript">
window.addEvent('domready', function() {
    window.fields = [
	<?php
	$fieldname = "document.mainform.${table}_0_";
	print "${fieldname}name, ";
    ?>];
    window.NoFields = [
	<?php
	print "";
	?>];
    window.DateFields = [
	<?php 
	    print "";
	?>];
});
</script>
<?php

printID($formParams);
printTextField('Project name', 'name', $formParams);
printTextArea('Description', 'description', $formParams);
printSubmitButton($formParams, $button);
?>
