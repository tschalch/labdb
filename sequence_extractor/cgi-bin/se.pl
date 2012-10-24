#!/usr/bin/perl
#Version 1.1 November 12, 2003.
#Written by Paul Stothard.
#This program can be run in taint mode.
require 5.003;
use strict;
use warnings;
use lib qw(.);

use CGI qw(:standard);
use CGI::Carp qw(fatalsToBrowser set_message);
BEGIN {
   sub handle_errors {
      my $msg = shift;
      print "Content-type: text/html\r\n\r\n" .
	    "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n" .
	    "<html lang=\"en\">\n" .
	    "<head>\n" .
	    "<title>Sequence Extractor</title>\n" .
	    "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n" .
	    "</head>\n" .
	    "</head>\n<body class=\"main\">\n" .
            "<br />\n" .
            "$msg <br />\n" .
            "<b>Email: stothard\@ualberta.ca</b><br />\n" .
	    "<b>Sequence Extractor version 1.0</b><br />\n" .
            "</body>\n</html>\n";
   }
   set_message(\&handle_errors);
}
$CGI::POST_MAX=1024 * 1000;  # max 1000K posts
$CGI::DISABLE_UPLOADS = 1;  # no uploads

my $test = param("SequenceRecord");
if (!$test && cgi_error()) {
   print "Content-type: text/html\r\n\r\n" .
   "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n" .
   "<html lang=\"en\">\n" .
   "<head>\n" .
   "<title>Sequence Extractor</title>\n" .
   "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n" .
   "</head>\n" .
   "<body>\n" .
   "There is a maximum POST limit of 1000K for this page\n" .
   "</body>\n" .
   "</html>\n";

   exit 0;
}

print "Content-type: text/html\r\n\r\n";
print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n" .
      "<html lang=\"en\">\n" .
      "<head>\n" .
      "<title>Sequence Extractor</title>\n" .
      "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n" .
      "<style type=\"text/css\">\n" .
      "body.main {font-family: arial, sans-serif; color: #000000; background-color: #FFFFFF;}\n" .
      "body.main a {color: #000099; text-decoration: none}\n" .
      "body.main a:visited {color: #000099; text-decoration: none}\n" .
      "body.main a:hover {color: #FF0000; text-decoration: underline}\n" .
      "body.main a:active {color: #FF0000; text-decoration: underline}\n" .
      "td.sms {font-size: xx-large; color: #FFFFFF; text-align: center; background-color: #6666FF}\n" .

      "div.pre {font-size: medium; color: #000000; font-family: courier, monospace; white-space: pre}\n" .
      "div.pre a {color: #000000; text-decoration: none}\n" .
      "div.pre a:visited {color: #000000; text-decoration: none}\n" .
      "div.pre a:hover {color: #000000; text-decoration: none}\n" .
      "div.pre a:active {color: #000000; text-decoration: none}\n" .

      "div.copyright {font-size: x-small; color: #000000}\n" .

      "span.special_link_rest_start a {color: #990066; text-decoration: none}\n" .
      "span.special_link_rest_start a:visited {color: #990066; text-decoration: none}\n" .
      "span.special_link_rest_start a:hover {color: #CC3399; text-decoration: none}\n" .
      "span.special_link_rest_start a:active {color: #CC3399; text-decoration: none}\n" .

      "span.special_link_rest_end a {color: #990066; text-decoration: none}\n" .
      "span.special_link_rest_end a:visited {color: #990066; text-decoration: none}\n" .
      "span.special_link_rest_end a:hover {color: #CC3399; text-decoration: none}\n" .
      "span.special_link_rest_end a:active {color: #CC3399; text-decoration: none}\n" .

      "span.special_link_PCR_start a {color: #339900; text-decoration: none}\n" .
      "span.special_link_PCR_start a:visited {color: #339900; text-decoration: none}\n" .
      "span.special_link_PCR_start a:hover {color: #99CC33; text-decoration: none}\n" .
      "span.special_link_PCR_start a:active {color: #99CC33; text-decoration: none}\n" .

      "span.special_link_PCR_end a {color: #CC0000; text-decoration: none}\n" .
      "span.special_link_PCR_end a:visited {color: #CC0000; text-decoration: none}\n" .
      "span.special_link_PCR_end a:hover {color: #FF3333; text-decoration: none}\n" .
      "span.special_link_PCR_end a:active {color: #FF3333; text-decoration: none}\n" .

      "span.forward_primer a {color: #339900}\n" .
      "span.forward_primer a:visited {color: #339900}\n" .
      "span.forward_primer a:hover {color: #99CC33}\n" .
      "span.forward_primer a:active {color: #99CC33}\n" .

      "span.restriction_site a {color: #990066}\n" .
      "span.restriction_site a:visited {color: #990066}\n" .
      "span.restriction_site a:hover {color: #CC3399}\n" .
      "span.restriction_site a:active {color: #CC3399}\n" .

      "span.forward_translation {color: #0000FF}\n" .
      "span.forward_translation a {color: #0000FF}\n" .
      "span.forward_translation a:visited {color: #0000FF}\n" .
      "span.forward_translation a:hover {color: #0000FF}\n" .
      "span.forward_translation a:active {color: #0000FF}\n" . 

      "span.forward_DNA {color: #000000}\n" .
      "span.number {color: #000000}\n" .

      "span.reverse_translation {color: #3366FF}\n" .
      "span.reverse_translation a {color: #3366FF}\n" .
      "span.reverse_translation a:visited {color: #3366FF}\n" .
      "span.reverse_translation a:hover {color: #3366FF}\n" .
      "span.reverse_translation a:active {color: #3366FF}\n" .

      "span.reverse_DNA {color: #808080}\n" .
      "span.rf_m1 {color: #3366FF}\n" .
      "span.rf_m2 {color: #3366FF}\n" .
      "span.rf_m3 {color: #3366FF}\n" .
      "span.rf_1 {color: #0000FF}\n" .
      "span.rf_2 {color: #0000FF}\n" .
      "span.rf_3 {color: #0000FF}\n" .

      "span.reverse_primer a {color: #CC0000}\n" .
      "span.reverse_primer a:visited {color: #CC0000}\n" .
      "span.reverse_primer a:hover {color: #FF3333}\n" .
      "span.reverse_primer a:active {color: #FF3333}\n" .

      "span.spacer_line {color: #000000}\n" .
      "td.found_none {color: #000000; background-color: #FFCCCC}\n" .
      "td.found_one {color: #000000; background-color: #99FF99}\n" .
      "td.found_many {color: #000000}\n" .
      "td.summary_title {font-weight: bold; color: #FFFFFF; background-color: #666666}\n" .
      "span.sequence_summary {font-size: large}\n" .
      "</style>\n";

use Sequence;
use make_sequence;
use make_primers;
use make_rest;
use add_rest;
use add_primers;
use make_html;
use make_rest_summary;
use make_primer_summary;
use make_cds_links;
use make_trans_links;
use make_total_protein;
use make_output;

my $sequenceRecord = '';
my $primerList = '';
my $geneticCodeSelection = '';
my $restrictionSetSelection = '';
my $readingFramesToShow = '';
my $isCircular = '';
my $checkForMismatchFivePrimeTails = '';
my $checkForMismatchThreePrimeTails = '';
my $checkForMiddleMatch = '';
my $minimumMatch = '';
my $basePerLine = '';
my $showNumberLine = '';
my $showReverseStrand = '';
my $showSpacerLine = '';
my $returnRestSummary = '';
my $returnPrimerSummary = '';
my $returnHelpInfo = '';
my $returnCdsLinks = '';
my $returnTransLinks = '';
my $returnOptionsChosen = '';

if (param ("sequenceRecord")) {
   $sequenceRecord = param ("sequenceRecord");
}
else {
   $sequenceRecord = q(gggggggggggggg);
}

if (param ("primerList")) {
   $primerList = param ("primerList");
}
else {
   $primerList = q();
}

if (param ("geneticCodeSelection")) {
   $geneticCodeSelection = param ("geneticCodeSelection");
}
else {
   $geneticCodeSelection = "standard";
}

if (param ("restrictionSetSelection")) {
   $restrictionSetSelection = param ("restrictionSetSelection");
}
else {
   $restrictionSetSelection = "common";
}

if (param ("readingFramesToShow")) {
   $readingFramesToShow = param ("readingFramesToShow");
}
else {
   $readingFramesToShow = "one";
}

if (param ("isCircular")) {
   $isCircular = param ("isCircular");
}
else {
   $isCircular = 0;
}

if (param ("checkForMismatchFivePrimeTails")) {
   $checkForMismatchFivePrimeTails = 1;
}
else {
   $checkForMismatchFivePrimeTails = 0;
}

if (param ("checkForMismatchThreePrimeTails")) {
   $checkForMismatchThreePrimeTails = 1;
}
else {
   $checkForMismatchThreePrimeTails = 0;
}

if (param ("minimumMatch")) {
   $minimumMatch = param ("minimumMatch");
}
else {
   $minimumMatch = 10;
}

if (param ("basePerLine")) {
   $basePerLine = param ("basePerLine");
}
else {
   $basePerLine = 80;
}

if (param ("showNumberLine")) {
   $showNumberLine = param ("showNumberLine");
}
else {
   $showNumberLine = 0;
}

if (param ("showReverseStrand")) {
   $showReverseStrand = param ("showReverseStrand");
}
else {
   $showReverseStrand = 0;
}

if (param ("showSpacerLine")) {
   $showSpacerLine = param ("showSpacerLine");
}
else {
   $showSpacerLine = 0;
}

if (param ("returnRestSummary")) {
   $returnRestSummary = param ("returnRestSummary");
}
else {
   $returnRestSummary = 0;
}

if (param ("returnPrimerSummary")) {
   $returnPrimerSummary = param ("returnPrimerSummary");
}
else {
   $returnPrimerSummary = 0;
}

if (param ("returnHelpInfo")) {
   $returnHelpInfo = param ("returnHelpInfo");
}
else {
   $returnHelpInfo = 0;
}

if (param ("returnCdsLinks")) {
   $returnCdsLinks = param ("returnCdsLinks");
}
else {
   $returnCdsLinks = 0;
}

if (param ("returnTransLinks")) {
   $returnTransLinks = param ("returnTransLinks");
}
else {
   $returnTransLinks = 0;
}

if (param ("returnTransLinks")) {
   $returnTransLinks = param ("returnTransLinks");
}
else {
   $returnTransLinks = 0;
}

if (param ("returnOptionsChosen")) {
   $returnOptionsChosen = param ("returnOptionsChosen");
}
else {
   $returnOptionsChosen = 0;
}

#preliminary check of all user values here
$primerList =~ s/[^A-Za-z\d\s\s\,\.\-\_ ]/ /g;
if ($primerList =~ m/(^[A-Za-z\d\s\s\,\.\-\_ ]+$)/) {
   $primerList = $1;
}
else {
   $primerList = '';
}

$sequenceRecord =~ s/[^A-Za-z\d\s\s\t\(\)\.\,\;\:\-\=\_\"\/\>\< ]/ /g;
if ($sequenceRecord =~ m/(^[A-Za-z\d\s\s\t\(\)\.\,\;\:\-\=\_\"\/\>\< ]+$)/) {
   $sequenceRecord = $1;
}
else {
   die ("No template sequence was entered.");
}

if ($geneticCodeSelection =~ m/(standard)/) {
   $geneticCodeSelection = $1;
}
else {
   die ("The 'genetic code' selection was not recognized.");
}

if ($restrictionSetSelection =~ m/(common|none)/) {
   $restrictionSetSelection = $1;
}
else {
   die ("The 'restriction set' selection was not recognized.");
}

if ($readingFramesToShow =~ m/(one|two|all_three|three|all_six|uppercase1|uppercase2|uppercase3|none)/) {
   $readingFramesToShow = $1;
}
else {
   die ("The 'reading frames to show translations for' selection was not recognized.");
}

if ($isCircular =~ m/(1|0)/) {
   $isCircular = $1;
}
else {
   die ("The 'topology' selection was not recognized.");
}

if ($checkForMismatchFivePrimeTails =~ m/(0|1)/) {
   $checkForMismatchFivePrimeTails = $1;
}
else {
   die ("The 'allow primers to have mismatched 5' tails' selection was not recognized.");
}

if ($checkForMismatchThreePrimeTails =~ m/(0|1)/) {
   $checkForMismatchThreePrimeTails = $1;
}
else {
   die ("The 'allow primers to have mismatched 3' tails' selection was not recognized.");
}

if (($checkForMismatchFivePrimeTails == 1) && ($checkForMismatchThreePrimeTails == 1)) {
   $checkForMiddleMatch = 1;
}
else {
   $checkForMiddleMatch = 0;
}

if ($minimumMatch =~ m/(5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20)/) {
   $minimumMatch = $1;
}
else {
   die ("The 'minimum bases required when mismatching alowed' selection was not recognized.");
}

if ($basePerLine =~ m/(60|80|100)/) {
   $basePerLine = $1;
}
else {
   die ("The 'bases per line' selection was not recognized.");
}

if ($showNumberLine =~ m/(0|1)/) {
   $showNumberLine = $1;
}
else {
   die ("The 'show number line' selection was not recognized.");
}

if ($showReverseStrand =~ m/(0|1)/) {
   $showReverseStrand = $1;
}
else {
   die ("The 'show reverse strand' selection was not recognized.");
}

if ($showSpacerLine =~ m/(0|1)/) {
   $showSpacerLine = $1;
}
else {
   die ("The 'show spacer line' selection was not recognized.");
}

if ($returnRestSummary =~ m/(0|1)/) {
   $returnRestSummary = $1;
}
else {
   die ("The 'return restriction summary' selection was not recognized.");
}

if ($returnPrimerSummary =~ m/(0|1)/) {
   $returnPrimerSummary = $1;
}
else {
   die ("The 'return primer summary' selection was not recognized.");
}

if ($returnHelpInfo =~ m/(0|1)/) {
   $returnHelpInfo = $1;
}
else {
   die ("The 'return help information' selection was not recognized.");
}

if ($returnCdsLinks =~ m/(0|1)/) {
   $returnCdsLinks = $1;
}
else {
   die ("The 'return coding sequence links' selection was not recognized.");
}

if ($returnTransLinks =~ m/(0|1)/) {
   $returnTransLinks = $1;
}
else {
   die ("The 'return translation links' selection was not recognized.");
}

if ($returnOptionsChosen =~ m/(0|1)/) {
   $returnOptionsChosen = $1;
}
else {
   die ("The 'return options selected' selection was not recognized.");
}

#end preliminary check of user values

my $totalTranslations = "";
my $restSummary = "";
my $primerSummary = "";
my $cdsLinks = "";
my $transLinks = "";
my $helpInfo = "";
my $optionInfo = "";

if ($returnOptionsChosen == 1) {
   $optionInfo = '<li>The following options were selected: genetic code=' . $geneticCodeSelection . '; restriction set=' . $restrictionSetSelection . '; reading frames to show translations for=' . $readingFramesToShow . '; ';
   if ($isCircular == 1) {
      $optionInfo = $optionInfo . 'topology=circular; ';
   }
   else {
      $optionInfo = $optionInfo . 'topology=linear; ';
   }

   if ($checkForMismatchFivePrimeTails == 1) {
      $optionInfo = $optionInfo . 'allow primers to have mismatched 5\' tails=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'allow primers to have mismatched 5\' tails=false; ';
   }

   if ($checkForMismatchThreePrimeTails == 1) {
      $optionInfo = $optionInfo . 'allow primers to have mismatched 3\' tails=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'allow primers to have mismatched 3\' tails=false; ';
   }

   $optionInfo = $optionInfo . 'matching bases required when mismatching bases allowed=' . $minimumMatch . '; ';

   $optionInfo = $optionInfo . 'bases per line=' . $basePerLine . '; ';

   if ($showNumberLine == 1) {
      $optionInfo = $optionInfo . 'show number line=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'show number line=false; ';
   }

   if ($showReverseStrand == 1) {
      $optionInfo = $optionInfo . 'show reverse strand=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'show reverse strand=false; ';
   }

   if ($showSpacerLine == 1) {
      $optionInfo = $optionInfo . 'show spacer line=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'show spacer line=false; ';
   }

   if ($returnHelpInfo == 1) {
      $optionInfo = $optionInfo . 'return help info=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'return help info=false; ';
   }

   if ($returnRestSummary == 1) {
      $optionInfo = $optionInfo . 'return restriction summary=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'return restriction summary=false; ';
   }

   if ($returnPrimerSummary == 1) {
      $optionInfo = $optionInfo . 'return primer summary=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'return primer summary=false; ';
   }

   if ($returnCdsLinks == 1) {
      $optionInfo = $optionInfo . 'return coding sequence links=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'return coding sequence links=false; ';
   }

   if ($returnTransLinks == 1) {
      $optionInfo = $optionInfo . 'return translation links=true; ';
   }
   else {
      $optionInfo = $optionInfo . 'return translation links=false; ';
   }
   $optionInfo =~ s/\;\s*$//;
   $optionInfo = $optionInfo . '.';
}

my $sequence = make_sequence::makeSequence($sequenceRecord, $geneticCodeSelection, $readingFramesToShow, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);

if (!($primerList =~ m/^[\s\t ]*$/)) {
   my $patternGroupPrimer = make_primers::makePrimers($primerList);
   $sequence = add_primers::addPrimers($sequence, $patternGroupPrimer, $isCircular, $checkForMismatchFivePrimeTails, $checkForMismatchThreePrimeTails, $checkForMiddleMatch, $minimumMatch);
}
else {
   $returnPrimerSummary = 0;
}

if (!($restrictionSetSelection eq 'none')) {
   my $patternGroupRestriction = make_rest::makeRest($restrictionSetSelection);
   $sequence = add_rest::addRest($sequence, $patternGroupRestriction, $isCircular);
}
else {
   $returnRestSummary = 0;
}

if ($returnHelpInfo == 1) {
   $helpInfo = "<li>Additional information about a primer, translation, or restriction site can be viewed by pointing to an item.</li>\n" .
	       "<li>Click on two restriction sites to perform a virtual restriction digest, or two primers to perform a virtual PCR reaction. To use the beginning or end of the sequence as a restriction boundary, click on the <b>RestStart</b> or <b>RestEnd</b> link followed by a restriction site. To use the beginning or end of the sequence as a PCR boundary, click on the <b>PCRStart</b> or <b>PCREnd</b> link followed by an appropriate primer. These functions require a JavaScript-enabled browser.</li>\n" .
	       "<li>Amino acids are aligned with the first base in the corresponding triplet that coded for them.</li>\n" .
	       "<li>Translations shown below the reverse DNA strand are written in reverse.</li>\n" .
	       "<li>Restriction sites are numbered as the first base after the cut site on the direct strand.</li>\n" .
	       "<li>Primers shown in italics contain mismatches with the template.</li>\n" .
	       "<li>Note that the primer annealing sites shown on this figure do not necessarily reflect true annealing sites.</li>\n" .
	       "<li>Primers annealing to the direct strand are numbered as the direct strand base under the 5' primer base.</li>\n" .
	       "<li>Primers annealing to the reverse strand are numbered as the first non-primer base preceding the 5' end base on the direct strand.</li>\n" .
	       "<li>The exact blunt size of a PCR product = reverse primer position - forward primer position.</li>\n" .
	       "<li>The exact direct strand length of a restriction fragment = downstream position - upstream position.</li>\n";
}

if ($returnRestSummary == 1) {
   $restSummary = make_rest_summary::makeRestSummary($sequence);
}

if ($returnPrimerSummary == 1) {
   $primerSummary = make_primer_summary::makePrimerSummary($sequence);
}

if ($returnCdsLinks == 1) {
   $cdsLinks = make_cds_links::makeCdsLinks($sequence);
}

if ($returnTransLinks == 1) {
   $transLinks = make_trans_links::makeTransLinks($sequence);
   $totalTranslations = make_total_protein::makeTotalProtein($sequence);
}

my $htmlGroup = make_html::makeHtml($sequence);
my $sequenceDescription = $sequence->getDescription();
my $dnaSequence = $sequence->getSequence();
make_output::makeOutput($htmlGroup, $sequenceDescription, $dnaSequence, $totalTranslations, $restSummary, $primerSummary, $cdsLinks, $transLinks, $helpInfo, $optionInfo, $basePerLine);
print "<div class=\"copyright\">Sequence Extractor copyright &copy; 2006 Paul Stothard<br />\n" .
      "email: stothard\@ualberta.ca</div>\n" .
      "</td>\n" .
      "</tr>\n" .

      "<tr>\n" .
      "<td>\n" .
      "<a href=\"http://validator.w3.org/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://www.w3.org/Icons/valid-xhtml10\" alt=\"Valid XHTML 1.0!\" height=\"31\" width=\"88\" /></a>\n" .
      "<a href=\"http://jigsaw.w3.org/css-validator/\"><img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\" /></a>\n" .
      "</td>\n" .
      "</tr>\n" .

      "</tbody>\n" .
      "</table>\n" .

      "</body>\n" .
      "</html>\n";
