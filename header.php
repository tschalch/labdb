<?php
require_once('../FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);
$firephp->setEnabled(true);

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

        <link rel="stylesheet" type="text/css" href="lab_v1.css" />
	<!--[if IE]>     <link rel="stylesheet" type="text/css" href="lab_iehacks.css" />   <![endif]-->   
	<!--[if lte IE 6]>     <script type="text/javascript" src="myiescripts.js"></script>   <![endif]-->
	<!--[if lt IE 6]>     <link rel="stylesheet" type="text/css" href="myiehacks-5.5.css" />   <![endif]--> 

	<link rel="shortcut icon" href="favicon.ico" />
        <link rel="stylesheet" href="lib/MenuMatic.css" type="text/css" media="screen" charset="utf-8" />
	<script src="labdb_v1.js" type="text/javascript"></script>
	<script src="lib/mootools-1.2.4-core.js" type="text/javascript"></script>
        <script src="lib/mootools-1.2.4.4-more.js" type="text/javascript"></script>
</head>
<body>
<div id="header"><h1><a href="index.php" style="color:black;">Lab Database</a></h1></div>

  <div id="labnav"><?php include("navigation.php");?></div>
  <div id="content">
