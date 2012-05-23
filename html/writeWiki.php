<?php
#writes a list of selected records to wiki table format
include_once("accesscontrol.php");
include_once("functions.php");

function wikiPrintEntry($entry){
    global $userid, $groups;
    $wikiString = "| ${entry['trackID']} || ";
    $wikiString .= "{{labdburl|id=${entry['trackID']}|label=${entry['name']}}} || ";
    if(isset($entry['PCRoligo1'])){
	$tmstring="";
	for ($i=1;$i<=2;$i++){
	    $oligoID=$entry["PCRoligo$i"];
	    $o = getRecord($oligoID, $userid, $groups);
	    $tm = $o['tm'] ? $o['tm'] : round(Tm($o['targetmatch'],'bre',$o['Saltconc']*1E-3, $o['PCRconc']*1E-9), 1);
	    $wikiString .= "{{labdburl|id=$oligoID|label=${o['name']}}} ($oligoID) || ";
	    $tmstring .= "$tm";
	    $tmstring .= ($i == 1) ? "/" : "";
	}
	$wikiString .= "$tmstring || ";
	$t = getRecord($entry['PCRtemplate'], $userid, $groups);
	$wikiString .= "{{labdburl|id=${entry['PCRtemplate']}|label=${t['name']}}} (${entry['PCRtemplate']}) || ";
	$wikiString .= CountATCG($entry['DNASequence'])." bp ";
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
    $wikiOutput .= "|+Constructs\n";
    $title = false;
    foreach ($selection as $entry){
	$entry = getRecord($entry, $userid, $groups);
	if (!$title){
	    $title = "! ID !! Construct !! Description\n";
	    if(isset($entry['PCRoligo1'])){
		$title = "! ID !! Construct !! Oligo 1 !! Oligo 2 !! Tm !! Template !! Product length\n";
	    }
	    $wikiOutput .= $title;
	}
	$wikiOutput .= "|-\n" . wikiPrintEntry($entry) . "\n";
    }
    $wikiOutput .= "|}\n";
    print "$wikiOutput\n";
}

?>
