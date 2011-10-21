<?php
$table = "oligos";
$mode = $_GET['mode'];
if (!$mode) $mode = 'display';
$formParams = array('table'=>'oligos','mode'=>$mode);
$titleName = "Oligo";
$submitFunction = "true";

include("formhead.php");
printID($formParams);
printTextField('Oligo Name', 'name', $formParams);
printProjectFields($formParams);
printTextArea('Description', 'description',$formParams);
printSequenceField('Sequence', 'oligo', 'sequence', $formParams, false, false);
printSequenceField('Target Matching Sequence', 'oligo', 'targetmatch', $formParams, false, false);
printTextField('Tm[&#176C]', 'tm',$formParams, '');
printTextField('Concentration in PCR [nm]', 'PCRconc',$formParams, '500');
printTextField('Salt in PCR [mM]', 'Saltconc',$formParams, '50');
if ($mode == 'modify'){
print "<div class=\"formRow\"><span class=\"formLabel\">&nbsp;</span>
	<input type=\"button\" value=\"Check oligo parameters\" 
	onclick=\"poptastic('oligoCalc.php?sequence='+ $('mainform').${table}_0_targetmatch.value);\">
	<span class=\"formField\"></span></div>";
} else {
}

printTextField('Supplier', 'supplier', $formParams, "Sigma Genosys");
printTextField('Scale', 'scale', $formParams, "0.025");
printTextField('Modifications','modifications', $formParams, "none");
printTextField('Purity', 'purity', $formParams, "DESALT");
printTextField('Price/BasePair', 'bpPrice', $formParams, "0.15");
printTextField('Concentration', 'concentration', $formParams);
printTextField('Order Date', 'orderDate', $formParams);
printReferenceLink('Relations', 'PCR reactions with this oligo', $id, 'fragment', $formParams, 'PCR');
printSubmitButton($formParams,$button);
?>

</form>

