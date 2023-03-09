<?php
#writes a primer list file for amplifx
include("header.php");
echo "<h2>Writing to file for amplifx</h2>";

$selection = $_POST['selection'];
#print_r($selection);
$file = "output/primerlist.txt";   
if (!$file_handle = fopen($file,"w")) { echo "Cannot open file"; }  
$num = is_countable($selection) ? count($selection) : 0;
$i = 0;
foreach($selection as $id){
	$data = $_POST["sequence_$id"];
	$name = $_POST["name_$id"];
	$line = "$data\t$name";
	$i++;
	if ($num > $i) $line .= "\n";
	if (!fwrite($file_handle, $line)) { echo "Cannot write to file"; } 
}
	echo "AmplifX primer list has been successfully written to $file.<br/>";   
fclose($file_handle);  
include("footer.php");


?> 