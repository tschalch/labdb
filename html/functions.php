<?php
#database functions
include('seq.inc.php');
include('protein.inc.php');
date_default_timezone_set('Europe/Berlin');

function write_log($log_msg){
  $log_filename = "/local/logs";
  $log_file_data = $log_filename.'/debug.log';
  file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

$DEBUG = 1;
function console_log( $data ){
  global $DEBUG;
  #echo '<script>';
  #echo 'console.log('. json_encode( $data ) .')';
  #echo '</script>';
  if ($DEBUG) write_log($data);
}

function pdo_query($q, $vars=array()){
  include('config.php');
  $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
  $options = [
    PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
    PDO::ATTR_EMULATE_PREPARES   => true, //allows parameter to be used multiple times
    ];
  //print "\"$q\"";
  try {
    $dbh = new PDO($dsn, $username, $password, $options);
  } catch (PDOException $e) {
    print "Database Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  console_log("query: $q");
  console_log("vars: ". print_r($vars,true));
  $stmt = $dbh->prepare($q);
  $stmt->execute($vars);
  #print "Result: "; print $stmt->rowCount(); print "<br/>\n";
  #echo "\n<br/>PDOStatement::errorInfo():<br/>\n";
  $arr = $stmt->errorInfo();
  #print_r($arr);
  $result = array();
  $i = 0;
#$dbhq = $dbh->query($q);
  //print_r($dbhq);
  if (substr_count($q,'INSERT', 0, 10)){
#$dbh->exec($q);
#print "query: $q <br/>";
    $result = $dbh->lastInsertId();
  } elseif (substr_count($q,'UPDATE', 0, 10)){
    $result = true;
  }
  elseif ($stmt->rowCount()){
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $result[] = $row;
    }
  }
  $dbh = null;
  $result = escape_quotes($result);
  #print "<br/>Result:";
  console_log("results: ". print_r($result, true));
  return $result;
}

function escape_quotes($receive) {
    if (!is_array($receive))
        $thearray = array($receive);
    else
        $thearray = $receive;

    foreach (array_keys($thearray) as $string) {
        if (is_array($thearray[$string])){
                $thearray[$string] = escape_quotes($thearray[$string]);
        } else {
                $thearray[$string] = preg_replace("/[\\\\]/","",$thearray[$string]);
                $thearray[$string] = htmlspecialchars_decode($thearray[$string], ENT_QUOTES);
                $thearray[$string] = htmlspecialchars($thearray[$string], ENT_QUOTES);
        }
    }

    if (!is_array($receive))
        return $thearray[0];
    else
        return $thearray;
}


function getInsertQuery($ds, $table, $id){
	$iq = "INSERT INTO `$table` (";
	$n = sizeof($ds);
	$count = 0;
	foreach ($ds as $field => $dat){
		$iq .= "`$field`";
		$count += 1;
		if($count < $n) $iq .= ", ";
	}
	$iq .= ") VALUES (";
	$count = 0;
	foreach ($ds as $field => $dat){
		if ($dat == 'mainID') $dat = $id;
		$iq .= "'$dat'";
		$count += 1;
		if($count < $n) $iq .= ", ";
	}

	$iq .= ")";
	return $iq;
}

function getUpdateQuery($ds, $table, $uid){
	$i = 0;
	if (array_key_exists('connID',$ds)){
	    $uid=$ds['connID'];
	}
	$num = count($ds);
	$uq = "UPDATE `$table` SET ";
	foreach ($ds as $key => $field){
	    $i++;
	    $uq .= "`$key` = '".$field."' ";
	    if ($i < $num) $uq .= ',';
	}
	$uq .= "WHERE connID='$uid';";
	//print $uq;
	return $uq;
}

#form functions
function printTextField($label, $field, $formParams, $default=null, $helpText=""){
	if ($helpText != ""){
		?>
		<script type="text/javascript">
			window.addEvent('domready', function() {
				var HelpField = $('<?php print "${field}Help" ?>');
				var HelpSlide = new Fx.Slide(HelpField);
				HelpField.set('html', '<?php print $helpText ?>');
				HelpField.set('class', 'formRowHelp');
				HelpSlide.hide();
				$('<?php print "${field}HelpToggle" ?>').addEvent('click', function(e){
					e.stop();
					HelpSlide.toggle();
				});
			})
			
		</script>
		<?php
	}
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	print "\n<div class=\"formRow\"><div class=\"formLabel\">$label:";
		if($helpText != ""){
			print "<a id=\"${field}HelpToggle\" href=\"#\"> ? </a>";
		}
	print "</div>\n";
	if($mode == "modify"){
		$value = (is_array($fields) && key_exists($field, $fields))?$fields[$field]:$default;
		print "<div class=\"formField\"><input type=\"text\" id=\"$field\"
			name=\"${table}_0_$field\" class=\"textfield\" value=\"$value\"/></div>";
		if($helpText != ""){
			print "<div id=\"${field}Help\"></div>";
		}
	}	
	if($mode == "display"){
		print "<div id=\"$field\" class=\"displayField\">$fields[$field]</div>";
		if($helpText != ""){
			print "<div id=\"${field}Help\"></div>";
		}
	}
	print "</div>\n\n";
}


function printHazards($label, $field, $formParams){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];

?> 
<style> 
img.GHS-picto { max-height: 6em; opacity: 0.1; display: none; } 
</style> 
<script>
function showHazardSelector(el){
  var hsymbols = $$('img');
  if (el.checked){
    $('hazardDisclaimer').set('html','Harzard signals are given for convenience and may not be accurate. Check COSSH and MSDS for details.');
    $$('img.GHS-picto').each(function (h){
      h.setStyle('display', 'inline');
    });
  }else{
    $$('img.GHS-picto').each(function (h){
      h.setStyle('display', 'none');
    $('hazardDisclaimer').set('html','Hazards not specified. Please check COSHH and MSDS.');
    });
    showHazards();
  }
}

function showHazards(){
  var hazards = JSON.decode($('hazard_field').get('value'));
  for (var h in hazards){
    if (hazards[h] == 1) {
      $('GHS-'+h).setStyle('opacity', 1);
      $('GHS-'+h).setStyle('display', 'inline');
      $('hazardDisclaimer').set('html','Harzard signals are given for convenience and may not be accurate. Check COSSH and MSDS for details.');
    }
  }
}

function toggleHazard(el){
  var hazards = JSON.decode($('hazard_field').get('value'));
  var haz = el.getProperty('data-hazard');
  if (hazards[haz] == 1) {
    hazards[haz] = 0;
  } else {
    hazards[haz] = 1;
  }
  $$('img.GHS-picto').each(function (h){
    h.setStyle('opacity', '0.1');
  });
  $('hazard_field').set('value', JSON.encode(hazards));
  showHazards();
}

window.addEvent('domready', function(){
  showHazards();
});
</script>

<?php
	print "\n<div class=\"formRow\"><div class=\"formLabel\">$label:";
	if($mode == "modify"){
    print "<input type=\"checkbox\" name=\"hasHazard\" onClick=\"showHazardSelector(this)\" >";
    $onclick = "\"toggleHazard(this)\"";
  }
	print "</div>\n";
  print "<div class=\"formField\"> \n";
  print "<div id=\"hazardDisclaimer\">Hazards not specified. Please check COSHH and MSDS.</div>";
  print "<div id=\"hazardSymbols\"> \n";
  print "<img id=\"GHS-toxic\" data-hazard=\"toxic\" onClick=$onclick title=\"Toxic\" class=\"GHS-picto\" src=\"img/GHS-pictogram-skull.svg\"> \n";
  print "<img id=\"GHS-harmful\" data-hazard=\"harmful\" onClick=$onclick title=\"Harmfull\" class=\"GHS-picto\" src=\"img/GHS-pictogram-exclam.svg\"> \n";
  print "<img id=\"GHS-health-hazard\" data-hazard=\"health-hazard\" onClick=$onclick title=\"Health Hazard\" class=\"GHS-picto\" src=\"img/GHS-pictogram-silhouette.svg\"> \n";
  print "<img id=\"GHS-env-hazard\" data-hazard=\"env-hazard\" onClick=$onclick title=\"Environmental Hazard\" class=\"GHS-picto\" src=\"img/GHS-pictogram-pollu.svg\"> \n";
  print "<img id=\"GHS-explosive\" data-hazard=\"explosive\" onClick=$onclick title=\"Explosive\" class=\"GHS-picto\" src=\"img/GHS-pictogram-explos.svg\"> \n";
  print "<img id=\"GHS-flammable\" data-hazard=\"flammable\" onClick=$onclick title=\"Flammable\" class=\"GHS-picto\" src=\"img/GHS-pictogram-flamme.svg\"> \n";
  print "<img id=\"GHS-oxidizing\" data-hazard=\"oxidizing\" onClick=$onclick title=\"Oxidizing\" class=\"GHS-picto\" src=\"img/GHS-pictogram-rondflam.svg\"> \n";
  print "<img id=\"GHS-corrosive\" data-hazard=\"corrosive\" onClick=$onclick title=\"Corrosive\" class=\"GHS-picto\" src=\"img/GHS-pictogram-acid.svg\"> \n";
  print "<img id=\"GHS-compressed-gas\" data-hazard=\"compressed-gas\" onClick=$onclick title=\"Compressed Gas\" class=\"GHS-picto\" src=\"img/GHS-pictogram-bottle.svg\"> \n";
  print "<img id=\"GHS-nohazard\" data-hazard=\"nohazard\" onClick=$onclick title=\"non-hazardous\" class=\"GHS-picto\" src=\"img/GHS-pictogram-nonhazardous.svg\"> \n";
  
  $value = (is_array($fields) && key_exists($field, $fields) && $fields[$field] != "")?$fields[$field]:"{}";
  print "<input id=\"hazard_field\" type=\"hidden\" name=\"${table}_0_$field\" value=\"$value\" />";
	print "</div>\n";
	print "</div>\n";
	print "</div>\n";
}

function printUploadField($label, $field, $formParams){
  $table = $formParams['table'];
  $fields = $formParams['fields'];


  print "\n<div class=\"formRow\"><div class=\"formLabel\">$label:";
  print "</div>\n";
  if (isset($fields['trackID'])){
    $trackID = ($fields['trackID'])? $fields['trackID']:"";
  }

  $mode = $formParams['mode'];
  $html = '<div class="formField"><div id="filestore"></div>';
  if($mode == "modify"){
    $html = '<div id="filestore-row" class="formField"><div id="filestore"></div>';
    $html .= ' <span></span><input type="file" name="file" id="file" />
      <span>Description: </span><input id="file_description" type="text" name="filedesc" />
      <button type="button" id="btn">Upload</button>
      </div>';
  }
  $value = (is_array($fields) && key_exists($field, $fields) && $fields[$field] != "")?$fields[$field]:"{}";
  $html .= "<input id=\"files_field\" type=\"hidden\" name=\"${table}_0_$field\" value=\"$value\" />";
  $html .= '</div>';
  print $html;
?>
<script src="js/File.Upload.js"></script>
<style> div.filerow > div {display:inline-block;}
  #filestore-row {border: 0.5px solid gray; padding: 0.2em;}
</style>
<script>

function removeFile(el){
  var fileStore = JSON.decode($('files_field').get('value'));
  var fileid = el.getProperty('data-fileid');
  delete fileStore.files[fileid];
  $('files_field').set('value', JSON.encode(fileStore));
  showFiles();
}

function showFiles(){
  var fileStore = JSON.decode($('files_field').get('value'));
  var files_string = "";
  for (var f in fileStore.files){
    var filename = fileStore.files[f].filename;
    var description = fileStore.files[f].description;
    files_string += `<div data-file-id="${f}">
     <div class="filerow"><div id="desc-${f}">${description}:</div> <div id="file-${f}"><a href="/uploads/${filename}" target="blank">${filename}</a></div>
    <?php if ($mode == "modify") print '<img onClick="removeFile(this);" data-fileid="${f}" style="display:inline; margin: 0.2em; cursor: pointer; vertical-align: middle; height:1.5em;" title="delete" alt="delete" src="img/delete-item.png" />'?>
    </div></div>`;
  }
  $('filestore').set('html', files_string);
}

function addFile(response){
  response = JSON.parse(response);
  if (typeof(response.jsonError) !== "undefined"){
    alert("Problem with upload (jsonError): " + response.jsonError);
    return;
  }
  if (typeof(response.fileError) !== "undefined"){
    if (response.fileError == "FileExists") {
      var retVal = confirm("File of that name already exists. The existing copy will be attached instead, OK?");
      if (retVal == false){
        return;
      }
    }
    if (response.fileError == "FileExtensionError") {
      alert("File type not supported. Please upload .pdf, .doc(x) or .xls(x) files.");
      return;
    }
  }
  var fileStore = JSON.decode($('files_field').get('value'));
  if (typeof(fileStore.pointer) === "undefined") fileStore.pointer = 0;
  var pointer = fileStore.pointer + 1;
  if (typeof(fileStore.files) === "undefined") fileStore.files = {};
  var files = fileStore.files;
  files[pointer] = response ;
  fileStore.pointer = pointer;
  $('files_field').set('value', JSON.encode(fileStore));
  showFiles();
}

</script>
<script>
window.addEvent('domready', function(){
  showFiles();
	$("btn").addEvent('click', function(){
    var file_desc = $('file_description').get('value');
    var files = $('file').get('value');
    if (file_desc == '' || files == ''){
      alert("File or file description missing! Please enter a short description of the file");
    }
    if (file_desc == ''){
      $('file_description').setStyle('outline','solid 1px red');
      return;
    };
    if (files == ''){
      $('file').setStyle('outline','solid 1px red');
      return;
    };
		var upload = new File.Upload({
			url: 'ajaxupload.php',
			data: {
            file_desc:  $('file_description').get('value')
			},
			images: ['file'],
			onComplete: function(response){
				console.log(response);
        addFile(response);
        $('file_description').setStyle('outline','none');
        $('file').setStyle('outline','none');
			}
		});
		
		upload.send();
	});
});
</script>
<?php       
}

function printID($formParams){
	print "\n<div class=\"formRow idfield\"><div class=\"formLabel\">ID:</div>\n";
	$fields = $formParams['fields'];
	if (isset($fields['trackID'])){
	    $value = ($fields['trackID'])? $fields['trackID']:"";
	    print "<div id=\"id\" class=\"displayField\" style=\"background-color: white;\">$value</div>
			<input type=\"hidden\" id=\"inp_id\" name=\"id\" value=\"$value\"/>\n";
	} else {
		$value = '';
	    print "<div id=\"id\" class=\"displayField\" style=\"background-color: white;\"></div>
		<input type=\"hidden\" id=\"inp_id\" name=\"id\" value=\"\"/>\n";
	}
	print "</div>\n\n";
	if ($value != ''){
		printTimestamp($formParams);
	}
}

function printTypeID($formParams, $idName){
	print "\n<div class=\"formRow idfield\"><div class=\"formLabel\">$idName:</div>\n";
	$fields = $formParams['fields'];
	if (isset($fields['hexID'])){
	    $value = ($fields['hexID'])? $fields['hexID']:"";
	    print "<div id=\"hexID\" class=\"displayField\" style=\"background-color: white;\">$value</div>
			<input type=\"hidden\" id=\"inp_hexID\" name=\"hexID\" value=\"$value\"/>\n";
	} else {
	    print "<div id=\"hexID\" class=\"displayField\" style=\"background-color: white;\"></div>
		<input type=\"hidden\" id=\"inp_hexID\" name=\"hexID\" value=\"\"/>\n";
	}
	print "</div>\n\n";
}

function printTimestamp($formParams){
	global $userid;
	global $groups;
	$mode = $formParams['mode'];
	print "\n<div class=\"formRow idfield\" style=\"margin-bottom: 5px\"><div class=\"formLabel\">Record created by:</div>\n";
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$id = $fields['trackID'];
	$track = getRecord($id, $userid, $groups);
	$permissions = getPermissionString($id);
	print "<div class=\"displayField\" style=\"background-color: white;\">${track['fullname']} on ${track['createDate']}, changed: ${track['changeDate']}, <span style=\"text-decoration: underline;\"
		title=\"$permissions\">Permissions</span></div></div>";
}


function printDateField($label, $field, $formParams, $default=null){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	if (isset($fields[$field]) and $fields[$field]!='0000-00-00'){
		$date = getdate(strtotime($fields[$field]));
	} else {
	    $date = array('mon'=>'','mday'=>'','year'=>'','0'=>'');
	}
	#print "time:" . $fields[$field] . "isset: ". isset($fields[$field]) .  "<br/>";
	#print strtotime($fields[$field])."<br/>";
	#print_r(getdate(date("Y-m-d",$fields[$field])));
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	if($mode == "modify"){
		$value = (is_array($fields) && key_exists($field, $fields))?$fields[$field]:$default;
		print "<div class=\"formField\">
			<input type=\"text\" id=\"${table}_0_${field}_m\" class=\"datefield\" size=\"3\" value=\"${date['mon']}\"/>
			<input type=\"text\" id=\"${table}_0_${field}_d\" class=\"datefield\" size=\"3\" value=\"${date['mday']}\"/>
			<input type=\"text\" id=\"${table}_0_${field}_y\" class=\"datefield\" size=\"5\" value=\"${date['year']}\"/> (Month/Day/Year)
			<input type=\"hidden\" id=\"${table}_0_$field\" name=\"${table}_0_$field\" value=\"${date['0']}\"/></div>";
	}	
	if($mode == "display"){
		if (isset($fields[$field]) and $fields[$field] != '0000-00-00'){
		    $date = date("m/d/Y",strtotime($fields[$field]));
		    print "<div class=\"displayField\">$date</div>\n";
		}
	}
	print "</div>\n\n";
}

function printCheckbox($label, $field, $formParams){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	$value = 1;
	if ($fields[$field]) $checked = "checked";
	if($mode == "modify"){
		print "<div class=\"formField\"><input type=\"checkbox\" 
			name=\"${table}_0_$field\" class=\"radiobox\" value=\"$value\" $checked/></div>";
	}	
	if($mode == "display"){
		print "<div class=\"displayField\"><input type=\"checkbox\" 
			disabled class=\"displayField\" value=\"$value\" $checked></input></div>";
	}
	print "</div>\n\n";
}


function printLinkField($label, $field, $formParams){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	if($mode == "modify"){
		print "<div class=\"formField\"><input id=\"$field\" type=\"text\" 
			name=\"${table}_0_$field\" class=\"textfield\" value=\"$fields[$field]\"/></div>";
	}	
	if($mode == "display"){
		print "<div class=\"displayField\"><a href=\"$fields[$field]\">$fields[$field]</a></div>";
	}
	print "</div>\n\n";
}

function printAttachmentField($label, $field, $formParams){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	if($mode == "modify"){
		print "<div class=\"formField\" id=\"fileUp\">";
		if ($fields[$field]!=''){
		print "<div class=\"formField\" id=\"fileUp\">
			<input id=\"hiddenFile\" type=\"text\" name=\"${table}_0_$field\"
			disabled class=\"textfield\" size=\"50\" value=\"$fields[$field]\"/>";
		}
		print "<input class=\"textfield\"  size=\"50\" type=\"file\" name=\"${table}_0_$field\"
			onChange=\"deleteField('hiddenFile', 'fileUp');\"></input></div>";
	}	
	if($mode == "display"){
		print "<div class=\"displayField\"><a href=\"attachments/$fields[$field]\">$fields[$field]</a></div>\n";
	}
	print "</div>\n\n";
}

function printReferenceLink($label, $text, $id, $type, $formParams, $cat=Null){
	global $userid;
	global $groupid;
	$mode = $formParams['mode'];
	$qSt = "SELECT * FROM sampletypes WHERE st_name = :type;";
	#print $qSt;
	$rSt = pdo_query($qSt, array( ':type' => $type ));
	#print_r($rSt);
	$list = $rSt[0]['list'];
	$category="";
	if ($cat) $category="&category=$cat";
	if($mode == "display"){
		print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
		print "<div class=\"displayField\"><a href=
			\"list.php?list=$list$category&ref=$id\">
			$text</a></div>";
		print "</div>";
	}	
}

function printTextArea($label, $field, $formParams){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	if (!isset($fields[$field])) $fields[$field] = '';
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>\n";
	if($mode == "modify"){
		print "<div class=\"formField\"><textarea name=\"${table}_0_$field\" class=\"form\">$fields[$field]</textarea></div>\n";
	}	
	if($mode == "display"){
		print "<div class=\"displayField\">$fields[$field]</div>\n";
	}
	print "</div>\n\n";
}

function printSeqEditor($seq){
	$lineLength = 110;
	$seq = fastaseq($seq, "<br/>", $lineLength);
	print "<div id=\"seqEditor\"><span id=\"editDNA\">$seq</span>\n";
	print "<span id=\"editProtein\"></span>\n";
	$lines = strlen($seq) ? explode("<br/>",$seq) : array();
	print "<div id=\"lineNumbers\">";
	for($l = 0; $l < sizeof($lines); $l++){
		$line = "<div style=\"float: right; position: relative; top: 0.4em;\">";
		$pos = $l * $lineLength + 1;
		for($j = 0; $j < $lineLength; $j++){
			$line .= ($j % 10 == 0) ? "." : "&nbsp;";
		}
		$line .= "</div>";
		print "<div style=\"float: left; width: 60px\">$pos</div>$line<br/>";
	}
	print "</div></div>\n";
}

function printSequenceField($label, $type, $field, $formParams, $area, $seqEditor){
	$table = $formParams['table'];
	$fields = $formParams['fields'];
	$mode = $formParams['mode'];
	$seq = $fields[$field];
	if(($type == 'oligo' or $type == 'DNA')){
		$seqLen = seqlen($seq);
	}
	$label = "$label:";

	print "<div class=\"formRow\"><div class=\"formLabel\">$label";
	if (sizeof ($seq)){
		if ($type == 'oligo'){
			printOligoData($formParams, $field);
		}
		if ($type == 'DNA'){
			print "<div>($seqLen bp)</div>";
		}
		if(($type == 'oligo' or $type == 'DNA') and isset($fields['trackID'])){
			print "<div id=\"DNAinfo\" style=\"margin-top: 2px\">
				<a href=\"sequence.php?table=$table&amp;field=$field
				&amp;id=${fields['trackID']}&amp;process=ic\">Inverse Complement</a></div>";
		}
		if($type == 'protein'){
			$mw = round(protein_molecular_weight($seq));
			$len = strlen(remove_non_coding_prot($seq));
			$a = molar_absorption_coefficient_of_prot($seq);
			printf("<div id=\"Proteininfo\" style=\"margin-top: 5px\">
			<a href=\"protein_properties.php?id=${fields['trackID']}\">
			Mw = $mw Da<br/> Length = $len<br/> Abs = %4.2f /(mg/ml)<br/></a></div>", $a);
		}
	}
	print "</div>\n";
	
	if($mode == "modify"){
		if ($area){
			print "<div class=\"formField\">";
			if ($seq && $seqEditor){
				$displayEditor = 'block';
				$displayField = 'none';
			} else {
				$displayEditor = 'none';
				$displayField = 'block';
			}
			print "<div id=\"SeqField\" class=\"seqtext\"  style=\"display: $displayField\">";
			print "<textarea id=\"$field\" class=\"form\" 
					name=\"${table}_0_$field\" 
					rows=\"10\">$seq</textarea>";
			print "</div>\n";
			if ($seq) $seq = fastaseq($seq, "\n", 0);
			print "</div>\n";
			print "<div id=\"SeqEditor\" style=\"display: $displayEditor\">";
			print "<div style=\"float: right\" class=\"button\" onClick =\"switchToSeqField()\">edit sequence</div>";
			printSeqEditor($seq);
			print "</div>";
		} else {
			print "<div class=\"formField\"><input type=\"text\" id=\"$field\"  class=\"textfield\" 
					onBlur=\"FilterSequence(this,'$type');\"
					name=\"${table}_0_$field\" 
					rows=\"10\" value=\"$seq\"/></div>";
		}
	}	
	if($mode == "display"){
		print "<div class=\"displayField\">";
		if ($seqEditor){
			print "</div>";
			printSeqEditor($seq);
		} elseif ($type == 'oligo'){
			print "<span style=\"\">$seq</span></div>";
			//printOligoData($formParams, $field);
			print "</div>";
		} else {
			print "<div><pre class=\"sequence\"><span style=\"position: relative; z-index: 2;\" id=\"disp_$field\">$seq</span>\n";
			print "</pre></div>\n";
			print "</div>";
		}
	}
}

function getComboBox($field, $table, $mode, $choices, $match, $action=null, $link=null){
	if ($mode == 'modify'){
		$cmbBox = "<div class=\"formField\">";
		if ($field){
			$name= "${table}_0_$field";
		} else {
			$name= "NA";
		}
		$cmbBox .= "<select id=\"cmb$field\" style=\"width: 100%;\" name=\"$name\"";
		if ($action) $cmbBox .= $action;
		$cmbBox .= ">\n";
		//$cmbBox .= "<option value=\"NA\"/>\n";
		$cmbBox .= "<option value=\"\">***none***</option>\n";
		#print_r($choices);
		if ($choices){
			foreach ($choices as $choice){
				$cmbBox .= "<option value=\"${choice['trackID']}\"";
				if ($match == $choice['trackID']){
					$cmbBox .=  " selected";
				}
				$cmbBox .= ">${choice['name']}</option>\n";
			}
		}
		$cmbBox .= "</select>\n\n";
	} else {
		$cmbBox = "<div class=\"displayField\">";
		if ($choices){
			foreach ($choices as $choice){
				$redchoices[$choice['trackID']]=$choice['name'];
			}
		}
		if ($link and isset($match) and $match ){
			$cmbBox .= "<a href=\"editEntry.php?id=$match&amp;mode=display\"> ${redchoices[$match]}</a>";
		} elseif ( isset($match) and $match){
			 $cmbBox .= "${redchoices[$match]}";
		}
		//$cmbBox = "</div>";
			
	}
	return $cmbBox;
}

function printComboBox($label, $field, $formParams, $choices, $match, $action=null, $link=null){
	$table = $formParams['table'];
	$mode = $formParams['mode'];
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	print getComboBox($field, $table, $mode, $choices, $match, $action, $link);
	print "</div></div>\n";
}

function getStatus($statusNr){
	$status = pdo_query("SELECT statusName FROM itemstatus WHERE statusNr=:statusNr;",
      array(':statusNr' => $statusNr));
	return $status[0]['statusName'];
}

function getCrossCombobox($connection, $table, $type, $fcounter, $mode, $userid){
    $selected = False;
    $option =  '';
    $fstyle = '';
    if ($connection != None){
		$fid = $connection['record'];
		$fragment = getRecord($fid, $userid);
		$type = ($fragment['type']) ? $fragment['type']: $fragment['st_name'];
		$table = $fragment['table'];
    } else {
		$connection = array('connID' => -1, 'start' => '', 'end' =>'');
    }
    //print "if:$fid, t:$type";
    //get choices
    $columns = array('tracker.trackID', ":table.name");
    $choices = array();
    if ($table == 'fragments'){
	$rows = getRecords($table, $userid, array(':table'=>$table, ':type'=>$type), $columns, " type=:type ");
    } else {
	$rows = getRecords($table, $userid, array(':table'=>$table), $columns);
    }
    foreach ($rows as $row) {
            $choices[$row['trackID']] = $row['name'];
    }

    print "<div id=\"$type$fcounter\" style=\"height: 1.5em;\">\n";
    if ($mode == 'display'){
		$display = "";
		$formDisplay = "display: none; ";
    } else {
		$display = "display: none; ";
		$formDisplay = '';
		print "<span style=\"width:20%; clear: left; float:left; text-align:left; margin-right:10px;height:1.5em;\">$type:</span>\n";
    }
	print "<span style=\"$display \"><a title=\"$type\" style=\"float: left\" href=\"editEntry.php?id=$fid&amp;mode=display\">
		${fragment['name']}</a>&nbsp;";
	if ($connection['start'] != 0 || $connection['end'] ) {
		print " (${connection['start']}-${connection['end']})";
	}
	print "</span>\n";
	
	//form fields
	print "<div style=\"$formDisplay\">";
	print "<select $hidden style=\"width: 35%; float: left\" name=\"connections_${fcounter}_record\">\n";
	//print "<option value=\"NA\"></option>\n";
	$selFrag = 0;
	foreach ($choices as $cid => $fname){
		print "<option value=\"$cid\"";
		if ($connection != None and !$selected and $cid == $fid){
			print  " selected ";
			$selected = True;
		}
	    print ">$fname</option>\n";
	}
    print "</select>\n\n";
    if($table == 'plasmids'){
	print "<input style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_start\" value=\"${connection['start']}\"/>\n";
	print "<input style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_end\" value=\"${connection['end']}\"/>\n";
	print "<select style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_direction\">\n";
	    $directions = array(1=>'forward', 0=>'reverse');
	    foreach($directions as $d => $dname){
		    print "<option value=\"$d\"";
		    if ($connection != None and $connection['direction']==$d){
			    print  " selected ";
		    }
		    print ">$dname</option>\n";
	    }
	    print "</select>\n";
    }
	print "<input type=\"hidden\" name=\"connections_${fcounter}_connID\" value=\"${connection['connID']}\"/>\n";
    print "<img onClick=\"$(this).getParent().getParent().destroy()\"
		style=\"float: left; display:inline; margin: 0.2em; cursor: pointer; vertical-align: middle\" alt=\"delete\" src=\"img/delete-item.png\" />";
    print "<div style=\"padding: 0 0 0.5em 0;\"></div>\n";
	print "</div>\n";
    print "</div>\n\n";
}

function getCrossAutoselectField($connection, $table, $type, $fcounter, $mode, $userid){
    if (isset($connection)){
		$fid = $connection['record'];
		$fragment = getRecord($fid, $userid);
		$type = ($fragment['type']) ? $fragment['type']: $fragment['st_name'];
		$table = $fragment['table'];
    } else {
		$connection = array('connID' => -1, 'start' => '', 'end' =>'');
    }
    print "<div id=\"$type$fcounter\" style=\"height: 1.5em;\">\n";
    if ($mode == 'display'){
		$display = "";
		$formDisplay = "display: none; ";
    } else {
		$display = "display: none; ";
		$formDisplay = '';
		print "<span style=\"width:20%; clear: left; float:left; text-align:left; 
		    margin-right:10px;height:1.5em;\">$type:</span>\n";
    }
    print "<span style=\"$display \"><a title=\"$type\" style=\"float: left\" href=\"editEntry.php?id=$fid&amp;mode=display\"> ${fragment['name']}</a>";
    if ($connection['start'] != 0 || $connection['end'] ) {
	    print " (${connection['start']}-${connection['end']})";
    }
    print "</span>\n";
	
    //form fields
    print "<div style=\"$formDisplay\">";

    $name = "connections_${fcounter}_record";
    $elementID = $name;

    getAutoselectField($table, $mode, $elementID, $name, $fid, 'fragmentField');
	//print_r($connection);

    if($fragment['type'] == 'gene' || $fragment['type'] == 'PCR' || $connection['connID'] == -1){
	print "<input style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_start\" value=\"${connection['start']}\"/>\n";
	print "<input style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_end\" value=\"${connection['end']}\"/>\n";
	print "<select style=\"width: 10%; float: left;\" name=\"connections_${fcounter}_direction\">\n";
	    $directions = array(1=>'forward', 0=>'reverse');
	    foreach($directions as $d => $dname){
		    print "<option value=\"$d\"";
		    if ($connection != Null and $connection['direction']==$d){
			    print  " selected ";
		    }
		    print ">$dname</option>\n";
	    }
	    print "</select>\n";
	}

    print "<input type=\"hidden\" name=\"connections_${fcounter}_connID\" value=\"${connection['connID']}\"/>\n";
    print "<img onClick=\"destroyFrag(this)\"
		style=\"float: left; display:inline; margin: 0.2em; cursor: pointer; vertical-align: middle; height:1.5em;\" title=\"delete\" alt=\"delete\" src=\"img/delete-item.png\" />";
    print "<div style=\"padding: 0 0 0.5em 0;\"></div>\n";
	print "</div>\n";
    print "</div>\n\n";
}

function getAutoselectField($table, $mode, $elementID, $post_name, $trackID, $class){
    global $userid;
    if ($mode == "modify"){
	print "<input class=\"$class\" float: left\" id=\"$elementID\" name=\"$post_name\"value=\"$trackID\"/>";
    }
    if ($mode == "display"){
	if (isset($trackID)) $record = getRecord($trackID, $userid);
	print "<span class=\"\"><a href=\"editEntry.php?id=$trackID&amp;mode=display\" >".$record['name']."</a></span>";
	print "<input type=\"hidden\" value=\"$trackID\" columns=\"50\" id=\"$elementID\" />";
    }
    ?>
	<script type="text/javascript">
	    window.addEvent('domready', function() {
		new Autocompleter.labdb("<?php print $elementID; ?>", 'autocomplete.php', {
		    'postData': {
		    'field': 'name', // send additional POST data, check the PHP code
		    'table': '<?php print $table;?>',
		    'extended': '1',
		    },
		});
	    });
	</script>
    <?php
}

function printCrossCombobxs($id, $types, $fcounter, $formParams, $unlink=False){
	$table = $formParams['table'];
	$mode = $formParams['mode'];
	global $userid;
	print "<div class=\"formRow\"><div class=\"formLabel\">Building Blocks:</div>\n";
	print "<div id=\"xcmbx\" class=\"displayField\">";
	if ($mode == 'modify'){
		print "<div style=\"\">\n";
	} else {
		print "<div style=\"display:none\">\n";
	}
	foreach ($types as $name=>$table){
		print "<div id=\"$name\" data-table=\"$table\" style=\"display: inline; color: #4682B4; margin-right: 1em;  height:2em; cursor:pointer\">Add $name</div>\n";
	}
	print "<div style=\"padding: 0 0 0.5em 0;\"></div>\n";
	print "</div>\n";
	
	#### gene fragments section
	$frags = array();
	$fcounter = 0;
	print "<div id=\"xcmbxFrags\" style=\"padding: 0em 0 0.5em 0\">\n";
	if ($id){
		$cnxs = getConnections($id);
		//if ($mode == 'display'){
		//	## header
		//	print "<div style=\"border-bottom: solid black 1px; overflow: hidden; margin-bottom: 10px \">
		//		<span style=\"width:20%; float:left; text-align:left; margin-right:10px;\">Type</span>\n";
		//	print "<span style=\"width: 50%; float: left\">Name</span>";
		//	print "<span style=\"width: 20%\">Position</span>";
		//	print "</div>\n";
		//}
		foreach ($cnxs as $c){
			if ($unlink == True) $c['connID'] = -1;
			getCrossAutoselectField($c, $table, Null, $fcounter, $mode, $userid);
			$fcounter++;
		}
	}
	print "</div>\n";
	print "</div></div>\n\n";
	return $fcounter;
}

function initProjects($noUserFilter, $noProjectFilter){
	global $project;
	global $projectSelect;
	global $currUid;
	if (!$noProjectFilter){
		if (array_key_exists('project', $_SESSION)) $project = $_SESSION['project'];
		if (array_key_exists("project", $_GET)) $project = $_GET["project"];
		if ($project == -1) {
			unset($_SESSION['project']);
			unset($project);
		}
		if (isset($project)){
			$_SESSION['project'] = $project;
			$projectSelect = array('q'=>" (tracker.project = :project)", 'vars'=>array(':project'=>$project));
		}
	}
	if (!$noUserFilter){
		if (array_key_exists('currUser', $_SESSION)) $currUid = $_SESSION['currUser'];
		if (array_key_exists("currUser", $_GET)) $currUid = $_GET["currUser"];
		if (!$currUid) $currUid = $userid;
		#print "userid: $currUid";
		if ($currUid == -1) {
			$_SESSION['currUser'] = $currUid;
			unset($currUid);
		}
		if (isset($currUid)){
			$_SESSION['currUser'] = $currUid;
			#$userSelect = " (tracker.owner = '$currUid')";
		}
	}
}

function setupProjects($userid){
	$pq = "SELECT * FROM projects WHERE parent=0";
	$projects = pdo_query($pq);
	print "<script type=\"text/javascript\" language=\"javascript\">\n<!--\n";
	print "var projects = new Object();\n";
	foreach ($projects as $project){
		$spq = "SELECT * FROM projects WHERE parent=:projectID";
		$spr = pdo_query($spq, array(':projectID' => ${project['id']}));
		print "var project = new Array();\n";
		print "projects[${project['id']}] = project;\n";
		print "project.push(new Option('*',0, true));\n";
		foreach ($spr as $subProject){
			print "var subProject = new Option('${subProject['name']}', 	
				'${subProject['id']}', false";
			//if ($project['id'] == $_SESSION['project'] AND
			//	$subProject['id'] == $_SESSION['subProject']){
			//	print ", true";
			//}
			print ");\n";
			print "project.push(subProject);\n";
		}
	}
	print "//-->\n</script>";

}

function getProjectCmbxs($projectID, $curUser){
	global $userid;
	$uq = "SELECT groups.belongsToGroup AS trackID, `fullname` AS `name` FROM groups
		JOIN user ON groups.belongsToGroup=user.ID WHERE groups.userid=:userid
		ORDER BY user.groupType";
	#print $uq;
	$users = pdo_query($uq, array(':userid' => $userid));
	if (!$users) $users = array();
	$columns = array('tracker.trackID','projects.name');
	$projects = getRecords('projects', $userid, array(':curUser'=>$curUser), $columns, " (owner = ':curUser') ");
	if (!$projects) $projects = array();
	$choices = array(array($curUser,$users,"currUser", "User: "),array($projectID,$projects,"project", "Project: "));
	print "<div class=\"project\">";
	foreach ($choices as $choice){
		print "<span class=\"projectCombo\">${choice[3]}</span>";
		$cmbBox = "<span class=\"projectCombo\">";
		$cmbBox .= "<select style=\"width: 200px;\" name=\"${choice[2]}\" 
				onchange=\"document.f1.submit();\">\n";
		$cmbBox .= "<option value=\"-1\">*</option>\n";
		foreach ($choice[1] as $item){
			$cmbBox .= "<option value=\"${item['trackID']}\"";
			if ($choice[0] == $item['trackID']){
				$cmbBox .=  " selected";
			}
			$cmbBox .= ">${item['name']}</option>\n";
		}
		$cmbBox .= "</select></span>\n\n";
		print $cmbBox;
	}
	print "</div>";
}

function printSubmitButton($formParams, $button){
	$mode = $formParams['mode'];
	if ($mode == 'modify'){
		//echo "<div class=\"formRow\">\n";
		//echo "<div id=\"log_res\"><!-- spanner --></div>\n";
		////echo "<input name=\"submitok\" type=\"submit\" value=\"$button\"/>\n";
		//echo "<img id=\"submit\" title=\"Save and Close\" src=\"img/Save.png\" />\n";
		//echo "<img id=\"apply\" title=\"Apply\" src=\"img/Apply.png\" />\n";
		//echo "<img id=\"stop\" title=\"Cancel\" src=\"img/Stop.png\" />\n";
		//echo "</div>\n";

		echo "<div class=\"formRow\">\n";
		echo "<div id=\"log_res\"><!-- spanner --></div>\n";
		//echo "<input name=\"submitok\" type=\"submit\" value=\"$button\"/>\n";
		echo "<div id=\"submit\" class=\"submitButtons\">Save</div>\n";
		echo "<div id=\"apply\" class=\"submitButtons\">Apply</div>\n";
		echo "<div id=\"stop\" class=\"submitButtons\">Cancel</div>\n";
		echo "</div>\n";

	}
}

function printCloseAddFragmentButton($formParams, $id){
	$mode = $formParams['mode'];
	if ($mode == 'modify'){
		echo "<div class=\"formRow\">";
		echo "<input name=\"closeAddFrag\" type=\"button\" onClick=\"CloseAddFragment();\" value=\"Close and add Fragment\"/></div>";
	}
	
}


function printOligoData($formParams, $field){
	$row = $formParams['fields'];
	$sequence = $row[$field];
	$tm = Tm($sequence,'bre',$row['Saltconc']*1E-3, $row['PCRconc']*1E-9);
	if (is_numeric($tm)){
	    $tm = sprintf("%6.1f", $tm);
	}
	if (CountATCG($sequence)) $gc = sprintf("%6.1f",CountCG($sequence) / CountATCG($sequence) * 100);
	$len = strlen($sequence);
	print "<div style=\"\">($len bp, Tm: $tm &deg;C; GC: $gc%)</div>";
}

function printProjectFields($formParams){
	global $userid;
	$columns = array('tracker.trackID','projects.name');
	$projects = getRecords('projects', $userid, array(), $columns);
	$row = $formParams['fields'];
	printComboBox('Project', 'project', $formParams, $projects, $row['project']);
	#setupProjects();
} 
function listActions($id, $hexID){
   	$action = "<td class=\"lists\" width=\"1%\">";
	$action .= "<input type=\"checkbox\" name=\"selection[]\" value=\"$id\"/>\n";
	$action .= "<input type=\"hidden\" name=\"hexID_$id\" value=\"$hexID\"/>\n";
	$actionID = "nav";
	$action .= "</td>\n";
	return $action;
}

function error($msg) {
   ?>
   <html>
   <head>
   <script language="JavaScript">
   <!--
       alert("<?php print $msg?>");
       history.back();
   //-->
   </script>
   </head>
   <body>
   </body>
   </html>
   <?php
   exit; 
}

function getTable($trackerID){
	$r1 = getSampleType($trackerID);
	#print_r($r1);
	if($r1) return $r1['table'];
}

function getSampleType($trackerID){
	if (!is_numeric($trackerID)) return;
	$q1 = "SELECT sampletypes.* FROM tracker INNER JOIN sampletypes ON sampletypes.id=tracker.sampleType WHERE trackID=:trackerID";
	#print "$q1";
	$r1 = pdo_query($q1, array(':trackerID' => $trackerID));
	//print_r($r1);
	if($r1) return $r1[0];
}

function getRecord($trackerID, $userid, $mode='display'){
	$table = getTable($trackerID);
	if ($mode == 'modify'){
		$accesscontrol = "AND ((owner = $userid AND permOwner > 1) OR
				  (permissions.userid=groups.belongsToGroup AND
				  permission > 1))";
		
	} else {
		$accesscontrol = "AND ((owner = $userid) OR
				  (permissions.userid=groups.belongsToGroup AND
				  permission > 0))";
	}
	if ($table){
		$q2 = "SELECT MAX(permission) AS maxpermission, tracker.*, $table.*, sampletypes.*,
		       DATE_FORMAT(tracker.created,'%m/%d/%y') AS createDate,
		       DATE_FORMAT(tracker.changed,'%m/%d/%y') AS changeDate, user.userid AS username,
			   CONCAT(sampletypes.st_code, '.', LPAD(CONV($table.id, 10, 36), 3, '0')) as hexID,
		       user.fullname FROM permissions
		       LEFT JOIN tracker ON tracker.trackID=permissions.trackID
		       LEFT JOIN sampletypes ON sampletypes.id=tracker.sampleType 
		       LEFT JOIN user ON user.id =  tracker.owner
		       LEFT JOIN $table ON tracker.sampleID=$table.id
		       JOIN groups ON groups.userid=:userid
		       WHERE permissions.trackID=:trackerID $accesscontrol
		       GROUP BY user.id";
		//print "<br/>$q2<br/>";
		$r2 = pdo_query($q2, array(':userid' => $userid, ':trackerID' => $trackerID));
		//print_r($r2);
		if ($r2) return $r2[0];
	}
}

function deleteRecord($trackerID, $userid, $groupids){
	$r = getRecord($trackerID, $userid, $groupids);
	if (($r['owner']==$userid and $r['permOwner']>1) or getPermissions($trackerID,$userid) > 1){
		$q = "UPDATE tracker SET deleted=CURDATE() WHERE trackID=:trackerID";
		$r = pdo_query($q, array(':trackerID'=>$trackerID));
	}
}

function changePermission($trackerID, $newuser, $permission, $userid){
	if (getRecord($trackerID, $userid, null, 'modify')){
		$q = "SELECT MAX(permission) FROM permissions
			WHERE trackID=:trackerID AND permissions.userid=:newuser";
		$r = pdo_query($q, array(':trackerID'=>$trackerID, ':newuser'=>$newuser));
		$existing = $r[0][0];			
		if ($existing==NULL){
			$pq = "INSERT INTO permissions (trackID,userid,permission)
			 VALUES (:trackerID, :newuser, :permission)";
		}else{
			$pq = "UPDATE permissions SET permission=:permission WHERE
			trackID=:trackerID AND userid=:newuser";
		}
		 pdo_query($pq, array(':trackerID'=>$trackerID, ':newuser'=>$newuser, ':permission'=>$permission));
	}
}

function getPermissionString($trackerID){
	$q = "SELECT fullname, permission, owner FROM permissions
		JOIN user ON user.ID=permissions.userid
		LEFT JOIN tracker on tracker.trackID=permissions.trackID
		WHERE permissions.trackID=:trackerID";
	#print $q;
	$r = pdo_query($q, array(':trackerID'=>$trackerID));
	$permString = array('None','Read','Write');
	$n=0;
	$str = '';
	foreach ($r as $perm){
		if ($n > 0) $str.=", ";
		$permstr = $permString[$perm['permission']];
		$str .= "${perm['fullname']}:$permstr";
		$n++;
	}
	return $str;
}

function getPermissions($trackerID, $userid){
	$q = "SELECT MAX(permission) FROM permissions
	        JOIN groups ON groups.userid=:userid
		WHERE trackID=:trackerID AND permissions.userid=groups.belongsToGroup";
	$r = pdo_query($q, array(':trackerID'=>$trackerID, ':userid'=>$userid) );
	return $r[0];
}

function getHexIDSQL($table){
	return "CONCAT(sampletypes.st_code, '.', LPAD(CONV($table.id, 10, 36), 3, '0'))";
}

function getRecords($table, $userid, $vars, $columns, $where='', $order='', $count = 0, $join='', $noTrack=0){
	global $_SESSION;
	global $noUserFilter;
  if(!is_array($vars)) $vars = array();
	$currUid = $_SESSION['currUser'];
	if($noUserFilter or $currUid == -1){
    $vars[':userid'] = $userid;
		$accesscontrol = " AND (tracker.owner = :userid OR (permissions.permission > 0
				   AND groups.belongsToGroup=permissions.userid))";
	}elseif($currUid == $userid){
    $vars[':userid'] = $userid;
		$accesscontrol = " AND tracker.owner = :userid ";
	} else {
		$accesscontrol = " AND (belongsToGroup = :currUid
				   AND permissions.userid =:currUid
				   AND permissions.permission > 0)";
    $vars[':currUid'] = $currUid;
	}
	$q = '';
	if($count){
		$q1 = "SELECT  COUNT(*) FROM ( SELECT DISTINCT tracker.* ";
	} else {
		$q1 = "SELECT DISTINCT ";
		$n = sizeof($columns);
		$i = 1;
		foreach($columns AS $col){
			$q1 .= "$col";
			($i < $n)? $q1 .=", ":$q1 .=" ";
			$i += 1;
		}
	}
	$q1 .= ", $table.id FROM permissions
  	        JOIN groups ON groups.userid = :userid
		JOIN tracker ON permissions.trackID = tracker.trackID
		JOIN user ON user.ID=tracker.owner
		JOIN (sampletypes, $table) ON (sampletypes.id=tracker.sampleType AND $table.id=tracker.sampleID)";
	$q1 .= " $join ";
	$q1 .= "WHERE sampletypes.table='$table' $accesscontrol AND deleted is NULL";
	if ($where) $q1 .= " AND $where ";
	$q1 .= " ORDER BY ";
	$order? $q1 .= "$order" : $q1 .= "$table.id DESC";
	if($count){
		$q1 .= ") AS foo";
	}

  if($noTrack){
  unset($vars[':userid']);
  $q1 = "SELECT DISTINCT ";
  $n = sizeof($columns);
  $i = 1;
  foreach($columns AS $col){
    $q1 .= "$col";
    ($i < $n)? $q1 .=", ":$q1 .=" ";
    $i += 1;
  }
      $q1 .= " FROM $table";
      if ($where) $q1 .= " WHERE $where";
      $q1 .= " ORDER BY ";
      $order? $q1 .= "$order" : $q1 .= "$col DESC";
      $q1 .= ";";
  }

	#print "<br/>$q1<br/>";
	$r1 = pdo_query($q1, $vars);
	if(isset($r1) & is_array($r1)){
	    return $r1;
	} else {
	    return array();
	}
}

function newRecord($table, $ds, $userid){
	if (array_key_exists('project', $ds)) $project = ($ds['project'] == '' ? -1 : $ds['project']);
	#$subProject = ($ds['subProject'] == '' ? -1 : $ds['subProject']);
	unset($ds['project']);
	#unset($ds['subProject']);
	$stq = "SELECT * FROM sampletypes WHERE `table`=:table";
	#print $stq;
	$str = pdo_query($stq, array(':table'=>$table));
	$st = $str[0]['id'];
	# insert query for data record
	#print_r($ds);
  $vars = array(':table'=>$table);
	$iq = "INSERT INTO `:table` (";
	$cnt = 0;
	$end = sizeof($ds);
	foreach ($ds as $field => $dat){
		$iq .= ":field$cnt";
    $vars[":field$cnt"] = $field;
		$cnt += 1;
		if($cnt <> $end) $iq .= ", ";
	}
	$iq .= ") VALUES (";
	$cnt = 0;
	foreach ($ds as $field => $dat){
		#$dat = escape_quotes($dat);
		if ($dat == 'mainID') $dat = $id;
		$iq .= "':dat$cnt'";
    $vars[":dat$cnt"] = "$dat";
		$cnt += 1;
		if($cnt <> $end) $iq .= ", ";
	}
	$iq .= ")";
	//print "$iq<br/>";
	$sampleID = pdo_query($iq, $vars);
	# insert query for tracker
	$iq = "INSERT INTO tracker (sampleID, sampleType, project, created, owner, permOwner, deleted, permGroup, permOthers)";
	$iq .= " VALUES (:sampleID, :st, :project, NOW(), :userid, 2, '0-0-0', 2, 0)";
	//print "$iq<br/>;
  $vars = array(':st'=>$st, ':project'=>$project, ':userid'=>$userid);
	$newTrackID = pdo_query($iq, $vars);
	# setup permissions
	$gq = "SELECT * FROM groups JOIN user ON belongsToGroup=user.id WHERE groups.userid=:userid AND user.groupType!=3;";
	#print "$gq<br/>";
	$groups = pdo_query($gq, array(':userid'=>$userid));
	#print_r($groups);
	foreach($groups as $g){
		$perm = $g['defaultPermissions'];
		if($g['groupType']==1){
			if ($str[0]['labPermission']>$perm){
				$perm = $str[0]['labPermission'];
			}
		}
		if ($perm > 0){
			$pq = "INSERT INTO permissions (trackID,userid,permission)
			 VALUES ($newTrackID,${g['belongsToGroup']},:perm)";
			 #print "$pq<br/>";
			 pdo_query($pq, array(':perm'=>$perm));
		}
	}
	
	#setup admin rights
	#find group that has admin rights in the users labgroup
	$aq = "SELECT DISTINCT user.ID FROM user JOIN groups ON belongsToGroup =
			(SELECT user.ID FROM groups JOIN user ON belongsToGroup=user.id
			WHERE groups.userid=:userid AND user.groupType=1)
		WHERE user.groupType=3;";
	$ags = pdo_query($aq, array(':userid'=>$userid));
	$admin = $ags[0]['ID'];
	$aiq = "INSERT INTO permissions (trackID,userid,permission)
		 VALUES ($newTrackID, :admin,1)";
		 #print "$aiq<br/>";
	pdo_query($aiq, array(':admin'=>$admin));
	#insert read permission for this group
	return $newTrackID;
}

function updateRecord($trackerID, $ds, $userid, $groupids){
	$table = getTable($trackerID);
  #$vars = array(':table'=>$table);
	$uq = "UPDATE $table, `tracker` SET ";
	#print_r($ds);
  $n = 0;
	foreach ($ds as $key => $field){
		$uq .= " `$key`=:field$n";
    #$vars[":key$n"] = $key;
    $vars[":field$n"] = $field;
    $n += 1;
		if (next($ds)!==FALSE) $uq .= ',';
	}
	$uq .= ", tracker.changed=NOW() ";
	$uq .= "WHERE tracker.sampleID=`$table`.id AND trackID=:trackerID;";
  $vars[':trackerID'] = $trackerID;
	//print "$uq <br/>";
	//$r = pdo_query($uq);
	pdo_query($uq, $vars);
}

function getConnections($trackerID){
	$q = "SELECT * FROM connections WHERE belongsTo=:trackerID";
	$r = pdo_query($q, array( 'trackerID' => $trackerID ));
	return $r;
}

function getConnection($connID){
	$q = "SELECT * FROM connections WHERE connID=:connID";
	$r = pdo_query($q, array(':connID'=>$connID));
	return $r[0];
}

function saveURI($uri){
	include('config.php');
	$link = mysql_connect($host, $username, $password);
	$uri = escape_quotes($uri, $link);
	$query = "UPDATE `settings` SET `value`=:uri WHERE `variable`='lastView';";
	pdo_query($query, array(':uri'=>$uri));
	return;
}
function getLastView(){
	$query = "SELECT * FROM `settings` WHERE variable='lastView'";
	$r = pdo_query($query);
	return $r[0]['value'];
}

function listProcessor($actions){
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function showOptions(element){
	(element.value =='2')?ChangeDisplayDiv(['permTable'], 'block'):ChangeDisplayDiv(['permTable'], 'none');
	(element.value =='0')?ChangeDisplayDiv(['oligoOptions'], 'block'):ChangeDisplayDiv(['oligoOptions'], 'none');
}

function get_po_number(){
    var ponumber=prompt("Please enter PO number if you have one for these items.");
    if (ponumber!=null && ponumber!="") {
	document.mainform.poNumber.value = ponumber;
    }
    return true;
}

function purgeUnchecked(){
    var inputs =  document.getElementsByTagName("input");
    var checkCount = 0;
    for(var i=0; i < inputs.length; i++){
	var cbx = inputs[i];
	if (cbx.name == "selection[]" && !cbx.checked){
		disableRow(cbx.value, true);
	}
	if (cbx.name == "selection[]" && cbx.checked){
	    disableRow(cbx.value, false);
	    checkCount += 1;
	}
    }
    if (checkCount > 100){
       alert("More than 100 items selected. Process will likely fail!");
    }
}

function disableRow(rowID, disabled){
    var trs = document.getElementsByTagName("tr");
    for(var i=0; i < trs.length; i++){
	var tr = trs[i];
	if (tr.dataset.record_id == rowID){
	    var inps =  tr.getElementsByTagName("input");
		for(var j=0; j < inps.length; j++){
		    inps[j].disabled = disabled;
	    }
	}
    }
    
}

// -->
</SCRIPT>
<tr><td colspan = "100"><input type="checkbox" name="allbox" value="1"/ onclick="selectAll();"> select all</td></tr>
<tr><td colspan = "100"><select style="width: 20em;float:left;" id="SelAction" name="action"" onChange="showOptions(this);">

<?php
	print "<option value=\"-1\">Choose Action:</option>";
	if (in_array(0, $actions)) print "<option value=\"0\">&nbsp;-Export oligos to:</option>";
	if (in_array(1, $actions)) print "<option value=\"1\">&nbsp;-Write to files</option>";
	if (in_array(2, $actions)) print "<option value=\"2\">&nbsp;-Change Permissions:</option>";
	if (in_array(3, $actions)) print "<option value=\"3\">&nbsp;-Delete Records</option>";
	if (in_array(4, $actions)) print "<option value=\"4\">&nbsp;-Order placed</option>";
	if (in_array(5, $actions)) print "<option value=\"5\">&nbsp;-Item received</option>";
	if (in_array(6, $actions)) print "<option value=\"6\">&nbsp;-Item finished</option>";
	if (in_array(7, $actions)) print "<option value=\"7\">&nbsp;-Export in mediawiki format</option>";
	if (in_array(8, $actions)) print "<option value=\"8\">&nbsp;-Export for ordering</option>";
	if (in_array(9, $actions)) print "<option value=\"9\">&nbsp;-Mark as billed</option>";
?>
	</select>
	<div id="permTable" name="permTable" style="clear:both; display:none; margin-right:20px" ><table>
		<tr>
			<td/><td>None</td><td>Read</td><td>Write</td>
		</tr>
		<tr><td>
<?php
	$uq = "SELECT `ID` AS trackID, `fullname` AS `name` FROM `user`";
	$users = pdo_query($uq);
	if (!$users) $users = array();
	$formParams = array('mode'=>'modify','table'=>'users');
	#getComboBox($field, $table, $mode, $choices, $match, $action=null, $link=null){
	print getComboBox("user",$formParams['table'], $formParams['mode'],$users,null);
?>
			</td>
			<td><input type="radio" name="perm" value="0" checked></td>
			<td><input type="radio" name="perm" value="1"></td>
			<td><input type="radio" name="perm" value="2"></td>
		</tr>
	</table></div>
	<input id="poNumber" type="hidden" name="poNumber"/>
</td></tr>
<tr><td colspan = "8"><input type="Submit" value="Do it"></td></tr>
<?php
}


function UploadFiles($file){
	if (($file["size"] < 2000000)){
	  if ($file["error"] > 0)
	    {
	    echo "Return Code: " . $file["error"] . "<br />";
	    }
	  else
	    {
	    echo "Upload: " . $file["name"] . "<br />";
	    echo "Type: " . $file["type"] . "<br />";
	    echo "Size: " . ($file["size"] / 1024) . " Kb<br />";
	    #echo "Temp file: " . $file["tmp_name"] . "<br />";
	    $filePath = dirname(__FILE__)."/attachments/";
	    #print "<br/>".$filePath."<br/>";
	    if (file_exists($filePath . $file["name"]))
	      {
	      echo $file["name"] . " already exists. ";
	      }
	else
	      {
	      move_uploaded_file($file["tmp_name"],
	      $filePath . $file["name"]);
	      echo "Stored in: " . $filePath . $file["name"];
	      }
	    }
	  }
	else
	  {
	  echo "Invalid file";
	  }
}

function getRestrictionSites($digString, $dnaSequence){
	include('config.php');
  include('lib/restriction_digest.php');
  $digestion = digestDNA( array("sequence" => $dnaSequence, 'IIs' => 0, 'IIb' => 0) );

	$output = array();
	$sites = array();
	global $userid;
	global $noUserFilter;
	$sitelen = 4;
	//print $enzymes;
	$lab_key = "lab"; //keyword used to specify all enzymes available in Lab Enzymes box.
  $digParams = explode(" ", $digString);
  $enzymes = explode(",", $digParams[0]);
  $enzymeList = [];
	if(in_array($lab_key, $enzymes)){
		$noUserFilter = True;
		$enzys = getRecords('vials', $userid, array(), array('vials.name'), " boxes.name='Lab Enzymes' ", "", 0, " LEFT JOIN boxes ON vials.boxID=boxes.id ");
    #print "enzymes: "; print_r($enzys);
		$noUserFilter = False;
		if (sizeof($enzys) > 0){
			$enzymeList = [];
			foreach($enzys as $enzyme){
				$arr = explode(' ',trim($enzyme[0]));
				$enzyme = $arr[0];
				$enzymeList[] = $enzyme;
			}
			#print_r($enzymeList);
		}
  } 

  foreach($enzymes as $e){
    if (in_array($e, $enzymeList)) continue;
    $enzymeList[] = $e;
  }
	// $enzymes;
	$limit ='';
  if (in_array('all', $enzymes)) {
    $all = True; 
    $enzymeList = array('all');
  }
	if ( $digestion == Null || $digestion == '' ){
	    print "<script type='text/javascript'>alert('Problem with restriction sites! No restriction sites are displayed.');</script>";
	}
	//print "Tmp: $tmp<br/>Pos: $pos<br/>";
  if ($maxPos = array_search('-max', $digParams)){
    $maxCuts = $digParams[ $maxPos + 1]; 
  } else {
    $maxCuts = 100;
  }
  #print "Maxcuts: $maxCuts";
  $sites = "new Array(";
  //print_r($digestion[0]);
  $digsts = [];
  $enzymes_array[] = get_array_of_Type_II_endonucleases();
  $enzymes_array[] = get_array_of_Type_IIs_endonucleases();
  foreach($enzymes_array as $enz_arr){
    foreach($enzymeList as $testEnz){
      foreach($digestion[0] as $enzyme => $cuts ){
        if (array_key_exists($enzyme, $enz_arr) && in_array($testEnz, explode(",", $enz_arr[$enzyme][0])) &&  sizeof($cuts['cuts']) <= $maxCuts ) {
          $digsts[$testEnz] = $cuts;
        }
      }
    }
  }
  foreach($digsts as $enzyme_name => $cuts){
    foreach($cuts['cuts'] as $s => $xy){
      $site = "['$enzyme_name', '$s', {'background-color': \"white\",
        fill: \"black\", \"font-size\": '8', \"font-family\": \"Verdana\",
        cursor:\"pointer\", 'text-anchor' : \"middle\"}],";
      #print "<pre>$site</pre>";
      $sites .= $site;
    }
  }

      #print_r($enzymes_array[$enzyme][0]); print "<br/>";
	$sites .= ")";
	#print "<pre>". $sites . "</pre>";
	return $sites;
}

?>
