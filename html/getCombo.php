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
    $table = $_GET['table'];
}
$field = 'sID';
$element_name= "${table}_0_$field";
$element_id="cmb$field";
$sTable = $sampleType['table'];
print "<div class=\"formRow\"><div class=\"formLabel\">Select content:</div>";
print  "<div class=\"formField\">";
getAutoselectField( $sTable, $mode, $element_id, $element_name, $trackID, "textfield");
print "</div>";
print "</div>\n";


?>
