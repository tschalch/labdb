<?php
include_once("accesscontrol.php");
include("header.php");

$mode = 'modify';
$formParams = array('table'=>'none', 'mode'=>$mode);
#include("formhead.php");

if (!isset($_FILES['importFile'])){
	?>
	<form method="post" action="<?php print "${_SERVER['PHP_SELF']}";?>"
	      enctype="multipart/form-data">
	<div class="formRow">
		<span class="formLabel">Upload File:</span>
		<span class="formField" id="fileUp">
			<input type="file" name="importFile"/>
		</span>
	</div>
	<div class="formRow">
		<input type='submit' value='Import' name='import'/>
	</div>
	</form>
	
	<?php
} else {
	$fp = fopen($_FILES['importFile']['tmp_name'], "r");
	while(!feof($fp)){
		$line = fgets($fp);
		print $line."<br/>";
		$line = str_replace("\n", "", $line);
		$line = str_replace("\r", "", $line);
		if ($line{0} == '>'){
			$table = trim(substr($line,1));
			print "Table to import to: $table <br/>";
			continue;
		}
		if ($line{0} == '#'){
			$line = substr($line,1);
			$fieldnames = explode(',', $line);
			print "Fieldnames: ";
			foreach ($fieldnames as $name) print "$name,";
			print "<br/>";
			continue;
		}
		$tokens = explode(',', $line);
		if (count($tokens) == count($fieldnames)){
			$dataset = array();
			for($i=0; $i < count($tokens); $i++){
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

