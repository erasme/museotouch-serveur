<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/incs/config.inc.php');

unset($_SESSION['id']);
unset($_SESSION['user']);
unset($_SESSION['pass']);
unset($_SESSION['user_seen']);
$_SESSION = array();
session_unset();
session_destroy();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Connexion</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/login.css" media="screen" />
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
</head>
<body>
<div>
	<div id="login">
		<img src="<?php echo $config['site_http']; ?>/imgs/logo.png" />
		<h1 class="cufon">Déconnexion</h1>
		<p class="valid_register">Vous êtes maintenant déconnecté.</p>
		<p><a href="login.php">Cliquez ici</a> si vous souhaitez à nouveau vous connecter.</p>
	</div>
</div>
<div class="footer" id="footer">
	<span>&copy; Tous droits réservés, <a href="http://wwww.erasme.org/" target="_blank">Erasme</a> 2011 – <a href="<?php echo $config['site_http']; ?>/legals.php">mentions légales</a></span>
<div>
</body>
</html>