<?php
include_once('accesscontrol.php');
if(array_key_exists('id', $_GET)){
    $id = $_GET['id'];
    header('Content-type: text/fasta');
    $data = getRecord($id, $userid, $groups);
    header("Content-Disposition: attachment; filename=\"${id}_${data['name']}.fasta\"");
    print ">${id}, ${data['name']}\n";
    ($data['DNASequence'])? $seq = $data['DNASequence'] : $seq = $data['sequence'];
    $sequence = fastaseq($seq, "\n");
    print $sequence;
    if  ($data['proteinSequence']){
	print "\n\n>${id}, ${data['name']} protein sequence\n";  
	print fastaseq($data['proteinSequence'], "\n");
    }
    print "";
}
?>
