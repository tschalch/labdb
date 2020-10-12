<?php
$titleName = "Resources";
$submitFunction = "validate_form()";
$mode = $_GET['mode'];
$table = 'resources';
$formParams = array('table'=>$table, 'mode'=>$mode);
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
printTextField('Resource', 'name', $formParams);
printTextField('Location', 'location', $formParams);
printTextArea('Description', 'description', $formParams);
if (!isset($id)) $formParams['fields']['active']['checked'] = 1;
printCheckbox('Is active', 'active', $formParams);
printSubmitButton($formParams, $button);
?>
