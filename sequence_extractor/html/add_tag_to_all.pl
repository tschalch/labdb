#use this to add a new html line to all html files in a directory
use warnings;
use strict;

my $version = "1.000";
my $dir="./";

opendir(DIR, $dir) || die("Cannot open directory");

my @files= readdir(DIR);

foreach (@files) {
    if ($_ =~ m/\.html$/) {
	print "working on file $_\n";
	#Open the input file and parse the sequences.
	open (INFILE, $_) or die( "Cannot open file for input: $!" );

	open (OUTFILE, ">" . $_ . ".new");

	while (my $line = <INFILE>) {

	    if ($line =~ m/table width="620"/) {
		print (OUTFILE '<table width="766" border="0" cellspacing="2" cellpadding="2" align="center">' . "\n");
		
	    }

	    else {
		print (OUTFILE $line);
	    }
	}

	close (INFILE) or die( "Cannot close file for input: $!");

	close (OUTFILE) or die( "Cannot close file for output: $!");
    }
}

closedir(DIR);
