<?php
ob_start();
ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
?>
<!--
 * Lab Database
 * Copyright (C) 2006 Thomas Schalch
 * 
-->
<html>
<head>
	<title><?php if (isset($title)) print "$title - ";?>Lab Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="stylesheet" type="text/css" href="css/lab_v1.css" />
<link rel="stylesheet" type="text/css" href="css/navmenu.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!--[if IE]>     <link rel="stylesheet" type="text/css" href="css/lab_iehacks.css" />   <![endif]-->   
	<!--[if lte IE 6]>     <script type="text/javascript" src="js/myiescripts.js"></script>   <![endif]-->
	<!--[if lt IE 6]>     <link rel="stylesheet" type="text/css" href="js/myiehacks-5.5.css" />   <![endif]--> 

	<link rel="shortcut icon" href="favicon.ico" />
	<!--<script src="js/mootools-1.2.5-core.js" type="text/javascript"></script>-->
<script src="js/MooTools-More-1.6.0-compat-compressed.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<!--<script src="js/mootools-1.2.5.1-more.js" type="text/javascript"></script>-->
<script type="text/javascript">
  //no conflict jquery
  jQuery.noConflict();
</script>
	<script src="js/labdb_v1.js" type="text/javascript"></script>
	<script src="js/navmenu.js" type="text/javascript"></script>

