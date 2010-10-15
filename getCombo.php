<?php
include_once("accesscontrol.php");
include_once("functions.php");

if (isset($fields['sID'])){
    $sampleType = getSampleType($fields['sID']);
    //print_r( $sampleType);
} else {
    $mode = $_GET['mode'];
    $sampleType = $_GET['sampletype'];
    $q = "SELECT * FROM sampletypes WHERE id=$sampleType;";
    $r = pdo_query($q);
    $sampleType = $r[0];
}
$name = 'sID';
$sTable = $sampleType['table'];
$cols = array('tracker.trackID', "CONCAT($sTable.name,' (id: ',tracker.trackID,')') AS name");
$choices = getRecords($sTable, $userid, $cols);
print "<div class=\"formRow\"><div class=\"formLabel\">Select content:</div>";
print getComboBox($name, 'vials', $mode, $choices, $fields['sID'], '', True);
print "</div></div>\n";


?>