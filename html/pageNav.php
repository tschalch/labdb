<?php
if (array_key_exists('page',$_GET)){
	$pageNr = $_GET['page'];
} else {	
	$pageNr = 1;
}

if ($pageNr != 'all'){
	$rowsPerPage = 25;
	if (isset($noRows[0][0])){
	    $noRows = $noRows[0][0];
	} else {
	    $noRows = 0;
	}
	$noPages = ceil($noRows/$rowsPerPage);
	$noDisplayed = 10;
	$lowLimit = round($pageNr - $noDisplayed / 2) - 1;
	$highLimit = round($pageNr + $noDisplayed / 2);
	if ($highLimit <= $noDisplayed) $highLimit = $noDisplayed + 1;
	if ($highLimit > $noPages) $lowLimit = $noPages-$noDisplayed;
	if ($noPages > 1){
		#print "noRows:$noRows, noPages:$noPages";
		$offset = ($pageNr - 1) * $rowsPerPage;
		$limit = " LIMIT $offset, $rowsPerPage ";
		$url = "list.php?";
		foreach($_GET as $key => $value){
			if($key!='page') $url .= "$key=$value&amp;";
		}
		$nextPage = $pageNr + 1;
		$prevPage = $pageNr - 1;
		print "<center>";
		if ($pageNr > 1) print "<a href=\"${url}page=$prevPage\">previous</a>";
		if ($lowLimit >= 1){
			print "<a href=\"${url}page=$lowLimit\"> .. </a>";
			$i = $lowLimit + 1;
		} else {
			$i = 1;
		}
		for($i; $i < $noPages+1 and $i < $highLimit; $i++){
			if ($i != $pageNr){
				print "<a href=\"${url}page=$i\"> $i </a>";
			} else {
				print " $i ";
			}
		}
		#print "i: $i, display: $noDisplayed";
		if ($i >= $highLimit and $i < $noPages + 1){
			print "<a href=\"${url}page=$i\"> .. </a>";
		}
		if ($pageNr < $noPages) print "<a href=\"${url}page=$nextPage\">next</a>";
		print "<a href=\"${url}page=all\"> - show all</a>";
		print "</center><br/>";
	}
}
?>
