<?php
ob_start();
ini_set('default_charset', 'UTF-8');

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

        <link rel="stylesheet" type="text/css" href="css/lab_v1.css" />
	<!--[if IE]>     <link rel="stylesheet" type="text/css" href="css/lab_iehacks.css" />   <![endif]-->   
	<!--[if lte IE 6]>     <script type="text/javascript" src="js/myiescripts.js"></script>   <![endif]-->
	<!--[if lt IE 6]>     <link rel="stylesheet" type="text/css" href="js/myiehacks-5.5.css" />   <![endif]--> 

	<link rel="shortcut icon" href="favicon.ico" />
	<script src="js/labdb_v1.js" type="text/javascript"></script>
	<script src="js/mootools-1.2.5-core.js" type="text/javascript"></script>
	<script src="js/mootools-1.2.5.1-more.js" type="text/javascript"></script>

