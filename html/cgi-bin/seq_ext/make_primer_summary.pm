#!/usr/bin/perl
# make_primer_summary.pm
# the make_primer_summary module. This module generates an html table summarizing the positions of annealing sites for each primer pattern. It returns a string of html code.
# Written by Paul Stothard

package make_primer_summary;

use strict;
use warnings;
use Sequence;
use Feature;
use CGI::Carp qw(fatalsToBrowser);

sub makePrimerSummary {
   my %primerSites = ();
   my $sequenceObject = shift();
   my $openTags = '';
   my $closeTags = '';
   my $htmlTableHead = "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n<tbody>\n<tr>\n<td class=\"summary_title\">" . $openTags . "Primer Summary" . $closeTags . "</td>\n</tr>\n";
   my $htmlTableTail = "</tbody>\n</table>\n";
   my $htmlToReturn = "";
   my @arrayOfSitesChecked = [];
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   foreach(@features) {
      my $featureType = $_->getType();
      my $sitesChecked = $_->getLabelToDisplay();
      if ($featureType eq "primersChecked") {
         @arrayOfSitesChecked = split(/\,/, $sitesChecked);
         last;
      }
   }

   foreach(@arrayOfSitesChecked) {
      if ($_ =~ m/^([^\/]+)\/\s+([^\/]+)/) {
         if (exists ($primerSites{$1})) {
            my $arrayReference = $primerSites{$1};
            my $existingSequences = $$arrayReference[0];
            $existingSequences = $existingSequences . ', ' . $2;
            $$arrayReference[0] = $existingSequences;
         }
         else {
            $primerSites{$1} = [$1 . ' ' . $2 , 'none'];
         }
      }
   }
      
   foreach(@features) {
      my $featureType = $_->getType();
      my $featurePosition = $_->getPosition();
      my $featureName = $_->getName();
      my $startTags = $_->getStartTags();
      if ($featureType eq "forwardPrimer") {
         if (exists ($primerSites{$featureName})) {
            my $arrayReference = $primerSites{$featureName};
            if ($startTags =~ m/Position\=(\d+)/) {
               push(@$arrayReference, $1);
            }
            else {
               die ("The startTags attribute for the primer does not contain the 'Position=' keyword.");
            }
         }
         else {
            die ("There was a problem building the primer summary.");
         }
      }
      if ($featureType eq "reversePrimer") {
         if (exists ($primerSites{$featureName})) {
            my $arrayReference = $primerSites{$featureName};
            if ($startTags =~ m/Position\=(\d+)/) {
               push(@$arrayReference, '-' . $1);
            }
            else {
               die ("The startTags attribute for the primer does not contain the 'Position=' keyword.");
            }
         }
         else {
            die ("There was a problem building the primer summary.");
         }
      }
   }

   my @keys = keys(%primerSites);
   @keys = sort @keys;
   my $i = 0;
   for ($i = 0; $i < @keys; $i = $i + 1) {
      my $arrayReference = $primerSites{$keys[$i]};
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
