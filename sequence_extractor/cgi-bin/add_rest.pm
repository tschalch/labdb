#!/usr/bin/perl
# add_rest.pm
# the add_rest module. This module accepts a DNA sequence and a PatternGroup of restriction sites and looks for matches on the DNA sequence. Each match is added as a feature to the DNA sequence.
# Written by Paul Stothard

package add_rest;

use strict;
use warnings;
use Sequence;
use Feature;
use PatternGroup;
use Pattern;
use SearchPattern;
use constants;
use CGI::Carp qw(fatalsToBrowser);

sub addRest {
   my $patternMatched = 0;
   my $lookAhead = constants::getLookAhead();
   my $sequence = shift();
   my $patternGroup = shift();
   my $isCircular = shift();
   my @restrictionSitesChecked = ();
   my $sequenceProper = $sequence->getSequence();
   my $originalLength = length($sequenceProper);
   my @patterns = @{$patternGroup->getArrayOfPatterns()};
   my $preSequence = '';
   my $postSequence = '';
   my $circleShift = 0;
   if ($isCircular) {
      if ($originalLength < $lookAhead) {
         $preSequence = $sequenceProper;
         $postSequence = $sequenceProper;
         $circleShift = $originalLength;
         $sequenceProper = $preSequence . $sequenceProper . $postSequence;
      }
      else {
           $preSequence = substr($sequenceProper, $originalLength - $lookAhead, $lookAhead);
           $postSequence = substr($sequenceProper, 0, $lookAhead);
           $circleShift = $lookAhead;
           $sequenceProper = $preSequence . $sequenceProper . $postSequence;
      }
   }
   for (my $j = 0; $j < @patterns; $j = $j + 1) {
      $patternMatched = 0;
      my $patternName = $patterns[$j]->getName();
      my $patternType = $patterns[$j]->getType();
      my $subPatternType = $patternType;
      my @regExps = @{$patterns[$j]->getArrayOfPatterns()};
      my $extraInfo = "";
      foreach(@regExps) {
         $extraInfo = $extraInfo . ' ' . $_->getMouseOver();
      }
      push (@restrictionSitesChecked, $patternName . '/' . $extraInfo);
      $patternName = "";

      for (my $k = 0; $k < @regExps; $k = $k + 1) {
         my $regularExpression = $regExps[$k]->getRegularExpression();
         my $labelToDisplay = $regExps[$k]->getLabelToDisplay();
         my $offsetValue = $regExps[$k]->getResultOffset();
         my $mouseOver = $regExps[$k]->getMouseOver();
         my $degenRegExp = replaceDegenerates($regularExpression);
         while ($sequenceProper =~ m/($degenRegExp)/gi) {
            $patternMatched = 1;
	    my $matched_length = length($1);
            my $matchPosition = pos($sequenceProper) - $circleShift - $offsetValue;
            if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
               next;
            }
            my $tempFeature = new Feature;
            $tempFeature->setName($patternName);
            $tempFeature->setType($subPatternType);
            $tempFeature->setLabelToDisplay($labelToDisplay);
            $tempFeature->setPosition($matchPosition);
            $tempFeature->addStartTag('<a href="javascript: showRestSequence(' . $matchPosition . ')" onmouseover="return overlib(\'' . 'Position=' . $matchPosition . ' ' . $mouseOver . '\');" onmouseout="return nd();">');
            $tempFeature->addEndTag('</a>');
            $sequence->addFeature($tempFeature);
	    pos($sequenceProper) = pos($sequenceProper) - $matched_length + 1;
         }
      }
   }
   my $tempFeature = new Feature;
   $tempFeature->setName("sitesChecked");
   $tempFeature->setType("sitesChecked");
   $tempFeature->setLabelToDisplay(join(",", @restrictionSitesChecked));
   $tempFeature->setPosition(0);
   $sequence->addFeature($tempFeature);
   return $sequence;
}

sub replaceDegenerates {
   my $primerPattern = shift();
   $primerPattern =~ s/u|t/[TU]/gi;
   $primerPattern =~ s/r/[AGR]/gi;
   $primerPattern =~ s/y/[CTUY]/gi;
   $primerPattern =~ s/s/[GCS]/gi;
   $primerPattern =~ s/w/[ATUW]/gi;
   $primerPattern =~ s/k/[GTUK]/gi;
   $primerPattern =~ s/m/[ACM]/gi;
   $primerPattern =~ s/b/[CGTUBSYK]/gi;
   $primerPattern =~ s/d/[AGTUDWRK]/gi;
   $primerPattern =~ s/h/[ACTUHYMW]/gi;
   $primerPattern =~ s/v/[ACGVSMR]/gi;
   $primerPattern =~ s/n/[ACGTURYSWKMBDHVN]/gi;
   return $primerPattern;
}

return 1;
