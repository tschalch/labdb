<?php
/**
 * Functions useful for nucleotide sequence analysis
 *
 * These functions are used in the class seq, but can also be used 
 * independently.
 *
 * Much code by Serge Gregorio, edited by Nico Stuurman
 *
 * @package biophp
 * @author Serge Gregorio
 *
 */
/**
 * complement() returs the genetic complement of a DNA or RNA sequence.
 *
 * @author Serge Gregorio
 *
 */

function CountCG($c){
	$c = strtoupper($c);
        $cg=substr_count($c,"G")+substr_count($c,"C");
        return $cg;
        }
        
function CountATCG($c){
	$c = strtoupper($c);
        $cg=substr_count($c,"A")+substr_count($c,"T")+substr_count($c,"G")+substr_count($c,"C");
        return $cg;
        }

function complement($seq, $moltype)
{
    if (isset($moltype) == FALSE) {
         $moltype = 'DNA'; // default to DNA.
    }
    $seq = strtoupper($seq);

    $dna_complements = array('A' => 'T',
                             'T' => 'A',
                             'G' => 'C',
                             'C' => 'G');

    $rna_complements = array('A' => 'U',
                             'U' => 'A',
                             'G' => 'C',
                             'C' => 'G');

    $moltype = strtoupper($moltype);
    if ($moltype == 'DNA') {
        $comp_r = $dna_complements;
    } elseif ($moltype == 'RNA') {
        $comp_r = $rna_complements;
    }
    $seqlen = strlen($seq);
    $compseq = '';
    //print "$seq <br/>";
    for($i = 0; $i < $seqlen; $i++) {
        $symbol = substr($seq, $i, 1);
        if (array_key_exists($symbol, $comp_r)) $compseq .= $comp_r[$symbol];
    }
    return $compseq;
}

/**
 * formseq() returns a formated sequence
 * Format sequence
**/

function formseq($seq){
	$formseq = "";
	$seq = remove_non_coding($seq);
	$len = strlen($seq);
	for ($i=0; $i<$len; $i+=10){
		$formseq .= substr($seq,$i,10);
		($i != 0 and $i % 50 == 40) ? $formseq.= "\n" : $formseq.= " ";
	}
	return $formseq;
}

function fastaseq($seq, $lb, $lineLength){
	$formseq = "";
	$lineLength = isset($lineLength) ? $lineLength: 60;
	$seq = strtoupper ($seq);
	$seq = remove_non_coding($seq);
	$len = strlen($seq);
	for ($i=0; $i<$len; $i+=$lineLength){
		if($i>0) $formseq .= $lb;
		$formseq .= substr($seq,$i, $lineLength);
	}
	return $formseq;       
}

function remove_non_coding($seq) {
        // change the sequence to upper case
        $seq=strtoupper($seq);
        // remove non-words (\W), con coding ([^ATGCYRWSKMDVHBN]) and digits (\d) from sequence
        $seq=preg_replace("/[^\*ATGCYRWSKMDVHBNEFILPQUX]/","",$seq);
        // replace all X by N (to normalized sequences)
        $seq=preg_replace("/X/","N",$seq);
        return $seq;
}


/**
 * revcomp() first gets the complement of a DNA or RNA sequence, and then returns it in reverse order.
 *
 * @author Serge Gregorio
 *
 */
function revcomp($seq, $moltype)
{
    return strrev(complement($seq, $moltype));
}

/**
 *
 * halfstr() returns one of the two palindromic "halves" of a palindromic string
 * @author Serge Gregorio
 *
 */
function halfstr($string, $no)
{
    // for now, this holds for mirror repeats.
    if (is_odd(strlen($string))) {
        $comp_len = (int) (strlen($string)/2);
        if ($no == 0) {
            return substr($string, 0, $comp_len);
        } else {
            return substr($string, $comp_len + 1);
        }
    } else {
        $comp_len = strlen($string)/2;
        if ($no == 0) {
            return substr($string, 0, $comp_len);
        } else {
            return substr($string, $comp_len);
        }
    }
}

/**
 * getbridge() returns the sequence located between two palindromic halves of a palindromic string.
 * Take note that the "bridge" as I call it, is not necessarily a genetic mirror or a palindrome.
 *
 * @author Serge Gregorio
 *
 */
function get_bridge($string)
{
    if (is_odd(strlen($string))) {
        $comp_len = (int) (strlen($string)/2);
        return substr($string, $comp_len, 1);
    } else {
        return false;
    }
}

/**
 * expand_na returns the expansion of a nucleic acid sequence, replacing special wildcard symbols
 * with the proper PERL regular expression.
 *
 * @author Serge Gregorio
 */
function expand_na($string)
{
    $string = preg_replace("/N|X/", ".", $string);
    $string = preg_replace("/R/", "[AG]", $string);
    $string = preg_replace("/Y/", "[CT]", $string);
    $string = preg_replace("/S/", "[GC]", $string);
    $string = preg_replace("/W/", "[AT]", $string);
    $string = preg_replace("/M/", "[AC]", $string);
    $string = preg_replace("/K/", "[TG]", $string);
    $string = preg_replace("/B/", "[CGT]", $string);
    $string = preg_replace("/D/", "[AGT]", $string);
    $string = preg_replace("/H/", "[ACT]", $string);
    $string = preg_replace("/R/", "[ACG]", $string);
    return $string;
}


/**
 * Tm calculates the melting temperature of a given DNA duplex with its
 * complementary sequence
 *
 * Methods: 
 * basic (basic): 
 *     Tm=(Na + Nt) * 2 + (Ng + Nc) * 4
 *   For sequences longer than 14 nucleotides, use:
 *     Tm=64.9+41*(Ng + Nc -16.4)/(Na + Nt + Ng + Nc)
 *
 * Salt adjusted (sa):
 *     Tm=(Na+Nt)*2 + (Ng+Nc)*4-16.6log10 (0.05/[Na+])
 *   For sequences longer than 14 nucleotides, use:
 *      Tm=100.5 + 41*(Ng+Nc)/(Na+Nt+Ng+Nc)-820/(Na+Nt+Ng+Nc)+16.6*log10([Na+])
 *     
 * Nearest-Neighbor Thermodynamics (nnt):
 *    Tm = dH/(dS + R lnC) -273
 *    C = Ct /4
 *    dS = dS0 + 0.368(N-1)ln[Na+]
 * We try to implement the method by 
 * John SantaLucia, JR.(1998).Proc. Natl. Acad. Sci. USA 95 (4), 1460-1465
 * http://www.pubmedcentral.nih.gov/articlerender.fcgi?tool=pubmed&pubmedid=9465037 
 *
 * All methods come from http://old.mclab.com/toolbox/tm.htm
 * Another great resource is the program 'melting', available 
 * http://www.ebi.ac.uk/~lenov/meltinghome.html
 * 
 *
 * $salt is monovalent cation concentration in M (default to 50mM)
 * $concentration is nucleotide concentration in M (default 250 pM)
 *
 * @author Nico Stuurman
 */
function Tm ($sequence, $method='bre', $salt=0.05, $concentration=250E-12) 
{
    // Sanity checks
    if (!$sequence) {
        return false;  // should throw an exception
    }
    $sequence=strtoupper ($sequence);
    // handy to have the string length
    $seqlen=strlen($sequence);
    $revcomp = revcomp($sequence, 'DNA');

    // implements method basic
    if ($method=='basic') {
        if ($seqlen < 15) {
            for ($i=0;$i<$seqlen;$i++) {
                switch ($sequence{$i}) {
                case 'G':
                case 'C':
                    $tm+=4;
                    break;
                case 'A':
                case 'T':
                case 'U':
                     $tm+=2;
                }
            }
        } else { // for sequences longer than 14 bp
            for ($i=0;$i<$seqlen;$i++) {
                switch ($sequence{$i}) {
                    case 'G':
                    case 'C':
                        $GCscore+=1;
                        break;
                }
            }
            $tm=64.9 + 41 * ($GCscore-16.4)/$seqlen;
        }
        return $tm;

    } elseif ($method=='sa') {
        for ($i=0;$i<$seqlen;$i++) {
            switch ($sequence{$i}) {
                case 'G':
                case 'C':
                    $GCscore+=1;
                    break;
                case 'A':
                case 'T':
                case 'U':
                    $ATscore+=1;
                    break;
           } 
        }
        if ($seqlength<15) {
            $tm=$ATscore*2 + $GCscore*4 - 16.6 * log10 (0.05/$salt);
        } else {
            $tm=100.5 + 41*$GCscore/$seqlength-820/$seqlength + 16.6 * log10($salt);
        }
        return $tm;

    } elseif ($method=='nnt') {
    	$DNA = array ('fw' => $sequence,
    	              'rev' => $revcomp);
        // first setup array with needed data (we somehow should load these things from a file
        // deltaH (kcal/mol) and deltaS (cal/k.mol) 
        $nn['A']['A']['H']=-7.9;
        $nn['A']['A']['S']=-22.2;
        $nn['T']['T']['H']=-7.9;
        $nn['T']['T']['S']=-22.2;
        $nn['A']['T']['H']=-7.2;
        $nn['A']['T']['S']=-20.4;
        $nn['T']['A']['H']=-7.2;
        $nn['T']['A']['S']=-21.3;
        $nn['C']['A']['H']=-8.5;
        $nn['C']['A']['S']=-22.7;
        $nn['T']['G']['H']=-8.5;
        $nn['T']['G']['S']=-22.7;
        $nn['G']['T']['H']=-8.4;
        $nn['G']['T']['S']=-22.4;
        $nn['A']['C']['H']=-8.4;
        $nn['A']['C']['S']=-22.4;
        $nn['C']['T']['H']=-7.8;
        $nn['C']['T']['S']=-21.0;
        $nn['A']['G']['H']=-7.8;
        $nn['A']['G']['S']=-21.0;
        $nn['G']['A']['H']=-8.2;
        $nn['G']['A']['S']=-22.2;
        $nn['T']['C']['H']=-8.2;
        $nn['T']['C']['S']=-22.2;
        $nn['C']['G']['H']=-10.6;
        $nn['C']['G']['S']=-27.2;
        $nn['G']['C']['H']=-9.8;
        $nn['G']['C']['S']=-24.4;
        $nn['G']['G']['H']=-8.0;
        $nn['G']['G']['S']=-19.9;
        $nn['C']['C']['H']=-8.0;
        $nn['C']['C']['S']=-19.9;
	$nn['initGC']['H']=0.1;
	$nn['initGC']['S']=-2.8;
	$nn['initAT']['H']=2.3;
	$nn['initAT']['S']=4.1;
        $nn['symmetrycorrection']['H']=0;
        $nn['symmetrycorrection']['S']=-1.4;

	$dH=0;
	$dS=0;
	$dS0=0;
	foreach ($DNA as $strand){
	        // sum dH and dS
	        if ($strand{0}=='G' || $strand{0}=='C') {
	             $dH+=$nn['initGC']['H'];
	             $dS0+=$nn['initGC']['S'];
	        } elseif ($strand{0}=='A' || $strand{0}=='T' || $strand{0}=='U') {
	             $dH+=$nn['initAT']['H'];
	             $dS0+=$nn['initAT']['S'];
	        }
	}
        //printf("Initial dH: %4.2f, dS0:%4.2f<br/>", $dH, $dS0);
        // loop through sequence and add up based on the table above
        $strand = $DNA['fw'];
        for ($i=0;$i<$seqlen-1;$i++) {
            $dH+=$nn[$strand{$i}][$strand{$i+1}]['H'];
            $dS0+=$nn[$strand{$i}][$strand{$i+1}]['S'];
	        //printf("Base step: %s%s, dH: %4.2f, dS0:%4.2f<br/>", $sequence{$i}, $sequence{$i+1}, $dH, $dS0);
        }
        // I guess we need to add the symetry correction now
        //$dH+=$nn['symmetrycorrection']['H']=0;
        //$dS+=$nn['symmetrycorrection']['S']=-1.4;

        // correction for salt concentration (SantaLucia 1998)
        $dS=$dS0+(0.368*($seqlen-1)*log($salt));

        // Assume we deal with a PCR and have an excess of one strand
        $C=$concentration/4;

        $R=1.987; // Gas Constant in cal/K.mol 

        // enthalpy was in kilo calory per mol, correct here 
        $tm=1000*$dH/($dS + ($R*log($C))) - 273.15;

        return $tm;
   } elseif ($method=='bre') {
        // nearest neighbor according to bresslauer
    	$DNA = array ('fw' => $sequence,
    	              'rev' => $revcomp);
        // first setup array with needed data (we somehow should load these things from a file
        // deltaH (kcal/mol) and deltaS (cal/k.mol) 
        $nn['A']['A']['H']=9.1;
        $nn['A']['A']['S']=24.0;
        $nn['T']['T']['H']=9.1;
        $nn['T']['T']['S']=24.0;
        $nn['A']['T']['H']=8.6;
        $nn['A']['T']['S']=23.9;
        $nn['T']['A']['H']=6.0;
        $nn['T']['A']['S']=16.9;
        $nn['C']['A']['H']=5.8;
        $nn['C']['A']['S']=12.9;
        $nn['T']['G']['H']=5.8;
        $nn['T']['G']['S']=12.9;
        $nn['G']['T']['H']=6.5;
        $nn['G']['T']['S']=17.3;
        $nn['A']['C']['H']=6.5;
        $nn['A']['C']['S']=17.3;
        $nn['C']['T']['H']=7.8;
        $nn['C']['T']['S']=20.8;
        $nn['A']['G']['H']=7.8;
        $nn['A']['G']['S']=20.8;
        $nn['G']['A']['H']=5.6;
        $nn['G']['A']['S']=13.5;
        $nn['T']['C']['H']=5.6;
        $nn['T']['C']['S']=13.5;
        $nn['C']['G']['H']=11.9;
        $nn['C']['G']['S']=27.8;
        $nn['G']['C']['H']=11.1;
        $nn['G']['C']['S']=26.7;
        $nn['G']['G']['H']=11.0;
        $nn['G']['G']['S']=26.6;
        $nn['C']['C']['H']=11.0;
        $nn['C']['C']['S']=26.6;

	$dH=-5.0;
	$dS=0;
	$dS0=0;
        //printf("Initial dH: %4.2f, dS0:%4.2f<br/>", $dH, $dS0);
        // loop through sequence and add up based on the table above
        $strand = $DNA['fw'];
        for ($i=0;$i<$seqlen-1;$i++) {
            $dH+=$nn[$strand{$i}][$strand{$i+1}]['H'];
            $dS0+=$nn[$strand{$i}][$strand{$i+1}]['S'];
	    //printf("Base step: %s%s, dH: %4.2f, dS0:%4.2f<br/>", $sequence{$i}, $sequence{$i+1}, $dH, $dS0);
        }
        // I guess we need to add the symetry correction now
        //$dH+=$nn['symmetrycorrection']['H']=0;
        //$dS+=$nn['symmetrycorrection']['S']=-1.4;

        // correction for salt concentration (SantaLucia 1998)
        //$dS=$dS0+(0.368*($seqlen-1)*log($salt));

        // Assume we deal with a PCR and have an excess of one strand
        $K=1/$concentration;

        $R=1.987; // Gas Constant in cal/K.mol 
        //printf("dH: %6.1f, dS: %6.1f <br/>",$dH,$dS0);
        // enthalpy was in kilo calory per mol, correct here 
        $tm=1000*$dH/($dS0 + ($R*log($K))) - 273.15;
        // salt correction
        $tm=$tm + 7.21*log($salt);

        return $tm;
   }
   // we could throw an exception if the method was not valid....
   
   return false;
} 

?>
