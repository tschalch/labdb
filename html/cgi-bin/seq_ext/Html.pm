#!/usr/bin/perl
# Html.pm
# The Html class. An Html object contains the actualText, startTags, endTags, a priority number, and a nonBreaking attribute.
# Written by Paul Stothard

package Html;

use strict;
use warnings;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { actualText => "",
                startTags => [],
                endTags => [],
                priority => 1,
                nonBreaking => 1};
   bless ( $self, $class );
   return $self;
}

sub setActualText {
   my $self = shift();
   $self->{ actualText } = shift();
}

sub getActualText {
   my $self = shift();
   return $self->{ actualText };
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

sub setPriority {
   my $self = shift();
   $self->{ priority } = shift();
}

sub getPriority {
   my $self = shift();
   return $self->{ priority };
}

sub setNonBreaking {
   my $self = shift();
   $self->{ nonBreaking } = shift();
}

sub getNonBreaking {
   my $self = shift();
   return $self->{ nonBreaking };
}

return 1;
