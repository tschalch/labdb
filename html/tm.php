<html><head><title>Melting Temperature (Tm) Calculation</title></head><body bgcolor=FFFFFF>
<center><table border=0><tr><td>
<center><h2>Melting Temperature (Tm) Calculation</h2></center><br>
<?php 
error_reporting(0);
$primer="";
$primer=strtoupper($_GET["primer"]);
$primer=preg_replace("/\W|[^ATGCYRWSKMDVHBN]|\d/","",$primer);

if ($primer!="" and strlen($primer)>=6 and strlen($primer)<=50){
        print "<pre>PRIMER                   5'-$primer-3'\n";
        print "LENGTH                   ".strlen($primer)."\n";
        $cg=round(100*CountCG($primer)/strlen($primer),1);
        print "C+G%                     $cg\n";
        Mol_wt($primer);
        Math.round(1000000/(this.gCount * 11.7 + this.cCount * 7.3 + this.aCount * 15.4 + this.tCount * 8.8));
        
        if (strlen($primer)!=CountATCG($primer)){print "\n\nTm        Minimun        ".Tm_min($primer)." &deg;C\n          Maximum        ".Tm_max($primer)." &deg;C";}else{print "\n\nTm                       ".Tm_min($primer)." &deg;C";}
}

function Mol_wt($primer){
$upper_mwt=molwt($primer,"DNA","upperlimit");
$lower_mwt=molwt($primer,"DNA","lowerlimit");
if ($upper_mwt==$lower_mwt){
        print "Molecular weight:        $upper_mwt";
        }else{
        print "Upper Molecular weight:  $upper_mwt\nLower Molecular weight:  $lower_mwt";
        }
        }
function CountCG($c){
        $cg=substr_count($c,"G")+substr_count($c,"C");
        return $cg;
        }
        
function CountATCG($c){
        $cg=substr_count($c,"A")+substr_count($c,"T")+substr_count($c,"G")+substr_count($c,"C");
        return $cg;
        }


function Tm_min($primer){
        $primer_len=strlen($primer);
        $primer2=preg_replace("/A|T|Y|R|W|K|M|D|V|H|B|N/","A",$primer);
        $n_AT=substr_count($primer2,"A");
        $primer2=preg_replace("/C|G|S/","G",$primer);
        $n_CG=substr_count($primer2,"G");
                
                if ($primer_len > 0) {
                        if ($primer_len < 14) {
                                return round(2 * ($n_AT) + 4 * ($n_CG));
                        }else{
                                return round(64.9 + 41*(($n_CG-16.4)/$primer_len),1);
                        }
                }
}

function Tm_max($primer){
        $primer_len=strlen($primer);
        $primer=primer_max($primer);
        $n_AT=substr_count($primer,"A");
        $n_CG=substr_count($primer,"G");                
                if ($primer_len > 0) {
                        if ($primer_len < 14) {
                                return round(2 * ($n_AT) + 4 * ($n_CG));
                        }else{
                                return round(64.9 + 41*(($n_CG-16.4)/$primer_len),1);
                        }
                }
}

function primer_min($primer){
        $primer=preg_replace("/A|T|Y|R|W|K|M|D|V|H|B|N/","A",$primer);
        $primer=preg_replace("/C|G|S/","G",$primer);
        return $primer;
        }

function primer_max($primer){
        $primer=preg_replace("/A|T|W/","A",$primer);
        $primer=preg_replace("/C|G|Y|R|S|K|M|D|V|H|B|N/","G",$primer);
        return $primer;
        }
function molwt($sequence,$moltype,$limit){
        // the following are single strand molecular weights / base
        $rna_A_wt = 329.245;
        $rna_C_wt = 305.215;
        $rna_G_wt = 345.245;
        $rna_U_wt = 306.195;

        $dna_A_wt = 313.245;
        $dna_C_wt = 289.215;
        $dna_G_wt = 329.245;
        $dna_T_wt = 304.225;

        $water = 18.015;

        $dna_wts = array('A' => array($dna_A_wt, $dna_A_wt),  // Adenine
                         'C' => array($dna_C_wt, $dna_C_wt),  // Cytosine
                         'G' => array($dna_G_wt, $dna_G_wt),  // Guanine
                         'T' => array($dna_T_wt, $dna_T_wt),  // Thymine
                         'M' => array($dna_C_wt, $dna_A_wt),  // A or C
                         'R' => array($dna_A_wt, $dna_G_wt),  // A or G
                         'W' => array($dna_T_wt, $dna_A_wt),  // A or T
                         'S' => array($dna_C_wt, $dna_G_wt),  // C or G
                         'Y' => array($dna_C_wt, $dna_T_wt),  // C or T
                         'K' => array($dna_T_wt, $dna_G_wt),  // G or T
                         'V' => array($dna_C_wt, $dna_G_wt),  // A or C or G
                         'H' => array($dna_C_wt, $dna_A_wt),  // A or C or T
                         'D' => array($dna_T_wt, $dna_G_wt),  // A or G or T
                         'B' => array($dna_C_wt, $dna_G_wt),  // C or G or T
                         'X' => array($dna_C_wt, $dna_G_wt),  // G, A, T or C
                         'N' => array($dna_C_wt, $dna_G_wt)   // G, A, T or C
           );

        $rna_wts = array('A' => array($rna_A_wt, $rna_A_wt),  // Adenine
                         'C' => array($rna_C_wt, $rna_C_wt),  // Cytosine
                         'G' => array($rna_G_wt, $rna_G_wt),  // Guanine
                         'U' => array($rna_U_wt, $rna_U_wt),  // Uracil
                         'M' => array($rna_C_wt, $rna_A_wt),  // A or C
                         'R' => array($rna_A_wt, $rna_G_wt),  // A or G
                         'W' => array($rna_U_wt, $rna_A_wt),  // A or U
                         'S' => array($rna_C_wt, $rna_G_wt),  // C or G
                         'Y' => array($rna_C_wt, $rna_U_wt),  // C or U
                         'K' => array($rna_U_wt, $rna_G_wt),  // G or U
                         'V' => array($rna_C_wt, $rna_G_wt),  // A or C or G
                         'H' => array($rna_C_wt, $rna_A_wt),  // A or C or U
                         'D' => array($rna_U_wt, $rna_G_wt),  // A or G or U
                         'B' => array($rna_C_wt, $rna_G_wt),  // C or G or U
                         'X' => array($rna_C_wt, $rna_G_wt),  // G, A, U or C
                         'N' => array($rna_C_wt, $rna_G_wt)   // G, A, U or C
             );

        $all_na_wts = array('DNA' => $dna_wts, 'RNA' => $rna_wts);
        //print_r($all_na_wts);
        $na_wts = $all_na_wts[$moltype];

        $mwt = 0;
        $NA_len = strlen($sequence);

        if($limit=="lowerlimit"){$wlimit=1;}
        if($limit=="upperlimit"){$wlimit=0;}
        
        for ($i = 0; $i < $NA_len; $i++) {
            $NA_base = substr($sequence, $i, 1);
            $mwt += $na_wts[$NA_base][$wlimit];
        }
        $mwt += $water;

        return $mwt;
    }
                
?>
</pre>
<hr>
<form method="get" action="<?php  print $_SERVER["PHP_SELF"]; ?>">
<b>Primer </b>(6-50 bases):<br>
<input type="text" name="primer" value="<?php  print $primer; ?>">
<input type="submit" value="Calculate Tm">
<br><font size=-1> Degenerated nucleotides are allowed</a></font>
</form>
<?php if ($_GET["formula"]!="show"){ ?>
<hr><font size=-1><a href=?formula=show>Calculations</a></font>
</td></tr></table>
<?php }else{ ?>
</td></tr></table></center>
<hr>
<h2>Basic Melting Temperature (Tm) Calculations</h2>
Two standard approximation calculations are used. 
<p>For sequences less than 14 nucleotides
the formula is: 
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tm= (wA+xT) * 2 + (yG+zC) * 4
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;where w,x,y,z are the number of the bases A,T,G,C in the sequence, respectively.
<p>For sequences longer than 13 nucleotides, the equation used is
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tm= 64.9 +41*(yG+zC-16.4)/(wA+xT+yG+zC)
<p>When degenerated nucleotides are included in the primer sequence (Y,R,W,S,K,M,D,V,H,B or N), those nucleotides will be internally substituted prior to minimum and maximum Tm calculation.
<p><pre>    Example: 
     Primer sequence:                            CTCT<b>RY</b>CT<b>WS</b>CTCTCT
     Sequence for minimum Tm calculation:        CTCT<b>AT</b>CT<b>AG</b>CTCTCT
     Sequence for maximum Tm calculation:        CTCT<b>GC</b>CT<b>AG</b>CTCTCT</pre>
<p><b>ASSUMPTIONS:</b>
<p>Both equations assume that the annealing occurs under the standard conditions of 50 nM primer, 50
mM Na<sup><font size=-2>+</font></sup>, and pH 7.0.
<hr>
<?php } ?>
</body></html>
