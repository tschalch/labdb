<?php
# data format table -> dataset -> field
$data = [];

foreach ($_POST as $key => $post){
	//print "$key-$post<br/>";
	if ($post != "NA"){
		$args = explode("_", $key, 3);
		//print_r($args);
		if ($args[0] != 'none' and count($args)==3){
			$data[$args[0]][$args[1]][$args[2]] = $post;
		}
	}
}

#print_r($_Files);
foreach ($_FILES as $key => $file){
	$args = explode("_", $key, 3);
	if ($args[0] != 'none' and $file['name'] != '' and count($args)==3){
		$data[$args[0]][$args[1]][$args[2]] = $file["name"];
	}
}
?>