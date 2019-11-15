<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

// CONNEXION À LA BDD
$bdd = new PDO($config['bdd_host'] . ';charset=UTF8;dbname=' . $config['bdd_name'], $config['bdd_user'], $config['bdd_pass']);
$bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$bdd = @mysqli_connect($config['bdd_host'], $config['bdd_user'], $config['bdd_pass']) or die('Erreur de connexion au serveur de base de données ! Merci de repasser plus tard mon gars!');

// selection de la table
/*
if (!$bdd || !@mysqli_select_db($config['bdd_name'], $bdd)) {
	die ('Erreur de connexion à la base de données ! Merci de repasser plus tard.');
}

// forçage de l'utf-8 pour la bdd
@mysqli_set_charset('utf8', $bdd);
*/