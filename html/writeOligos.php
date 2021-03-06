<?php
#writes a primer list file for amplifx
include_once("accesscontrol.php");
include_once("functions.php");
#include("header.php");

if(isset($_POST['output'])) $output = $_POST['output'];
if(isset($_POST['selection'])) $selection = $_POST['selection'];
if(isset($_POST['template'])) $template =  $_POST['template'];

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
    include("config.php");
	if ($selection){
    $primers = "";
		foreach($selection as $id){
			if ($id!='NA'){
				$oligo = getRecord($id, $userid);
				$primers .= "${oligo['sequence']} ${oligo['name']},\n";
			}
		}
		$_SESSION['primers'] = $primers;
	}
    	$_SESSION['template'] = $template;
	header( "Location: $labdbUrl/sequence_extractor/index.php" );
} else {
    header('Content-type: text/fasta');
    if (isset($selection[0])){
	$id = $selection[0];
	$order = $_POST["orderDate_$id"];
	header("Content-Disposition: attachment; filename=\"${order}_oligoOrder.dat\"");
    }
    $num = count($selection);
    $i = 0;
    $head="Oligo name,Sequence,Sequence Length,Purification,Scale,5' Modification,Inner Modification (5),Inner Modification (6),Inner Modification (7),Inner Modification (8),3' Modification,,DES,HPL,PAG,HPD,GEN,0.04,0.2,1.0,,,,,,,,,,,,,,,,,,,,,,,,,,,,,";
    if ($output == 'microsynth') print "$head\n";
    foreach($selection as $id){
	$data = $_POST["sequence_$id"];
	$targetmatch = $_POST["targetmatch_$id"];
	$name = $_POST["name_$id"];
	$hexID = $_POST["hexID_$id"];
	#$name = "${hexID}_${id}_$name";
	$name = substr(${hexID}, 2)."_$name";
	$scale = $_POST["scale_$id"];
	$purification = $_POST["purity_$id"];
	$length = strlen($data);
	$mods = $_POST["mods_$id"];
	if ($output == 'amplifx') $line = "$data\t$name\n";
	if ($output == 'sigma') $line = "$name,$data,$scale,$purification\n";
	if ($output == 'microsynth') $line = "$name,$data,$length,$purification,$scale,$mods\n";
	if ($output == 'extractor') $line = "$data $name,\n";
	if ($output == 'finnzymes') $line = "$name $targetmatch,\n";
	$i++;
	print $line;
    }
}

#include("footer.php");


?> 
