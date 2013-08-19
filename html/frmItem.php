<?php
$titleName = "Item";
$mode = $_GET['mode'];
$table = 'inventory';
$formParams = array('table'=>$table, 'mode'=>$mode);
$submitFunction = "validate_form()";
$noUserFilter = true;
include("formhead.php");
if(isset($duplicate) and $duplicate){
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
    if (!checkDate("<?php print "${table}_0_orderDate";?>") | !checkDate("<?php print "${table}_0_received";?>") | !checkDate("<?php print "${table}_0_billed";?>")){
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
printLinkField('Link','www', $formParams);
$lcol = array('tracker.trackID','locations.name');
$locations = getRecords('locations', $userid, $lcol, '', "name");
printComboBox("Location",'location', $formParams, $locations, (isset($fields['location']) ? $fields['location']: null));
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
     new Autocompleter.Request.HTML('funding', 'autocomplete.php', {
		'postData': {
		    'field': 'funding', // send additional POST data, check the PHP code
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
printTextField('P.O. number', 'poNumber', $formParams);
printDateField('Order date', 'orderDate', $formParams);
printDateField('Date received', 'received', $formParams);
$statusChoices = pdo_query("SELECT statusNr AS trackID, statusName AS name FROM itemstatus;");
if (isset($fields['status']) and $fields['status'] > 2){
    unset($statusChoices[0]);
    unset($statusChoices[1]);
}
printComboBox("Status", 'status', $formParams, $statusChoices, (isset($fields['status']) ? $fields['status'] : null));
printDateField("Billed on", 'billed', $formParams);
$typeChoices = array(array("trackID"=>0, "name"=>"None"), array("trackID"=>1, "name"=>"Instrument"), array("trackID"=>2, "name"=>"Column"));
printComboBox("Use in Log as", 'type', $formParams, $typeChoices, (isset($fields['type']) ? $fields['type'] : null));
printSubmitButton($formParams, $button);
?>
