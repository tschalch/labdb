#!/usr/bin/perl
# make_total_protein.pm
# the make_total_protein module. This module accepts a Sequence object and returns the TotalProteinSequence feature label if there is one.
# Written by Paul Stothard

package make_total_protein;

use strict;
use warnings;
use Sequence;
use Feature;
use CGI::Carp qw(fatalsToBrowser);

sub makeTotalProtein {
   my $sequenceObject = shift();
   my $featureFound = 0;
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   my $totalProteinSequence = '';
   foreach(@features) {
      my $featureType = $_->getType();
      if ($featureType eq "TotalProteinSequence") {
         $totalProteinSequence = $_->getLabelToDisplay();
         $featureFound = 1;
         last;
      }
   }
   if (!$featureFound) {
      return '';
   }
   else {
      return $totalProteinSequence;
   }
}

return 1;
