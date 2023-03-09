<?php
$title = "CSV batch import";
include_once("accesscontrol.php");
include("header.php");

$mode = '';
$formParams = ['table'=>'none', 'mode'=>$mode];
include("formhead.php");

$helpText = "
<pre>Import Help:
-----------------

The file to import must have as the first line the target table marked with '>'.
The second line contains the table header starting with #.
The records need to be in CSV format.

Example of a import file for three plasmid entries:
-----------------

>plasmids
#name,description,generation,enzymes,sequence
\"AcMNPV genome (8075)\",\"wildtype baculovirus genome\",,,\"GAATTCTACCCGTAAAGCGAGTTTAGTTTTGAAAAACAAATGACATCATTTGTATAATGACATCATCCCC
TGATTGTGTTTTACAAGTAGAATTCTATCCGTAAAGCGAGTTCAGTTTTGAAAACAAATGAGTCATACCT
AAACACGTTAATAATCTTCTGATATCAGCTTATGACTCAAGTTATGAGCCGTGTGCAAAACATGAGATAA\"
\"ARC_creFusion_OneStrepSumo-ago1opt (4691)\",\"SF9 codon optimized RITS complex for insect cell expression\",\"sequencial cre-fusion of 3231+3814+3815\",,
\"chp1(504-960)RK762AA_OSS-Tas3(8-83) (8537)\",,\"cre 8452-4479\",,
</pre>";


if (!isset($_FILES['importFile'])){
	?>
	<form method="post" action="<?php print "${_SERVER['PHP_SELF']}";?>"
	      enctype="multipart/form-data">
	<div class="formRow">
		<span class="formLabel">Upload File (CSV format):</span>
		<span class="formField" id="fileUp">
			<input type="file" name="importFile"/>
		</span>
		<div class="formRowHelp"><?php echo $helpText; ?></div>
	</div>
	<div class="formRow">
		<input type='submit' value='Import' name='import'/>
	</div>
	</form>
	
	<?php
} else {
	$fp = fopen($_FILES['importFile']['tmp_name'], "r");
	while(!feof($fp)){
		$csvRecord = fgetcsv($fp);
		print_r($csvRecord);
		if ($csvRecord[0][0] == '>'){
			$table = trim(substr($csvRecord[0],1));
			print "Table to import to: $table <br/>";
			continue;
		}
		if ($csvRecord[0][0] == '#'){
			$csvRecord[0] = substr($csvRecord[0],1);
			$fieldnames = $csvRecord;
			print "Fieldnames: ";
			foreach ($fieldnames as $name) print "$name,";
			print "<br/>";
			continue;
		}
		$tokens = $csvRecord;
		if (($tokens === null ? 0 : count($tokens)) == count($fieldnames)){
			$dataset = [];
			for($i=0; $i < ($tokens === null ? 0 : count($tokens)); $i++){
				if(strlen(trim($tokens[$i])) > 0){
					$dataset[$fieldnames[$i]] = trim($tokens[$i]);
				}
			}
			print $table;
			print_r($dataset);
			$id = newRecord($table, $dataset, $userid);
			print "<br/>new ID: $id<br/>";
		}
	}
	fclose($fp);
}
?>

