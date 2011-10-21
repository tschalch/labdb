#use this to finish the replacements done by my other scripts that make .new files.
use warnings;
use strict;

my $dir="./";

opendir(DIR, $dir) || die("Cannot open directory");

my @files= readdir(DIR);

foreach (@files) {
    if ($_ =~ m/\.html$/) {
	#example: index.html
        #move files like index.html to index.html.trash
	my $firstMove = "mv $_ $_.trash";
	my $firstMoved = system($firstMove);
	if ($firstMoved == 0) {
	    print "moved $_ to $_.trash\n";
	}
	#now move its twin index.html.new (created by another script) to index.html
	my $secondMove = "mv $_.new $_";
	my $secondMoved = system($secondMove);
	if ($secondMoved == 0) {
	    print "moved $_.new to $_\n";
	}
	else {
	    die ("could not move $_.new to $_");
	}
    }
}

closedir(DIR);
