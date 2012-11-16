<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

// Génération d'un mot de passe aléatoire complexe
function newpasswd() {
	$chaine = 'abcdefghijklmnopqrstuvwxyz@$%*!?#[]()0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$newpass = '';
	for ($i=0;$i<9;$i++) {
		$newpass .= $chaine{rand(0, strlen($chaine)-1)};
	}
	return $newpass;
}

// Test s'il s'agit réellement d'un integer
function isint($int) {
	// First check if it's a numeric value as either a string or number
	if(is_numeric($int)){
		if((int)$int == $int){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function aaccess() {
	// test si l'expo existe
	if (isset($_GET['expo']) && !empty($_GET['expo'])) {
		if ($_SESSION['is_admin']==2) {
			$sql = @mysql_query("SELECT `id` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['expo'])."'");
			if (@mysql_num_rows($sql) < 1) {
				header("Location:".$config['site_http'].'/expo.php');
			}
		} else {
			$sql = @mysql_query("SELECT e.`id` FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' AND e.`id`='".mysql_real_escape_string($_GET['expo'])."'");
			if (@mysql_num_rows($sql) < 1) {
				header("Location:".$config['site_http'].'/expo.php');
			}
		}
	}
}

function isempty($value) {
	if (empty($value)) {
		return 'N/C';
	} else {
		return $value;
	}
}


?>