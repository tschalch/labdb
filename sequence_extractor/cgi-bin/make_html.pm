#!/usr/bin/perl
# make_html.pm
# the make_html module. This module converts sequence features into html objects. It returns an Html_group of Html objects.
# Written by Paul Stothard

package make_html;

use strict;
use warnings;
use Html;
use Html_group;
use CGI::Carp qw(fatalsToBrowser);

sub makeHtml {
   my %priority = ( specialLinkRestStart => 1,
		    specialLinkRestEnd => 1,
		    specialLinkPCRStart => 1,
		    specialLinkPCREnd => 1,
                    forwardPrimer => 2,
                    restrictionSite => 3,
                    translationForReadingFrame3 => 4,
                    translationForReadingFrame2 => 5,
                    translationForReadingFrame1 => 6,
                    forwardTranslation => 7,
                    forwardDna => 8,
                    number => 9,
                    reverseDna => 10,
                    reverseTranslation => 11,
                    translationForReadingFramem1 => 12,
                    translationForReadingFramem2 => 13,
                    translationForReadingFramem3 => 14,
                    reversePrimer => 2,
                    spacerLine => 16);

   my %startTag = ( specialLinkRestStart => "<span class=\"special_link_rest_start\">",
		    specialLinkRestEnd => "<span class=\"special_link_rest_end\">",
		    specialLinkPCRStart => "<span class=\"special_link_PCR_start\">",
		    specialLinkPCREnd => "<span class=\"special_link_PCR_end\">",
                    forwardPrimer => "<span class=\"forward_primer\">",
                    restrictionSite => "<span class=\"restriction_site\">",
                    translationForReadingFrame3 => "<span class=\"rf_3\">",
                    translationForReadingFrame2 => "<span class=\"rf_2\">",
                    translationForReadingFrame1 => "<span class=\"rf_1\">",
                    forwardTranslation => "<span class=\"forward_translation\">",
                    forwardDna => "<span class=\"forward_DNA\">",
                    number => "<span class=\"number\">",
                    reverseDna => "<span class=\"reverse_DNA\">",
                    reverseTranslation => "<span class=\"reverse_translation\">",
                    translationForReadingFramem1 => "<span class=\"rf_m1\">",
                    translationForReadingFramem2 => "<span class=\"rf_m2\">",
                    translationForReadingFramem3 => "<span class=\"rf_m3\">",
                    reversePrimer => "<span class=\"reverse_primer\">",
                    spacerLine => "<span class=\"spacer_line\">");

   my %endTag = (   specialLinkRestStart => "</span>",
		    specialLinkRestEnd => "</span>",
		    specialLinkPCRStart => "</span>",
		    specialLinkPCREnd => "</span>",
                    forwardPrimer => "</span>",
                    restrictionSite => "</span>",
                    translationForReadingFrame3 => "</span>",
                    translationForReadingFrame2 => "</span>",
                    translationForReadingFrame1 => "</span>",
                    forwardTranslation => "</span>",
                    forwardDna => "</span>",
                    number => "</span>",
                    reverseDna => "</span>",
                    reverseTranslation => "</span>",
                    translationForReadingFramem1 => "</span>",
                    translationForReadingFramem2 => "</span>",
                    translationForReadingFramem3 => "</span>",
                    reversePrimer => "</span>",
                    spacerLine => "</span>");

   my %nonBreak = ( specialLinkRestStart => 1,
		    specialLinkRestEnd => 1,
		    specialLinkPCRStart => 1,
		    specialLinkPCREnd => 1,
                    forwardPrimer => 0,
                    restrictionSite => 1,
                    translationForReadingFrame3 => 0,
                    translationForReadingFrame2 => 0,
                    translationForReadingFrame1 => 0,
                    forwardTranslation => 0,
                    forwardDna => 0,
                    number => 0,
                    reverseDna => 0,
                    reverseTranslation => 0,
                    translationForReadingFramem1 => 0,
                    translationForReadingFramem2 => 0,
                    translationForReadingFramem3 => 0,
                    reversePrimer => 0,
                    spacerLine => 0);

   my $sequenceObject = shift();
   my @features = @{$sequenceObject->getArrayOfFeatures()};
   my $htmlGroup = new Html_group;
   foreach(@features) {
      my $htmlObject = new Html;
      my $featureType = $_->getType();
      my $featurePosition = $_->getPosition();
      my $featureLabel = $_->getLabelToDisplay();
      my $featureName = $_->getName();
      my $existingStartTags = $_->getStartTags();
      my $existingEndTags = $_->getEndTags();
      if ($featurePosition > 0) {
         #$htmlObject->addStartTag($existingStartTags);
         #$htmlObject->addEndTag($existingEndTags);
         $htmlObject->setActualText($featureLabel . " " . $featureName);
         if (exists ($priority{$featureType})) {
            $htmlObject->setPriority($priority{$featureType});
         }
         if (exists ($startTag{$featureType})) {
            $htmlObject->addStartTag($startTag{$featureType});
         }
         if (exists ($endTag{$featureType})) {
            $htmlObject->addEndTag($endTag{$featureType});
         }
         if (exists ($nonBreak{$featureType})) {
            $htmlObject->setNonBreaking($nonBreak{$featureType});
	 }
         $htmlObject->addStartTag($existingStartTags);
         $htmlObject->addEndTag($existingEndTags);

         $htmlGroup->pushHtmlObject($htmlObject,$featurePosition);
      }
   }
   return $htmlGroup;
}

return 1;
