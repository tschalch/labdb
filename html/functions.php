<?php
#database functions
include('seq.inc.php');
include('protein.inc.php');


function pdo_query($q){
	try {
		include('config.php');
		//print "\"$q\"";
		$dbh = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		//$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = array();
		$i = 0;
	      	//print "query: $q <br/>";
	      	$dbhq = $dbh->query($q);
		//print_r($dbhq);
	      	if (substr_count($q,'INSERT')){
	      		#$dbh->exec($q);
	      		#print "query: $q <br/>";
	      		$result = $dbh->lastInsertId();
	      	}
	      	elseif ($dbhq){
	         		foreach ($dbhq as $row){
	         			#print_r($row);print "<br/>";
				#print "test<br/>";
	      			$result[$i] = $row;
	      			$i++;
	      		}
	      	}
	        $dbh = null;
		$result = escape_quotes($result);
		//print_r($result);
	      	return $result;
	} catch (PDOException $e) {
		print "Database Error!: " . $e->getMessage() . "<br/>";
		die();
	}
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
	$num = count($ds);
	$uq = "UPDATE `$table` SET ";
	foreach ($ds as $key => $field){
		$i++;
		if ($key == 'connID'){
			$uid = $field;
		} else {
			$uq .= "`$key` = '".$field."' ";
			if ($i < $num) $uq .= ',';
		}
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
		$value = ($fields[$field])?$fields[$field]:$default;
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

function printID($formParams){
	print "\n<div class=\"formRow idfield\"><div class=\"formLabel\">ID:</div>\n";
	$fields = $formParams['fields'];
	$value = ($fields['trackID'])? $fields['trackID']:"";
	print "<div id=\"id\" class=\"displayField\" style=\"background-color: white;\">$value</div>
			<input type=\"hidden\" id=\"inp_id\" name=\"id\" value=\"$value\"/>\n";
	print "</div>\n\n";
	if ($value != ''){
		printTimestamp($formParams);
	}
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
	if ($fields[$field]==null) $fields[$field] = $default;
	if ($fields[$field]!='0000-00-00' and $fields[$field]){
		$date = getdate(strtotime($fields[$field]));
	}
	#print "time:" . $fields[$field] . "<br/>";
	#print strtotime($fields[$field])."<br/>";
	#print_r(getdate(date("Y-m-d",$fields[$field])));
	print "<div class=\"formRow\"><div class=\"formLabel\">$label:</div>";
	if($mode == "modify"){
		$value = ($fields[$field])?$fields[$field]:$default;
		print "<div class=\"formField\">
			<input type=\"text\" id=\"${table}_0_${field}_m\" class=\"datefield\" size=\"3\" value=\"${date['mon']}\"/>
			<input type=\"text\" id=\"${table}_0_${field}_d\" class=\"datefield\" size=\"3\" value=\"${date['mday']}\"/>
			<input type=\"text\" id=\"${table}_0_${field}_y\" class=\"datefield\" size=\"5\" value=\"${date['year']}\"/> (Month/Day/Year)
			<input type=\"hidden\" id=\"${table}_0_$field\" name=\"${table}_0_$field\" value=\"${date['0']}\"/></div>";
	}	
	if($mode == "display"){
		if ($fields[$field]!='0000-00-00' and $fields[$field]){
			$date = date("m/d/Y",strtotime($fields[$field]));
		}
		print "<div class=\"displayField\">$date</div>\n";
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
		if($checked) print "<input type=\"hidden\" name=\"${table}_0_inStock\" value=\"0\"/>";
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
		print "<div class=\"formField\"><input type=\"text\" 
			name=\"${table}_0_$field\" class=\"textfield\" value=\"$fields[$field]\"></input></div>";
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
	$qSt = "SELECT * FROM sampletypes WHERE st_name = '$type';";
	#print $qSt;
	$rSt = pdo_query($qSt);
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
		$seqLen = CountATCG($seq);
	}
	$label = "$label:";

	print "<div class=\"formRow\" style=\"clear:both;width:100%;\"><div class=\"formLabel\">$label";
	if (sizeof ($seq)){
		if ($type == 'oligo'){
			printOligoData($formParams, $field);
		}
		if ($type == 'DNA'){
			print "<div>($seqLen bp)</div>";
		}
		if($type == 'oligo' or $type == 'DNA'){
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
			print "<div id=\"SeqField\" style=\"display: $displayField\">";
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
		if ($link){
			$cmbBox .= "<a href=\"editEntry.php?id=$match&amp;mode=display\"> ${redchoices[$match]}</a>";
		}else{
			$cmbBox .= "${redchoices[$match]}";
		}
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
	$status = pdo_query("SELECT statusName FROM itemstatus WHERE statusNr=$statusNr;");
	return $status[0]['statusName'];
}

function getCrossCombobox($connection, $table, $type, $fcounter, $mode, $userid){
    $selected = False;
    $option =  '';
    $fstyle = '';
    if ($connection != None){
		$fid = $connection['record'];
		$fragment = getRecord($fid, $userid, $groups);
		$type = ($fragment['type']) ? $fragment['type']: $fragment['st_name'];
		$table = $fragment['table'];
    } else {
		$connection = array('connID' => -1, 'start' => '', 'end' =>'');
    }
    //print "if:$fid, t:$type";
    //get choices
    $columns = array('tracker.trackID', "$table.name");
    $choices = array();
    if ($table == 'fragments'){
	$rows = getRecords($table, $userid, $columns, "type='$type' ");
    } else {
	$rows = getRecords($table, $userid, $columns);
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
    if($type == 'gene'){
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
		style=\"float: left; display:inline; margin: 0.2em; cursor: pointer; vertical-align: middle\" alt=\"delete\" src=\"img/b_drop.png\" />";
    print "<div style=\"padding: 0 0 0.5em 0;\"></div>\n";
	print "</div>\n";
    print "</div>\n\n";
}

function printCrossCombobxs($id, $types, $fcounter, $formParams){
	$table = $formParams['table'];
	$mode = $formParams['mode'];
	global $userid;
	global $groups;
	print "<div class=\"formRow\"><div class=\"formLabel\">Building Blocks:</div>\n";
	print "<div id=\"xcmbx\" class=\"displayField\">";
	if ($mode == 'modify'){
		print "<div style=\"\">\n";
	} else {
		print "<div style=\"display:none\">\n";
	}
	foreach ($types as $type){
		print "<div id=\"$type\" style=\"display: inline; color: #4682B4; margin-right: 1em;  height:2em; cursor:pointer\">Add $type</div>\n";
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
			getCrossCombobox($c, None, None, $fcounter, $mode, $userid);
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
		if ($project){
			$_SESSION['project'] = $project;
			$projectSelect = " (tracker.project = '$project')";
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
		if ($currUid){
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
		$spq = "SELECT * FROM projects WHERE parent=${project['id']}";
		$spr = pdo_query($spq);
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
	global $groups;
	$uq = "SELECT groups.belongsToGroup AS trackID, `fullname` AS `name` FROM groups
		JOIN user ON groups.belongsToGroup=user.ID WHERE groups.userid=$userid
		ORDER BY user.groupType";
	#print $uq;
	$users = pdo_query($uq);
	if (!$users) $users = array();
	$columns = array('tracker.trackID','projects.name');
	$projects = getRecords('projects', $userid, $columns, " (owner = '$curUser') ");
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
	$tm = sprintf("%6.1f",Tm($sequence,'bre',$row['Saltconc']*1E-3, $row['PCRconc']*1E-9));
	if (CountATCG($sequence)) $gc = sprintf("%6.1f",CountCG($sequence) / CountATCG($sequence) * 100);
	$len = strlen($sequence);
	print "<div style=\"\">($len bp, Tm: $tm&#176;C; GC: $gc%)</div>";
}

function printProjectFields($formParams){
	global $userid;
	global $groups;
	$columns = array('tracker.trackID','projects.name');
	$projects = getRecords('projects', $userid, $columns);
	$row = $formParams['fields'];
	printComboBox('Project', 'project', $formParams, $projects, $row['project']);
	#setupProjects();
} 
function listActions($id, $edit, $vial){
	$action = "<td class=\"lists\" width=\"1%\">";
	$action .= "<input type=\"checkbox\" name=\"selection[]\" value=\"$id\"/></td>\n";
	$action .= "<td class=\"lists\" style=\"text-align:center;\" width=\"2%\">";
	$spacerSize = 20;
	if ($edit) $spacerSize += 20;
	if ($vial) $spacerSize += 20;
	$action .= "<img style=\"float:left\" src=\"img/point.jpg\" width=\"${spacerSize}px\" height=\"0px\"/>";
	$action .= "<a style=\"\" href=\"newEntry.php?id=$id&amp;mode=modify\">
		  <img style=\"\" title=\"New record based on this one\" src=\"img/copy.png\" /></a>";
	if ($edit){
#			    <a style=\"display:inline\" href=\"\" onclick=\"deleteRecord($id);\"><img 
#			    style=\"display:inline\" alt=\"delete\" src=\"img/b_drop.png\" /></a>";
#		($public)? $perm = "img/unlock-icon.jpg" : $perm = "img/lock-icon.jpg";
		$action .= "<a style=\"\" href=\"editEntry.php?id=$id&amp;mode=modify\">
			  <img style=\"\" title=\"Edit record\" title=\"edit\"src=\"img/b_edit.png\" /></a>";
#		$action .= "<img style=\"display:inline\" onclick=\"changePermission($id);\" hspace=\"1px\" alt=\"lock/unlock record\"src=\"$perm\"/>";
	}
	if ($vial){
		$action .= "<a href=\"newEntry.php?form=frmVial&amp;mode=modify&amp;template=$id\">
		<img style=\"\" title=\"New vial based on this record.\"src=\"img/vial.png\" /></a>";
	}
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
	$q1 = "SELECT sampletypes.* FROM tracker INNER JOIN sampletypes ON sampletypes.id=tracker.sampleType WHERE trackID=$trackerID";
	#print "$q1";
	$r1 = pdo_query($q1);
	#print_r($r1);
	if($r1) return $r1[0];
}

function getRecord($trackerID, $userid, $mode='display'){
	global $_SESSION;
	$currUid = $_SESSION['currUser'];
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
		       user.fullname FROM permissions
		       LEFT JOIN tracker ON tracker.trackID=permissions.trackID
		       LEFT JOIN sampletypes ON sampletypes.id=tracker.sampleType 
		       LEFT JOIN user ON user.id =  tracker.owner
		       LEFT JOIN $table ON tracker.sampleID=$table.id
		       JOIN groups ON groups.userid=$userid
		       WHERE permissions.trackID=$trackerID $accesscontrol
		       GROUP BY user.id";
		//print "<br/>$q2<br/>";
		$r2 = pdo_query($q2);
		//print_r($r2);
		if ($r2) return $r2[0];
	}
}

function deleteRecord($trackerID, $userid, $groupids){
	$r = getRecord($trackerID, $userid, $groupids);
	if (($r['owner']==$userid and $r['permOwner']>1) or getPermissions($trackerID,$userid) > 1){
		$q = "UPDATE tracker SET deleted=CURDATE() WHERE trackID=$trackerID";
		$r = pdo_query($q);
	}
}

function changePermission($trackerID, $newuser, $permission, $userid){
	if (getRecord($trackerID, $userid, None, 'modify')){
		$q = "SELECT MAX(permission) FROM permissions
			WHERE trackID=$trackerID AND permissions.userid=$newuser";
		$r = pdo_query($q);
		$existing = $r[0][0];			
		if ($existing==NULL){
			$pq = "INSERT INTO permissions (trackID,userid,permission)
			 VALUES ($trackerID, $newuser, $permission)";
		}else{
			$pq = "UPDATE permissions SET permission=$permission WHERE
			trackID=$trackerID AND userid=$newuser";
		}
		 pdo_query($pq);
	}
}

function getPermissionString($trackID){
	$q = "SELECT fullname, permission, owner FROM permissions
		JOIN user ON user.ID=permissions.userid
		LEFT JOIN tracker on tracker.trackID=permissions.trackID
		WHERE permissions.trackID=$trackID";
	#print $q;
	$r = pdo_query($q);
	$permString = array('None','Read','Write');
	$n=0;
	foreach ($r as $perm){
		if ($n > 0) $str.=", ";
		$permstr = $permString[$perm['permission']];
		$str .= "${perm['fullname']}:$permstr";
		$n++;
	}
	return $str;
}

function getPermissions($trackID, $userid){
	$q = "SELECT MAX(permission) FROM permissions
	        JOIN groups ON groups.userid=$userid
		WHERE trackID=$trackID AND permissions.userid=groups.belongsToGroup";
	$r = pdo_query($q);
	return $r[0][0];
}

function getRecords($table, $userid, $columns, $where='', $order='', $count = 0, $join=''){
	global $_SESSION;
	global $noUserFilter;
	$currUid = $_SESSION['currUser'];
	if($noUserFilter or $currUid == -1){
		$accesscontrol = " AND (tracker.owner=$userid OR (permissions.permission > 0
				   AND groups.belongsToGroup=permissions.userid))";
	}elseif($currUid == $userid){
		$accesscontrol = " AND tracker.owner=$userid ";
	}else {
		$accesscontrol = " AND (belongsToGroup = $currUid
				   AND permissions.userid =$currUid
				   AND permissions.permission > 0)";
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
	$q1 .= "FROM permissions
  	        JOIN groups ON groups.userid=$userid
		JOIN tracker ON permissions.trackID = tracker.trackID
		JOIN user ON user.ID=tracker.owner
		JOIN (sampletypes, $table) ON (sampletypes.id=tracker.sampleType AND $table.id=tracker.sampleID)";
	$q1 .= " $join ";
	$q1 .= "WHERE sampletypes.table='$table' $accesscontrol AND deleted
		is NULL";
	if ($where) $q1 .= " AND $where";
	$q1 .= " ORDER BY ";
	$order? $q1 .= "$order" : $q1 .= "$table.id";
	if($count){
		$q1 .= ") AS foo";
	}
	//print "<br/>$q1<br/>";
	$r1 = pdo_query($q1);
	if($r1) return $r1;
}

function newRecord($table, $ds, $userid){
	include('config.php');
	$link = mysql_connect($host, $username, $password);
	if (!$link) {
	    die('Could not connect: ' . mysql_error());
	}
	$project = $ds['project'];
	$subProject = $ds['subProject'];
	unset($ds['project']);
	unset($ds['subProject']);
	$stq = "SELECT * FROM sampletypes WHERE `table`='$table'";
	#print $stq;
	$str = pdo_query($stq);
	$st = $str[0]['id'];
	# insert query for data record
	#print_r($ds);
	$iq = "INSERT INTO `$table` (";
	$cnt = 0;
	$end = sizeof($ds);
	foreach ($ds as $field => $dat){
		$iq .= "$field";
		$cnt += 1;
		if($cnt <> $end) $iq .= ", ";
	}
	$iq .= ") VALUES (";
	$cnt = 0;
	foreach ($ds as $field => $dat){
		$dat = escape_quotes($dat);
		if ($dat == 'mainID') $dat = $id;
		$iq .= "'$dat'";
		$cnt += 1;
		if($cnt <> $end) $iq .= ", ";
	}
	$iq .= ")";
	#print $iq;
	$sampleID = pdo_query($iq);
	# insert query for tracker
	$iq = "INSERT INTO tracker (sampleID, sampleType, project, subProject, created, owner, permOwner)";
	$iq .= " VALUES ($sampleID, $st, '$project', '$subProject', NOW(), $userid, 2)";
	//print $iq;
	$newTrackID = pdo_query($iq);
	# setup permissions
	$gq = "SELECT * FROM groups JOIN user ON belongsToGroup=user.id WHERE groups.userid=$userid AND user.groupType!=3;";
	#print "$gq<br/>";
	$groups = pdo_query($gq);
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
			 VALUES ($newTrackID,${g['belongsToGroup']},$perm)";
			 #print "$pq<br/>";
			 pdo_query($pq);
		}
	}
	
	#setup admin rights
	#find group that has admin rights in the users labgroup
	$aq = "SELECT DISTINCT user.ID FROM user JOIN groups ON belongsToGroup =
			(SELECT user.ID FROM groups JOIN user ON belongsToGroup=user.id
			WHERE groups.userid=$userid AND user.groupType=1)
		WHERE user.groupType=3;";
	$ags = pdo_query($aq);
	$admin = $ags[0]['ID'];
	$aiq = "INSERT INTO permissions (trackID,userid,permission)
		 VALUES ($newTrackID,$admin,1)";
		 #print "$aiq<br/>";
	pdo_query($aiq);
	#insert read permission for this group
	mysql_close($link);
	return $newTrackID;
}


function updateRecord($trackerID, $ds, $userid, $groupids){
	include('config.php');
	$link = mysql_connect($host, $username, $password);
	if (!$link) {
	    die('Could not connect: ' . mysql_error());
	}
	$table = getTable($trackerID);
	$uq = "UPDATE `$table`, `tracker` SET ";
	#print_r($ds);
	foreach ($ds as $key => $field){
		$uq .= "`$key` = '".escape_quotes($field)."' ";
		if (next($ds)!==FALSE) $uq .= ',';
	}
	$uq .= ", tracker.changed=NOW() ";
	$uq .= "WHERE tracker.sampleID=`$table`.id AND trackID='$trackerID';";
	//print "$uq <br/>";
	$r = pdo_query($uq);
	mysql_close($link);
}

function getConnections($trackerID){
	$q = "SELECT * FROM connections WHERE belongsTo=$trackerID";
	$r = pdo_query($q);
	return $r;
}

function getConnection($connID){
	$q = "SELECT * FROM connections WHERE connID=$connID";
	$r = pdo_query($q);
	return $r[0];
}

function saveURI($uri){
	include('config.php');
	$link = mysql_connect($host, $username, $password);
	$uri = escape_quotes($uri, $link);
	$query = "UPDATE `settings` SET `value`='$uri' WHERE `variable`='lastView';";
	pdo_query($query);
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
	(element.value =='2')?ChangeDisplayDiv(['permTable'], 'inline'):ChangeDisplayDiv(['permTable'], 'none');
	(element.value =='0')?ChangeDisplayDiv(['oligoOptions'], 'block'):ChangeDisplayDiv(['oligoOptions'], 'none');
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


?>
	</select>
	<div id="permTable" name="permTable" style="display:none; margin-right:20px" ><table>
		<tr>
			<td/><td>None</td><td>Read</td><td>Write</td>
		</tr>
		<tr><td>
<?php
	$uq = "SELECT `ID` AS trackID, `fullname` AS `name` FROM `user`";
	$users = pdo_query($uq);
	if (!$users) $users = array();
	$formParams = array('mode'=>'modify');
	#getComboBox($field, $table, $mode, $choices, $match, $action=null, $link=null){
	print getComboBox("user",$formParams['table'], $formParams['mode'],$users,None);
?>
			</td>
			<td><input type="radio" name="perm" value="0" checked></td>
			<td><input type="radio" name="perm" value="1"></td>
			<td><input type="radio" name="perm" value="2"></td>
		</tr>
	</table></div>
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

function getRestrictionSites($enzymes, $dnaSequence){
	$output = array();
	$sites = array();
	global $userid;
	global $noUserFilter;
	$sitelen = 4;
	//print $enzymes;
	if(strpos($enzymes, "leemorlab") === 0){
		$noUserFilter = True;
		$enzys = getRecords('vials', $userid, array('vials.name'), " trackBoxes.name='Lab Enzymes' ", '', 0, " LEFT JOIN trackBoxes ON vials.boxID=trackBoxes.tID ");
		$noUserFilter = False;
		if (sizeof($enzys) > 0){
			$enzymeList = '';
			foreach($enzys as $enzyme){
				$enzymeList .= "${enzyme[0]},";
			}
			$enzymes = substr($enzymeList, 0, strlen($enzymeList)-1).substr($enzymes,9, strlen($enzymes));
		}
	}
	// $enzymes;
	$limit ='';
	if (substr($enzymes, 0, 3) != 'all') $limit = "-limit N"; 
	$cmd = "echo '$dnaSequence' | /usr/bin/restrict -warning Y -rformat excel $limit --commercial Y --filter --auto -sitelen $sitelen -enzymes $enzymes 2>&1";
	//print "$cmd";
	$tmp = exec($cmd, $output);
	//print_r($output);
	//print $tmp;
	foreach($output as $line){
		if (substr($line, 0, 1)==  "#") continue;
		//print "$line<br/>";
		$line = explode("\t", trim($line));
		//print_r($line);
		if (is_numeric($line[0])){
			$cutsite = $line[5] - 1;
			$sites[] = ("['${line[3]}', $cutsite, {'background-color': \"white\",
				    fill: \"black\", \"font-size\": '8', \"font-family\": \"Verdana\",
				    cursor:\"pointer\", 'text-anchor' : \"middle\"}]");
		}
	}
	$sites = "new Array(". implode(',', $sites) .")";
	//print $sites;
	return $sites;
}

?>
