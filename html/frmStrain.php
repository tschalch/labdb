<?php
$table = "strains";
$titleName = "Strain";
$submitFunction = "validate_form()";
$mode = $_GET['mode'];
if (!$mode) $mode = 'display';
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

# get choices for fragment comboboxes
$types = ['plasmid'=>'plasmids', 'fragment' =>'fragments', 'parent strain'=>'strains'];
$choices = [];
#print_r($rows);
#print mysql_error($link);


printID($formParams);
printTypeID($formParams, "Strain ID");
printTextField('Strain Name', 'name', $formParams);
printProjectFields($formParams);
printTextField('Organism', 'organism', $formParams);
printTextField('Genotype', 'strain', $formParams);
printTextArea('Description', 'description', $formParams);

$unlink = False;
if(!array_key_exists('trackID',$fields)) $unlink = True;
$fcounter = printCrossCombobxs($id, $types, 0, $formParams, $unlink);
printReferenceLink('Freezer', 'Freezer locations', $id, 'vial', $formParams);
printSubmitButton($formParams,$button);

$ac_counter = 0;
foreach($types as $name=>$table){
?>

<script type="text/javascript" charset="utf-8">

	window.addEvent('domready', function() {
		fcounter = <?php print $fcounter; ?>;
		mode = '<?php print $mode; ?>';
		<?php print "table$ac_counter = '$table'"; $ac_counter += 1; ?>;
		$('<?php print $name; ?>').addEvent('click', addcmbx);
	})

</script>


<?php } ?>
