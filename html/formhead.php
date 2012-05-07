<?php
include_once("accesscontrol.php");
#$noUserFilter = True;
if ($edit or $duplicate){
	if($form){
		$query="SELECT `$table`.*, tracker.*, sampletypes.`table`, sampletypes.`form`, sampletypes.st_name AS stName FROM `$table` LEFT JOIN tracker ON $table.id=tracker.sampleID LEFT JOIN sampletypes ON sampletypes.`table`='$table' WHERE $table.id='$id' AND tracker.sampleType=sampletypes.id AND tracker.owner=$userid ";
		#print $query;
		$rows = pdo_query($query);
		$row = $rows[0];
	} else {
		$row = getRecord($id, $userid, $groups);
	}
	#print_r($row);
	if ($duplicate) unset($row['trackID']);
	foreach ($row as $key => $field){
		$fields[$key] = $field;
	}
	$id = $fields['trackID'];
	if ($mode == 'display'){
		$title = "$titleName ${fields['name']}";
	} else {
		$title = "Edit $titleName ${fields['name']}";
	}
}
if ($new){
	$title = "New $titleName Entry";
	$formaction = "insert.php";
	if($_POST['DNASequence']) $fields['DNASequence'] = fastaseq($_POST['DNASequence'], "\n", 60);
	if($_POST['proteinSequence']) $fields['proteinSequence'] = fastaseq($_POST['proteinSequence'], "\n", 60);
}
$button = "Save Entry";
include("header.php");
?>
<link media="all" type="text/css" href="css/ui-themes/base/jquery.ui.all.css" rel="stylesheet">
<link media="all" type="text/css" href="css/MenuMatic.css" rel="stylesheet">
<link rel="stylesheet" href="css/Autocompleter.css" type="text/css" />

<script src="js/Autocompleter.js" type="text/javascript"></script>
<script src="js/Autocompleter.Request.js" type="text/javascript"></script>
<script src="js/Autocompleter.labdb.js" type="text/javascript"></script>
<script src="js/Observer.js" type="text/javascript"></script>

</head>
<body>

<?php 
include("title_bar.php");
include("navigation.php");
?>
<div id="content">
<?php
initProjects($noUserFilter, $noProjectFilter);
$formParams['fields'] = $fields;
//print_r($fields);
?>
<div id='title'><h2><?php echo "$title";?></h2></div>
<?php
echo "<form id=\"mainform\" name=\"mainform\" action=\"saveRecord.php?\" method=\"post\">\n";
if ($mode == 'modify'):
?>
<script type="text/javascript">
window.addEvent('domready', function() {
	$('mainform').addEvent('submit', function(e) {
		//Prevents the default submit event from loading a new page.
		e.stop();
		//Empty the log and show the spinning indicator.
		if (!<?php print "$submitFunction" ?>) return;
		var log = $('log_res').empty().addClass('ajax-loading');
		//Set the options of the form's Request handler. 
		//("this" refers to the $('myForm') element).
		this.set('send', {onComplete: function(response) { 
				log.removeClass('ajax-loading');
				$('id').set('html', response);
				$('inp_id').set('value', response);
				//$('title').set('html', "<h2>Edit "+$('name').get('value')+"<\/h2>");				
				if (goBack) {
					history.back();
				} else {
					var loc = "editEntry.php?id=" + response + "&mode=modify";
					if (loc == location){
						history.go(0);
					} else {
						location.replace(loc);
					}
				}
				goBack = false;
			}
		});
		//Send the form.
		this.send();
	});
	$('submit').addEvent('click', function(e){
		goBack = true;
		$('mainform').fireEvent('submit', e);
		//history.back();
	});
	$('apply').addEvent('click', function(e){
		goBack = false;
		$('mainform').fireEvent('submit', e);
		//history.back();
	});
	$('stop').addEvent('click', function(e){
		history.back();
	});
});
</script>
<?php
endif;
print "<input type=\"hidden\" name=\"maintable\" value=\"$table\"/>\n";
?>
