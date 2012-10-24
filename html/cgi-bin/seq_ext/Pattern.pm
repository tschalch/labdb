#!/usr/bin/perl
# Pattern.pm
# The Pattern class. A Pattern object contains a name, a type, and an array of SearchPattern objects.
# Written by Paul Stothard

package Pattern;

use strict;
use warnings;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { name => "",
                type => "",
                searchPatterns => [] };
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

sub setType {
   my $self = shift();
   $self->{ type } = shift();
}

sub getType {
   my $self = shift();
   return $self->{ type };
}

sub addSearchPattern {
   my $self = shift();
   my $newPattern = shift();
   my $arrayReference = $self->{ searchPatterns };
   push(@$arrayReference, $newPattern);
}

sub getArrayOfPatterns {
   my $self = shift();
   return $self->{ searchPatterns };
}

return 1;
