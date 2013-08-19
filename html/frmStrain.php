<?php
$table = "strains";
$titleName = "Strain";
$submitFunction = "validate_form()";
$mode = $_GET['mode'];
if (!$mode) $mode = 'display';
$formParams = array('table'=>$table,'mode'=>$mode);

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

# get choices for fragment comboboxes
$types = array('Plasmid'=>'plasmid');
$choices = array();
#print_r($rows);
#print mysql_error($link);


printID($formParams);
printTextField('Strain Name', 'name', $formParams);
printProjectFields($formParams);
printTextField('Organism', 'organism', $formParams);
printTextField('Strain', 'strain', $formParams);
printTextArea('Description', 'description', $formParams);
printCrossCombobxs($id, $types, 0, $formParams);
printSubmitButton($formParams,$button);
?>

<script type="text/javascript" charset="utf-8">

window.addEvent('domready', function() {
	fcounter = 0;
	mode = '<?php print $mode; ?>';
	table = 'plasmids';
	$('plasmid').addEvent('click', addcmbx);
})

</script>
