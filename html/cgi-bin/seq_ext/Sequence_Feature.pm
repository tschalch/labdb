#!/usr/bin/perl
# Feature.pm
# The Feature class. A Feature object contains a name, a label for display, a position on the DNA sequence, a type, and HTML start and end tags.
# Written by Paul Stothard

package Sequence_Feature;

use strict;
use warnings;
use Sequence;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { name => "",
                labelToDisplay => "",
                position => "",
                type => "",
                startTags => [],
                endTags => []};
   bless ( $self, $class );
   return $self;
}

sub setName {
   my $self = shift();
   $self->{ name } = shift();
}

sub getName {
   my $self = shift();
   return $self->{ name };
}

sub setLabelToDisplay {
   my $self = shift();
   $self->{ labelToDisplay } = shift();
}

sub getLabelToDisplay {
   my $self = shift();
   return $self->{ labelToDisplay };
}

sub setPosition {
   my $self = shift();
   $self->{ position } = shift();
}

sub getPosition {
   my $self = shift();
   return $self->{ position };
}
   
sub setType {
   my $self = shift();
   $self->{ type } = shift();
}

sub getType {
   my $self = shift();
   return $self->{ type };
}

sub addStartTag {
   my $self = shift();
   my $newTag = shift();
   my $arrayReference = $self->{ startTags };
   push(@$arrayReference, $newTag);
}

sub getStartTags {
   my $self = shift();
   my $tempString = join ("",@{$self->{ startTags }});
   return $tempString;
}

sub addEndTag {
   my $self = shift();
   my $newTag = shift();
   my $arrayReference = $self->{ endTags };
   push(@$arrayReference, $newTag);
}

sub getEndTags {
   my $self = shift();
   my @reversed = reverse(@{$self->{ endTags }});
   my $tempString = join ("",@reversed);
   return $tempString;
}

return 1;
