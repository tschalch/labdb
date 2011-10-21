<?php
header("Cache-Control: max-age=0, must-revalidate");
include_once("accesscontrol.php");
include("header.php");
include_once("functions.php");
?>
<script type="text/javascript" language="javascript">
    <!--
    // js form validation stuff
    var confirmMsg  = 'Do you really want to delete these records?';
    //-->
</script>
<?php
include_once("listTitle.php");
include("list_doit.php");
$searchfields[] = 'user.fullname';
$columns[] = 'user.fullname';

if (isset($category)){
	echo   "<div id=\"navcontainer\">
			<ul id=\"navlist\">";
	foreach ($categories as $cat => $name){
		if ($cat == $category){
			echo "<li id=\"active\"><a href=\"#\" id=\"current\">$name</a></li>";
		} else {
			echo "<li><a href=\"list.php?list=listGene&amp;category=$cat\">$name</a></li>";
		}
	}
	echo "</ul></div>";
}
if (!isset($query)){
    if (!isset($where)) {
	$where = '';
    }
#	$query="SELECT tracker.trackID,";
#	if ($queryFields){
#		foreach ($queryFields as $qf){
#			$query .= "`$table`.`$qf`";
#			if(next($queryFields)) $query .= ", ";
#		}
#	} else {
#		//$query="SELECT `$table`.*";
#		$query.="`$table`.*";
#	}
#	$query .= " FROM `$table` LEFT JOIN `tracker` ON `$table`.id = tracker.sampleID
#			LEFT JOIN sampletypes ON sampletypes.`table`='$table'";
#	if ($_GET['searchword'] and $_GET['searchword'] != " Search..."){
#		$searchwords = explode (" ", $_GET['searchword']);
#	}
#	//print "selected $projectSelect<br/>";
#	$query .= " WHERE (tracker.`sampleType` = sampletypes.id) ";
#	//if(($searchwords and $searchfields) or ($category) or ($projectSelect)) $query .= " WHERE";
	if ($_GET['searchword'] and $_GET['searchword'] != " Search..."){
		$searchwords = explode (" ", $_GET['searchword']);
	}
	if (isset($searchwords) and isset($searchfields)){
		print "This list is filtered for";
		if (isset($where) and strlen($where)) {
		    $where .= " AND ";
		}
		$where .= "(";
                $i=0;
                $inum = count($searchwords);
		foreach ($searchwords as $searchword){
			print" \"$searchword\"";
                        $j=0;
                        $jnum = count($searchfields);
			foreach ($searchfields as $sfield){
                                $s = explode(" ",  $sfield);
				$where .= " ${s[0]} LIKE '%$searchword%'";
				$j++;
				if ($j < $jnum) $where .= " OR";
			}
                        $i++;
			if ($i < $inum){ 
				$where .= ") AND (";
                                print " AND ";
			} else {
				$where .= ")";
			}
		}
		print ".<br/>";
	}
	
	if (isset($projectSelect)){
	    if (isset($where) and strlen($where)) $where .= " AND ";
	    $where .= $projectSelect;
	}
	if (isset($userSelect)){
	    if (isset($where) and strlen($where)) $where .= " AND ";
	    $where .= $userSelect;
	}
	if(isset($category)){
		//if ($projectSelect or ($searchwords and $searchfields)) $query .= " AND";
		if (isset($where) and strlen($where)) $where .= " AND ";
		$where .= "(";
		$where .= " type='$category'";
		$where .= ")";
	}
#	// user control
#	$query .= " AND (tracker.owner=$userid OR tracker.public=TRUE)";
	
	if (isset($defaultOrder)){
		$order = $defaultOrder;
	}
	if (array_key_exists('order', $_GET)) $order = " ${_GET['order']}";
}
//
//print "where: $where <br/>";
#$rows = pdo_query($query);
if (!isset($join)) $join = '';
$noRows = getRecords($table, $userid, array('trackID'), $where, "", 1, $join);
include("pageNav.php");
if (isset($limit)) $order .= "$limit";
$rows = getRecords($table, $userid, $columns, $where, $order, 0, $join);
if (!isset($rows)) $rows = array();
//print_r($rows);
?>
<form name="mainform" onsubmit="if (document.mainform.SelAction.value==3) return deleteRecords();" 
			action="<?php echo $formaction;?>" method="post">
<input type="hidden" name="table" value="<?php echo $table; ?>"/>
<table class="lists" >
<tr>
<?php
$selfAddress = "list.php?";
$exclude = array("currUser","page");
foreach($_GET as $key => $value){
	if (!in_array($key, $exclude)){
            #print "Search: $searchlist";
		$selfAddress .= "$key=$value&";
	}
}

echo "<th/><th/>";
foreach ($fields as $key => $field){
	print "<th class=\"lists\" >";
	if(is_string($field)) echo "$field ";
	if(is_string($key)) echo "<a href=\"$selfAddress&order=$key\"><img src=\"img/up.png\"/></a><a href=\"$selfAddress&order=$key DESC\"><img src=\"img/down.png\"/></a>";
	print "</th>";
}
?>
</tr>