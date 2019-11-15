<?php
session_start();
define('INDEX_ADMIN', true);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// connexion à la BDD
require_once(dirname(__FILE__).'/incs/config.inc.php');
require_once(dirname(__FILE__).'/incs/connect.inc.php');
// vérification si l'utilisateur n'est pas déjà connecté
require_once(dirname(__FILE__).'/incs/logged.inc.php');
require_once(dirname(__FILE__).'/incs/function.inc.php');


// partie oubli de mot de passe.
if (isset($_GET['forgotpassword'])) {

$error_msg = 0;

if (isset($_POST['courriel'])) {
	
	$sql = @mysqli_query("SELECT `id`, `mailaddress` FROM `users` WHERE `mailaddress`='".mysqli_real_escape_string(strtolower($_POST['courriel']))."' LIMIT 0,1");
	if (@mysqli_num_rows($sql) > 0) {
		$result = @mysqli_fetch_assoc($sql);
		unset($_SESSION);
		$newpass = newpasswd();
		$headers = 'From: '.$config['site_mail']."\r\n".'Reply-To: '.$config['site_mail'];
		if (@mysqli_query("UPDATE `users` SET `password`='".(sha1($result['mailaddress'].md5($newpass)))."' WHERE `mailaddress`='".$result['mailaddress']."'")) {
			if (mail($result['mailaddress'], $config['site_name'].' : votre nouveau mot de passe.', "Bonjour.\r\n\r\nUne demande afin de réinitialiser votre mot de passe de connexion à votre compte ".$config['site_name']." a été effectuée.\r\nVoici votre nouveau mot de passe : ".$newpass."\r\n\r\nCordialement.", $headers)) {
				$error_msg = 2;
			} else {
				$error_msg = 4;
			}
		} else {
			$error_msg = 3;
		}
	} else {
		$error_msg = 1;
	}

}

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $config['site_name']; ?> : mot de passe perdu ?</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/login.css" media="screen" />
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.forgotpass.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
</head>
<body>
<div>
	<form action="<?php echo $config['site_http']; ?>/login.php?forgotpassword" method="post" id="forgotpass">
		<img src="<?php echo $config['site_http']; ?>/imgs/logo.png" />
		<h1 class="cufon">Mot de passe perdu ?</h1>
		<?php
		switch($error_msg) {
			case 1:
				echo '<p class="error_list">Aucun utilisateur n\'est associé à ce courriel.</p>';
				break;
			case 2:
				echo '<p class="valid_register">Un courriel contenant un nouveau mot de passe a été envoyé à '.$_POST['courriel'].'.</p>';
				break;
			case 3:
				echo '<p class="error_list">Impossible de mettre à jour votre mot de passe.<br />Merci de contacter la société Erasme afin de leur exposer le problème.</p>';
				break;
			case 4:
				echo '<p class="error_list">Impossible de vous envoyer votre nouveau mot de passe.<br />Merci de contacter la société Erasme afin de leur exposer le problème.</p>';
				break;
		}
		?>
		<p>
			<label for="name">Courriel</label>
			<input id="courriel" name="courriel" type="text" maxlength="50" />
		</p>
		<p class="login-submit">
			<input type="submit" id="send" value="Envoyez un nouveau mot de passe" />
		</p>
	</form>
	<p><a href="<?php echo $config['site_http']; ?>/login.php">Se connecter</a></p>
</div>
<div class="footer" id="footer">
	<span>&copy; Tous droits réservés, <a href="http://wwww.erasme.org/" target="_blank">Erasme</a> 2011 – <a href="<?php echo $config['site_http']; ?>/legals.php">mentions légales</a></span>
</div>
</body>
</html>
<?php



// partie login
} else {

$error_msg = 0;

if (isset($_POST['courriel'])) {
    $result = array();
    $req = $bdd->prepare('SELECT * FROM users WHERE mailaddress = ? AND password = ? LIMIT 0,1');
    // sha1(strtolower($_POST['courriel']).md5($_POST['password']))
    try
    {
        $bdd->beginTransaction();
        $req->execute([
            $_POST['courriel'],
            sha1(strtolower($_POST['courriel']).md5($_POST['password']))
        ]);
        $bdd->commit();
        $result = $req->fetch();
    }
    catch(PDOException $e)
    {
        $bdd->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
    }
	
	//$sql = @mysqli_query("SELECT `id`, `lastname`, `firstname`, `is_admin`, `mailaddress`, `password` FROM `users` WHERE `mailaddress`='".mysqli_real_escape_string(strtolower($_POST['courriel']))."' AND `password`='".mysqli_real_escape_string(sha1(strtolower($_POST['courriel']).md5($_POST['password'])))."' LIMIT 0,1");
	if ($result != array()) {
		$_SESSION['id'] = $result['id'];
		$_SESSION['user'] = $result['mailaddress'];
		$_SESSION['pass'] = $result['password'];
		$_SESSION['is_admin'] = $result['is_admin'];
		if (empty($result['firstname']) && empty($result['lastname'])) {
			$_SESSION['user_seen'] = $result['mailaddress'];
		} else {
			$_SESSION['user_seen'] = $result['firstname'].' '.$result['lastname'];
		}
		header("Location:index.php");
	} else {
		$error_msg = 1;
	}

}

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $config['site_name']; ?> : connexion</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/login.css" media="screen" />
	<?php //if ($error_msg === 2) { echo '<meta http-equiv="refresh" content="3; url=index.php" />'; } ?>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.login.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
</head>
<body>
<div>
	<form action="<?php echo $config['site_http']; ?>/login.php" method="post" id="login">
		<img src="<?php echo $config['site_http']; ?>/imgs/logo.png" />
		<h1 class="cufon">Connexion</h1>
		<?php
		if ($error_msg === 1) {
			echo '<p class="error_list">Le courriel ou le mot de passe ne correspondent pas à un compte enregistré.</p>';
		}
		?>
		<p>
			<label for="name">Courriel</label>
			<input id="courriel" name="courriel" type="text" maxlength="50" />
		</p>
		<p>
			<label for="password">Mot de passe (<a href="login.php?forgotpassword">Mot de passe oublié ?</a>)</label>
			<input id="password" name="password" type="password" />
		</p>
		<p class="login-submit">
			<input type="submit" id="send" value="Se connecter" />
		</p>
	</form>
	<p><a href="<?php echo $config['site_http']; ?>/register.php">S'inscrire</a></p>
</div>
<div class="footer" id="footer">
	<span>&copy; Tous droits réservés, <a href="http://wwww.erasme.org/" target="_blank">Erasme</a> 2011 – <a href="<?php echo $config['site_http']; ?>/legals.php">mentions légales</a></span>
</div>
</body>
</html>
<?php
}
?>