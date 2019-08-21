#!/usr/bin/perl
# make_rest.pm
# the make_rest module. This module converts restriction sites and names into a PatternGroup object.
# Written by Paul Stothard

package make_rest;

use strict;
use warnings;
use PatternGroup;
use Pattern;
use SearchPattern;
use constants;
use CGI::Carp qw(fatalsToBrowser);

sub makeRest {
   my $restrictionSetSelection = shift();
   my $patternGroup = new PatternGroup;
   my $restrictionPatternsAllowed = constants::getRestrictionPatternsAllowed();
   my $restrictionPatternsFound = 0;
   my $tempString = "";
   $patternGroup->setName("Restriction sites");
   $patternGroup->setDescription("Restriction sites");
   if (constants::getRestrictionSites($restrictionSetSelection) =~ m/\/[A-Za-z]+\s+\/[^\,]+[\,]/)	{
      my @restSplit = split(/\,[\f\n\r]*/, constants::getRestrictionSites($restrictionSetSelection));
      foreach(@restSplit) {
         if ($_ =~ m/^\s*\/([A-Za-z]+)\s+\/[^\s]+\s+\/[^\s]+/) {
            my $newPattern = new Pattern;
            my $name = $1;
            $name = filterProb($name);
            $newPattern->setName($name);
            $newPattern->setType("restrictionSite");
            $tempString = $_;
            $tempString =~ s/$1//;
            my @lineSplit = split(/\//, $tempString);
            for (my $i = 0; $i < @lineSplit; $i = $i + 2) {
               if ($lineSplit[$i] =~ m/([A-Za-z]+)(\d+)\s/) {
                  my $patternSeq = $1;
                  $patternSeq =~ s/[^acgturyswkmbdhvn]//gi;
                  my $indent = $2;
                  if ($lineSplit[$i + 1] =~ m/([^\s]+)/) {
                     my $mouseOver = $1;
                     $mouseOver = filterProb($mouseOver);
                     my $newSearchPattern = new SearchPattern;
                     $newSearchPattern->setLabelToDisplay($name);
                     $newSearchPattern->setMouseOver($name . " " . $mouseOver);
                     $newSearchPattern->setRegularExpression($patternSeq);
                     $newSearchPattern->setResultOffset($indent - 1);
                     $newPattern->addSearchPattern($newSearchPattern);
                     $restrictionPatternsFound = $restrictionPatternsFound + 1;
                     if ($restrictionPatternsFound > $restrictionPatternsAllowed) {
                        die("The number of restriction patterns is greater than " . $restrictionPatternsAllowed . ".");
                     }
                  }
               }
            }
            $patternGroup->addPattern($newPattern);
         }
      }
   }
   return $patternGroup;
}

sub filterProb {
   my $textToFilter = shift();
   $textToFilter =~ s/[\f\n\r\t\'\"\>\<\\\/\&\.\, ]+/ /g;
   $textToFilter =~ s/ +/ /g;
   $textToFilter =~ s/^ | $//g;
   return $textToFilter;
}

return 1;
