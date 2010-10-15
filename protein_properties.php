<?php
include("functions.php");
include_once("accesscontrol.php");
?>
<html>
  <head>
    <title>Protein sequence information</title>
  </head>
  <body bgcolor=FFFFFF>
<?php
// author    Joseba Bikandi
// license   GNU GPL v2

// Developers: consider adding new options to the form

// the code in the top will manipulated the input protein sequence
// in the middle of the file is located the form
// in the bottom are located the functions used in this script


//############################################################################
//#################      lets manipulated the sequence       #################
//############################################################################
if ($_SERVER["QUERY_STRING"]=="info"){
        print_information();die();
}elseif($_GET){
	$id = $_GET['id'];
	if ($id){
		$row = getRecord($id, $userid, $groups);
		$seq = remove_non_coding_prot($row['proteinSequence']);
		$name = $row['name'];
	        $original_seq = chunk_split($seq, 70);
	        $seqlen=strlen($seq);
        	$pH=7.0;
        	//print "$name, $seqlen, $seq";
	} else {
	print_information();die();
	}
}elseif($_POST){
       	$seq=$_POST["seq"];
       	$name= $_POST["proteinName"];
        $pH=$_POST["pH"];
        $data_source=$_POST["data_source"];
        $result="";

        // remove non coding (works by default)
        $seq=remove_non_coding_prot($seq);

        // we will save the original sequence, just in case subsequence is used
        $original_seq = chunk_split($seq, 70);
        // if subsequence is requested
        if ($_POST["start"] or $_POST["end"]){
            if($_POST["start"]!=""){$start=$_POST["start"]-1;}else{$start=0;}
            if($_POST["end"]!=""){$end=$_POST["end"];}else{$end=strlen($seq);}
            $seq=substr($seq,$start,$end-$start);
            $result.="<p><b>Subsequence used for calculations:</b><br>".chunk_split($seq, 70);

        }

	$result .= "<p><b>Protein Sequence</b><br/>".format_sequence($seq);


        // length of sequence
        $seqlen=strlen($seq);

        // compute requested parameter
        if ($_POST["composition"]==1 or $_POST["molweight"]==1 or $_POST["abscoef"]==1 or $_POST["charge"]==1 or $_POST["charge2"]==1){
                    // calculate nucleotide conposition
                    $aminoacid_content=aminoacid_content($seq);
                    // prepare nucleotide composition to be printed out
                    if ($_POST["composition"]==1){
                        $result.="<p><b>Aminoacid composition of protein:</b><br>".print_aminoacid_content($aminoacid_content);
                    }
        }

        if ($_POST["molweight"]==1 or $_POST["abscoef"]==1){
                    // calculate molecular weight of protein
                    $molweight=protein_molecular_weight($seq);
                    if ($_POST["molweight"]==1){
                        $result.="<p><b>Molecular weight:</b><br>$molweight Daltons";
                    }
        }

        if ($_POST["abscoef"]==1){
                    $mabscoef=molar_absorption_coefficient_of_prot($seq);
                    $abscoef=absorption_coefficient_of_prot($seq);
                    $result.="<p><b>Absorption Coefficients at 280 nm measured in water:</b><br>";
	            $result.="<table class=\"abscoef\" border=\"0\" width=\"500\">";
                    $result.="<tr><td class=\"abscoef\">Extinction Coefficient with cysteines:</td><td class=\"abscoef\">".round($abscoef,2)." M-1 cm-1</td></tr>";
                    $result.="<tr><td class=\"abscoef\">Abs 0.1% (=1 g/l):</td><td class=\"abscoef\">".round($mabscoef,3)." AU/mg</td></tr>";
	            $result.="</table>";
        }

        if ($_POST["charge"]==1){
                    // get pk values for charged aminoacids
                    $pK=pK_values ($data_source);
                    // calculate isoelectric point of protein
                    $charge=protein_isoelectric_point($pK,$aminoacid_content);
                    $result.="<p><b>Isoelectric point of sequence ($data_source):</b><br>".round($charge,2);
        }
        if ($_POST["charge2"]==1){
                    // get pk values for charged aminoacids
                    $pK=pK_values ($data_source);
                    // calculate charge of protein at requested pH
                    $charge=protein_charge($pK,$aminoacid_content,$pH);
                    $result.="<p><b>Charge of sequence at pH = $pH ($data_source):</b><br>".round($charge,2);
        }

         // colored sequence based in plar/non-plar/charged aminoacids
        if ($_POST["3letters"]==1){
                // get the colored sequence (html code)
                $three_letter_code=seq_1letter_to_3letter($seq);
                // add to result
                $result.="<p><b>Sequence as three letters aminoacid code:</b><br>".$three_letter_code;

        }
        // 50 characters per line before output
        $seq = chunk_split($seq, 70);

        // colored sequence based in polar/non-plar/charged aminoacids
        if ($_POST["type1"]==1){
                // get the colored sequence (html code)
                $colored_seq=protein_aminoacid_nature1($seq);
                // add to result
                $result.="<p><b><font color=Magenta>Polar</font>, <font color=yellow>Nonpolar</font></b> or <b><font color=red>Charged</font></b> aminoacids:<br>".$colored_seq;

        }
        // colored sequence based in polar/non-plar/charged aminoacids
        if ($_POST["type2"]==1){
                // get the colored sequence (html code)
                $colored_seq=protein_aminoacid_nature2($seq);
                // add to result
                $result.="<p><b><font color=magenta>Polar</font>, <font color=yellow>small non-polar</font>, <font color=green>hydrophobic</font>, <font color=red>negatively</font></b> or <b><font color=blue>positively</font> charged</b> aminoacids:<br>".$colored_seq;

        }
}else{
        $seq="ARNDCEQGHILKMFPSTWYVX*";
        $original_seq=$seq;
        $seqlen=strlen($seq);
        $pH=7.0;
}

//############################################################################
//################# we have already manipulated the sequence #################
//############################# bellow is the form ###########################
//############################################################################
?>

    <table width="500">
    <tr><td>
    <center><H2><?php print $name;?><br/>Protein sequence information </H2></center>
<?php
	if ($result!=""){
	        print "<tr><td bgcolor=FFFFFF><pre>$result</pre></td></tr>";
	} else {
		include('protein_properties_form.php');
	}
?>
    </td></tr>
    </table>
  </body>
</html>