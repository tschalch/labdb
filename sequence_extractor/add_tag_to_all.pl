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

	    if ($line =~ m/<td class=\"link_bar\"><a href=\"index\.html\">/) {
		print (OUTFILE '<td class="link_bar"><a href="index.html">Main</a> | <a href="features.html">Features</a> | <a href="help.html">Help</a> | <a href="download.html">Download</a> | <a href="license.html">License</a> | <a href="about.html">About</a></td>' . "\n");
		
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
