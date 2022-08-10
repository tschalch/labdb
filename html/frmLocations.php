<?php
$titleName = "Location";
$submitFunction = "validate_form()";
$mode = $_GET['mode'];
$table = 'locations';
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
printTextField('Location name', 'name', $formParams);
$lcol = array('tracker.trackID','locations.name');
$locations = getRecords('locations', $userid, array(), $lcol, '', "name");
printComboBox("Contained in location",'location', $formParams, $locations, (isset($fields['location']) ? $fields['location']: null), null, true);
printTextArea('Description', 'description', $formParams);
echo "<div class=\"formRow\"><div class=\"formLabel\">Sub-locations:</div>";
echo "<div class=\"formField\"><a href=\"list.php?list=listLocations&location=$id\">Show sub-locations</a></div></div>";
echo "<div class=\"formRow\"><div class=\"formLabel\">Boxes:</div>";
echo "<div class=\"formField\"><a href=\"list.php?list=listBoxes&location=$id\">Show boxes in location</a></div></div>";
printCheckbox('obsolete', 'obsolete', $formParams);
printSubmitButton($formParams, $button);
?>
