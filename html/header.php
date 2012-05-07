<?php
ob_start();

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" 
	\"http://www.w3.org/TR/html4/strict.dtd\">";
?>
<!--
 * Lab Database
 * Copyright (C) 2006 Thomas Schalch
 * 
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php if ($title) print "$title - ";?>Lab Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />

        <link rel="stylesheet" type="text/css" href="css/lab_v1.css" />
	<!--[if IE]>     <link rel="stylesheet" type="text/css" href="css/lab_iehacks.css" />   <![endif]-->   
	<!--[if lte IE 6]>     <script type="text/javascript" src="js/myiescripts.js"></script>   <![endif]-->
	<!--[if lt IE 6]>     <link rel="stylesheet" type="text/css" href="js/myiehacks-5.5.css" />   <![endif]--> 

	<link rel="shortcut icon" href="favicon.ico" />
	<script src="js/labdb_v1.js" type="text/javascript"></script>
	<script src="js/mootools-1.2.5-core.js" type="text/javascript"></script>

