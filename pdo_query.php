<?php
function pdo_query($query
try {
   $dbh = new PDO('mysql:host=localhost;dbname=lab', 'root', 'labmice');
   $result = array();
   $i = 0;
   foreach ($dbh->query($query) as $row){
		$result[$i] = $row;
		$i++;
	}
   $dbh = null;
} catch (PDOException $e) {
   print "Database Error!: " . $e->getMessage() . "<br/>";
   die();
}
?>