<?php

if (!defined('INDEX_ADMIN')) { header("Location:../profil.php"); }

// message d'ajout d'utilisateur (obligation d'affichage dûe à la redirection vers la page d'édition de l'utilisateur directement après son ajout dans la BDD)
if (isset($_GET['ok'])) {
	echo '<div class="div_valid">Vous avez correctement édité votre profil.</div>';
}

// modification des données de base de l'utilisateur
if (isset($_POST['user_lastpasswd'])) {

	$user['user_lastpasswd'] = @$_POST['user_lastpasswd'];
	$user['user_newpasswd1'] = @$_POST['user_newpasswd1'];
	$user['user_newpasswd2'] = @$_POST['user_newpasswd2'];
	$user['user_id'] = @$_POST['user_id'];
	
	// si la session et l'id envoyé du formulaire sont égaux
	if ($_SESSION['id'] == $user['user_id']) {
	
		// édition
		$sql = @mysql_query("SELECT `id` FROM `users` WHERE `id`='".mysql_real_escape_string($user['user_id'])."' AND `password`='".mysql_real_escape_string(sha1(strtolower($_SESSION['user']).md5($user['user_lastpasswd'])))."'");
		// l'utilisateur existe
		if (@mysql_num_rows($sql) > 0) {
		
			// compteur d'erreur avec message
			$error_msg = array();
			
			if (empty($user['user_lastpasswd'])) { $error_msg[] = 'l\'ancien mot de passe doit obligatoirement être renseigné'; }
			if (empty($user['user_newpasswd1'])) { $error_msg[] = 'le nouveau mot de passe doit obligatoirement être renseigné'; }
			if (empty($user['user_newpasswd2'])) { $error_msg[] = 'la confirmation du nouveau mot de passe doit obligatoirement être renseigné'; }
			if ($user['user_newpasswd1'] != $user['user_newpasswd2']) { $error_msg[] = 'le nouveau mot de passe ainsi que sa confirmation doivent être identiques'; }
			
			if (count($error_msg) < 1) {
		
				$sql = @mysql_query("UPDATE `users` SET `password`='".mysql_real_escape_string(sha1(strtolower($_SESSION['user']).md5($user['user_newpasswd1'])))."' WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
				if ($sql) {
					echo '<div class="div_valid">Vous avez correctement modifié votre mot de passe.</div>';
				} else {
					echo '<div class="div_error">Impossible de modifier votre mot de passe. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
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
		header("Location:".$config['site_http'].'/profil.php?act=passwd');
	}
	
}



?>
<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Modification des informations</th></tr>
	<tbody>
		<tr><td class="tablerow1">Courriel associé</td><td class="tablerow2"><?php echo $_SESSION['user']; ?></td></tr>
		<tr><td class="tablerow1">Ancien mot de passe</td><td class="tablerow2"><input type="password" name="user_lastpasswd" value="" /></td></tr>
		<tr><td class="tablerow1">Nouveau mot de passe</td><td class="tablerow2"><input type="password" name="user_newpasswd1" value="" /></td></tr>
		<tr><td class="tablerow1">Nouveau mot de passe (confirmation)</td><td class="tablerow2"><input type="password" name="user_newpasswd2" value="" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php echo '<input type="hidden" name="user_id" value="'.$_SESSION['id'].'" />'; ?>
			<input type="submit" value="Modifier" />
		</td></tr>
	</tbody></table>
</form>
