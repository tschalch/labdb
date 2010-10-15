<?php 
initProjects($noUserFilter, $noProjectFilter);
?>
<form name="f1" action="list.php" method="get">
<?php
if (!array_key_exists('searchword', $_GET) or $_GET['searchword']=='Search...') $_GET['searchword']='';
$exclude = array("searchword","project","currUser","page");
foreach($_GET as $key => $value){
	if (!in_array($key, $exclude)){
            #print "Search: $searchlist";
                if ($key=='list' and isset($searchlist)) $value = $searchlist;
		print "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
	}
}
#print "<br/>$selfAddress<br/>";
?>
	<div class="search">
		<input name="searchword" id="mod_search_searchword" maxlength="20"
                       alt="search" class="inputbox" type="text" size="40"
                       value="Search..." onblur="if(this.value=='') this.value=' Search...';"
                       onfocus="if(this.value=='Search...') this.value='';" />	</div>
                       
<?php
if (!$noProjectFilter){
	getProjectCmbxs($project, $currUid);
}
?>

</form>	

<h2 style="clear: both; margin: 0; padding: 10px 0 10px 0"><?php echo "$title";?></h2>