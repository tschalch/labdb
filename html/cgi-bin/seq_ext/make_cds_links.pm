#!/usr/bin/perl
# make_cds_links.pm
# the make_cds_links module. This module generates html code that calls a JavaScript function and passes it the ranges of coding sequences described in the feature table or generated by the program. It returns a string of html code.
# Written by Paul Stothard

package make_cds_links;

use strict;
use warnings;
use Sequence;
use Feature;
use CGI::Carp qw(fatalsToBrowser);

sub makeCdsLinks {
   my $sequenceObject = shift();
   my $htmlA = q(<li>Use the following links to view the coding segments removed and joined (requires JavaScript-enabled browser): );
   my $htmlAEnd = q(. </li>);
   my $htmlB = q(<li>Use the following links to view the coding segments in uppercase (requires JavaScript-enabled browser): );
   my $htmlBEnd = q(. </li>);
   my $featureFound = 0;
   my @arrayOfRanges = ();
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   foreach(@features) {
      my $featureType = $_->getType();
      my $ranges = $_->getLabelToDisplay();
      if ($featureType eq "CdsDnaRanges") {
         @arrayOfRanges = split(/\/\//, $ranges);
         $featureFound = 1;
         last;
      }
   }
   if (!$featureFound) {
      return '';
   }

   for (my $i = 0; $i < @arrayOfRanges; $i = $i + 1) {
      if ($arrayOfRanges[$i] =~ m/^([^\/]+)\/([^\/]+)\/([^\/]+)/) {
         my $geneName = $1;
         my $strand = $2;
         my $ranges = $3;
         $ranges =~ s/\,$/\]/;
         $ranges = '[' . $ranges;
         $htmlA = $htmlA . '<a href="javascript: featureExtractor(\'joined\', \'' . $geneName . '\', \'' . $strand . '\', ' . $ranges . ')" onmouseover="return overlib(\'View cDNA\');" onmouseout="return nd();">' . $geneName . '</a>, ';
         $htmlB = $htmlB . '<a href="javascript: featureExtractor(\'uppercase\', \'' . $geneName . '\', \'' . $strand . '\', ' . $ranges . ')" onmouseover="return overlib(\'View exons\');" onmouseout="return nd();">' . $geneName . '</a>, ';
      }
   }
   $htmlA =~ s/\, $//;
   $htmlB =~ s/\, $//;
   return $htmlA . $htmlAEnd . $htmlB . $htmlBEnd;
}

return 1;
