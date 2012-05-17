<?php
#writes a list of selected records to wiki table format
include_once("accesscontrol.php");
include_once("functions.php");
include("header.php");

print "</head><body>";
include("title_bar.php");
include("navigation.php");


function wikiPrintEntry($entry){
    global $userid, $groups;
    $wikiString = "| ${entry['trackID']} || ";
    $wikiString .= "[{{labdburl}}/editEntry.php?id=${entry['trackID']}&mode=display ${entry['name']}] || ";
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
	$wikiString .= "[{{labdburl|id=${entry['description']}}} (=${entry['description']})";
    }
    return $wikiString;
}

if ($_GET["output"] and $_GET["selection"]){
	$output = $_GET["output"];
	$selection = explode(",", $_GET["selection"]);
	$wikiOutput = "{|class=\"wikitable\"<br/>\n";
	$wikiOutput .= "|+Construct<br/\n>";
	if(isset($selection[0]['PCRoligo1'])){
	    $wikiOutput .= "! ID !! Construct !! Oligo 1 !! Oligo 2 !! Tm !! Template !! Product length<br/>\n";
	} else {
	    $wikiOutput .= "! ID !! Construct !! Description<br/>\n";
	}
	foreach ($selection as $entry){
	    $entry = getRecord($entry, $userid, $groups);
	    $wikiOutput .= "|-<br/>" . wikiPrintEntry($entry) . "<br/>\n";
	}
	$wikiOutput .= "|}<br/><br/>\n";
	print "<div id=\"content\">". $wikiOutput . "</div>\n";
}

include("footer.php");
?>
