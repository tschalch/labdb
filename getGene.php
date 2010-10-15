<?php
include("connect.php");
#print $_GET['geneIDs'];
$geneIDs = explode(":",$_GET['geneIDs']);
$type = $_GET['type'];
$sequence = '';
#print_r($geneIDs);
foreach ($geneIDs as $geneID){
	if ($geneID){
		$query = "SELECT * FROM genes WHERE id=$geneID";
		#print $query;
		$result=mysql_query($query);
		#print mysql_error($link);
		$row = mysql_fetch_assoc($result);
		if($type=='DNA')$part = $row['DNASequence'];
		if($type=='protein')$part = $row['proteinSequence'];
		if($part){
			$sequence .= $part;
		} else {
			$sequence = "sequence info missing!";
			break;	
		}
	}
}
mysql_close();
print $sequence;
?>