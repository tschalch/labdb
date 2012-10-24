#!/usr/bin/perl
# Sequence.pm
# the Sequence class. Sequence objects have an accession number, a description, a type, a sequence, and an array of Feature objects.
# Written by Paul Stothard

package Sequence;

use strict;
use warnings;
use Sequence;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { accession => "",
                description => "",
                type => "",
                sequence => "",
                features => []};
   bless ( $self, $class );
   return $self;
}

sub setAccession {
   my $self = shift();
   $self->{ accession } = shift();
}

sub getAccession {
   my $self = shift();
   return $self->{ accession };
}

sub setDescription {
   my $self = shift();
   $self->{ description } = shift();
}

sub getDescription {
   my $self = shift();
   return $self->{ description };
}

sub setType {
   my $self = shift();
   $self->{ type } = shift();
}

sub getType {
   my $self = shift();
   return $self->{ type };
}

sub setSequence {
   my $self = shift();
   $self->{ sequence } = shift();
}

sub getSequence {
   my $self = shift();
   return $self->{ sequence };
}

sub addFeature {
   my $self = shift();
   my $newFeature = shift();
   my $arrayReference = $self->{ features };
   push(@$arrayReference, $newFeature);
}

sub getArrayOfFeatures {
   my $self = shift();
   return $self->{ features };
}

return 1;
