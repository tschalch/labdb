<?php
include_once("accesscontrol.php");
include_once("header.php");
?>
</head>
<body>
<?php 
include("title_bar.php");
include("navigation.php");
?>
<div id="content" class="container">
<p>	This site stores information about chemicals, clones and so on.</p>
         <p><form method="get" action="
 	<?php print "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
        ?>">
           <div class="">
			<h2>Quick Search:</h2>
	   </div>
	   <div class="form-group">
		<input id="searchField" class="form-control " type="text" value="<?php if ( isset ($_GET['searchTerm'])) print $_GET['searchTerm']; ?>" name="searchTerm"/>
	   </div>
            <div class="form-group">
		<input class="btn btn-secondary mr-sm-2" type="submit" value="search" />
	    </div>
        </form></p>
         
<?php
	if ( isset($_GET['searchTerm']) and $_GET['searchTerm'] != ""){
	    $searchwords = explode (" ", $_GET['searchTerm']);
            $searchString = implode("+",$searchwords);
            $sql = "SELECT `st_name`, sampletypes.plural, `table`,`list` FROM sampletypes";
            $tables = pdo_query($sql);
            //print_r($tables);
            $found = [];
            foreach($tables as $table){
                $t = $table['table'];
			    $hexIDSQL = getHexIDSQL($t);
                $where = '';
                $searchfields = ["$t.name", "$t.description", "sampletypes.st_name", "tracker.trackID"];
				$columns = $searchfields;
				$columns[] = getHexIDSQL($t)." as hexID";
                if (isset($searchwords)){
                       $where .= "(";
                       $i=0;
                       $inum = count($searchwords);
                       foreach ($searchwords as $searchword){
                               $j=0;
                               $jnum = count($searchfields);
							   $where .= " $hexIDSQL LIKE :searchword OR ";
                               foreach ($searchfields as $sfield){
                                       $where .= " $sfield LIKE :searchword";
                                       $j++;
                                       if ($j < $jnum) $where .= " OR";
                               }
                               $i++;
                               if ($i < $inum){ 
                                       $where .= ") AND (";
                               } else {
                                       $where .= ")";
                               }
                       }
               }
               //print "where: $where";
               $noUserFilter = True;
               $results = getRecords($t, $userid, [':searchword'=>"%$searchword%"], $columns, $where);
                if (sizeof($results)){
                   $found[$t] = $results;
                   print "<a href=\"list.php?list=${table['list']}&searchword=$searchString\"><h2 class=\"qs\">${table["plural"]}</h2></a>";
                   foreach($results as $record){
                        $description = '';
                        if ($record['description'] !='') $description = "${record['description']}<br/>";
                       print "<div class=\"qsh3\"><a class=\"qsh3\" href=\"editEntry.php?id=${record['trackID']}&mode=display\">${record['hexID']}: ${record['name']}</a>
                       <a class=\"\" href=\"editEntry.php?id=${record['trackID']}&mode=modify\">[edit]</a>
                           <p>$description</p>
                           </div>";
                   }
               }
           }
        if (!sizeof($found)) {
            print "Sorry, search returned no results.";
        }
    }        
?>


<!--
	<img src="img/diagram-0_1.png"  type='image/png'
	     border=0 usemap='#ideamap'>
	<map name='ideamap'><area shape='RECT' coords='649, 533, 649, 533' href='' title = '' alt = ''>
<area shape='POLY' coords='551, 630, 523, 636, 495, 630, 470, 602, 495, 574, 523, 569, 551, 574, 577, 602,'>
<area shape='RECT' coords='303, 459, 303, 459' href='' title = '' alt = ''>
<area shape='POLY' coords='329, 495, 352, 485, 397, 485, 419, 495, 419, 514, 397, 523, 352, 523, 329, 514'>
<area shape='RECT' coords='410, 299, 410, 299' href='' title = '' alt = ''>
<area shape='POLY' coords='498, 362, 480, 365, 462, 362, 435, 344, 462, 326, 480, 324, 498, 326, 526, 344,'>
<area shape='RECT' coords='300, 387, 300, 387' href='' title = '' alt = ''>
<area shape='POLY' coords='326, 423, 343, 413, 378, 413, 395, 423, 395, 442, 378, 451, 343, 451, 326, 442'>
<area shape='RECT' coords='1119, 354, 1119, 354' href='' title = '' alt = ''>
<area shape='RECT' coords='71, 236, 71, 236'>
<area shape='RECT' coords='96, 261, 349, 334'>
<area shape='RECT' coords='513, 354, 513, 354' href='' title = '' alt = ''>
<area shape='POLY' coords='593, 417, 575, 420, 557, 417, 538, 399, 557, 381, 575, 379, 593, 381, 613, 399,'>
<area shape='RECT' coords='202, 429, 202, 429' href='' title = '' alt = ''>
<area shape='RECT' coords='227, 454, 287, 498'>
<area shape='RECT' coords='227, 54, 227, 54' href='' title = '' alt = ''>
<area shape='RECT' coords='252, 79, 583, 146' href='inventory.php' title = '' alt = ''>

<area shape='RECT' coords='309, 149, 309, 149' href='' title = '' alt = ''>
<area shape='RECT' coords='334, 174, 390, 226'>
<area shape='RECT' coords='137, 540, 137, 540' href='' title = '' alt = ''>
<area shape='RECT' coords='162, 565, 222, 609'>
<area shape='RECT' coords='553, 490, 553, 490' href='' title = '' alt = ''>
<area shape='POLY' coords='667, 577, 638, 582, 609, 577, 578, 548, 609, 519, 638, 515, 667, 519, 699, 548,'>
<area shape='RECT' coords='329, 523, 329, 523' href='' title = '' alt = ''>
<area shape='POLY' coords='355, 559, 370, 549, 400, 549, 415, 559, 415, 578, 400, 587, 370, 587, 355, 578'>
<area shape='RECT' coords='94, 333, 94, 333' href='' title = '' alt = ''>
<area shape='POLY' coords='400, 654, 281, 712, 162, 654, 119, 535, 162, 416, 281, 358, 400, 416, 443, 535,'>
<area shape='RECT' coords='281, 269, 281, 269' href='' title = '' alt = ''>
<area shape='POLY' coords='667, 620, 526, 664, 385, 620, 306, 479, 385, 338, 526, 294, 667, 338, 746, 479,'>
--!>
<?php
include("footer.php");

?>
