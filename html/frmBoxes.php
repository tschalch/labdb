<?php
$submitFunction = "validate_form()";
$titleName = "Box";
$mode = $_GET['mode'];
$table = 'boxes';
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
printTextField('Box name', 'name', $formParams);
$lcol = ['tracker.trackID', 'locations.name'];
$locations = getRecords('locations', $userid, [], $lcol, '', "name");
printComboBox("Location",'location', $formParams, $locations, ($fields['location'] ?? null), null, true);
printTextArea('Description', 'description', $formParams);
echo "<div class=\"formRow\"><div class=\"formLabel\">Vials in this box:</div>";
echo "<div class=\"formField\"><a href=\"list.php?list=listVials&box=$id\">Show vials</a></div></div>";
printSubmitButton($formParams, $button);
?>
