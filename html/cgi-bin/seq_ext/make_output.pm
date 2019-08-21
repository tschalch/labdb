#!/usr/bin/perl
# make_output.pm
# the make_output module. This module converts an Html_group object into a an html file.
# Written by Paul Stothard

package make_output;

use strict;
use warnings;
use Html;
use Html_group;
use CGI::Carp qw(fatalsToBrowser);

sub makeOutput {
   my $htmlGroup = shift();
   my $sequenceDescription = shift();
   my $dnaSequence = shift();
   my $totalTranslations = shift();
   my $dnaSequenceLength = length($dnaSequence);
   my $restSummary = '<br />' . shift();
   my $primerSummary = '<br />' . shift();
   my $cdsLinks = shift();
   my $transLinks = shift();
   my $htmlInfo = shift();
   my $optionInfo = shift();
   my $basePerLine = shift();
   my $nbsp = " ";
   my $currentBase = 1;
   my $lineStart = 1;
   my $brokenLabels = 0;
   my $currentHtml = "";
   my @outputArray = (
      [$lineStart, "", 8, []],
   );
   my @spacingArray = ($nbsp);
   my @brokenTags = ();
   our $out = "";
   my $javaScript = q(<script type="text/javascript"> 
restOne = ""; 
restTwo = "";
primerCharsOne = ["","",""];
primerCharsTwo = ["","",""];);

$javaScript = $javaScript . "\n" . 'dnaSequence = "' . $dnaSequence . '";' . "\n";
$javaScript = $javaScript . 'totalTranslations = "' . $totalTranslations . '";' . "\n";

my $javaScriptFunctions = q(function complement (dnaSequence)	{
    dnaSequence = dnaSequence.replace(/g/g,"1");
    dnaSequence = dnaSequence.replace(/c/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"c");
    dnaSequence = dnaSequence.replace(/2/g,"g");
    dnaSequence = dnaSequence.replace(/G/g,"1");
    dnaSequence = dnaSequence.replace(/C/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"C");
    dnaSequence = dnaSequence.replace(/2/g,"G");	

    dnaSequence = dnaSequence.replace(/a/g,"1");
    dnaSequence = dnaSequence.replace(/t/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"t");
    dnaSequence = dnaSequence.replace(/2/g,"a");
    dnaSequence = dnaSequence.replace(/A/g,"1");
    dnaSequence = dnaSequence.replace(/T/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"T");
    dnaSequence = dnaSequence.replace(/2/g,"A");

    dnaSequence = dnaSequence.replace(/r/g,"1");
    dnaSequence = dnaSequence.replace(/y/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"y");
    dnaSequence = dnaSequence.replace(/2/g,"r");
    dnaSequence = dnaSequence.replace(/R/g,"1");
    dnaSequence = dnaSequence.replace(/Y/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"Y");
    dnaSequence = dnaSequence.replace(/2/g,"R");	

    dnaSequence = dnaSequence.replace(/k/g,"1");
    dnaSequence = dnaSequence.replace(/m/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"m");
    dnaSequence = dnaSequence.replace(/2/g,"k");
    dnaSequence = dnaSequence.replace(/K/g,"1");
    dnaSequence = dnaSequence.replace(/M/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"M");
    dnaSequence = dnaSequence.replace(/2/g,"K");

    dnaSequence = dnaSequence.replace(/b/g,"1");
    dnaSequence = dnaSequence.replace(/v/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"v");
    dnaSequence = dnaSequence.replace(/2/g,"b");
    dnaSequence = dnaSequence.replace(/B/g,"1");
    dnaSequence = dnaSequence.replace(/V/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"V");
    dnaSequence = dnaSequence.replace(/2/g,"B");

    dnaSequence = dnaSequence.replace(/d/g,"1");
    dnaSequence = dnaSequence.replace(/h/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"h");
    dnaSequence = dnaSequence.replace(/2/g,"d");
    dnaSequence = dnaSequence.replace(/D/g,"1");
    dnaSequence = dnaSequence.replace(/H/g,"2");
    dnaSequence = dnaSequence.replace(/1/g,"H");
    dnaSequence = dnaSequence.replace(/2/g,"D");
            	
    return dnaSequence;
}

function featureExtractor (outputType, sequenceName, strand, arrayOfRanges) {
    var dnaToAdd = "";
    var sequenceBeforeAddition = "";
    var sequenceAfterAddition = "";
    var featureSequence = "";
    var extraInfo = "";
    var length = 0;
    if (outputType == "uppercase") {
        featureSequence = dnaSequence.toLowerCase();
        extraInfo = "<br />Coding segments are shown in uppercase, other regions in lowercase.";
    }
    for (var i = 0; i < arrayOfRanges.length; i++) {
        var digitArray = arrayOfRanges[i].split(/\.\./);
        var realStart = parseInt(digitArray[0]) - 1;
        var realStop = parseInt(digitArray[1]);
        if (realStart > realStop) {
	    featureSequence = "There was a problem with this coding sequence.";
	    break;
	}
        if ((realStart > dnaSequence.length) || (realStop > dnaSequence.length)) {
	    featureSequence = "There was a problem with this coding sequence.";
	    break;
	}
	dnaToAdd = dnaSequence.substring(realStart,realStop);
        if (outputType == "joined") {
	    featureSequence = featureSequence + dnaToAdd;
	}
        else {
	    sequenceBeforeAddition = featureSequence.substring(0,realStart);
	    sequenceAfterAddition = featureSequence.substring(realStop,featureSequence.length);
	    featureSequence = sequenceBeforeAddition + dnaToAdd.toUpperCase() + sequenceAfterAddition;
	}
	length = length + dnaToAdd.length;
    }
    if (strand == 'reverse') {
        featureSequence = reverse(complement(featureSequence));
    }
    windowWriter('Feature Window', '<b>Sequence shown for: </b>' + sequenceName + '.<br /><b>Length: </b>' + length + ' bp.' + extraInfo, featureSequence);
}

function reverse (sequence) {	
    var tempDnaArray = new Array;
    if (sequence.search(/./) != -1) {
        tempDnaArray = sequence.match(/./g);
        tempDnaArray = tempDnaArray.reverse();
        sequence = tempDnaArray.join("");
    }
    return sequence;
}

function showPrimerSequence (primerChars) {
    var tempOne = ["","",""];
    var tempTwo = ["","",""];
    var swap = ["","",""];
    var length = 0;
    var sequenceToReturn = "";
    restOne = ""; 
    restTwo = "";
    if (primerCharsOne[0] == "") {
        primerCharsOne[0] = primerChars[0];
        primerCharsOne[1] = primerChars[1];
        primerCharsOne[2] = parseInt(primerChars[2]);
        window.status = primerChars[0] + " primer position = " + primerChars[2];
    }
    else {
        primerCharsTwo[0] = primerChars[0];
        primerCharsTwo[1] = primerChars[1];
        primerCharsTwo[2] = parseInt(primerChars[2]);
        window.status = primerChars[0] + " primer position = " + primerChars[2];
    }
    if ((primerCharsOne[0] != "") && (primerCharsTwo[0] != "")) {
        tempOne = primerCharsOne;
        tempTwo = primerCharsTwo;
        if (tempOne[2] > tempTwo[2]) {
	    swap = tempTwo;
	    tempTwo = tempOne;
	    tempOne = swap;
	}
        swap = primerCharsOne;
        primerCharsOne = primerCharsTwo;
        primerCharsTwo = swap;
        var forwardPrimerSequence = tempOne[1];
        var reversePrimerSequence = tempTwo[1];
        var forwardPrimerLength = forwardPrimerSequence.length;
        var reversePrimerLength = reversePrimerSequence.length;
        if ((tempOne[0] == "forward") && (tempTwo[0] == "reverse") && ((tempOne[2] + forwardPrimerLength) <= tempTwo[2] )) {
	    primerCharsOne = ["","",""];
	    primerCharsTwo = ["","",""];
	    sequenceToReturn = forwardPrimerSequence + dnaSequence.substring(tempOne[2] - 1 + forwardPrimerLength, tempTwo[2] - 1) + reversePrimerSequence;
	    length = sequenceToReturn.length;
	    windowWriter('PCR Product Window', '<b>PCR product from: </b>' + tempOne[2] + ' to ' + (tempTwo[2] + reversePrimerLength - 1) + '.<br /><b>Length: </b>' + length + ' bp.<br />The sequence of the PCR primers replaces the template sequence.', sequenceToReturn);
	}
    }
}
   
function showRestSequence (position)	{
    var tempOne = 0;
    var tempTwo = 0;
    var swap = 0;
    var length = 0;
    var sequenceToReturn = "";
    primerCharsOne = ["","",""];
    primerCharsTwo = ["","",""];
    if (restOne == "") {
        restOne = parseInt(position);
        window.status = "First site position = " + restOne;
    }
    else {
        restTwo = parseInt(position);
        window.status = "Second site position = " + restTwo;
    }
    if ((restOne != "") && (restTwo != "")) {
        tempOne = restOne;
        tempTwo = restTwo;
        if (tempOne > tempTwo) {
	    swap = tempTwo;
	    tempTwo = tempOne;
	    tempOne = swap;
	}
        if (tempOne != tempTwo) {
	    restOne = "";
	    restTwo = "";
	    sequenceToReturn = dnaSequence.substring(tempOne - 1, tempTwo - 1);
	    length = sequenceToReturn.length;
	    windowWriter('Restriction Fragment Window', '<b>Restriction fragment from: </b>' + tempOne + ' to ' + (tempTwo - 1) + '.<br /><b>Length: </b>' + length + ' bp.', sequenceToReturn);
	}
    }
}

function showTrans (name, start, stop) {
    var length = 0;
    var sequenceToReturn = "";
    sequenceToReturn = totalTranslations.substring(start,stop);
    length = sequenceToReturn.length;
    windowWriter ('Translation Window', '<b>Translation for: </b>' + name + '.<br /><b>Length: </b>' + length + ' amino acids.', sequenceToReturn);
}

function windowWriter (title, caption, output) {
    outputWindow=window.open("","my_new_window","toolbar=no, location=no, directories=no, status=yes, menubar=yes, scrollbars=yes, resizable=yes, copyhistory=no, width=800, height=400");
    outputWindow.focus();
    outputWindow.status = 'Please wait...';
    outputWindow.document.write ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n" +
			        "<html lang=\"en\">\n" +
				"<head>\n" +
                                "<title>" + title + "</title>\n" +                         
                                "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n" +
                                "<style type=\"text/css\">\n" +
                                "body.main {font-family: arial, sans-serif; color: #000000; background-color: #FFFFFF;}\n" +
                                "div.title {font-size: xx-large; color: #000000; text-align: left; background-color: #FFFFFF}\n" +
				"div.copyright {font-size: xx-small; color: #000000}\n" +
                                "</style>\n" +
                                "</head>\n" +
                                "<body class=\"main\">\n" +
                                "<div class=\"title\">" + title + "</div>\n");
    outputWindow.document.write (caption + "<br />\n");
    outputWindow.document.write ("<pre>\n");
    var re = /(.{60})/g;
    output = output.replace(re, "$1\n");
    outputWindow.document.write (output);
    outputWindow.document.write ("\n\n</pre>\n");
    outputWindow.document.write ("<a href=\"http://validator.w3.org/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://www.w3.org/Icons/valid-xhtml10\" alt=\"Valid XHTML 1.0!\" height=\"31\" width=\"88\" /></a>\n");
    outputWindow.document.write ("<a href=\"http://jigsaw.w3.org/css-validator/\"><img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\" /></a>\n");
    outputWindow.document.write ("</body>\n</html>\n");
    outputWindow.status = 'Done.';
    outputWindow.document.close();
});

   my $javaScriptFooter = "\n</script>\n";

   $javaScript = $javaScript . $javaScriptFunctions . $javaScriptFooter;
   #$javaScript =~ s/ +/ /g;
   #$javaScript =~ s/[\f\n\r\t]//g;

   $out .= "\n<!-- Begin JavaScript for figure interaction -->\n";

   $out .= ($javaScript);

   $out .= "<!-- End JavaScript for figure interaction -->\n\n";

   #use overlib library for mouseover
   $out .= "<script type=\"text/javascript\" src=\"http://localhost/seqext/includes/overlib.js\"><!-- overLIB (c) Erik Bosrup --></script>\n";

   $out .= "</head>\n" .
      "<body class=\"main\">\n" .
      "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n" .
      "<table width=\"650\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\" align=\"left\">\n" .
      "<tbody>\n" .
      "<tr>\n" .
      "<td class=\"sms\">Sequence Extractor - Results</td>\n" .
      "</tr>\n" .
      "<tr>\n" .
      "<td>\n";   


   $out .= ("<span class=\"sequence_summary\">$sequenceDescription </span><br />\n");
   $out .= ("<ul>\n");
   $out .= ($htmlInfo);
   $out .= ($cdsLinks);
   $out .= ($transLinks);
   $out .= ($optionInfo);
   if ($htmlInfo eq "") {
       $out .= ("<li>Mouse over items on the map to view additional information. Click on two restriction sites or two compatible PCR primers to generate a product (requires JavaScript).</li>\n");
   }
   $out .= ("</ul>\n");
   $out .= ("<div class=\"pre\">\n");

   for (my $i = 1; $i <= $basePerLine; $i = $i + 1) {
      $spacingArray[$i] = $nbsp;
   }
   do {
      my $strings = "";
      my $returnString = "";
      $currentHtml = $htmlGroup->popHtmlObject($currentBase);
      while ($currentHtml != 0) {
         my $actualText = $currentHtml->getActualText();
         my $actualTextLength = length($actualText);
         my $startTags = $currentHtml->getStartTags();
         my $endTags = $currentHtml->getEndTags();
         my $priority = $currentHtml->getPriority();
         my $nonBreaking = $currentHtml->getNonBreaking();
         my $spacingString = "";
         my @tempSpacingArray = @spacingArray;
         my @tempArray = ();
         for (my $i = 0; $i < @outputArray; $i = $i + 1) {
            if (($outputArray[$i][0] <= $currentBase) && ($outputArray[$i][2] == $priority)) {
               if ($nonBreaking) {
                  @tempArray = splice (@tempSpacingArray, 0, $currentBase - $outputArray[$i][0]);
                  $spacingString = join ("", @tempArray); 
                  $outputArray[$i][0] = $currentBase + $actualTextLength + 2;
                  $actualText =~ s/ /$nbsp/g;
                  push(@{$outputArray[$i][3]}, $spacingString);
                  push(@{$outputArray[$i][3]}, $nbsp);
                  push(@{$outputArray[$i][3]}, $startTags);
                  push(@{$outputArray[$i][3]}, $actualText); #
                  push(@{$outputArray[$i][3]}, $endTags);
                  push(@{$outputArray[$i][3]}, $nbsp);
                  last;
               }
               elsif ($currentBase + $actualTextLength < $lineStart + $basePerLine) {
                  @tempArray = splice (@tempSpacingArray, 0, $currentBase - $outputArray[$i][0]);
                  $spacingString = join ("", @tempArray); 
                  $outputArray[$i][0] = $currentBase + $actualTextLength + 2;
                  $actualText =~ s/ /$nbsp/g;
                  push(@{$outputArray[$i][3]}, $spacingString);
                  push(@{$outputArray[$i][3]}, $nbsp);
                  push(@{$outputArray[$i][3]}, $startTags);
                  push(@{$outputArray[$i][3]}, $actualText);
                  push(@{$outputArray[$i][3]}, $endTags);
                  push(@{$outputArray[$i][3]}, $nbsp);
                  last;
               }
               else {
                  @tempArray = splice (@tempSpacingArray, 0, $currentBase - $outputArray[$i][0]);
                  $spacingString = join ("", @tempArray); 
                  my $beginningOfActualText = substr($actualText, 0, $lineStart + $basePerLine - $currentBase, "");
                  $outputArray[$i][0] = $currentBase + length($beginningOfActualText) + 1;
                  $beginningOfActualText =~ s/ /$nbsp/g;
                  push(@{$outputArray[$i][3]}, $spacingString);
                  push(@{$outputArray[$i][3]}, $nbsp);
                  push(@{$outputArray[$i][3]}, $startTags);
                  push(@{$outputArray[$i][3]}, $beginningOfActualText);
                  push(@{$outputArray[$i][3]}, $endTags);
                  my $newHtmlObject = new Html;
                  $newHtmlObject->setActualText($actualText);
                  $newHtmlObject->addStartTag($startTags);
                  $newHtmlObject->addEndTag($endTags);
                  $newHtmlObject->setPriority($priority);
                  $newHtmlObject->setNonBreaking($nonBreaking);
                  $brokenTags[$i] = $newHtmlObject;
                  $brokenLabels = 1;
                  last;
               }
            }
            elsif (scalar(@outputArray) == $i + 1) {
               push (@outputArray, [$lineStart, "", $priority, []]);
            }
         }   
         $currentHtml = $htmlGroup->popHtmlObject($currentBase);
      }

      if ($currentBase%$basePerLine == 0) {
         my $tempLength = scalar(@outputArray);
         @brokenTags = reverse(@brokenTags);
         for (my $k = 0; $k < @brokenTags; $k = $k + 1) {
            if (defined($brokenTags[$k])) {
               $htmlGroup->pushHtmlObject($brokenTags[$k], $basePerLine + $lineStart);
            }
         }
         $brokenLabels = 0;
         @brokenTags = ();
         $lineStart = $currentBase + 1;
         @outputArray = reverse(@outputArray);
         for (my $j = 1; $j <= 16; $j = $j + 1) {
            for (my $i = 0; $i < @outputArray; $i = $i + 1) {
               if ($outputArray[$i][2] == $j) {
                  $out .= (join("",@{$outputArray[$i][3]}) . "\n"); #
               }
            }
         }
         @outputArray = ([$lineStart, "", 8, []]);
      }
      $currentBase = $currentBase + 1;
   } until (($currentBase > $dnaSequenceLength + $basePerLine) && ($brokenLabels == 0));
   @outputArray = reverse(@outputArray);
   for (my $j = 1; $j <= 16; $j = $j + 1) {
      for (my $i = 0; $i < @outputArray; $i = $i + 1) {
         if ($outputArray[$i][2] == $j) {
            $out .= (join("",@{$outputArray[$i][3]}) . "\n"); #
         }
      }
   }
   $out .= ("</div>\n");
   $out .= ($restSummary);
   $out .= ($primerSummary);
   return $out;
}

return 1;
