#!/usr/bin/perl
# SearchPattern.pm
# The SearchPattern class. A SearchPattern object contains a labelToDisplay, a regularExpression, a resultOffset, a type, and mouseOver text.
# Written by Paul Stothard

package SearchPattern;

use strict;
use warnings;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { labelToDisplay => "",
                regularExpression => "",
                type => "",
                resultOffset => "",
                mouseOver => "" };
   bless ( $self, $class );
   return $self;
}

sub setLabelToDisplay {
   my $self = shift();
   $self->{ labelToDisplay } = shift();
}

sub getLabelToDisplay {
   my $self = shift();
   return $self->{ labelToDisplay };
}

sub setRegularExpression {
   my $self = shift();
   $self->{ regularExpression } = shift();
}

sub getRegularExpression {
   my $self = shift();
   return $self->{ regularExpression };
}

sub setResultOffset {
   my $self = shift();
   $self->{ resultOffset } = shift();
}

sub getResultOffset {
   my $self = shift();
   return $self->{ resultOffset };
}

sub setType {
   my $self = shift();
   $self->{ type } = shift();
}

sub getType {
   my $self = shift();
   return $self->{ type };
}

sub setMouseOver {
   my $self = shift();
   $self->{ mouseOver } = shift();
}

sub getMouseOver {
   my $self = shift();
   return $self->{ mouseOver };
}

return 1;
