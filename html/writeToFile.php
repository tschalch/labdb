<?php
include("functions.php");
include_once("seq.inc.php");
include_once("accesscontrol.php");
include("header.php");
echo "<h2>Writing to Files</h2>";

$selection = $_POST['selection'];
$table = $_POST['table'];
#print_r($selection);
foreach($selection as $id){
	$data = getRecord($id, $userid, $groups);
	$file = "output/${data['name']}.fasta";   
	if (!$file_handle = fopen($file,"w")) { echo "Cannot open file"; }  
	if (!fwrite($file_handle, ">${data['name']}\n")) { echo "Cannot write to file"; }
	$sequence = fastaseq($file_handle, $data['sequence'], "\n");
	fwrite($sequence);  
	fwrite($file_handle, fastaseq($data['DNASequence'], "\n")); 
	if  ($data['proteinSequence']){
		fwrite($file_handle, ">${data['name']} protein sequence\n");  
		fwrite($file_handle, fastaseq($data['proteinSequence'], "\n"));
	}
	echo "You have successfully written data to <a href=\"$file\">$file</a>.<br/>";   
	fclose($file_handle);  
}
include("footer.php");


?> 