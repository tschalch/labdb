#!/usr/bin/perl
use strict;
use warnings;

use Sequence;
use make_sequence;
use make_rest;
use add_rest;
use make_html;
use make_rest_summary;
use make_cds_links;
use make_trans_links;
use make_total_protein;
use make_output;
use constants;

my $sequenceRecord = '';
my $geneticCodeSelection = '';
my $restrictionSetSelection = '';
my $readingFramesToShow = '';
my $isCircular = '';
my $basePerLine = '';
my $showNumberLine = '';
my $showReverseStrand = '';
my $showSpacerLine = '';
my $returnRestSummary = '';
my $returnHelpInfo = '';
my $returnCdsLinks = '';
my $returnTransLinks = '';
my $returnOptionsChosen = '';
my $templateDirectory = '';
my $fileFound = 0;

$geneticCodeSelection = constants::getGeneticCodeSelection();
$restrictionSetSelection = constants::getRestrictionSetSelection();
$readingFramesToShow = constants::getReadingFramesToShow();
$isCircular = constants::getIsCircular();
$basePerLine = constants::getBasePerLine();
$showNumberLine = constants::getShowNumberLine();
$showReverseStrand = constants::getShowReverseStrand();
$showSpacerLine = constants::getShowSpacerLine();
$returnRestSummary = constants::getReturnRestSummary();
$returnHelpInfo = constants::getReturnHelpInfo();
$returnCdsLinks = constants::getReturnCdsLinks();
$returnTransLinks = constants::getReturnTransLinks();
$returnOptionsChosen = constants::getReturnOptionsChosen();
#$templateDirectory = 'templates\\'; #for Windows
$templateDirectory = './templates/'; #for Unix

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

opendir ( DIR, $templateDirectory ) or die ( "Could not open 'templates' directory: $!" );
foreach ( readdir( DIR ) ) {
   my $file = "<" . $templateDirectory . $_;
   if ($_ =~ m/\.txt/) {
      $/ = undef;
      $fileFound = 1;
      my $fileToWrite = $_;
      print "reading " . $fileToWrite . " from the 'templates' directory\n";
      $fileToWrite =~ s/\.txt//i;
      open ( IN, $file ) or die ( "Cannot open file for reading: $!" );
      $sequenceRecord = <IN>;
      close ( IN ) or die ( "Cannot close file for reading" );
      $/ = "\n";
      my $sequence = make_sequence::makeSequence($sequenceRecord, $geneticCodeSelection, $readingFramesToShow, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      if (!($restrictionSetSelection eq 'none')) {
         my $patternGroupRestriction = make_rest::makeRest($restrictionSetSelection);
         $sequence = add_rest::addRest($sequence, $patternGroupRestriction, $isCircular);
      }
      else {
         $returnRestSummary = 0;
      }

      if ($returnHelpInfo == 1) {
         $helpInfo = q(<li>This page can be saved for future use. </li><li>Additional information about a translation or restriction site can be viewed in the status bar by pointing to the item with the mouse. </li><li>Click on two restriction sites to perform a virtual restriction digest. To use the beginning or end of the sequence as a restriction boundary, click on the <b>RestStart</b> or <b>RestEnd</b> link followed by a restriction site. This function requires a JavaScript-enabled browser. </li><li>Amino acids are aligned with the first base in the corresponding triplet that coded for them. </li><li>Translations shown below the reverse DNA strand are written in reverse. </li><li>Restriction sites are numbered as the first base after the cut site on the direct strand. </li><li>The exact direct strand length of a restriction fragment = downstream position - upstream position. </li>);
      }

      if ($returnRestSummary == 1) {
         $restSummary = make_rest_summary::makeRestSummary($sequence);
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
      make_output::makeOutput($fileToWrite, $htmlGroup, $sequenceDescription, $dnaSequence, $totalTranslations, $restSummary, $cdsLinks, $transLinks, $helpInfo, $optionInfo, $basePerLine);
   }
}
if (!($fileFound)) {
  print "no .txt files were found in the 'templates' directory\n";
}

