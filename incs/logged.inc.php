<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }


// vérification qu'il y a déjà des informations en session
if(isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['pass']) && !empty($_SESSION['pass'])) {

	// vérification que les informations en session sont des données existantes
	$sql = @mysql_query("SELECT `id`, `lastname`, `firstname`, `is_admin`, `mailaddress`, `password` FROM `users` WHERE `mailaddress`='".mysql_real_escape_string(strtolower($_SESSION['user']))."' AND `password`='".mysql_real_escape_string($_SESSION['pass'])."' LIMIT 0,1");
	// si les informations de connexion existent et sont correctent => reinitialisation des valeurs du session
	if (@mysql_num_rows($sql) > 0) {

		unset($_SESSION);
		$result = @mysql_fetch_assoc($sql);
		$_SESSION['id'] = $result['id'];
		$_SESSION['user'] = $result['mailaddress'];
		$_SESSION['pass'] = $result['password'];
		$_SESSION['is_admin'] = $result['is_admin'];
		if (empty($result['firstname']) && empty($result['lastname'])) {
			$_SESSION['user_seen'] = $result['mailaddress'];
		} else {
			$_SESSION['user_seen'] = $result['firstname'].' '.$result['lastname'];
		}
		
		// au cas où on se trouve sur les pages d'enregistrement ou de connexion
		if (strpos($_SERVER['REQUEST_URI'], 'register.php') > 0 || strpos($_SERVER['REQUEST_URI'], 'login.php') > 0) {
			header("Location:index.php");	
		}
		
	// sinon, redirection vers la page de login
	} else {
		if (!strpos($_SERVER['REQUEST_URI'], 'register.php') && !strpos($_SERVER['REQUEST_URI'], 'login.php')) { header("Location:login.php"); }
	}
} else {
	if (!strpos($_SERVER['REQUEST_URI'], 'register.php') && !strpos($_SERVER['REQUEST_URI'], 'login.php')) { header("Location:login.php"); }
}

?>