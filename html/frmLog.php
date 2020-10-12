<?php
$titleName = "Instrument Log";
$mode = $_GET['mode'];
$table = 'logbook';
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
    if (!checkDate("<?php print "${table}_0_date";?>")){
      valid = false;
    }
    var fields = [
<?php
$fieldname = "document.mainform.${table}_0_";
print "${fieldname}sample, ${fieldname}buffer, ${fieldname}instrumentID, ${fieldname}columnID,
  //${fieldname}bypresbef, ${fieldname}bypresaf, ${fieldname}colpresbef, ${fieldname}colpresaf, ${fieldname}storage, 
  ${fieldname}user";
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
print "";
?>];
for (var i=0; i<NoFields.length; i++){
  NoFields[i].style.border  = "";
  if (isNaN(NoFields[i].value) | (NoFields[i].value.length < 1)){
    NoFields[i].style.border  = "1px solid #FF6633";
    valid = false;
  }
  }
if (!valid) alert ( "Form contains errors. Please correct or complete where marked." );
return valid;
  }

//-->
</script>
<?php
printID($formParams);
printDateField('Date', 'date', $formParams, date('Y-m-d'));
printTextField('Sample', 'sample', $formParams);
printTextField('User', 'user', $formParams);
printTextArea('Buffer', 'buffer', $formParams);
$linst = array('tracker.trackID',' CONCAT(inventory.name, " (id#: ", tracker.trackID, ")") AS name');
$instruments = getRecords('inventory', $userid, array(), $linst, " inventory.type=1 ");
printComboBox("Instrument",'instrumentID', $formParams, $instruments, $fields['instrumentID']);
$columns = getRecords('inventory', $userid, array(), $linst, " inventory.type=2 ");
printComboBox("Column",'columnID', $formParams, $columns, $fields['columnID']);
printTextField('Pressure on bypass before run', 'bypresbef', $formParams, null, "Measure pressure @ 4 ml/min in water");
printTextField('Pressure on bypass after run', 'bypresaf', $formParams,  null, "Measure pressure @ 4 ml/min in water");
printTextField('Pressure on column before run', 'colpresbef', $formParams, null, "Measure pressure @ default flow rate in water");
printTextField('Pressure on column after run', 'colpresaf', $formParams,  null, "Measure pressure @ default flow rate in water");
printTextField('Storage buffer', 'storage', $formParams);
printTextArea('Remarks', 'remarks', $formParams);
printSubmitButton($formParams, $button);
?>