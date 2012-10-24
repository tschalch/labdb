#!/usr/bin/perl
# Html_group.pm
# the Html_group class. Html_group objects have a name, a description, and a hash table of arrays of Html objects. The keys for the hash table are the positions in the DNA sequence. For example, all the features starting at base 1 are stored in an array stored under the key value 1 in the hash table. Features are added and removed to the Html_group using their position and the methods pushHtmlObject and popHtmlObject.
# Written by Paul Stothard

package Html_group;

use strict;
use warnings;
use Html;
use CGI::Carp qw(fatalsToBrowser);

sub new {
   my $object = shift();
   my $class = ref( $object ) || $object;
   my $self = { name => "",
                description => "",
                htmlObjects => { 1 => []}};
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

sub pushHtmlObject {
   my $self = shift();
   my $newObject = shift();
   my $position = shift();
   if (exists (${$self->{ htmlObjects }}{$position})) {
      push(@{${$self->{ htmlObjects }}{$position}}, $newObject);
   }
   else {
      ${$self->{ htmlObjects }}{$position} = [];
      push(@{${$self->{ htmlObjects }}{$position}}, $newObject);
   }
}

sub popHtmlObject {
   my $self = shift();
   my $position = shift();
   if (exists (${$self->{ htmlObjects }}{$position})) {
      if (scalar(@{${$self->{ htmlObjects }}{$position}}) != 0 ) {
         my $returnObject = pop(@{${$self->{ htmlObjects }}{$position}});
         if (scalar(@{${$self->{ htmlObjects }}{$position}}) == 0 ) {
            delete (${$self->{ htmlObjects }}{$position});
         }
         return $returnObject;
      }
      else {
         return 0;
      }
   }
   else {
      return 0;
   }
}

return 1;
