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
<meta HTTP-EQUIV="Pragma" content="no-cache">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!--[if IE]>     <link rel="stylesheet" type="text/css" href="css/lab_iehacks.css" />   <![endif]-->   
	<!--[if lte IE 6]>     <script type="text/javascript" src="js/myiescripts.js"></script>   <![endif]-->
	<!--[if lt IE 6]>     <link rel="stylesheet" type="text/css" href="js/myiehacks-5.5.css" />   <![endif]--> 

	<link rel="shortcut icon" href="favicon.ico" />

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<!-- Optional theme -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">

<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">

<script type="text/javascript">
//no conflict jquery 
jQuery.noConflict(); 
</script> 
<script src="js/labdb_v1.js" type="text/javascript"></script> 
<link rel="stylesheet" type="text/css" href="css/lab_v1.css" />
