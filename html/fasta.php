<?php
include_once('accesscontrol.php');
include_once('seq.inc.php');

if(array_key_exists('id', $_GET)){
  $id = $_GET['id'];
  header('Content-type: text/fasta');
  $data = getRecord($id, $userid);
  header("Content-Disposition: attachment; filename=\"${id}_${data['name']}.fasta\"");
  print ">${id} ${data['name']}\n";
  (array_key_exists('DNASequence', $data))? $seq = $data['DNASequence'] : $seq = $data['sequence'];
  $sequence = fastaseq($seq, "\n");
  print $sequence;
  if (array_key_exists('proteinSequence', $data)) {
    print "\n\n>${id}, ${data['name']} protein sequence\n";
    print fastaseq($data['proteinSequence'], "\n");
  }
  print "";
}
?>
