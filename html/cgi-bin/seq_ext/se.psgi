use CGI::PSGI;
use CGI::Carp qw(fatalsToBrowser set_message);

require 5.003;
use strict;
use warnings;

use Plack::Builder;

BEGIN {
    sub handle_errors {
        our $env = shift;
        our $q = CGI::PSGI->new($env);
        my $body = "<body class=\"main\">\n" .
        "<br />\n" .
        "$env <br />\n" .
        "<b>Email: stothard\@ualberta.ca</b><br />\n" .
        "<b>Sequence Extractor version 1.0</b><br />\n" .
        "</body>\n";
        return [
        '200',
            $q->psgi_header('text/html'),
            [ $body ],
            ];
    };
    set_message(\&handle_errors);
}

$CGI::POST_MAX=1024 * 1000;  # max 1000K posts
$CGI::DISABLE_UPLOADS = 1;  # no uploads

my $app = sub {
    our $env = shift;
    our $q = CGI::PSGI->new($env);
    our $body = '';
    our $style ='';


    my $test = $q->param("SequenceRecord");
    if (!$test && $q->cgi_error()) {
        $body = "<body>\n" .
        "There is a maximum POST limit of 1000K for this page\n" .
        "</body>\n" .
        "</html>\n";
    }

    $style = "<style type=\"text/css\">\n" .
        "body.main {font-family: arial, sans-serif; color: #000000; background-color: #FFFFFF;}\n" .
        "body.main a {color: #000099; text-decoration: none}\n" .
        "body.main a:visited {color: #000099; text-decoration: none}\n" .
        "body.main a:hover {color: #FF0000; text-decoration: underline}\n" .
        "body.main a:active {color: #FF0000; text-decoration: underline}\n" .
        "td.sms {font-size: xx-large; color: #FFFFFF; text-align: center; background-color: #6666FF}\n" .

        "div.pre {font-size: medium; color: #000000; font-family: courier, monospace; white-space: pre}\n" .
        "div.pre a {color: #000000; text-decoration: none}\n" .
        "div.pre a:visited {color: #000000; text-decoration: none}\n" .
        "div.pre a:hover {color: #000000; text-decoration: none}\n" .
        "div.pre a:active {color: #000000; text-decoration: none}\n" .

        "div.copyright {font-size: x-small; color: #000000}\n" .

        "span.special_link_rest_start a {color: #990066; text-decoration: none}\n" .
        "span.special_link_rest_start a:visited {color: #990066; text-decoration: none}\n" .
        "span.special_link_rest_start a:hover {color: #CC3399; text-decoration: none}\n" .
        "span.special_link_rest_start a:active {color: #CC3399; text-decoration: none}\n" .

        "span.special_link_rest_end a {color: #990066; text-decoration: none}\n" .
        "span.special_link_rest_end a:visited {color: #990066; text-decoration: none}\n" .
        "span.special_link_rest_end a:hover {color: #CC3399; text-decoration: none}\n" .
        "span.special_link_rest_end a:active {color: #CC3399; text-decoration: none}\n" .

        "span.special_link_PCR_start a {color: #339900; text-decoration: none}\n" .
        "span.special_link_PCR_start a:visited {color: #339900; text-decoration: none}\n" .
        "span.special_link_PCR_start a:hover {color: #99CC33; text-decoration: none}\n" .
        "span.special_link_PCR_start a:active {color: #99CC33; text-decoration: none}\n" .

        "span.special_link_PCR_end a {color: #CC0000; text-decoration: none}\n" .
        "span.special_link_PCR_end a:visited {color: #CC0000; text-decoration: none}\n" .
        "span.special_link_PCR_end a:hover {color: #FF3333; text-decoration: none}\n" .
        "span.special_link_PCR_end a:active {color: #FF3333; text-decoration: none}\n" .

        "span.forward_primer a {color: #339900}\n" .
        "span.forward_primer a:visited {color: #339900}\n" .
        "span.forward_primer a:hover {color: #99CC33}\n" .
        "span.forward_primer a:active {color: #99CC33}\n" .

        "span.restriction_site a {color: #990066}\n" .
        "span.restriction_site a:visited {color: #990066}\n" .
        "span.restriction_site a:hover {color: #CC3399}\n" .
        "span.restriction_site a:active {color: #CC3399}\n" .

        "span.forward_translation {color: #0000FF}\n" .
        "span.forward_translation a {color: #0000FF}\n" .
        "span.forward_translation a:visited {color: #0000FF}\n" .
        "span.forward_translation a:hover {color: #0000FF}\n" .
        "span.forward_translation a:active {color: #0000FF}\n" . 

        "span.forward_DNA {color: #000000}\n" .
        "span.number {color: #000000}\n" .

        "span.reverse_translation {color: #3366FF}\n" .
        "span.reverse_translation a {color: #3366FF}\n" .
        "span.reverse_translation a:visited {color: #3366FF}\n" .
        "span.reverse_translation a:hover {color: #3366FF}\n" .
        "span.reverse_translation a:active {color: #3366FF}\n" .

        "span.reverse_DNA {color: #808080}\n" .
        "span.rf_m1 {color: #3366FF}\n" .
        "span.rf_m2 {color: #3366FF}\n" .
        "span.rf_m3 {color: #3366FF}\n" .
        "span.rf_1 {color: #0000FF}\n" .
        "span.rf_2 {color: #0000FF}\n" .
        "span.rf_3 {color: #0000FF}\n" .

        "span.reverse_primer a {color: #CC0000}\n" .
        "span.reverse_primer a:visited {color: #CC0000}\n" .
        "span.reverse_primer a:hover {color: #FF3333}\n" .
        "span.reverse_primer a:active {color: #FF3333}\n" .

        "span.spacer_line {color: #000000}\n" .
        "td.found_none {color: #000000; background-color: #FFCCCC}\n" .
        "td.found_one {color: #000000; background-color: #99FF99}\n" .
        "td.found_many {color: #000000}\n" .
        "td.summary_title {font-weight: bold; color: #FFFFFF; background-color: #666666}\n" .
        "span.sequence_summary {font-size: large}\n" .
        "</style>\n";

    use Sequence;
    use make_sequence;
    use make_primers;
    use make_rest;
    use add_rest;
    use add_primers;
    use make_html;
    use make_rest_summary;
    use make_primer_summary;
    use make_cds_links;
    use make_trans_links;
    use make_total_protein;
    use make_output;

    my $sequenceRecord = '';
    my $primerList = '';
    my $geneticCodeSelection = '';
    my $restrictionSetSelection = '';
    my $readingFramesToShow = '';
    my $isCircular = '';
    my $checkForMismatchFivePrimeTails = '';
    my $checkForMismatchThreePrimeTails = '';
    my $checkForMiddleMatch = '';
    my $minimumMatch = '';
    my $basePerLine = '';
    my $showNumberLine = '';
    my $showReverseStrand = '';
    my $showSpacerLine = '';
    my $returnRestSummary = '';
    my $returnPrimerSummary = '';
    my $returnHelpInfo = '';
    my $returnCdsLinks = '';
    my $returnTransLinks = '';
    my $returnOptionsChosen = '';

    if ($q->param ("sequenceRecord")) {
        $sequenceRecord = $q->param ("sequenceRecord");
    }
    else {
        $sequenceRecord = q(gggggggggggggg);
    }

    if ($q->param ("primerList")) {
        $primerList = $q->param ("primerList");
    }
    else {
        $primerList = q();
    }

    if ($q->param ("geneticCodeSelection")) {
        $geneticCodeSelection = $q->param ("geneticCodeSelection");
    }
    else {
        $geneticCodeSelection = "standard";
    }

    if ($q->param ("restrictionSetSelection")) {
        $restrictionSetSelection = $q->param ("restrictionSetSelection");
    }
    else {
        $restrictionSetSelection = "common";
    }

    if ($q->param ("readingFramesToShow")) {
        $readingFramesToShow = $q->param ("readingFramesToShow");
    }
    else {
        $readingFramesToShow = "one";
    }

    if ($q->param ("isCircular")) {
        $isCircular = $q->param ("isCircular");
    }
    else {
        $isCircular = 0;
    }

    if ($q->param ("checkForMismatchFivePrimeTails")) {
        $checkForMismatchFivePrimeTails = 1;
    }
    else {
        $checkForMismatchFivePrimeTails = 0;
    }

    if ($q->param ("checkForMismatchThreePrimeTails")) {
        $checkForMismatchThreePrimeTails = 1;
    }
    else {
        $checkForMismatchThreePrimeTails = 0;
    }

    if ($q->param ("minimumMatch")) {
        $minimumMatch = $q->param ("minimumMatch");
    }
    else {
        $minimumMatch = 10;
    }

    if ($q->param ("basePerLine")) {
        $basePerLine = $q->param ("basePerLine");
    }
    else {
        $basePerLine = 80;
    }

    if ($q->param ("showNumberLine")) {
        $showNumberLine = $q->param ("showNumberLine");
    }
    else {
        $showNumberLine = 0;
    }

    if ($q->param ("showReverseStrand")) {
        $showReverseStrand = $q->param ("showReverseStrand");
    }
    else {
        $showReverseStrand = 0;
    }

    if ($q->param ("showSpacerLine")) {
        $showSpacerLine = $q->param ("showSpacerLine");
    }
    else {
        $showSpacerLine = 0;
    }

    if ($q->param ("returnRestSummary")) {
        $returnRestSummary = $q->param ("returnRestSummary");
    }
    else {
        $returnRestSummary = 0;
    }

    if ($q->param ("returnPrimerSummary")) {
        $returnPrimerSummary = $q->param ("returnPrimerSummary");
    }
    else {
        $returnPrimerSummary = 0;
    }

    if ($q->param ("returnHelpInfo")) {
        $returnHelpInfo = $q->param ("returnHelpInfo");
    }
    else {
        $returnHelpInfo = 0;
    }

    if ($q->param ("returnCdsLinks")) {
        $returnCdsLinks = $q->param ("returnCdsLinks");
    }
    else {
        $returnCdsLinks = 0;
    }

    if ($q->param ("returnTransLinks")) {
        $returnTransLinks = $q->param ("returnTransLinks");
    }
    else {
        $returnTransLinks = 0;
    }

    if ($q->param ("returnTransLinks")) {
        $returnTransLinks = $q->param ("returnTransLinks");
    }
    else {
        $returnTransLinks = 0;
    }

    if ($q->param ("returnOptionsChosen")) {
        $returnOptionsChosen = $q->param ("returnOptionsChosen");
    }
    else {
        $returnOptionsChosen = 0;
    }

#preliminary check of all user values here
    $primerList =~ s/[^A-Za-z\d\s\s\,\.\-\_ ]/ /g;
    if ($primerList =~ m/(^[A-Za-z\d\s\s\,\.\-\_ ]+$)/) {
        $primerList = $1;
    }
    else {
        $primerList = '';
    }

    $sequenceRecord =~ s/[^A-Za-z\d\s\s\t\(\)\.\,\;\:\-\=\_\"\/\>\< ]/ /g;
    if ($sequenceRecord =~ m/(^[A-Za-z\d\s\s\t\(\)\.\,\;\:\-\=\_\"\/\>\< ]+$)/) {
        $sequenceRecord = $1;
}
else {
    die ("No template sequence was entered.");
}

if ($geneticCodeSelection =~ m/(standard)/) {
    $geneticCodeSelection = $1;
}
else {
    die ("The 'genetic code' selection was not recognized.");
}

if ($restrictionSetSelection =~ m/(common|none)/) {
    $restrictionSetSelection = $1;
}
else {
    die ("The 'restriction set' selection was not recognized.");
}

if ($readingFramesToShow =~ m/(one|two|all_three|three|all_six|uppercase1|uppercase2|uppercase3|none)/) {
    $readingFramesToShow = $1;
}
else {
    die ("The 'reading frames to show translations for' selection was not recognized.");
}

if ($isCircular =~ m/(1|0)/) {
    $isCircular = $1;
}
else {
    die ("The 'topology' selection was not recognized.");
}

if ($checkForMismatchFivePrimeTails =~ m/(0|1)/) {
    $checkForMismatchFivePrimeTails = $1;
}
else {
    die ("The 'allow primers to have mismatched 5' tails' selection was not recognized.");
}

if ($checkForMismatchThreePrimeTails =~ m/(0|1)/) {
    $checkForMismatchThreePrimeTails = $1;
}
else {
    die ("The 'allow primers to have mismatched 3' tails' selection was not recognized.");
}

if (($checkForMismatchFivePrimeTails == 1) && ($checkForMismatchThreePrimeTails == 1)) {
    $checkForMiddleMatch = 1;
}
else {
    $checkForMiddleMatch = 0;
}

if ($minimumMatch =~ m/(5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20)/) {
    $minimumMatch = $1;
}
else {
    die ("The 'minimum bases required when mismatching alowed' selection was not recognized.");
}

if ($basePerLine =~ m/(60|80|100)/) {
    $basePerLine = $1;
}
else {
    die ("The 'bases per line' selection was not recognized.");
}

if ($showNumberLine =~ m/(0|1)/) {
    $showNumberLine = $1;
}
else {
    die ("The 'show number line' selection was not recognized.");
}

if ($showReverseStrand =~ m/(0|1)/) {
    $showReverseStrand = $1;
}
else {
    die ("The 'show reverse strand' selection was not recognized.");
}

if ($showSpacerLine =~ m/(0|1)/) {
    $showSpacerLine = $1;
}
else {
    die ("The 'show spacer line' selection was not recognized.");
}

if ($returnRestSummary =~ m/(0|1)/) {
    $returnRestSummary = $1;
}
else {
    die ("The 'return restriction summary' selection was not recognized.");
}

if ($returnPrimerSummary =~ m/(0|1)/) {
    $returnPrimerSummary = $1;
}
else {
    die ("The 'return primer summary' selection was not recognized.");
}

if ($returnHelpInfo =~ m/(0|1)/) {
    $returnHelpInfo = $1;
}
else {
    die ("The 'return help information' selection was not recognized.");
}

if ($returnCdsLinks =~ m/(0|1)/) {
    $returnCdsLinks = $1;
}
else {
    die ("The 'return coding sequence links' selection was not recognized.");
}

if ($returnTransLinks =~ m/(0|1)/) {
    $returnTransLinks = $1;
}
else {
    die ("The 'return translation links' selection was not recognized.");
}

if ($returnOptionsChosen =~ m/(0|1)/) {
    $returnOptionsChosen = $1;
}
else {
    die ("The 'return options selected' selection was not recognized.");
}

#end preliminary check of user values

my $totalTranslations = "";
my $restSummary = "";
my $primerSummary = "";
my $cdsLinks = "";
my $transLinks = "";
my $helpInfo = "";
my $optionInfo = "";

if ($returnOptionsChosen == 1) {
    $optionInfo = '<li>The following options were selected: genetic code=' . $geneticCodeSelection . '; restriction set=' . $restrictionSetSelection . '; reading frames to show translations for=' . $readingFramesToShow . '; ';
    if ($isCircular == 1) {
        $optionInfo = $optionInfo . 'topology=circular; ';
    }
    else {
        $optionInfo = $optionInfo . 'topology=linear; ';
    }

    if ($checkForMismatchFivePrimeTails == 1) {
        $optionInfo = $optionInfo . 'allow primers to have mismatched 5\' tails=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'allow primers to have mismatched 5\' tails=false; ';
    }

    if ($checkForMismatchThreePrimeTails == 1) {
        $optionInfo = $optionInfo . 'allow primers to have mismatched 3\' tails=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'allow primers to have mismatched 3\' tails=false; ';
    }

    $optionInfo = $optionInfo . 'matching bases required when mismatching bases allowed=' . $minimumMatch . '; ';

    $optionInfo = $optionInfo . 'bases per line=' . $basePerLine . '; ';

    if ($showNumberLine == 1) {
        $optionInfo = $optionInfo . 'show number line=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'show number line=false; ';
    }

    if ($showReverseStrand == 1) {
        $optionInfo = $optionInfo . 'show reverse strand=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'show reverse strand=false; ';
    }

    if ($showSpacerLine == 1) {
        $optionInfo = $optionInfo . 'show spacer line=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'show spacer line=false; ';
    }

    if ($returnHelpInfo == 1) {
        $optionInfo = $optionInfo . 'return help info=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'return help info=false; ';
    }

    if ($returnRestSummary == 1) {
        $optionInfo = $optionInfo . 'return restriction summary=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'return restriction summary=false; ';
    }

    if ($returnPrimerSummary == 1) {
        $optionInfo = $optionInfo . 'return primer summary=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'return primer summary=false; ';
    }

    if ($returnCdsLinks == 1) {
        $optionInfo = $optionInfo . 'return coding sequence links=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'return coding sequence links=false; ';
    }

    if ($returnTransLinks == 1) {
        $optionInfo = $optionInfo . 'return translation links=true; ';
    }
    else {
        $optionInfo = $optionInfo . 'return translation links=false; ';
    }
    $optionInfo =~ s/\;\s*$//;
    $optionInfo = $optionInfo . '.';
}

my $sequence = make_sequence::makeSequence($sequenceRecord, $geneticCodeSelection, $readingFramesToShow, $showNumberLine, $showReverseStrand, $showSpacerLine, $basePerLine);

if (!($primerList =~ m/^[\s\t ]*$/)) {
    my $patternGroupPrimer = make_primers::makePrimers($primerList);
    $sequence = add_primers::addPrimers($sequence, $patternGroupPrimer, $isCircular, $checkForMismatchFivePrimeTails, $checkForMismatchThreePrimeTails, $checkForMiddleMatch, $minimumMatch);
}
else {
    $returnPrimerSummary = 0;
}

if (!($restrictionSetSelection eq 'none')) {
    my $patternGroupRestriction = make_rest::makeRest($restrictionSetSelection);
    $sequence = add_rest::addRest($sequence, $patternGroupRestriction, $isCircular);
}
else {
    $returnRestSummary = 0;
}

if ($returnHelpInfo == 1) {
    $helpInfo = "<li>Additional information about a primer, translation, or restriction site can be viewed by pointing to an item.</li>\n" .
        "<li>Click on two restriction sites to perform a virtual restriction digest, or two primers to perform a virtual PCR reaction. To use the beginning or end of the sequence as a restriction boundary, click on the <b>RestStart</b> or <b>RestEnd</b> link followed by a restriction site. To use the beginning or end of the sequence as a PCR boundary, click on the <b>PCRStart</b> or <b>PCREnd</b> link followed by an appropriate primer. These functions require a JavaScript-enabled browser.</li>\n" .
        "<li>Amino acids are aligned with the first base in the corresponding triplet that coded for them.</li>\n" .
        "<li>Translations shown below the reverse DNA strand are written in reverse.</li>\n" .
        "<li>Restriction sites are numbered as the first base after the cut site on the direct strand.</li>\n" .
        "<li>Primers shown in italics contain mismatches with the template.</li>\n" .
        "<li>Note that the primer annealing sites shown on this figure do not necessarily reflect true annealing sites.</li>\n" .
        "<li>Primers annealing to the direct strand are numbered as the direct strand base under the 5' primer base.</li>\n" .
        "<li>Primers annealing to the reverse strand are numbered as the first non-primer base preceding the 5' end base on the direct strand.</li>\n" .
        "<li>The exact blunt size of a PCR product = reverse primer position - forward primer position.</li>\n" .
        "<li>The exact direct strand length of a restriction fragment = downstream position - upstream position.</li>\n";
}

if ($returnRestSummary == 1) {
    $restSummary = make_rest_summary::makeRestSummary($sequence);
}

if ($returnPrimerSummary == 1) {
    $primerSummary = make_primer_summary::makePrimerSummary($sequence);
}

if ($returnCdsLinks == 1) {
    $cdsLinks = make_cds_links::makeCdsLinks($sequence);
}

if ($returnTransLinks == 1) {
    $transLinks = make_trans_links::makeTransLinks($sequence);
    $totalTranslations = make_total_protein::makeTotalProtein($sequence);
}

my $htmlGroup = make_html::makeHtml($sequence);
my $sequenceDescription = $sequence->getDescription();
my $dnaSequence = $sequence->getSequence();
$body .= make_output::makeOutput($htmlGroup, $sequenceDescription, $dnaSequence, $totalTranslations, $restSummary, $primerSummary, $cdsLinks, $transLinks, $helpInfo, $optionInfo, $basePerLine);
$body .= "<div class=\"copyright\">Sequence Extractor copyright &copy; 2006 Paul Stothard<br />\n" .
"email: stothard\@ualberta.ca</div>\n" .
"</td>\n" .
"</tr>\n" .

"<tr>\n" .
"<td>\n" .
"<a href=\"http://validator.w3.org/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://www.w3.org/Icons/valid-xhtml10\" alt=\"Valid XHTML 1.0!\" height=\"31\" width=\"88\" /></a>\n" .
"<a href=\"http://jigsaw.w3.org/css-validator/\"><img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\" /></a>\n" .
"</td>\n" .
"</tr>\n" .

"</tbody>\n" .
"</table>\n" .

"</body>\n" .
"</html>\n";
return [
'200',
    $q->psgi_header('text/html'),
    [ $body ],
    ];
};

builder {
    # Enable Interactive debugging
    enable "InteractiveDebugger";
    #enable "Debug";

    # Make Plack middleware render some static for you
    #enable "Static",
    #       path => qr{\.(?:js|css|jpe?g|gif|ico|png|html?|swf|txt)$},
    #       root => './htdocs';

    # Let Plack care about length header
    enable "ContentLength";

    $app;
}
 
__DATA__
<!doctype html>
<html>
 
<head>
<title>Few words about Plack and PSGI</title>
</head>
 
<body>
<h1>Hello World!</h1>
</body>
 
</html> 

# vi:syntax=perl  softtabstop=4 shiftwidth=4 expandtab
