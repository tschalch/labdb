<?php
$titleName = "Item";
$mode = $_GET['mode'];
$table = 'inventory';
$formParams = array('table'=>$table, 'mode'=>$mode);
$submitFunction = "validate_form()";
$noUserFilter = true;

include("formhead.php");

?>

<script type="text/javascript">
window.addEvent('domready', function() {
  window.fields = [

<?php

$fieldname = "document.mainform.${table}_0_";
print "${fieldname}price, ${fieldname}name, ${fieldname}quantity,
  ${fieldname}unitMeas, ${fieldname}status, ${fieldname}orderNumber,
  ${fieldname}supplier";
?>
];
window.NoFields = [
<?php
print "${fieldname}price, ${fieldname}status";
?>];
window.DateFields = [
<?php 
print "\"${table}_0_orderDate\", \"${table}_0_received\", \"${table}_0_billed\"";
?>];
});
</script>

<?php

if(isset($duplicate) and $duplicate){
  $fields['status'] = 1;
  $formParams['fields']['orderDate'] = NULL ;
  $formParams['fields']['received'] = NULL ;
  $formParams['fields']['billed'] = NULL ;
  $formParams['fields']['funding'] = '';
  $formParams['fields']['poNumber'] = '';
}
printID($formParams);
printTypeID($formParams, "Item ID");
printTextField('Item name', 'name', $formParams);
printTextArea('Description', 'description', $formParams);
printHazards('Hazard signals', 'hazards', $formParams);
printUploadField('Files (COSSH, MSDS, Quotes)', 'files', $formParams);
printLinkField('Link','www', $formParams);
$lcol = array('tracker.trackID','locations.name');
$locations = getRecords('locations', $userid, array(), $lcol, '', "name");
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
include('config.php');
printTextField('Catalogue number', 'orderNumber', $formParams);
printTextField('CAS number', 'casNumber', $formParams);
printTextField('How many?', 'quantity', $formParams);
printTextField('Unit Measure', 'unitMeas', $formParams);
printTextField("Price per Unit in $currency", 'price', $formParams);
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
echo "</form>";
?>
