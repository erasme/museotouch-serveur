<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

// CONNEXION À LA BDD
$bdd = @mysql_connect($config['bdd_host'], $config['bdd_user'], $config['bdd_pass']) or die('Erreur de connexion au serveur de base de données ! Merci de repasser plus tard !');

// selection de la table
if (!$bdd || !@mysql_select_db($config['bdd_name'], $bdd)) {
	die ('Erreur de connexion à la base de données ! Merci de repasser plus tard.');
}

// forçage de l'utf-8 pour la bdd
@mysql_set_charset('utf8', $bdd);

?>