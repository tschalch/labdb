<?php 
session_start();
include_once("../functions.php");
include_once("../accesscontrol.php");
include_once("../config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html lang="en">
<head>
<title>Sequence Extractor</title>
<meta name="keywords" content="bioinformatics, genomics, software, restriction map, PCR" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" href="includes/stylesheet.css"  type="text/css" />
</head>

<body class="main">

<table width="620" border="0" cellspacing="2" cellpadding="2" align="left">
<tbody>

<tr>
<td class="sms">Sequence Extractor</td>
</tr>

<tr>
<td class="link_bar"><a href="index.html">Main</a> | <a href="features.html">Features</a> | <a href="help.html">Help</a> | <a href="download.html">Download</a> | <a href="license.html">License</a> | <a href="about.html">About</a></td>
</tr>

<tr>
<td class="description">Sequence Extractor generates a clickable restriction map and PCR primer map of a DNA sequence. Protein translations and intron/exon boundaries are also shown. Use Sequence Extractor to build DNA constructs <i>in silico</i>. Please read the list of <a href="features.html">program features</a> to learn more.
<br />
<br />
<form method="post" action="<?php print "http://${_SERVER['SERVER_NAME']}:${_SERVER['SERVER_PORT']}/cgi-bin/seq_ext/se.pl" ?>">
Paste a sequence into the text area below. Accepted formats are: <a href="raw_sample.html">raw</a>, <a href="genbank_sample.html">GenBank</a>, <a href="embl_sample.html">EMBL</a>, and <a href="fasta_sample.html">FASTA</a>.
<br /><textarea name="sequenceRecord" rows="6" cols="81">
<?php
$plasmid = getRecord($_SESSION['template'], $userid, $groups);
if ($plasmid) print $plasmid['sequence'];
?>
</textarea><br /><br />
If there are primers you would like shown on the map, enter each primer as follows: the sequence of the primer, a blank space, and the name of the primer. Use commas to separate multiple primer entries.
<br /><textarea name="primerList" rows="6" cols="81">
<?php print $_SESSION['primers'];?>
</textarea><br />
<button type="submit">Submit</button>
<button type="button" onclick="document.forms[0].elements[0].value = ' '; document.forms[0].elements[1].value = ' '">Clear</button>
<button type="reset">Reset</button><br />
<br />

<div class="link_bar">Advanced Options</div>

<div class="description">Use the following options to alter the output of Sequence Extractor. For more details about individual options, see the <a href="help.html">help</a>.</div>

<div class="options">
<ul>
<li>
<a href="help.html#genetic_code">Genetic code</a>: <select name="geneticCodeSelection">
<option selected="selected" value="standard">standard</option>
</select>.</li>
<li>
<a href="help.html#restriction_set">Restriction set</a>: <select name="restrictionSetSelection">
<option selected="selected" value="common">common</option>
<option value="none">none</option>
</select>.</li>
<li>
<a href="help.html#translate">Translate reading frame</a>: <select name="readingFramesToShow">
<option selected="selected" value="one">one</option>
<option value="two">two</option>
<option value="three">three</option>
<option value="all_three">one to three</option>
<option value="all_six">all six</option>
<option value="uppercase1">uppercase one</option>
<option value="uppercase2">uppercase two</option>
<option value="uppercase3">uppercase three</option>
<option value="none">none</option>
</select>.</li>
<li>
<a href="help.html#topology">Topology</a>: <select name="isCircular">
<option selected="selected" value="0">linear</option>
<option value="1">circular</option>
</select>.</li>
<li>
<a href="help.html#allow_primers">Allow primers to have mismatched</a>: <input type="checkbox" name="checkForMismatchFivePrimeTails" value="1" checked="checked" /><a href="help.html#five_prime">5' tails</a>, <input type="checkbox" name="checkForMismatchThreePrimeTails" value="1" /><a href="help.html#three_prime">3' tails</a>.<br />
<a href="help.html#bases_required">Matching bases required when mismatching bases allowed</a>: <select name="minimumMatch">
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option selected="selected" value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
</select>.</li>
<li>
<a href="help.html#bases_per_line">Bases per line</a>: <select name="basePerLine">
<option value="60">60</option>
<option selected="selected" value="80">80</option>
<option value="100">100</option>
</select>.</li>
<li>
<a href="help.html#show">Show</a><input type="checkbox" name="showReverseStrand" value="1" checked="checked" /><a href="help.html#reverse_strand">reverse strand</a>, <input type="checkbox" name="showNumberLine" value="1" checked="checked" /><a href="help.html#number_line">number line</a>, <input type="checkbox" name="showSpacerLine" value="1" checked="checked" /><a href="help.html#spacer_line">spacer line</a>.</li>
<li>
<a href="help.html#return">Return</a><input type="checkbox" name="returnRestSummary" value="1" checked="checked" /><a href="help.html#rest_sum">restriction summary</a>, <input type="checkbox" name="returnPrimerSummary" value="1" checked="checked" /><a href="help.html#primer_sum">primer summary</a>, <input type="checkbox" name="returnHelpInfo" value="1" /><a href="help.html#help_info">help information</a>, <input type="checkbox" name="returnCdsLinks" value="1" /><a href="help.html#coding_links">coding sequence links</a>, <input type="checkbox" name="returnTransLinks" value="1" /><a href="help.html#trans_links">translation links</a>, <input type="checkbox" name="returnOptionsChosen" value="1" /><a href="help.html#options_selected">options selected</a>.</li>
</ul>
</div>
</form>
<div class="copyright">Sequence Extractor copyright &copy; 2006 Paul Stothard</div>
</td>
</tr>

<tr>
<td>
<a href="http://validator.w3.org/check/referer"><img style="border:0;width:88px;height:31px" src="includes/valid-xhtml10.png" alt="Valid XHTML 1.0!" height="31" width="88" /></a>
<a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="includes/vcss.png" alt="Valid CSS!" /></a>
</td>
</tr>

</tbody>
</table>

</body>
</html>
