<?php
#writes a list of selected records to wiki table format
include_once("accesscontrol.php");
include_once("functions.php");

function wikiPrintEntry($entry){
	global $userid, $groups;
	$wikiString = "| ${entry['trackID']} || ";
	$wikiString .= "| ${entry['hexID']} || ";
	$wikiString .= "{{labdburl|id=${entry['trackID']}|label=${entry['name']}}} || ";
	if(isset($entry['PCRoligo1']) and $entry['type']=='PCR'){
		$tmstring="";
		for ($i=1;$i<=2;$i++){
			$oligoID=$entry["PCRoligo$i"];
			$o = getRecord($oligoID, $userid, $groups);
			$ohexID = $o['hexID'];
			$trackID = $o['trackID'];
			$tm = $o['tm'] ?: round(Tm($o['targetmatch'],'bre',$o['Saltconc']*1E-3, $o['PCRconc']*1E-9), 1);
			$wikiString .= "{{labdburl|id=$oligoID|label=${o['name']}}} ($trackID/$ohexID) || ";
			$tmstring .= "$tm";
			$tmstring .= ($i == 1) ? "/" : "";
		}
		$wikiString .= "$tmstring || ";
		$t = getRecord($entry['PCRtemplate'], $userid, $groups);
		$wikiString .= "{{labdburl|id=${entry['PCRtemplate']}|label=${t['name']}}} (${entry['PCRtemplate']}) || ";
		$wikiString .= CountATCG($entry['DNASequence'])." bp ";
	} elseif ($entry['sampleType']==2){
		$seq = $entry['sequence'];
		$tm = $entry['tm'] ?: round(Tm($entry['targetmatch'],'bre',$entry['Saltconc']*1E-3, $entry['PCRconc']*1E-9), 1);
		$wikiString .= "$seq || $tm ||";
		$wikiString .= seqlen($seq);
	} elseif (isset($entry['strain'])){
		$wikiString .= "${entry['strain']} || ";
		$wikiString .= "${entry['description']}";
	}else{
		$wikiString .= "${entry['description']}";
	}
	return $wikiString;
}

if ($_GET["output"]=='wiki' and $_GET["selection"]){
	header('Content-type: text/wiki');
	header("Content-Disposition: attachment; filename=\"export.wiki\"");
	$selection = explode(",", $_GET["selection"]);
	$wikiOutput = "{|class=\"wikitable\"\n";
	$title = false;
	foreach ($selection as $entry){
		$entry = getRecord($entry, $userid, $groups);
		if (!$title){
			$caption = "|+Constructs\n";
			$title = "! dbID !! sampleID !! Construct !! Description\n";
			if(isset($entry['PCRoligo1']) and $entry['type']=='PCR'){
				$wikiOutput .= "|+PCR reactions\n";
				$title = "! dbID !! PCRID !! Construct !! Oligo 1 !! Oligo 2 !! Tm !! Template !! Product length\n";
			}
			if($entry['sampleType']==2){
				$caption = "|+Oligos\n";
				$title = "! dbID !! oligoID !! Construct !! Sequence !! Tm !! Length\n";
			}
			if (isset($entry['strain'])){
				$caption = "|+Strains\n";
				$title = "! dbID !! strainID !! Construct !! Genotype !! Description\n";
			}
			$wikiOutput .= $caption.$title;
		}
		$wikiOutput .= "|-\n" . wikiPrintEntry($entry) . "\n";
	}
	$wikiOutput .= "|}\n";
	print "$wikiOutput\n";
}

?>
