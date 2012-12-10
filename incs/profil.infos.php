<?php

if (!defined('INDEX_ADMIN')) { header("Location:../profil.php"); }

// message d'ajout d'utilisateur (obligation d'affichage dûe à la redirection vers la page d'édition de l'utilisateur directement après son ajout dans la BDD)
if (isset($_GET['ok'])) {
	echo '<div class="div_valid">Vous avez correctement édité votre profil.</div>';
}

// modification des données de base de l'utilisateur
if (isset($_POST['user_lastname'])) {

	$user['user_lastname'] = @$_POST['user_lastname'];
	$user['user_firstname'] = @$_POST['user_firstname'];
	$user['user_id'] = @$_POST['user_id'];
	
	// si la session et l'id envoyé du formulaire sont égaux
	if ($_SESSION['id'] == $user['user_id']) {
	
		// édition
		$sql = @mysql_query("SELECT `id` FROM `users` WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
		// l'utilisateur existe
		if (@mysql_num_rows($sql) > 0) {
		
			// compteur d'erreur avec message
			$error_msg = array();
			
			if (empty($user['user_lastname'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
			if (empty($user['user_firstname'])) { $error_msg[] = 'le prénom doit obligatoirement être renseigné'; }
			
			if (count($error_msg) < 1) {
		
				$sql = @mysql_query("UPDATE `users` SET `lastname`='".mysql_real_escape_string(strtoupper($user['user_lastname']))."', `firstname`='".mysql_real_escape_string(ucfirst(strtolower($user['user_firstname'])))."' WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
				if ($sql) {
					echo '<div class="div_valid">Vous avez correctement modifié votre profil.</div>';
				} else {
					echo '<div class="div_error">Impossible de modifier votre profil. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
				}
				
			} else {
				echo '<div class="div_error"><ul class="block">';
				foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
				echo '</ul></div>';
			}

		// l'utilisateur n'existe pas
		} else {
			echo '<div class="div_error">Vous tentez de modifier le profil d\'une autre personne.</div>';
			return false; // sortie du script sans empêcher l'affichage des autres fichiers inclus (footer par exemple)
		}
		
	} else {
		header("Location:".$config['site_http'].'/profil.php?act=infos');
	}
	
}


$sql = @mysql_query("SELECT `id`, `lastname`, `firstname` FROM `users` WHERE `id`='".mysql_real_escape_string($_SESSION['id'])."' LIMIT 0,1");
if (@mysql_num_rows($sql) > 0) {
	$don = @mysql_fetch_assoc($sql);
	$user = array();
	$user['user_lastname'] = $don['lastname'];
	$user['user_firstname'] = $don['firstname'];
	$user['user_id'] = $don['id'];
} else {
	header("Location:".$config['site_http'].'/logout.php');
}



?>
<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Modification des informations</th></tr>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_lastname" value="<?php echo @$user['user_lastname']; ?>" /></td></tr>
		<tr><td class="tablerow1">Prénom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_firstname" value="<?php echo @$user['user_firstname']; ?>" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />'; ?>
			<input type="submit" value="Modifier" />
		</td></tr>
	</tbody></table>
</form>
