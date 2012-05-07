<?php
$titleName = "Item";
$mode = $_GET['mode'];
$table = 'inventory';
$formParams = array('table'=>$table, 'mode'=>$mode);
$submitFunction = "validate_form()";
$noUserFilter = true;
include("formhead.php");
if($duplicate){
        $fields['status'] = 0;
	$formParams['fields']['orderDate'] = '0000-00-00';
	$formParams['fields']['received'] = '0000-00-00';
	$formParams['fields']['funding'] = '';
}
?>
<script type="text/javascript">
<!--
function validate_form ( )
{
    valid = true;
    field = $(document.mainform.<?php print "${table}_0_name";?>);
    if ( field.value == "" ){
	field.style.border  = "1px solid #FF6633";
        valid = false;
    }
    var fields = [
        <?php
        $fieldname = "document.mainform.${table}_0_";
        print "${fieldname}price, ${fieldname}name, ${fieldname}quantity,
                ${fieldname}unitMeas, ${fieldname}status, ${fieldname}orderNumber,
                 ${fieldname}supplier";
        ?>];
    for (var i=0; i<fields.length; i++){
        fields[i].style.border  = "";
        if (fields[i].value.length < 1){
            fields[i].style.border  = "1px solid #FF6633";
            valid = false;
        }
    }
    var NoFields = [
        <?php
        print "${fieldname}price, ${fieldname}status";
        ?>];
    for (var i=0; i<NoFields.length; i++){
        NoFields[i].style.border  = "";
        if (isNaN(NoFields[i].value) | (NoFields[i].value.length < 1)){
            NoFields[i].style.border  = "1px solid #FF6633";
            valid = false;
        }
    }
    if (!checkDate("<?php print "${table}_0_orderDate";?>") | !checkDate("<?php print "${table}_0_received";?>")){
	valid = false;
    }
    if (!valid) alert ( "Form contains errors. Please correct or complete where marked." );
    return valid;
}

//-->
</script>
<?php
printID($formParams);
printTextField('Item name', 'name', $formParams);
printTextArea('Description', 'description', $formParams);
$lcol = array('tracker.trackID','locations.name');
$locations = getRecords('locations', $userid, $lcol);
printComboBox("Location",'location', $formParams, $locations, $fields['location']);
printTextField('Manufacturer', 'manufacturer', $formParams);
printTextField('Supplier', 'supplier', $formParams);
if ($mode == 'modify'):
?>
<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function() {
     new Autocompleter.Request.HTML('manufacturer', 'autocomplete.php', {
		'postData': {
		    'field': 'manufacturer', // send additional POST data, check the PHP code
		    'table': 'inventory',
		}
    });
     new Autocompleter.Request.HTML('supplier', 'autocomplete.php', {
		'postData': {
		    'field': 'supplier', // send additional POST data, check the PHP code
		    'table': 'inventory',
		}
    });
});
//]]>
</script>
<?php
endif;
printTextField('Catalogue number', 'orderNumber', $formParams);
printTextField('CAS number', 'casNumber', $formParams);
printTextField('How many?', 'quantity', $formParams);
printTextField('Unit Measure', 'unitMeas', $formParams);
printTextField('Price per Unit in $', 'price', $formParams);
printTextField('Funding source', 'funding', $formParams);
printDateField('Order date', 'orderDate', $formParams);
printDateField('Date received', 'received', $formParams);
$statusChoices = pdo_query("SELECT statusNr AS trackID, statusName AS name FROM itemstatus;");
if ($fields['status'] > 2){
    unset($statusChoices[0]);
    unset($statusChoices[1]);
}
printComboBox("Status", 'status', $formParams, $statusChoices, $fields['status']);
$typeChoices = array(array("trackID"=>0, "name"=>"None"), array("trackID"=>1, "name"=>"Instrument"), array("trackID"=>2, "name"=>"Column"));
printComboBox("Use in Log as", 'type', $formParams, $typeChoices, $fields['type']);
printSubmitButton($formParams, $button);
?>
