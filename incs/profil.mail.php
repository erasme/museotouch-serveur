<?php

if (!defined('INDEX_ADMIN')) { header("Location:../profil.php"); }

// message d'ajout d'utilisateur (obligation d'affichage dûe à la redirection vers la page d'édition de l'utilisateur directement après son ajout dans la BDD)
if (isset($_GET['ok'])) {
	echo '<div class="div_valid">Vous avez correctement édité votre profil.</div>';
}

// modification des données de base de l'utilisateur
if (isset($_POST['user_mail'])) {

	$user['user_mail'] = @$_POST['user_mail'];
	$user['user_passwd'] = @$_POST['user_passwd'];
	$user['user_id'] = @$_POST['user_id'];
	
	// si la session et l'id envoyé du formulaire sont égaux
	if ($_SESSION['id'] == $user['user_id']) {
	
		// édition
		$sql = @mysql_query("SELECT `id` FROM `users` WHERE `id`='".mysql_real_escape_string($user['user_id'])."' AND `password`='".mysql_real_escape_string(sha1(strtolower($_SESSION['user']).md5($user['user_passwd'])))."'");
		// l'utilisateur existe
		if (@mysql_num_rows($sql) > 0) {
		
			// compteur d'erreur avec message
			$error_msg = array();
			
			if (empty($user['user_mail']) || !preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,6})$`', $user['user_mail'])) { $error_msg[] = 'le courriel doit obligatoirement être renseigné et valide'; }
			
			if (count($error_msg) < 1) {
		
				$sql = @mysql_query("UPDATE `users` SET `password`='".mysql_real_escape_string(sha1(strtolower($user['user_mail']).md5($user['user_passwd'])))."', `mailaddress`='".mysql_real_escape_string(strtolower($user['user_mail']))."' WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
				if ($sql) {
					echo '<div class="div_valid">Vous avez correctement modifié votre courriel.</div>';
				} else {
					echo '<div class="div_error">Impossible de modifier votre courriel. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
				}
				
			} else {
				echo '<div class="div_error"><ul class="block">';
				foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
				echo '</ul></div>';
			}

		// l'utilisateur n'existe pas
		} else {
			echo '<div class="div_error">Votre ancien mot de passe n\'est pas correct.</div>';
		}
		
	} else {
		header("Location:".$config['site_http'].'/profil.php?act=mail');
	}
	
}

$sql = @mysql_query("SELECT `id`, `mailaddress` FROM `users` WHERE `id`='".mysql_real_escape_string($_SESSION['id'])."' LIMIT 0,1");
if (@mysql_num_rows($sql) > 0) {
	$don = @mysql_fetch_assoc($sql);
	$user = array();
	$user['user_mailaddress'] = $don['mailaddress'];
	$user['user_id'] = $don['id'];
} else {
	header("Location:".$config['site_http'].'/logout.php');
}



?>
<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Modification des informations</th></tr>
	<tbody>
		<tr><td class="tablerow1">Courriel</td><td class="tablerow2"><input type="text" name="user_mail" value="<?php echo $user['user_mailaddress']; ?>" maxlength="50" /></td></tr>
		<tr><td class="tablerow1">Mot de passe</td><td class="tablerow2"><input type="password" name="user_passwd" value="" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />'; ?>
			<input type="submit" value="Modifier" />
		</td></tr>
	</tbody></table>
</form>
