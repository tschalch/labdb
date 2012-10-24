#!/usr/bin/perl
# make_primers.pm
# the make_primers module. This module converts primer sequences and names into a PatternGroup object.
# Written by Paul Stothard

package make_primers;

use strict;
use warnings;
use PatternGroup;
use Pattern;
use SearchPattern;
use constants;
use CGI::Carp qw(fatalsToBrowser);

sub makePrimers {
   my $primerList = shift();
   my $patternGroup = new PatternGroup;
   my $primersAllowed = constants::getPrimersAllowed();
   my $primersFound = 0;
   $patternGroup->setName("Primers");
   $patternGroup->setDescription("Primers");
   if ($primerList =~ m/[A-Za-z]+\s+[^\s]+/)	{
      my @primerSplit = split(/\,[\f\n\r]*|[\f\n\r]+/, $primerList);
      foreach(@primerSplit) {
         if ($_ =~ m/^\s*([A-Za-z]+)\s+([^\,]+)$/) {
            my $primerSeq = $1;
            my $primerName = $2;
            $primerSeq =~ tr/iI/nN/;
            $primerSeq =~ s/[^acgturyswkmbdhvn]//gi;
            if (!($primerSeq =~ m/[acgturyswkmbdhvnACGTURYSWKMBDHVN]/)) {
               die ("One of the primer sequences was entered incorrectly");
            }
            $primerName = filterProb($primerName);
            if (!($primerName =~ m/[A-Za-z0-9]/)) {
               die ("One of the primer names was entered incorrectly");
            }
            my $newPattern = new Pattern;
            $newPattern->setName($primerName);
            $newPattern->setType("primer");
            my $newSearchPattern = new SearchPattern;
            $newSearchPattern->setLabelToDisplay($primerSeq . ' 3\'');
            $newSearchPattern->setRegularExpression($primerSeq);
            $newSearchPattern->setResultOffset(length($primerSeq)-1);
            $newSearchPattern->setType("forwardPrimer");
            $newSearchPattern->setMouseOver($primerName . ' &gt;&gt;&gt; direction &gt;&gt;&gt;');
            $newPattern->addSearchPattern($newSearchPattern);
            my $revPattern = new SearchPattern;
            my $revLabelSeq = reverseSeq($primerSeq);
            my $revCompExp = reverseCompSeq($primerSeq);
            $revPattern->setLabelToDisplay($revLabelSeq . ' 5\'');
            $revPattern->setRegularExpression($revCompExp);
            $revPattern->setResultOffset(length($primerSeq)-1);
            $revPattern->setType("reversePrimer");
            $revPattern->setMouseOver($primerName . ' &lt;&lt;&lt; direction &lt;&lt;&lt;');
            $newPattern->addSearchPattern($revPattern);
            $patternGroup->addPattern($newPattern);
            $primersFound = $primersFound + 1;
            if ($primersFound > $primersAllowed) {
               die ("The maximum number of primers allowed is " . $primersAllowed . ".");
            }
         }
      }
   }
   else {
      die ("The primer list was not entered correctly.");
   }
   return $patternGroup;
}

sub reverseSeq {
   my $primerPattern = shift();
   my @primerBases = split(/\B/, $primerPattern);
   my @revPrimer = reverse(@primerBases);
   my $revPrimer = join ("", @revPrimer);
   return $revPrimer;
}

sub reverseCompSeq {
   my $primerPattern = shift();
   my $revPrimer = reverseSeq($primerPattern);
   $revPrimer =~ tr/GARKBDCTYMVHU/CTYMVHGARKBDA/;
   $revPrimer =~ tr/garkbdctymvhu/ctymvhgarkbda/;
   return $revPrimer;
}

sub filterProb {
   my $textToFilter = shift();
   $textToFilter =~ s/[\f\n\r\t\'\"\>\<\\\/\&\|\.\,\= ]+/ /g;
   $textToFilter =~ s/ +/ /g;
   $textToFilter =~ s/^ | $//g;
   return $textToFilter;
}

return 1;
