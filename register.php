<?php
session_start();
define('INDEX_ADMIN', true);

// connexion à la BDD
require_once(dirname(__FILE__).'/incs/config.inc.php');
require_once(dirname(__FILE__).'/incs/connect.inc.php');
// vérification si l'utilisateur n'est pas déjà connecté
require_once(dirname(__FILE__).'/incs/logged.inc.php');

$error_msg = array();

if (isset($_POST['courriel'])) {

	// vérification de l'existence du courriel
	$sql_check = @mysql_query("SELECT `id` FROM `users` WHERE `mailaddress`='".mysql_real_escape_string($_POST['courriel'])."'");
	if (@mysql_num_rows($sql_check) > 0) {
		$error_msg['courriel'] = 'un compte existe déjà avec ce courriel.';
	}
	
	// vérification qu'il s'agit bien d'un courriel valide (risque de problème avec les possibles nouvelles extensions de nom de domaine)
	if (!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,6})$`', $_POST['courriel'])) {
		$error_msg['courriel'] = 'le courriel renseigné n\'est pas valide.';
	}
	
	// vérification du nom et prénom
	if (empty($_POST['lastname']) || empty($_POST['firstname'])) {
		$error_msg['firstlastname'] = 'le nom et le prénom doivent être remplis.';
	}
	
	// vérification des deux mots de passe
	if (($_POST['password1'] != $_POST['password2']) || empty($_POST['password1']) || empty($_POST['password1'])) {
		$error_msg['password1'] = 'le mot de passe et sa vérification doivent être remplis et exacts.';
	}
	
	// si aucune erreur => validation de l'enregistrement
	if (count($error_msg) == 0) {
		@mysql_query("INSERT INTO `users`(`id`, `password`, `firstname`, `lastname`, `mailaddress`) VALUES(NULL, '".mysql_real_escape_string(sha1(strtolower($_POST['courriel']).md5($_POST['password1'])))."', '".mysql_real_escape_string(ucfirst(strtolower($_POST['firstname'])))."', '".mysql_real_escape_string(strtoupper($_POST['lastname']))."', '".mysql_real_escape_string(strtolower($_POST['courriel']))."')");
		$error_msg['validate'] = true;
		unset($_POST);
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $config['site_name']; ?> : inscription</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/login.css" media="screen" />
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.register.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
</head>
<body>
<div>
	<form action="<?php echo $config['site_http']; ?>/register.php" method="post" id="register">
		<img src="<?php echo $config['site_http']; ?>/imgs/logo.png" />
		<h1 class="cufon">S'inscrire</h1>
		<?php
		if (!empty($error_msg)) {
			if (array_key_exists('validate', $error_msg)) {
				echo '<p class="valid_register">Vous êtes maintenant enregistré.<br /><a href="login.php">Vous pouvez désormais vous connecter</a>.</p>';
			} else {
				echo '<ul class="error_list">'; foreach($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; } echo '</ul>';
			}
		}
		?>
		<p>
			<label for="courriel">Courriel</label>
			<input id="courriel" name="courriel" value="<?php echo @$_POST['courriel']; ?>" type="text" maxlength="50"<?php if (array_key_exists('courriel', $error_msg)) { echo ' class="error"'; } ?> />
		</p>
		<p>
			<label for="password1">Mot de passe (5 caractères minimum)</label>
			<input id="password1" name="password1" value="" type="password"<?php if (array_key_exists('password', $error_msg)) { echo ' class="error"'; } ?> />
		</p>
		<p>
			<label for="password2">Mot de passe (vérification)</label>
			<input id="password2" name="password2" value="" type="password"<?php if (array_key_exists('password', $error_msg)) { echo ' class="error"'; } ?> />
		</p>
		<p>
			<label for="lastname">Nom</label>
			<input id="lastname" name="lastname" value="<?php echo @$_POST['lastname']; ?>" type="text" maxlength="50" />
		</p>
		<p>
			<label for="firstname">Prénom</label>
			<input id="firstname" name="firstname" value="<?php echo @$_POST['firstname']; ?>" type="text" maxlength="50" />
		</p>
		<p class="login-submit">
			<input type="submit" id="send" value="S'enregistrer" />
		</p>
	</form>
	<p><a href="<?php echo $config['site_http']; ?>/login.php">Se connecter</a></p>
</div>
<div class="footer" id="footer">
	<span>&copy; Tous droits réservés, <a href="http://wwww.erasme.org/" target="_blank">Erasme</a> 2011 – <a href="<?php echo $config['site_http']; ?>/legals.php">mentions légales</a></span>
<div>
</body>
</html>