#!/usr/bin/perl
# add_primers.pm
# the add_primers module. This module accepts a sequence and a PatternGroup of primers and looks for matches in the DNA sequence. Each match is added as a feature to the DNA sequence.
# Written by Paul Stothard

package add_primers;

use strict;
use warnings;
use Sequence;
use Feature;
use PatternGroup;
use Pattern;
use SearchPattern;
use constants;
use CGI::Carp qw(fatalsToBrowser);

sub addPrimers {
   my $lookAhead = constants::getLookAhead();
   my $sequence = shift();
   my $patternGroup = shift();
   my $isCircular = shift();
   my $checkForMismatchFivePrimeTails = shift();
   my $checkForMismatchThreePrimeTails = shift();
   my $checkForMiddleMatch = shift();
   my $minimumMatch = shift();
   my @primersChecked = ();
   my $sequenceProper = $sequence->getSequence();
   my @patterns = @{$patternGroup->getArrayOfPatterns()};
   my $originalLength = length($sequenceProper);
   my $preSequence = '';
   my $postSequence = '';
   my $circleShift = 0;
   my $matchesAllowed = constants::getMatchesAllowed();
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
      my $patternName = $patterns[$j]->getName();
      my $patternType = $patterns[$j]->getType();
      my $subPatternType = $patternType;
      my @regExps = @{$patterns[$j]->getArrayOfPatterns()};
      my %forwardPrimerSites = ();
      my %reversePrimerSites = ();
      my $matchesFound = 0;
      for (my $k = 0; $k < @regExps; $k = $k + 1) {
         my $regularExpression = $regExps[$k]->getRegularExpression();
         my $labelToDisplay = $regExps[$k]->getLabelToDisplay();
         my $offsetValue = $regExps[$k]->getResultOffset();
         my $mouseOver = $regExps[$k]->getMouseOver();
         my $degenRegExp = replaceDegenerates($regularExpression);
         $subPatternType = $regExps[$k]->getType();
         if ($subPatternType eq "forwardPrimer") {
            push (@primersChecked, $patternName . '/ ' . $regularExpression);
         }
         while ($sequenceProper =~ m/$degenRegExp/gi) {
            $matchesFound = $matchesFound + 1;
            if ($matchesFound > $matchesAllowed) {
               die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
            }
            my $matchPosition = pos($sequenceProper) - $circleShift - $offsetValue;
            if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
               next;
            }
            my $tempFeature = new Feature;
            $tempFeature->setName($patternName);
            $tempFeature->setType($subPatternType);
            $tempFeature->setLabelToDisplay($labelToDisplay);
            if ($subPatternType eq "forwardPrimer") {
               $forwardPrimerSites{pos($sequenceProper)} = 'forward';
               $tempFeature->addStartTag('<a href="javascript: showPrimerSequence([\'forward\',\'' . specialFilter($labelToDisplay) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . $matchPosition . ' ' . $mouseOver . '\');" onmouseout="return nd();">');
               $tempFeature->addEndTag('</a>');
               if ($matchPosition >= 4) {
                  $tempFeature->setPosition($matchPosition - 3);
                  $tempFeature->setLabelToDisplay("5' " . $labelToDisplay);
               }
               else {
                  $tempFeature->setPosition($matchPosition);
               }
            }
            if ($subPatternType eq "reversePrimer") {
               $reversePrimerSites{pos($sequenceProper)} = 'reverse';
               my $tempExp = $regularExpression;
               $tempExp =~ s/\[[A-Za-z]+\]/x/g;
               $tempExp =~ s/[^A-Za-z]//g;
               my $primerLength = length($tempExp);
               $tempFeature->addStartTag('<a href="javascript: showPrimerSequence([\'reverse\',\'' . compSeq(specialFilter($labelToDisplay)) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . ($matchPosition + $primerLength) . ' ' . $mouseOver . '\');" onmouseout="return nd();">');
               $tempFeature->addEndTag('</a>');
               if ($matchPosition >= 4) {
                  $tempFeature->setPosition($matchPosition - 3);
                  $tempFeature->setLabelToDisplay("3' " . $labelToDisplay);
               }
               else {
                  $tempFeature->setPosition($matchPosition);
               }
            }
            $sequence->addFeature($tempFeature);
         }
      }
      if ($checkForMismatchFivePrimeTails || $checkForMismatchThreePrimeTails || $checkForMiddleMatch) {
         for (my $k = 0; $k < @regExps; $k = $k + 1) {
            my $regularExpression = $regExps[$k]->getRegularExpression();
            my $labelToDisplay = $regExps[$k]->getLabelToDisplay();
            my $offsetValue = $regExps[$k]->getResultOffset;
            my $subPatternType = $regExps[$k]->getType();
            my $mouseOver = $regExps[$k]->getMouseOver();
            if ($checkForMismatchFivePrimeTails) {
               if ($subPatternType eq "forwardPrimer") {
                  my $mismatchResult = firstHalfMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($forwardPrimerSites{$_})) {
                           if ($forwardPrimerSites{$_} eq 'forward') {
                              next;
                           }
                        }
                        $forwardPrimerSites{$_} = 'forward';
                        my $tempFeature = new Feature;
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'forward\',\'' . specialFilter($labelToDisplay) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . $matchPosition . ' ' . $mouseOver . ' MISMATCHING 5 PRIME TAIL' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("5' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
               elsif ($subPatternType eq "reversePrimer") {
                  my $mismatchResult = secondHalfMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($reversePrimerSites{$_})) {
                           if ($reversePrimerSites{$_} eq 'reverse') {
                              next;
                           }
                        }
                        $reversePrimerSites{$_} = 'reverse';
                        my $tempFeature = new Feature;
                        my $tempExp = $regularExpression;
                        $tempExp =~ s/\[[A-Za-z]+\]/x/g;
                        $tempExp =~ s/[^A-Za-z]//g;
                        my $primerLength = length($tempExp);
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'reverse\',\'' . compSeq(specialFilter($labelToDisplay)) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . ($matchPosition + $primerLength) . ' ' . $mouseOver . ' MISMATCHING 5 PRIME TAIL' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("3' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
            }
            if ($checkForMismatchThreePrimeTails) {
               if ($subPatternType eq "forwardPrimer") {
                  my $mismatchResult = secondHalfMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($forwardPrimerSites{$_})) {
                           if ($forwardPrimerSites{$_} eq 'forward') {
                              next;
                           }
                        }
                        $forwardPrimerSites{$_} = 'forward';
                        my $tempFeature = new Feature;
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'forward\',\'' . specialFilter($labelToDisplay) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . $matchPosition . ' ' . $mouseOver . ' MISMATCHING 3 PRIME TAIL' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("5' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
               elsif ($subPatternType eq "reversePrimer") {
                  my $mismatchResult = firstHalfMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($reversePrimerSites{$_})) {
                           if ($reversePrimerSites{$_} eq 'reverse') {
                              next;
                           }
                        }
                        $reversePrimerSites{$_} = 'reverse';
                        my $tempFeature = new Feature;
                        my $tempExp = $regularExpression;
                        $tempExp =~ s/\[[A-Za-z]+\]/x/g;
                        $tempExp =~ s/[^A-Za-z]//g;
                        my $primerLength = length($tempExp);
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'reverse\',\'' . compSeq(specialFilter($labelToDisplay)) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . ($matchPosition + $primerLength) . ' ' . $mouseOver . ' MISMATCHING 3 PRIME TAIL' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("3' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
            }
            if ($checkForMiddleMatch) {
               if ($subPatternType eq "forwardPrimer") {
                  my $mismatchResult = middleMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($forwardPrimerSites{$_})) {
                           if ($forwardPrimerSites{$_} eq 'forward') {
                              next;
                           }
                        }
                        $forwardPrimerSites{$_} = 'forward';
                        my $tempFeature = new Feature;
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'forward\',\'' . specialFilter($labelToDisplay) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . $matchPosition . ' ' . $mouseOver . ' MISMATCHING 5 PRIME AND 3 PRIME TAILS' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("5' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
               elsif ($subPatternType eq "reversePrimer") {
                  my $mismatchResult = middleMatch ($sequenceProper, $regularExpression, $minimumMatch);
                  if (scalar(@$mismatchResult) > 0) {
                     foreach(@$mismatchResult) {
                        $matchesFound = $matchesFound + 1;
                        if ($matchesFound > $matchesAllowed) {
                           die ("The primer '" . $patternName . "' matches more than " . $matchesAllowed . " sites.");
                        }
                        my $matchPosition = $_ - $circleShift - $offsetValue;
                        if (($matchPosition < 1) || ($matchPosition > $originalLength) || (length($regularExpression) > $originalLength)) {
                           next;
                        }
                        if (exists ($reversePrimerSites{$_})) {
                           if ($reversePrimerSites{$_} eq 'reverse') {
                              next;
                           }
                        }
                        $reversePrimerSites{$_} = 'reverse';
                        my $tempFeature = new Feature;
                        my $tempExp = $regularExpression;
                        $tempExp =~ s/\[[A-Za-z]+\]/x/g;
                        $tempExp =~ s/[^A-Za-z]//g;
                        my $primerLength = length($tempExp);
                        $tempFeature->setName($patternName);
                        $tempFeature->setType($subPatternType);
                        $tempFeature->setLabelToDisplay($labelToDisplay);
                        $tempFeature->addStartTag('<i><a href="javascript: showPrimerSequence([\'reverse\',\'' . compSeq(specialFilter($labelToDisplay)) . '\',' . $matchPosition . '])" onmouseover="return overlib(\'' . 'Position=' . ($matchPosition + $primerLength) . ' ' . $mouseOver . ' MISMATCHING 5 PRIME AND 3 PRIME TAILS' . '\');" onmouseout="return nd();">');
                        $tempFeature->addEndTag('</a></i>');
                        if ($matchPosition >= 4) {
                           $tempFeature->setPosition($matchPosition - 3);
                           $tempFeature->setLabelToDisplay("3' " . $labelToDisplay);
                        }
                        else {
                           $tempFeature->setPosition($matchPosition);
                        }
                        $sequence->addFeature($tempFeature);
                     }
                  }
               }
            }
         }
      }
   }
   my $tempFeature = new Feature;
   $tempFeature->setName("primersChecked");
   $tempFeature->setType("primersChecked");
   $tempFeature->setLabelToDisplay(join(",", @primersChecked));
   $tempFeature->setPosition(0);
   $sequence->addFeature($tempFeature);
   return $sequence;
}

sub firstHalfMatch {
   my $sequence = shift();
   my $regularExpression = shift();
   my $minimumMatch = shift();
   my $newExpression = "";
   my @arrayOfLetters = split(/\b|\B/, $regularExpression);
   my @arrayOfMatches = ();
   if (scalar(@arrayOfLetters) <= $minimumMatch) {
      return \@arrayOfMatches;
   }
   for (my $i = 0; $i < @arrayOfLetters - $minimumMatch; ++$i) {
      $arrayOfLetters[$i] = 'n';
   }
   $newExpression = replaceDegenerates(join("",@arrayOfLetters));
   while ($sequence =~ m/$newExpression/gi) {
      push (@arrayOfMatches, pos($sequence));
   }
   return \@arrayOfMatches;
}

sub secondHalfMatch {
   my $sequence = shift();
   my $regularExpression = shift();
   my $minimumMatch = shift();
   my $newExpression = "";
   my @arrayOfLetters = split(/\b|\B/, $regularExpression);
   my @arrayOfMatches = ();
   if (scalar(@arrayOfLetters) <= $minimumMatch) {
      return \@arrayOfMatches;
   }
   for (my $i = $minimumMatch; $i < @arrayOfLetters; ++$i) {
      $arrayOfLetters[$i] = 'n';
   }
   $newExpression = replaceDegenerates(join("",@arrayOfLetters));
   while ($sequence =~ m/$newExpression/gi) {
      push (@arrayOfMatches, pos($sequence));
   }
   return \@arrayOfMatches;
}

sub middleMatch {
   my $sequence = shift();
   my $regularExpression = shift();
   my $minimumMatch = shift();
   my $newExpression = "";
   my @arrayOfLetters = split(/\b|\B/, $regularExpression);
   my @arrayOfMatches = ();
   if (scalar(@arrayOfLetters) <= ($minimumMatch - 1)) {
      return \@arrayOfMatches;
   }
   my $startPos = int((scalar(@arrayOfLetters)-10) /2);
   for (my $i = 0; $i < @arrayOfLetters; ++$i) {
      if (($i < $startPos) || ($i >= ($startPos + 10))) {
         $arrayOfLetters[$i] = 'n';
      }
   }
   $newExpression = replaceDegenerates(join("",@arrayOfLetters));
   while ($sequence =~ m/$newExpression/gi) {
      push (@arrayOfMatches, pos($sequence));
   }
   return \@arrayOfMatches;
}  

sub compSeq {
   my $primerPattern = shift();
   $primerPattern =~ tr/GARKBDCTYMVHU/CTYMVHGARKBDA/;
   $primerPattern =~ tr/garkbdctymvhu/ctymvhgarkbda/;
   return $primerPattern;
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

sub specialFilter {
   my $primerPattern = shift();
   if ($primerPattern =~ m/^([^\s]+)/g) {
      $primerPattern = $1;
   }
   return $primerPattern;
}

return 1;
