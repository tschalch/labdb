#!/usr/bin/perl
# make_rest_summary.pm
# the make_rest_summary module. This module generates an html table summarizing the positions of sites for each restriction pattern. It returns a string of html code.
# Written by Paul Stothard

package make_rest_summary;

use strict;
use warnings;
use Sequence;
use Feature;
use CGI::Carp qw(fatalsToBrowser);

sub makeRestSummary {
   my %restSites = ();
   my $sequenceObject = shift();
   my $openTags = '';
   my $closeTags = '';
   my $htmlTableHead = "<table border=\"1\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n<tbody>\n<tr>\n<td class=\"summary_title\">" . $openTags . "Restriction Summary" . $closeTags . "</td>\n</tr>\n";
   my $htmlTableTail = "</tbody>\n</table>\n";
   my $htmlToReturn = "";
   my @arrayOfSitesChecked = [];
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   foreach(@features) {
      my $featureType = $_->getType();
      my $sitesChecked = $_->getLabelToDisplay();
      if ($featureType eq "sitesChecked") {
         @arrayOfSitesChecked = split(/\,/, $sitesChecked);
         last;
      }
   }

   foreach(@arrayOfSitesChecked) {
      if ($_ =~ m/^([^\/]+)\/\s+([^\/]+)/) {
         $restSites{$1} = [$2 , 'none'];
      }
   }
      
   foreach(@features) {
      my $featureType = $_->getType();
      my $featurePosition = $_->getPosition();
      my $featureLabel = $_->getLabelToDisplay();
      if ($featureType eq "restrictionSite") {
         if (exists ($restSites{$featureLabel})) {
            my $arrayReference = $restSites{$featureLabel};
            push(@$arrayReference, $featurePosition);
         }
         else {
            die ("There was a problem building the restriction summary.");
         }
      }
   }

   my @keys = keys(%restSites);
   @keys = sort @keys;
   my $i = 0;
   for ($i = 0; $i < @keys; $i = $i + 1) {
      my $arrayReference = $restSites{$keys[$i]};
      my @arrayOfResults = @$arrayReference;
      my $labelString = shift(@arrayOfResults);
      if (scalar(@arrayOfResults) == 0) {
         push(@arrayOfResults, 'none');
      }
      if (scalar(@arrayOfResults) == 1) {
         $htmlToReturn = $htmlToReturn . "<tr><td class=\"found_none\">" . $openTags . $labelString . ": " . "none" . "." . $closeTags . "</td></tr>\n"; #none
      }
      else {
         shift(@arrayOfResults);
         @arrayOfResults = sort { $a <=> $b } @arrayOfResults;
         if (scalar(@arrayOfResults) == 1) {
            $htmlToReturn = $htmlToReturn . "<tr><td class=\"found_one\">" . $openTags . $labelString . ': ' . join(', ' , @arrayOfResults) . '.' . $closeTags . "</td></tr>\n"; #one
         }
         else {
            $htmlToReturn = $htmlToReturn . "<tr><td class=\"found_many\">" . $openTags . $labelString . ': ' . join(', ' , @arrayOfResults) . '.' . $closeTags . "</td></tr>\n"; #many
         }
      }
   }
   $htmlToReturn = $htmlTableHead . $htmlToReturn . $htmlTableTail;
   return $htmlToReturn;
}

return 1;
