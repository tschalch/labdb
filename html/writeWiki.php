<?php
#writes a list of selected records to wiki table format
include_once("accesscontrol.php");
include_once("functions.php");
include("header.php");

print "</head><body>";
include("title_bar.php");
include("navigation.php");

function wikiPrintEntry($entry){
    $wikiString = "| ${entry['trackID']} || ";
    $wikiString .= "[{{labdburl}}/editEntry.php?id=${entry['trackID']}&mode=display ${entry['name']}] || ";
    $wikiString .= "${entry['description']}";
    return $wikiString;
}

if ($_GET["output"] and $_GET["selection"]){
	$output = $_GET["output"];
	$selection = explode(",", $_GET["selection"]);
	$wikiOutput = "{|class=\"wikitable\"<br/>\n";
	$wikiOutput .= "|+Construct<br/\n>";
	$wikiOutput .= "! ID !! Construct !! Description<br/>\n";
	foreach ($selection as $entry){
	    $entry = getRecord($entry, $userid, $groups);
	    $wikiOutput .= "|-<br/>" . wikiPrintEntry($entry) . "<br/>\n";
	}
	$wikiOutput .= "|}<br/><br/>\n";
	print "<div id=\"content\">". $wikiOutput . "</div>\n";
}

include("footer.php");
?>
