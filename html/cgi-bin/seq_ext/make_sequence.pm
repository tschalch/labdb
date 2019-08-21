#!/usr/bin/perl
# make_sequence.pm
# the make_sequence module. This module converts GenBank, EMBL, FASTA, or raw text into a Sequence object with its associated features.
# Written by Paul Stothard

package make_sequence;

use strict;
use warnings;
use Sequence;
use Sequence_Feature;
use translate;
use constants;
use CGI::Carp qw(fatalsToBrowser);

my $tempString = "";
my $maxSeqLength = constants::getMaxSeqLength();

sub makeSequence {
   my $sequenceRecord = shift();
   my $geneticCodeSelection = shift();
   my $readingFramesToShow = shift();
   my $showNumberLine = shift();
   my $showReverseStrand = shift();
   my $showSpacerLine = shift();
   my $basePerLine = shift();
   my $newSequence = new Sequence;
   if ($sequenceRecord =~ m/^\s*LOCUS[\W\w]*^\s*DEFINITION[\W\w]*^\s*ACCESSION[\W\w]*^\s*FEATURES[\W\w]*^\s*ORIGIN/m)	{
      return genBank ($sequenceRecord, $newSequence, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
   }
   elsif ($sequenceRecord =~ m/^\s*ID[\W\w]*^\s*AC[\W\w]*^\s*DT[\W\w]*^\s*DE[\W\w]*^\s*FT[\W\w]*^\s*SQ/m) {
      return embl ($sequenceRecord, $newSequence, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
   }
   elsif ($sequenceRecord =~ m/LOCUS[\W\w]*DEFINITION[\W\w]*ACCESSION[\W\w]*FEATURES[\W\w]*ORIGIN/) {
      die ("The GenBank entry could not be parsed properly.");
   }
   elsif ($sequenceRecord =~ m/ID[\W\w]*AC[\W\w]*DT[\W\w]*DE[\W\w]*FT[\W\w]*SQ/m) {
      die ("The EMBL entry could not be parsed properly.");
   }
   elsif ($sequenceRecord =~ m/^[\s ]*>[^\f\n\r]+/) {
      $sequenceRecord =~ s/^[\s ]*>//;
      if ($sequenceRecord =~ m/[acgturyswkmbdhvnACGTURYSWKMBDHVN]/) {
         return fasta ($sequenceRecord, $newSequence, $geneticCodeSelection, $readingFramesToShow, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      }
      else {
         die ("The FASTA entry contained no sequence data.");
      }
   }
   elsif ($sequenceRecord =~ m/[acgturyswkmbdhvnACGTURYSWKMBDHVN]/) {
      return raw ($sequenceRecord, $newSequence, $geneticCodeSelection, $readingFramesToShow, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
   }
   else {
      die ("The sequence format was not recognized.");
   }
}

sub genBank {
   my $myFile = shift();
   my $mainSequence = shift();
   my $showNumberLine = shift();
   my $showReverseStrand = shift();
   my $showSpacerLine = shift();
   my $basePerLine = shift();
   my $descriptionString = '<b>Template: </b>';
   if (!($myFile =~ m/\/\/\s*$/)) {
      die ("The end of the GenBank file was missing. Your Web browser may limit the amount of text that can be entered. Try again using a different Web browser.");
   }
   my @mainArray = split(/^\s*LOCUS|^\s*DEFINITION|^\s*ACCESSION|^\s*FEATURES|^\s*ORIGIN/m, $myFile);
   if (scalar(@mainArray) != 6) {
      die ("There is a problem with the GenBank file format.");
   }
   if ($mainArray[2] =~ m/([^\f\n\r]+)/) {
      $tempString = $1;
      $descriptionString = $descriptionString . filterProb($tempString) . ".<br />\n";
   }
   else {
      die ("A sequence description was not found.");
   }
   if ($mainArray[3] =~ m/(\w+)/) {
      $mainSequence->setAccession(filterProb($1));
      $descriptionString = $descriptionString . "<b>Accession: </b>" . filterProb($1) . ".<br />\n";
   }
   if ($mainArray[5] =~ m/[acgturyswkmbdhvnACGTURYSWKMBDHVN]/) {
      $mainArray[5] =~ s/[^acgturyswkmbdhvnACGTURYSWKMBDHVN]//g;
      $mainArray[5] = lc($mainArray[5]);
      if (length($mainArray[5]) > $maxSeqLength) {
         die ("The sequence must be less than " . $maxSeqLength . ".");
      }
      $mainSequence->setSequence($mainArray[5]);
      $descriptionString = $descriptionString . '<b>Length: </b>' . length($mainArray[5]) . " bp.<br />\n";
   }
   else {
      die ("The DNA sequence was missing.");
   }
   $mainSequence->setDescription($descriptionString);
   $mainSequence->setType("GenBank");
   my $DnaFeature = "";
   my $revSequence = "";

   if ($showReverseStrand == 1) {
      $revSequence = $mainArray[5];
      $revSequence =~ tr/garkbdctymvhu/ctymvhgarkbda/;
   }

   if ($showNumberLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("number");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getNumberLine($basePerLine), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showSpacerLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("spacerLine");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getSpacerLine(), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('RestStart');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(1)" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestEnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('RestEnd');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(' . (length($mainSequence->getSequence()) + 1) . ');" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCRStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('PCRStart');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'forward\',\'\',1])" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCREnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('PCREnd');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'reverse\',\'\',' . (length($mainSequence->getSequence()) + 1) .'])" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);
   
   $mainArray[4] =~ s/^\s*Location\/Qualifiers//;
   $mainArray[4] =~ s/^[\f\n\r]*//;
   my $tabIn = 0;
   if ($mainArray[4] =~ m/(^\s+)/) {
      $tabIn = length($1);
   }
   else {
      die ( "There was a problem interpreting the GenBank file.");
   }
   my @featureArray = split(/^\s{$tabIn}?\b/m, $mainArray[4]);
   my $featureDirectionKeeper = 'forward';
   my $featureTitleKeeper = '';
   my $featureRangeKeeper = '';
   my $proteinRangeKeeper = '';
   my $proteinSequenceKeeper = '';
   my $totalProteinSequence = '';
   my $cdsTally = 0;
   my $proteinRangeStart = 0;
   my $proteinRangeStop = 0;
   my @CdsDnaRanges = ();
   my @CdsProteinRanges = ();
   foreach (@featureArray) {
      if ($_ =~ m/^CDS/) {
         $cdsTally = $cdsTally + 1;
         if ($cdsTally > constants::getCdsLimit()) {
            die ("The template sequence contains more than " . constants::getCdsLimit() . " coding sequences.");
         }
         my @innerFeatureArray = split(/^[\s]{20,}?\//m, $_);
         if (scalar(@innerFeatureArray) < 2) {
            die ("There was a problem interpreting the GenBank file.");
         }
         my $featureRange = $innerFeatureArray[0];
         $featureRange =~ s/^CDS|\s|\<|\>//g;
         my $firstQualifier = $innerFeatureArray[1];
         my $startCodon = 1;
         foreach (@innerFeatureArray) {
            if ($_ =~ m/^codon\_start\=(\d+)/) {
               $startCodon = $1;
            }
            if ($_ =~ m/^gene\=/) {
               $firstQualifier = $_;
            }
            $featureTitleKeeper = filterProb($firstQualifier);
            if ($_ =~ m/^translation/) {
               my $extraTransChar = "";
               my $codingLength = 1 - $startCodon;
               my @featurePositions = ([]);
               while ($featureRange =~ m/(\d+)\.\.(\d+)/gi) {
                  my $featureStart = $1;
                  my $featureEnd = $2;
                  if ($featureStart >= $featureEnd) {
                     die ("One of the feature ranges was not properly specified.");
                  }
                  $codingLength = $codingLength + $featureEnd - $featureStart + 1;
               }
               if ($codingLength%3 != 0) {
                  die ( "One of the CDS features was not properly specified.");
               }
               $tempString = $_;
               $tempString =~ s/translation=\"|[^A-Za-z]//g;
               $proteinSequenceKeeper = $tempString;
               my @featureBases = split(/\B/, $tempString);
               if ($codingLength/3 == scalar(@featureBases)) {
                  $extraTransChar = "";
               }
               elsif ($codingLength/3 == scalar(@featureBases) + 1) {
                  $extraTransChar = "Z";
               }
               else {
                  die ( "One of the CDS features was not properly specified.");
               }
               push (@featureBases, $extraTransChar);
               $tempString = join ("  ", @featureBases);
               @featureBases = split(/\b|\B/, $tempString);
               my $featureType = "forwardTranslation";
               my $indexCounter = 0;
               if ($featureRange =~ m/complement\([j\d]/) {
                     push(@featureBases," ");
                     push(@featureBases," ");
                     @featureBases = reverse(@featureBases);
                     $featureType = "reverseTranslation";
                     $firstQualifier = "REVERSE STRAND " . $firstQualifier;
                     $featureDirectionKeeper = 'reverse';
               }
               if (!($featureRange =~ m/\:/)) {
                  while ($featureRange =~ m/(\d+)\.\.(\d+)/gi) {
                     $featurePositions [$indexCounter][0] = $1;
                     $featurePositions [$indexCounter][1] = $2;
                     if (($1 > length($mainArray[5])) || ($2 > length($mainArray[5]))) {
                        die ( "One of the CDS features extends past the DNA sequence.");
                     }

                     if ($featureType eq "forwardTranslation") {
                        my $beforeSubstring = substr($mainArray[5], 0, $featurePositions [$indexCounter][0] - 1);
                        my $changedSubstring = substr($mainArray[5], $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                        $changedSubstring = uc($changedSubstring);
                        my $afterSubstring = substr($mainArray[5], $featurePositions [$indexCounter][1], length($mainArray[5]) - $featurePositions [$indexCounter][1]);
                        $mainArray[5] = $beforeSubstring . $changedSubstring . $afterSubstring;
                     }
                     
                     if (($featureType eq "reverseTranslation") && ($showReverseStrand == 1)) {
                        my $beforeSubstring = substr($revSequence, 0, $featurePositions [$indexCounter][0] - 1);
                        my $changedSubstring = substr($revSequence, $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                        $changedSubstring = uc($changedSubstring);
                        my $afterSubstring = substr($revSequence, $featurePositions [$indexCounter][1], length($revSequence) - $featurePositions [$indexCounter][1]);
                        $revSequence = $beforeSubstring . $changedSubstring . $afterSubstring;
                     }
                     
                     ++$indexCounter;
                  }
                  my @sortedPositions = sort {$$a[0] <=> $$b[0]} @featurePositions;
                  foreach (@sortedPositions) {
                     my $featureStart = $$_[0];
                     if (($startCodon != 1) && ($featureType eq "forwardTranslation")) {       
                        $featureStart = $featureStart + $startCodon - 1;
                        $startCodon = 1;
                     }
                     if ($featureStart < 1) {
                        die ( "One of the CDS Sequence_Features starts before the first base.");
                     }
                     my $featureEnd = $$_[1];
                     my $featureLength = $featureEnd - $featureStart + 1;
                     my $newFeature = new Sequence_Feature;
                     my $featureDescription = "";
                     if ($featureType eq "reverseTranslation") {
                        $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureEnd . " to " . $featureStart . ")");
                     }
                     else {
                        $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureStart . " to " . $featureEnd . ")");
                     }
                     $newFeature->addStartTag('<a href="javascript: window.focus()" onmouseover="return overlib(\'' . $featureDescription . '\');" onmouseout="return nd();">');
                     $newFeature->addEndTag('</a>');
                     $newFeature->setType($featureType);
                     $newFeature->setPosition($featureStart);
                     my @basesToShow = splice(@featureBases, 0, $featureLength);
                     $tempString = join ("", @basesToShow);
                     $newFeature->setLabelToDisplay($tempString);
                     $mainSequence->addFeature($newFeature);
                     $featureRangeKeeper = $featureRangeKeeper . '\'' . $featureStart . '..' . $featureEnd . '\',';
                  }
               }
               else {
                  while ($featureRange =~ m/([^\d])*(\d+)\.\.(\d+)/gi) {
                     my $isColon = $1;
                     my $firstNumber = $2;
                     my $secondNumber = $3;
                     $featurePositions [$indexCounter][0] = $firstNumber;
                     $featurePositions [$indexCounter][1] = $secondNumber;
                     if ($isColon =~ m/\:/) {
                        $featurePositions [$indexCounter][2] = "not shown";
                     }
                     else {
                        $featurePositions [$indexCounter][2] = "shown";
                        if (($firstNumber > length($mainArray[5])) || ($secondNumber > length($mainArray[5]))) {
                           die ( "One of the CDS features extends past the DNA sequence.");
                        }
                        if ($featureType eq "forwardTranslation") {
                           my $beforeSubstring = substr($mainArray[5], 0, $featurePositions [$indexCounter][0] - 1);
                           my $changedSubstring = substr($mainArray[5], $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                           $changedSubstring = uc($changedSubstring);
                           my $afterSubstring = substr($mainArray[5], $featurePositions [$indexCounter][1], length($mainArray[5]) - $featurePositions [$indexCounter][1]);
                           $mainArray[5] = $beforeSubstring . $changedSubstring . $afterSubstring;
                        }
                     
                        if (($featureType eq "reverseTranslation") && ($showReverseStrand == 1)) {
                           my $beforeSubstring = substr($revSequence, 0, $featurePositions [$indexCounter][0] - 1);
                           my $changedSubstring = substr($revSequence, $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                           $changedSubstring = uc($changedSubstring);
                           my $afterSubstring = substr($revSequence, $featurePositions [$indexCounter][1], length($revSequence) - $featurePositions [$indexCounter][1]);
                           $revSequence = $beforeSubstring . $changedSubstring . $afterSubstring;
                        }
                     }
                     ++$indexCounter;
                  }
             
                  foreach (@featurePositions) {
                     my @basesToShow = ();
                     my $featureStart = $$_[0];
                     if (($startCodon != 1) && ($featureType eq "forwardTranslation")) {       
                        $featureStart = $featureStart + $startCodon - 1;
                        $startCodon = 1;
                     }
                     if ($featureStart < 1) {
                        die ( "One of the CDS features starts before the first base.");
                     }
                     my $featureEnd = $$_[1];
                     my $featureLength = $featureEnd - $featureStart + 1;
                     
                     if ($featureType eq "forwardTranslation") {
                        @basesToShow = splice(@featureBases, 0, $featureLength);
                     }
                     else {
                        my $tempLength = scalar(@featureBases);
                        @basesToShow = splice(@featureBases, $tempLength - $featureLength, $featureLength);
                     }
                     if ($$_[2] eq "shown") {
                        my $newFeature = new Sequence_Feature;
                        my $featureDescription = "";
                        if ($featureType eq "reverseTranslation") {
                           $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureEnd . " to " . $featureStart . ")");
                        }
                        else {
                           $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureStart . " to " . $featureEnd . ")");
                        }
                        $newFeature->addStartTag('<a href="javascript: window.focus()" onmouseover="return overlib(\'' . $featureDescription . '\');" onmouseout="return nd();">');
                        $newFeature->addEndTag('</a>');
                        $newFeature->setType($featureType);
                        $newFeature->setPosition($featureStart);
                        $tempString = join ("", @basesToShow);
                        $newFeature->setLabelToDisplay($tempString);
                        $mainSequence->addFeature($newFeature);
                        $featureRangeKeeper = $featureRangeKeeper . '\'' . $featureStart . '..' . $featureEnd . '\',';
                    }
                  }
               }
            }
         }
      }
      $totalProteinSequence = $totalProteinSequence . $proteinSequenceKeeper;
      $proteinRangeStop = length($totalProteinSequence);
      $proteinRangeKeeper = $proteinRangeStart . '..' . $proteinRangeStop;
      if (($featureTitleKeeper ne '') && ($featureRangeKeeper ne '')) {
         push (@CdsDnaRanges, $featureTitleKeeper . '/' . $featureDirectionKeeper . '/' . $featureRangeKeeper);
      }
      if (($featureTitleKeeper ne '') && ($proteinRangeKeeper ne '')) {      
         push (@CdsProteinRanges, $featureTitleKeeper . '/' . $proteinRangeKeeper);
      }
      $proteinRangeStart = $proteinRangeStop;
      $featureRangeKeeper = '';
      $featureTitleKeeper = '';
      $proteinSequenceKeeper = '';
      $featureDirectionKeeper = 'forward';
   }
   if (scalar(@CdsDnaRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsDnaRanges");
      $tempFeature->setType("CdsDnaRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsDnaRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   if (scalar(@CdsProteinRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsProteinRanges");
      $tempFeature->setType("CdsProteinRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsProteinRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }
   
   if (length($totalProteinSequence) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("TotalProteinSequence");
      $tempFeature->setType("TotalProteinSequence");
      $tempFeature->setLabelToDisplay($totalProteinSequence);
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }
   
   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("forwardDna");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay($mainArray[5]);
   $mainSequence->addFeature($DnaFeature);
   
   if ($showReverseStrand == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("reverseDna");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay($revSequence);
      $mainSequence->addFeature($DnaFeature);
   }
   $mainSequence->setSequence($mainArray[5]);
   return $mainSequence;
}

sub embl {
   my $myFile = shift();
   my $mainSequence = shift();
   my $showNumberLine = shift();
   my $showReverseStrand = shift();
   my $showSpacerLine = shift();
   my $basePerLine = shift();
   my $descriptionString = '<b>Template: </b>';
   if (!($myFile =~ m/\/\/\s*$/)) {
      die ("The end of the EMBL file was missing. Your Web browser may limit the amount of text that can be entered. Try again using a different Web browser.");
   }
   #my @mainArray = split(/^\s*ID|^\s*AC|^\s*DE|^\s*FH   Key             Location\/Qualifiers[\s]+^\s*FH|^\s*XX[\s]*^\s*SQ   Sequence[\W\w]*other;/m, $myFile);
   my @mainArray = split(/^\s*FH   Key             Location\/Qualifiers[\s]+^\s*FH|^\s*XX[\s]*^\s*SQ[^\f\n\r]*/m, $myFile);
   if (scalar(@mainArray) != 3) {
      die ("There is a problem with the EMBL file format.");
   }
   
   if ($mainArray[0] =~ m/^DE\s*([^\f\n\r]+)/m) {
      $tempString = $1;
      $descriptionString = $descriptionString . filterProb($tempString) . ".<br />\n";
   }
   else {
      die ( "A sequence description was not found.");
   }
   if ($mainArray[0] =~ m/^AC\s*([^\f\n\r;]+)/m) {
      $mainSequence->setAccession(filterProb($1));
      $descriptionString = $descriptionString . "<b>Accession: </b>" . filterProb($1) . ".<br />\n";
   }

   if ($mainArray[2] =~ m/[acgturyswkmbdhvnACGTURYSWKMBDHVN]/) {
      $mainArray[2] =~ s/[^acgturyswkmbdhvnACGTURYSWKMBDHVN]//g;
      $mainArray[2] = lc($mainArray[2]);
      if (length($mainArray[2]) > $maxSeqLength) {
         die ("The sequence must be less than " . $maxSeqLength . ".");
      }

      $mainSequence->setSequence($mainArray[2]);
      $descriptionString = $descriptionString . '<b>Length: </b>' . length($mainArray[2]) . " bp.<br />\n";
   }
   else {
      die ("The DNA sequence was missing.");
   }
   $mainSequence->setDescription($descriptionString);
   $mainSequence->setType("EMBL");
   
   my $DnaFeature = "";
   my $revSequence = "";

   if ($showReverseStrand == 1) {
      $revSequence = $mainArray[2];
      $revSequence =~ tr/garkbdctymvhu/ctymvhgarkbda/;
   }

   if ($showNumberLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("number");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getNumberLine($basePerLine), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showSpacerLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("spacerLine");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getSpacerLine(), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('RestStart');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(1)" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestEnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('RestEnd');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(' . (length($mainSequence->getSequence()) + 1) . ');" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCRStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('PCRStart');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'forward\',\'\',1])" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCREnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('PCREnd');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'reverse\',\'\',' . (length($mainSequence->getSequence()) + 1) .'])" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);
   
   $mainArray[1] =~ s/^FT/  /mg;
   $mainArray[1] =~ s/^[\f\n\r]*//;
   my $tabIn = 0;
   if ($mainArray[1] =~ m/(^\s+)/) {
      $tabIn = length($1);
   }
   else {
      die ( "There was a problem interpreting the EMBL file.");
   }
   my @featureArray = split(/^\s{$tabIn}?\b/m, $mainArray[1]);
   my $featureDirectionKeeper = 'forward';
   my $featureTitleKeeper = '';
   my $featureRangeKeeper = '';
   my $proteinRangeKeeper = '';
   my $proteinSequenceKeeper = '';
   my $totalProteinSequence = '';
   my $cdsTally = 0;
   my $proteinRangeStart = 0;
   my $proteinRangeStop = 0;
   my @CdsDnaRanges = ();
   my @CdsProteinRanges = ();
   foreach (@featureArray) {
      if ($_ =~ m/^CDS/) {
         $cdsTally = $cdsTally + 1;
         if ($cdsTally > constants::getCdsLimit()) {
            die ("The template sequence contains more than " . constants::getCdsLimit() . " coding sequences.");
         }
         my @innerFeatureArray = split(/^[\s]{20,}?\//m, $_);
         if (scalar(@innerFeatureArray) < 2) {
            die ("There was a problem interpreting the EMBL file.");
         }
         my $featureRange = $innerFeatureArray[0];
         $featureRange =~ s/^CDS|\s|\<|\>//g;
         my $firstQualifier = $innerFeatureArray[1];
         my $startCodon = 1;
         foreach (@innerFeatureArray) {
            if ($_ =~ m/^codon\_start\=(\d+)/) {
               $startCodon = $1;
            }
            if ($_ =~ m/^gene\=/) {
               $firstQualifier = $_;
            }
            $featureTitleKeeper = filterProb($firstQualifier);
            if ($_ =~ m/^translation/) {
               my $extraTransChar = "";
               my $codingLength = 1 - $startCodon;
               my @featurePositions = ([]);
               while ($featureRange =~ m/(\d+)\.\.(\d+)/gi) {
                  my $featureStart = $1;
                  my $featureEnd = $2;
                  if ($featureStart >= $featureEnd) {
                     die ("One of the feature ranges was not properly specified.");
                  }
                  $codingLength = $codingLength + $featureEnd - $featureStart + 1;
               }
               if ($codingLength%3 != 0) {
                  die ( "One of the CDS features was not properly specified.");
               }
               $tempString = $_;
               $tempString =~ s/translation=\"|[^A-Za-z]//g;
               $proteinSequenceKeeper = $tempString;
               my @featureBases = split(/\B/, $tempString);
               if ($codingLength/3 == scalar(@featureBases)) {
                  $extraTransChar = "";
               }
               elsif ($codingLength/3 == scalar(@featureBases) + 1) {
                  $extraTransChar = "Z";
               }
               else {
                  die ( "One of the CDS features was not properly specified.");
               }
               push (@featureBases, $extraTransChar);
               $tempString = join ("  ", @featureBases);
               @featureBases = split(/\b|\B/, $tempString);
               my $featureType = "forwardTranslation";
               my $indexCounter = 0;
               if ($featureRange =~ m/complement\([j\d]/) {
                     push(@featureBases," ");
                     push(@featureBases," ");
                     @featureBases = reverse(@featureBases);
                     $featureType = "reverseTranslation";
                     $firstQualifier = "REVERSE STRAND " . $firstQualifier;
                     $featureDirectionKeeper = 'reverse';
               }
               if (!($featureRange =~ m/\:/)) {
                  while ($featureRange =~ m/(\d+)\.\.(\d+)/gi) {
                     $featurePositions [$indexCounter][0] = $1;
                     $featurePositions [$indexCounter][1] = $2;
                     if (($1 > length($mainArray[2])) || ($2 > length($mainArray[2]))) {
                        die ( "One of the CDS features extends past the DNA sequence.");
                     }
                     
                     if ($featureType eq "forwardTranslation") {
                        my $beforeSubstring = substr($mainArray[2], 0, $featurePositions [$indexCounter][0] - 1);
                        my $changedSubstring = substr($mainArray[2], $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                        $changedSubstring = uc($changedSubstring);
                        my $afterSubstring = substr($mainArray[2], $featurePositions [$indexCounter][1], length($mainArray[2]) - $featurePositions [$indexCounter][1]);
                        $mainArray[2] = $beforeSubstring . $changedSubstring . $afterSubstring;
                     }
                     
                     if (($featureType eq "reverseTranslation") && ($showReverseStrand == 1)) {
                        my $beforeSubstring = substr($revSequence, 0, $featurePositions [$indexCounter][0] - 1);
                        my $changedSubstring = substr($revSequence, $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                        $changedSubstring = uc($changedSubstring);
                        my $afterSubstring = substr($revSequence, $featurePositions [$indexCounter][1], length($revSequence) - $featurePositions [$indexCounter][1]);
                        $revSequence = $beforeSubstring . $changedSubstring . $afterSubstring;
                     }
                     ++$indexCounter;
                  }
                  my @sortedPositions = sort {$$a[0] <=> $$b[0]} @featurePositions;

                  foreach (@sortedPositions) {
                     my $featureStart = $$_[0];
                     if (($startCodon != 1) && ($featureType eq "forwardTranslation")) {       
                        $featureStart = $featureStart + $startCodon - 1;
                        $startCodon = 1;
                     }
                     if ($featureStart < 1) {
                        die ( "One of the CDS features starts before the first base.");
                     }
                     my $featureEnd = $$_[1];
                     my $featureLength = $featureEnd - $featureStart + 1;
                     my $newFeature = new Sequence_Feature;
                     my $featureDescription = "";
                     if ($featureType eq "reverseTranslation") {
                        $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureEnd . " to " . $featureStart . ")");
                     }
                     else {
                        $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureStart . " to " . $featureEnd . ")");
                     }
                     $newFeature->addStartTag('<a href="javascript: window.focus()" onmouseover="return overlib(\'' . $featureDescription . '\');" onmouseout="return nd();">');
                     $newFeature->addEndTag('</a>');
                     $newFeature->setType($featureType);
                     $newFeature->setPosition($featureStart);
                     my @basesToShow = splice(@featureBases, 0, $featureLength);
                     $tempString = join ("", @basesToShow);
                     $newFeature->setLabelToDisplay($tempString);
                     $mainSequence->addFeature($newFeature);
                     $featureRangeKeeper = $featureRangeKeeper . '\'' . $featureStart . '..' . $featureEnd . '\',';
                  }
               }
               else {
                  while ($featureRange =~ m/([^\d])*(\d+)\.\.(\d+)/gi) {
                     my $isColon = $1;
                     my $firstNumber = $2;
                     my $secondNumber = $3;
                     $featurePositions [$indexCounter][0] = $firstNumber;
                     $featurePositions [$indexCounter][1] = $secondNumber;
                     if ($isColon =~ m/\:/) {
                        $featurePositions [$indexCounter][2] = "not shown";
                     }
                     else {
                        $featurePositions [$indexCounter][2] = "shown";
                        if (($firstNumber > length($mainArray[2])) || ($secondNumber > length($mainArray[2]))) {
                           die ( "One of the CDS features extends past the DNA sequence.");
                        }
                        if ($featureType eq "forwardTranslation") {
                           my $beforeSubstring = substr($mainArray[2], 0, $featurePositions [$indexCounter][0] - 1);
                           my $changedSubstring = substr($mainArray[2], $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                           $changedSubstring = uc($changedSubstring);
                           my $afterSubstring = substr($mainArray[2], $featurePositions [$indexCounter][1], length($mainArray[2]) - $featurePositions [$indexCounter][1]);
                           $mainArray[2] = $beforeSubstring . $changedSubstring . $afterSubstring;
                        }
                     
                        if (($featureType eq "reverseTranslation") && ($showReverseStrand == 1)) {
                           my $beforeSubstring = substr($revSequence, 0, $featurePositions [$indexCounter][0] - 1);
                           my $changedSubstring = substr($revSequence, $featurePositions [$indexCounter][0] - 1, $featurePositions [$indexCounter][1] - $featurePositions [$indexCounter][0] + 1);
                           $changedSubstring = uc($changedSubstring);
                           my $afterSubstring = substr($revSequence, $featurePositions [$indexCounter][1], length($revSequence) - $featurePositions [$indexCounter][1]);
                           $revSequence = $beforeSubstring . $changedSubstring . $afterSubstring;
                        }
                     }
                     ++$indexCounter;
                  }
             
                  foreach (@featurePositions) {
                     my @basesToShow = ();
                     my $featureStart = $$_[0];
                     if (($startCodon != 1) && ($featureType eq "forwardTranslation")) {       
                        $featureStart = $featureStart + $startCodon - 1;
                        $startCodon = 1;
                     }
                     if ($featureStart < 1) {
                        die ( "One of the CDS features starts before the first base.");
                     }
                     my $featureEnd = $$_[1];
                     my $featureLength = $featureEnd - $featureStart + 1;
                     
                     if ($featureType eq "forwardTranslation") {
                        @basesToShow = splice(@featureBases, 0, $featureLength);
                     }
                     else {
                        my $tempLength = scalar(@featureBases);
                        @basesToShow = splice(@featureBases, $tempLength - $featureLength, $featureLength);
                     }
                     if ($$_[2] eq "shown") {
                        my $newFeature = new Sequence_Feature;
                        my $featureDescription = "";
                        if ($featureType eq "reverseTranslation") {
                           $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureEnd . " to " . $featureStart . ")");
                        }
                        else {
                           $featureDescription = filterProb(filterProb($firstQualifier) . " (bases " . $featureStart . " to " . $featureEnd . ")");
                        }
                        $newFeature->addStartTag('<a href="javascript: window.focus()" onmouseover="return overlib(\'' . $featureDescription . '\');" onmouseout="return nd();">');
                        $newFeature->addEndTag('</a>');
                        $newFeature->setType($featureType);
                        $newFeature->setPosition($featureStart);
                        $tempString = join ("", @basesToShow);
                        $newFeature->setLabelToDisplay($tempString);
                        $mainSequence->addFeature($newFeature);
                        $featureRangeKeeper = $featureRangeKeeper . '\'' . $featureStart . '..' . $featureEnd . '\',';
                     }
                  }
               }
            }
         }
      }
      $totalProteinSequence = $totalProteinSequence . $proteinSequenceKeeper;
      $proteinRangeStop = length($totalProteinSequence);
      $proteinRangeKeeper = $proteinRangeStart . '..' . $proteinRangeStop;
      if (($featureTitleKeeper ne '') && ($featureRangeKeeper ne '')) {
         push (@CdsDnaRanges, $featureTitleKeeper . '/' . $featureDirectionKeeper . '/' . $featureRangeKeeper);
      }
      if (($featureTitleKeeper ne '') && ($proteinRangeKeeper ne '')) {      
         push (@CdsProteinRanges, $featureTitleKeeper . '/' . $proteinRangeKeeper);
      }
      $proteinRangeStart = $proteinRangeStop;
      $featureRangeKeeper = '';
      $featureTitleKeeper = '';
      $proteinSequenceKeeper = '';
      $featureDirectionKeeper = 'forward';
   }

   if (scalar(@CdsDnaRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsDnaRanges");
      $tempFeature->setType("CdsDnaRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsDnaRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   if (scalar(@CdsProteinRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsProteinRanges");
      $tempFeature->setType("CdsProteinRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsProteinRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   if (length($totalProteinSequence) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("TotalProteinSequence");
      $tempFeature->setType("TotalProteinSequence");
      $tempFeature->setLabelToDisplay($totalProteinSequence);
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("forwardDna");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay($mainArray[2]);
   $mainSequence->addFeature($DnaFeature);
   
   if ($showReverseStrand == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("reverseDna");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay($revSequence);
      $mainSequence->addFeature($DnaFeature);
   }
   $mainSequence->setSequence($mainArray[2]);
   
   return $mainSequence;
}

sub fasta {
   my $myFile = shift();
   my $mainSequence = shift();
   my $geneticCodeSelection = shift();
   my $readingFramesToShow = shift();
   my $showNumberLine = shift();
   my $showReverseStrand = shift();
   my $showSpacerLine = shift();
   my $basePerLine = shift();
   my $descriptionString = '<b>Template: </b>';
   my $tempTitle = "";
   if ($myFile =~ m/(^[^\f\n\r]+[\f\n\r])/) {
      $tempTitle = filterProb($1);
      $descriptionString = $descriptionString . $tempTitle . ".<br />\n";
   }
   else {
      die ( "There was an error reading a FASTA file.");
   }
   
   $myFile =~ s/^[^\f\n\r]+[\f\n\r]//;
   $myFile =~ s/[^acgturyswkmbdhvnACGTURYSWKMBDHVN]//g;
   if ($readingFramesToShow =~ m/uppercase(\d)/) {
      my $upperCaseFrame = $1;
      my $dnaForUppercase = $myFile;
      my $featureRanges = "(";
      while ($myFile =~ m/([A-Z]+)/g) {
         $featureRanges = $featureRanges . (pos($myFile) - length($1) + 1) . ".." . pos($myFile) . ",";
      }
      $featureRanges =~ s/\,$/\)/;
      $dnaForUppercase =~ s/[a-z]//g;
      my @arrayOfBases = split(/\b|\B/, $dnaForUppercase);
      my $cdsTranslation = translate::translate(\@arrayOfBases, $upperCaseFrame, $geneticCodeSelection);
      $cdsTranslation =~ s/\s//g;
      if ((length($cdsTranslation) == 0) || (!($featureRanges =~ m/\d/))) {
         return fasta (">" . $tempTitle . "\n" . $myFile, $mainSequence, $geneticCodeSelection, "none", $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      }
      else {
         my $fakeGenBankRecord = "LOCUS\nDEFINITION  " . $tempTitle . "\nACCESSION\nFEATURES             Location/Qualifiers\n     CDS             " . $featureRanges . "\n                     \/codon_start=" . $upperCaseFrame . "\n                     \/gene=\"". $readingFramesToShow . "\n                     \/translation=\"" . $cdsTranslation . "\"\n" . "BASE COUNT\nORIGIN\n" . $myFile . "\n\/\/";
         return genBank($fakeGenBankRecord, $mainSequence, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      }
   }
   
   if ($myFile =~ m/[A-Za-z]/) {
      if (length($myFile) > $maxSeqLength) {
         die ("The sequence must be less than " . $maxSeqLength . ".");
      }
      $mainSequence->setSequence($myFile);
      $descriptionString = $descriptionString . '<b>Length: </b>' . length($myFile) . " bp.<br />\n";
   }
   else {
      die ( "A FASTA file with no DNA sequence was encountered.");
   }
   $mainSequence->setType("FASTA");
   $mainSequence->setAccession("FASTA");
   $mainSequence->setDescription($descriptionString);

   my $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("forwardDna");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay($myFile);
   $mainSequence->addFeature($DnaFeature);

   if ($showReverseStrand == 1) {
      my $revSequence = $myFile;
      $revSequence =~ tr/GARKBDCTYMVHU/CTYMVHGARKBDA/;
      $revSequence =~ tr/garkbdctymvhu/ctymvhgarkbda/;
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("reverseDna");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay($revSequence);
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showNumberLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("number");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getNumberLine($basePerLine), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showSpacerLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("spacerLine");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getSpacerLine(), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }
  
   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('RestStart');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(1)" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestEnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('RestEnd');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(' . (length($mainSequence->getSequence()) + 1) . ');" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCRStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('PCRStart');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'forward\',\'\',1])" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCREnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('PCREnd');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'reverse\',\'\',' . (length($mainSequence->getSequence()) + 1) .'])" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);
   
   my $featureDirectionKeeper = '';
   my $featureTitleKeeper = '';
   my $featureRangeKeeper = '';
   my $proteinRangeKeeper = '';
   my $totalProteinSequence = '';
   my $proteinRangeStart = 0;
   my $proteinRangeStop = 0;
   my @CdsDnaRanges = ();
   my @CdsProteinRanges = ();
   my @readingFrame = ();
   if ($readingFramesToShow eq 'all_six') {
      if (length($mainSequence->getSequence()) > constants::getSixFrameLimit()) {
         die ("The translations for all six reading frames can only be displayed for sequences less than " . constants::getSixFrameLimit() . " bases in length.");
      }
      else {
         @readingFrame = ( "1", "2", "3", "m1", "m2", "m3" );
      }
   }
   elsif ($readingFramesToShow eq 'none') {
      @readingFrame = ();
   }
   elsif ($readingFramesToShow eq 'one') {
      @readingFrame = ("1");
   }
   elsif ($readingFramesToShow eq 'two') {
      @readingFrame = ("2");
   }
   elsif ($readingFramesToShow eq 'three') {
      @readingFrame = ("3");
   }
   elsif ($readingFramesToShow eq 'all_three') {
      if (length($mainSequence->getSequence()) > constants::getThreeFrameLimit()) {
         die ("The translations for reading frames one to three can only be displayed for sequences less than " . constants::getThreeFrameLimit() . " bases in length.");
      }
      else {
         @readingFrame = ("1", "2", "3");
      }
   }
   else {
      die ( "The reading frame option was not was not available.");
   }
   
   foreach (@readingFrame) {
      my $newFeature = new Sequence_Feature;
      $newFeature->setType("translationForReadingFrame" . $_);
      $newFeature->setPosition(1);
      my @arrayOfBases = split(/\b|\B/, $myFile);
      my $translation = translate::translate(\@arrayOfBases, $_, $geneticCodeSelection);
      $newFeature->setLabelToDisplay($translation);
      $mainSequence->addFeature($newFeature);
      $featureTitleKeeper = 'reading frame= ';
      $translation =~ s/ //g;
      if ($_ =~ m/^(\d)/) {
         my $featureStart = 1 + $1 - 1;
         my $featureEnd = length($mainSequence->getSequence());
         $featureRangeKeeper = '\'' . $featureStart . '..' . $featureEnd . '\',';
         $featureDirectionKeeper = 'forward';
         $totalProteinSequence = $totalProteinSequence . $translation;
         $featureTitleKeeper = $featureTitleKeeper . $1;
      }
      elsif ($_ =~ m/^m(\d)/) {
         my $featureStart = 1;
         my $featureEnd = length($mainSequence->getSequence()) - $1 + 1;
         $featureRangeKeeper = '\'' . $featureStart . '..' . $featureEnd . '\',';
         $featureDirectionKeeper = 'reverse';
         my @tempTranslation = split(/\b|\B/, $translation);
         @tempTranslation = reverse(@tempTranslation);
         $totalProteinSequence = $totalProteinSequence . join ("", @tempTranslation);
         $featureTitleKeeper = $featureTitleKeeper . '-' . $1;
      }
      else {
         die ("A problem was encountered generating the protein translations.");
      }
      $proteinRangeStop = length($totalProteinSequence);
      $proteinRangeKeeper = $proteinRangeStart . '..' . $proteinRangeStop;
      push (@CdsDnaRanges, $featureTitleKeeper . '/' . $featureDirectionKeeper . '/' . $featureRangeKeeper);
      push (@CdsProteinRanges, $featureTitleKeeper . '/' . $proteinRangeKeeper);
      $proteinRangeStart = $proteinRangeStop;    
   }

   if (scalar(@CdsDnaRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsDnaRanges");
      $tempFeature->setType("CdsDnaRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsDnaRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   if (scalar(@CdsProteinRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsProteinRanges");
      $tempFeature->setType("CdsProteinRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsProteinRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }
   
   if (length($totalProteinSequence) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("TotalProteinSequence");
      $tempFeature->setType("TotalProteinSequence");
      $tempFeature->setLabelToDisplay($totalProteinSequence);
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   return $mainSequence;
}

sub raw {
   my $myFile = shift();
   my $mainSequence = shift();
   my $geneticCodeSelection = shift();
   my $readingFramesToShow = shift();
   my $showNumberLine = shift();
   my $showReverseStrand = shift();
   my $showSpacerLine = shift();
   my $basePerLine = shift();
   my $descriptionString = '<b>Length: </b>';
   $myFile =~ s/[^acgturyswkmbdhvnACGTURYSWKMBDHVN]//g;
   if ($readingFramesToShow =~ m/uppercase(\d)/) {
      my $upperCaseFrame = $1;
      my $dnaForUppercase = $myFile;
      my $featureRanges = "(";
      while ($myFile =~ m/([A-Z]+)/g) {
         $featureRanges = $featureRanges . (pos($myFile) - length($1) + 1) . ".." . pos($myFile) . ",";
      }
      $featureRanges =~ s/\,$/\)/;
      $dnaForUppercase =~ s/[a-z]//g;
      my @arrayOfBases = split(/\b|\B/, $dnaForUppercase);
      my $cdsTranslation = translate::translate(\@arrayOfBases, $upperCaseFrame, $geneticCodeSelection);
      $cdsTranslation =~ s/\s//g;
      if ((length($cdsTranslation) == 0) || (!($featureRanges =~ m/\d/))) {
         return raw ($myFile, $mainSequence, $geneticCodeSelection, "none", $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      }
      else {
         my $fakeGenBankRecord = "LOCUS\nDEFINITION  Raw sequence\nACCESSION\nFEATURES             Location/Qualifiers\n     CDS             " . $featureRanges . "\n                     \/codon_start=" . $upperCaseFrame . "\n                     \/gene=\"". $readingFramesToShow . "\n                     \/translation=\"" . $cdsTranslation . "\"\n" . "BASE COUNT\nORIGIN\n" . $myFile . "\n\/\/";
         return genBank($fakeGenBankRecord, $mainSequence, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);
      }
   }
   if ($myFile =~ m/[A-Za-z]/) {
      $descriptionString = $descriptionString . length($myFile) . " bp.<br />\n";
   }
   else {
      die ( "A raw file with no DNA sequence was encountered.");
   }
   if (length($myFile) > $maxSeqLength) {
      die ("The sequence must be less than " . $maxSeqLength . ".");
   }
   $mainSequence->setSequence($myFile);
   $mainSequence->setType("raw");
   $mainSequence->setDescription($descriptionString);
   $mainSequence->setAccession("raw");

   my $feat = print($INC{"Feature.pm"});
   my $DnaFeature = Sequence_Feature->new();
   $DnaFeature->setType("forwardDna");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay($myFile);
   $mainSequence->addFeature($DnaFeature);

   if ($showReverseStrand == 1) {
      my $revSequence = $myFile;
      $revSequence =~ tr/GARKBDCTYMVHU/CTYMVHGARKBDA/;
      $revSequence =~ tr/garkbdctymvhu/ctymvhgarkbda/;
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("reverseDna");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay($revSequence);
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showNumberLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("number");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getNumberLine($basePerLine), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   if ($showSpacerLine == 1) {
      $DnaFeature = new Sequence_Feature;
      $DnaFeature->setType("spacerLine");
      $DnaFeature->setPosition(1);
      $DnaFeature->setLabelToDisplay(substr(constants::getSpacerLine(), 0, length($mainSequence->getSequence())));
      $mainSequence->addFeature($DnaFeature);
   }

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('RestStart');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(1)" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkRestEnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('RestEnd');
   $DnaFeature->addStartTag('<a href="javascript: showRestSequence(' . (length($mainSequence->getSequence()) + 1) . ');" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCRStart");
   $DnaFeature->setPosition(1);
   $DnaFeature->setLabelToDisplay('PCRStart');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'forward\',\'\',1])" onmouseover="return overlib(\'Sequence Start\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   $DnaFeature = new Sequence_Feature;
   $DnaFeature->setType("specialLinkPCREnd");
   $DnaFeature->setPosition(length($mainSequence->getSequence()) + 1);
   $DnaFeature->setLabelToDisplay('PCREnd');
   $DnaFeature->addStartTag('<a href="javascript: showPrimerSequence([\'reverse\',\'\',' . (length($mainSequence->getSequence()) + 1) .'])" onmouseover="return overlib(\'Sequence End\');" onmouseout="return nd();">');
   $DnaFeature->addEndTag('</a>');
   $mainSequence->addFeature($DnaFeature);

   my $featureDirectionKeeper = '';
   my $featureTitleKeeper = '';
   my $featureRangeKeeper = '';
   my $proteinRangeKeeper = '';
   my $totalProteinSequence = '';
   my $proteinRangeStart = 0;
   my $proteinRangeStop = 0;
   my @CdsDnaRanges = ();
   my @CdsProteinRanges = ();
   my @readingFrame = ();
   if ($readingFramesToShow eq 'all_six') {
      if (length($mainSequence->getSequence()) > constants::getSixFrameLimit()) {
         die ("The translations for all six reading frames can only be displayed for sequences less than " . constants::getSixFrameLimit() . " bases in length.");
      }
      else {
         @readingFrame = ( "1", "2", "3", "m1", "m2", "m3" );
      }
   }
   elsif ($readingFramesToShow eq 'none') {
      @readingFrame = ();
   }
   elsif ($readingFramesToShow eq 'one') {
      @readingFrame = ("1");
   }
   elsif ($readingFramesToShow eq 'two') {
      @readingFrame = ("2");
   }
   elsif ($readingFramesToShow eq 'three') {
      @readingFrame = ("3");
   }
   elsif ($readingFramesToShow eq 'all_three') {
      if (length($mainSequence->getSequence()) > constants::getThreeFrameLimit()) {
         die ("The translations for reading frames one to three can only be displayed for sequences less than " . constants::getThreeFrameLimit() . " bases in length.");
      }
      else {
         @readingFrame = ("1", "2", "3");
      }
   }
   else {
      die ( "The reading frame option was not was not available.");
   }
   foreach (@readingFrame) {
      my $newFeature = new Sequence_Feature;
      $newFeature->setType("translationForReadingFrame" . $_);
      $newFeature->setPosition(1);
      my @arrayOfBases = split(/\b|\B/, $myFile);
      my $translation = translate::translate(\@arrayOfBases, $_, $geneticCodeSelection);
      $newFeature->setLabelToDisplay($translation);
      $mainSequence->addFeature($newFeature);
      $featureTitleKeeper = 'reading frame= ';
      $translation =~ s/ //g;
      if ($_ =~ m/^(\d)/) {
         my $featureStart = 1 + $1 - 1;
         my $featureEnd = length($mainSequence->getSequence());
         $featureRangeKeeper = '\'' . $featureStart . '..' . $featureEnd . '\',';
         $featureDirectionKeeper = 'forward';
         $totalProteinSequence = $totalProteinSequence . $translation;
         $featureTitleKeeper = $featureTitleKeeper . $1;
      }
      elsif ($_ =~ m/^m(\d)/) {
         my $featureStart = 1;
         my $featureEnd = length($mainSequence->getSequence()) - $1 + 1;
         $featureRangeKeeper = '\'' . $featureStart . '..' . $featureEnd . '\',';
         $featureDirectionKeeper = 'reverse';
         my @tempTranslation = split(/\b|\B/, $translation);
         @tempTranslation = reverse(@tempTranslation);
         $totalProteinSequence = $totalProteinSequence . join ("", @tempTranslation);
         $featureTitleKeeper = $featureTitleKeeper . '-' . $1;
      }
      else {
         die ("A problem was encountered generating the protein translations.");
      }
      $proteinRangeStop = length($totalProteinSequence);
      $proteinRangeKeeper = $proteinRangeStart . '..' . $proteinRangeStop;
      push (@CdsDnaRanges, $featureTitleKeeper . '/' . $featureDirectionKeeper . '/' . $featureRangeKeeper);
      push (@CdsProteinRanges, $featureTitleKeeper . '/' . $proteinRangeKeeper);
      $proteinRangeStart = $proteinRangeStop;
      
   }

   if (scalar(@CdsDnaRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsDnaRanges");
      $tempFeature->setType("CdsDnaRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsDnaRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   if (scalar(@CdsProteinRanges) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("CdsProteinRanges");
      $tempFeature->setType("CdsProteinRanges");
      $tempFeature->setLabelToDisplay(join('//', @CdsProteinRanges));
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }
   
   if (length($totalProteinSequence) > 0) {
      my $tempFeature = new Sequence_Feature;
      $tempFeature->setName("TotalProteinSequence");
      $tempFeature->setType("TotalProteinSequence");
      $tempFeature->setLabelToDisplay($totalProteinSequence);
      $tempFeature->setPosition(0);
      $mainSequence->addFeature($tempFeature);
   }

   return $mainSequence;
}

sub filterProb {
   my $textToFilter = shift();
   $textToFilter =~ s/[\f\n\r\t\'\"\>\<\\\/\&\| ]+/ /g;
   $textToFilter =~ s/ +/ /g;
   $textToFilter =~ s/^ | $//g;
   $textToFilter =~s/[\,\.]+$//g;
   return $textToFilter;
}

return 1;
