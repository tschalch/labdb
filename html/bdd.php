<?php
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=lab_labdb;charset=utf8', 'calendar', 'gJ17y#eskC2^');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
