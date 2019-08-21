#!/usr/bin/perl
# PatternGroup.pm
# the PatternGroup class. PatternGroup objects have a name, a description, and an array of Pattern objects.
# Written by Paul Stothard

package PatternGroup;

use strict;
use warnings;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { name => "",
                description => "",
                patterns => []};
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

sub setDescription {
   my $self = shift();
   $self->{ description } = shift();
}

sub getDescription {
   my $self = shift();
   return $self->{ description };
}

sub addPattern {
   my $self = shift();
   my $newPattern = shift();
   my $arrayReference = $self->{ patterns };
   push(@$arrayReference, $newPattern);
}

sub getArrayOfPatterns {
   my $self = shift();
   return $self->{ patterns };
}

return 1;
