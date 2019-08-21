#!/usr/bin/perl
# translate.pm
# the translate module. This module accepts an array of nucleotides and a reading frame, and a genetic code selection. It obtains the genetic code from the constants module.
# Written by Paul Stothard

package translate;

use strict;
use warnings;
use constants;
use CGI::Carp qw(fatalsToBrowser);

sub translate {
   my $foundMatch = 0;
   my $tempString = "";
   my @arrayOfBases = @{shift()};
   my $lengthCoding = scalar(@arrayOfBases);
   my $readingFrame = shift();
   my $geneticCodeSelection = shift();
   my @arrayOfPatterns = ();
   my @arrayOfResults = ();
   my @arrayOfAminos = ();
   my %geneticCode = ( A => "",
                       C => "",
                       D => "",
                       E => "",
                       F => "",
                       G => "",
                       H => "",
                       I => "",
                       K => "",
                       L => "",
                       M => "",
                       N => "",
                       P => "",
                       Q => "",
                       R => "",
                       S => "",
                       T => "",
                       V => "",
                       W => "",
                       Y => "",
                       Z => "");
   my %readingFrameIndents = ( rf1r0 => "",
                               rf2r0 => " ",
                               rf3r0 => "  ",
                               rf1r2 => "",
                               rf2r2 => " ",
                               rf3r2 => "  ",
                               rf1r1 => "",
                               rf2r1 => " ",
                               rf3r1 => "  ",
                               rfm1r0 => "  ",
                               rfm2r0 => "    ",
                               rfm3r0 => "   ",
                               rfm1r2 => "    ",
                               rfm2r2 => "   ",
                               rfm3r2 => "  ",
                               rfm1r1 => "   ",
                               rfm2r1 => "  ",
                               rfm3r1 => "    ");
   if (constants::getGeneticCode($geneticCodeSelection) =~ m/[A-Za-z\[\]\|]+\=[A-Za-z][\,\s]/)	{
      my @fileSplit = split(/\,[\s]*/, constants::getGeneticCode($geneticCodeSelection));
      foreach(@fileSplit) {
         if ($_ =~ m/^\s*([A-Za-z\[\]\|]+)\=([A-Za-z])$/) {
            $geneticCode{$2} = $1;
         }
      }
   }
   my @keys = keys(%geneticCode);

   foreach(@keys) {
      if (!(exists($geneticCode{$_}))) {
         die ("An amino acid was not specified by the genetic code.");
      }
      push(@arrayOfPatterns, $geneticCode{$_});
      push(@arrayOfResults, $_);
   }

   if ($readingFrame =~ m/m/) {
      @arrayOfBases = reverse(@arrayOfBases);
      $tempString = join ("", @arrayOfBases);
      $tempString =~ tr/A-Z/a-z/;
      $tempString =~ tr/garkbdctymvhu/ctymvhgarkbda/;
      @arrayOfBases = split(/\b|\B/, $tempString);
   }
   if ($readingFrame =~ m/2/i) {
      $tempString = shift(@arrayOfBases);
   }
   if ($readingFrame =~ m/3/i) {
      $tempString = shift(@arrayOfBases);
      $tempString = shift(@arrayOfBases);
   }
   for (my $i = 0; $i < @arrayOfBases - 2; $i = $i + 3) {
   	  $foundMatch = 0;
      my $codon = $arrayOfBases[$i] . $arrayOfBases[$i+1] . $arrayOfBases[$i+2];
      for (my $j = 0; $j < @arrayOfPatterns; $j = $j + 1) {
         if ($codon =~ m/$arrayOfPatterns[$j]/i) {
            $foundMatch = 1;
            push (@arrayOfAminos, $arrayOfResults[$j]);
            last;
         }
      }
      if (!$foundMatch) {
         push (@arrayOfAminos, 'X');
      }
   }
   if ($readingFrame =~ m/m/) {
      @arrayOfAminos = reverse(@arrayOfAminos);
   }
   $tempString = join ("  ", @arrayOfAminos);
   @arrayOfAminos = split(/\b|\B/, $tempString);
   if ($readingFrame =~ m/(m*\d)/) {
      my $indexValue = "rf" . $1 . "r" . ($lengthCoding%3);
      unshift(@arrayOfAminos, $readingFrameIndents{$indexValue});
   }
   return join("",@arrayOfAminos);
}

return 1;
