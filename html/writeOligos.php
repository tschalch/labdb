<?php
#writes a primer list file for amplifx
include_once("accesscontrol.php");
include_once("functions.php");
#include("header.php");

$output = $_POST['output'];
$selection = $_POST['selection'];
$template =  $_POST['template'];

# code for ajax requests
if ($_GET["output"]){
	$output = $_GET["output"];
	$selection = array();
	for ($i=1;$i<=2;$i++){
		$selection[] = $_GET["oligo$i"];
	}
	$template = $_GET['template'];
}
#print_r($selection);
if ($output == 'vectornti'){
	echo "<b>Written to file</b><br/><br/>";
	foreach($selection as $id){
		$data = $_POST["sequence_$id"];
		$name = $_POST["name_$id"];
		$file = "output/$name.ols";   
		if (!$file_handle = fopen($file,"w")) { echo "Cannot open file"; }  
		if (!fwrite($file_handle, $data)) { echo "Cannot write to file"; }  
		echo "You have successfully written data to <a href=\"$file\">$file</a>.<br/><br/>";   
		fclose($file_handle);  
	}
} elseif ($output == 'extractor'){
	echo "<b>Preparing Sequence Extractor</b><br/><br/>";
	if ($selection){
		foreach($selection as $id){
			if ($id!='NA'){
				$oligo = getRecord($id, $userid, $groups);
				$primers .= "${oligo['sequence']} ${oligo['name']},\n";
			}
		}
		$_SESSION['primers'] = $primers;
	}
    	$_SESSION['template'] = $template;
    	print "Perform PCR in <a target=\"blank\" href=\"sequence_extractor/index.php\"> Sequence Extractor</a><br/><br/>";   
} else {
	echo "<b>Written oligos to file</b><br/><br/>";
	$file = "output/primerlist.txt";   
	if (!$file_handle = fopen($file,"w")) { echo "Cannot open file"; }  
	$num = count($selection);
	$i = 0;
	foreach($selection as $id){
		$data = $_POST["sequence_$id"];
		$targetmatch = $_POST["targetmatch_$id"];
		$name = $_POST["name_$id"];
		$scale = $_POST["scale_$id"];
		$purification = $_POST["purity_$id"];
		if ($output == 'amplifx') $line = "$data\t$name";
		if ($output == 'sigma') $line = "$name,$data,$scale,$purification";
		if ($output == 'extractor') $line = "$data $name,";
		if ($output == 'finnzymes') $line = "$name $targetmatch,";
		$i++;
		if ($num > $i) $line .= "\n";
		if (!fwrite($file_handle, $line)) { echo "Cannot write to file"; } 
	}
		echo "Primer list has been successfully written to <a href=\"$file\">$file</a>.<br/><br/>";   
	fclose($file_handle);  
}

#include("footer.php");


?> 