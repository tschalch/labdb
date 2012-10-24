#!/usr/bin/perl
# make_trans_links.pm
# the make_trans_links module. This module generates html code that calls a JavaScript function that displays protein translations. It returns a string of html code.
# Written by Paul Stothard

package make_trans_links;

use strict;
use warnings;
use Sequence;
use Feature;
use CGI::Carp qw(fatalsToBrowser);

sub makeTransLinks {
   my $sequenceObject = shift();
   my $htmlA = q(<li>Use the following links to view the translations (requires JavaScript-enabled browser): );
   my $htmlAEnd = q(. </li>);
   my $featureFound = 0;
   my @arrayOfRanges = ();
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   foreach(@features) {
      my $featureType = $_->getType();
      my $ranges = $_->getLabelToDisplay();
      if ($featureType eq "CdsProteinRanges") {
         @arrayOfRanges = split(/\/\//, $ranges);
         $featureFound = 1;
         last;
      }
   }
   if (!$featureFound) {
      return '';
   }

   for (my $i = 0; $i < @arrayOfRanges; $i = $i + 1) {
      if ($arrayOfRanges[$i] =~ m/^([^\/]+)\/(\d+)\.\.(\d+)/) {
         my $geneName = $1;
         my $startOfProtein = $2;
         my $endOfProtein = $3;
         $htmlA = $htmlA . '<a href="javascript: showTrans(\'' . $geneName . '\', ' . $startOfProtein . ', ' . $endOfProtein . ')" onmouseover="return overlib(\'View translation\');" onmouseout="return nd();">' . $geneName . '</a>, ';
      }
   }
   $htmlA =~ s/\, $//;
   return $htmlA . $htmlAEnd;
}

return 1;
