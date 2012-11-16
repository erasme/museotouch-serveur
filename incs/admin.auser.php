<?php

if (!defined('INDEX_ADMIN')) { header("Location:../admin.php"); }

if (isset($_GET['edit']) && !empty($_GET['edit'])) {

// message d'ajout d'utilisateur (obligation d'affichage dûe à la redirection vers la page d'édition de l'utilisateur directement après son ajout dans la BDD)
if (isset($_GET['ok'])) {
	echo '<div class="div_valid">L\'utilisateur a correctement été ajoutée.</div>';
}


$sql = @mysql_query("SELECT `id`, `lastname`, `firstname`, `mailaddress`, `is_admin` FROM `users` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."' LIMIT 0,1");
if (@mysql_num_rows($sql) > 0) {
	$don = @mysql_fetch_assoc($sql);
	$user = array();
	$user['user_lastname'] = $don['lastname'];
	$user['user_firstname'] = $don['firstname'];
	$user['user_mailaddress'] = $don['mailaddress'];
	$user['user_isadmin'] = $don['is_admin'];
	$user['user_id'] = $don['id'];
} else {
	header("Location:".$config['site_http'].'/admin.php?act=luser');
}




// modification des données de base de l'utilisateur
if (isset($_POST['user_lastname'])) {

	$user['user_lastname'] = @$_POST['user_lastname'];
	$user['user_firstname'] = @$_POST['user_firstname'];
	$user['user_isadmin'] = @$_POST['user_admin'];
	$user['user_id'] = @$_POST['user_id'];
	
	// édition
	$sql = @mysql_query("SELECT `id` FROM `users` WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
	// l'utilisateur existe
	if (@mysql_num_rows($sql) > 0) {
	
		// compteur d'erreur avec message
		$error_msg = array();
		
		if (empty($user['user_lastname'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
		if (empty($user['user_firstname'])) { $error_msg[] = 'le prénom doit obligatoirement être renseigné'; }
		
		if (count($error_msg) < 1) {
	
			$sql = @mysql_query("UPDATE `users` SET `lastname`='".mysql_real_escape_string(strtoupper($user['user_lastname']))."', `firstname`='".mysql_real_escape_string(ucfirst(strtolower($user['user_firstname'])))."', `is_admin`='".mysql_real_escape_string($user['user_isadmin'])."' WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
			if ($sql) {
				echo '<div class="div_valid">L\'utilisateur a correctement été modifié.</div>';
			} else {
				echo '<div class="div_error">Impossible de modifier l\'utilisateur. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
			}
			
		} else {
			echo '<div class="div_error"><ul class="block">';
			foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
			echo '</ul></div>';
		}

	// l'utilisateur n'existe pas
	} else {
		echo '<div class="div_error">L\'utilisateur n\'existe pas et ne peut donc pas être modifié.</div>';
		return false; // sortie du script sans empêcher l'affichage des autres fichiers inclus (footer par exemple)
	}
	
}


// modification des données sensibles de l'utilisateur
if (isset($_POST['user_mailaddress'])) {

	$user['user_mailaddress'] = @$_POST['user_mailaddress'];
	$user['user_password'] = @$_POST['user_password'];
	$user['user_id'] = @$_POST['user_id'];
	
	// édition
	$sql = @mysql_query("SELECT `id` FROM `users` WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
	// l'utilisateur existe
	if (@mysql_num_rows($sql) > 0) {
	
		// compteur d'erreur avec message
		$error_msg = array();
		
		if (empty($user['user_mailaddress']) || !preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,6})$`', $user['user_mailaddress'])) { $error_msg[] = 'le courriel doit obligatoirement être renseigné et valide'; }
		if (empty($user['user_password'])) { $error_msg[] = 'le mot de passe doit obligatoirement être renseigné'; }
		
		if (count($error_msg) < 1) {
	
			$sql = @mysql_query("UPDATE `users` SET `mailaddress`='".mysql_real_escape_string($user['user_mailaddress'])."', `password`='".mysql_real_escape_string(sha1(strtolower($user['user_mailaddress']).md5($user['user_password'])))."' WHERE `id`='".mysql_real_escape_string($user['user_id'])."'");
			if ($sql) {
				echo '<div class="div_valid">L\'utilisateur a correctement été modifié.</div>';
			} else {
				echo '<div class="div_error">Impossible de modifier l\'utilisateur. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
			}
			
		} else {
			echo '<div class="div_error"><ul class="block">';
			foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
			echo '</ul></div>';
		}
		
	// l'utilisateur n'existe pas
	} else {
		echo '<div class="div_error">L\'utilisateur n\'existe pas et ne peut donc pas être modifié.</div>';
		return false; // sortie du script sans empêcher l'affichage des autres fichiers inclus (footer par exemple)
	}
	
}



?>
<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Modification des informations</th></tr>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_lastname" value="<?php echo @$user['user_lastname']; ?>" /></td></tr>
		<tr><td class="tablerow1">Prénom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_firstname" value="<?php echo @$user['user_firstname']; ?>" /></td></tr>
		<tr><td class="tablerow1">Niveau d'administration</td><td class="tablerow2"><select name="user_admin">
			<option value="0"<?php echo ((@$user['user_isadmin'] == 0)?' selected="selected"':''); ?>>Utilisateur</option>
			<option value="1"<?php echo ((@$user['user_isadmin'] == 1)?' selected="selected"':''); ?>>Administrateur d'expositions</option>
			<option value="2"<?php echo ((@$user['user_isadmin'] == 2)?' selected="selected"':''); ?>>Super administrateur</option>
		</select></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($user['user_id']) && !empty($user['user_id'])) { echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />'; } ?>
			<input type="submit" value="Modifier" />
		</td></tr>
	</tbody></table>
</form>

<br /><br />

<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Modification des informations sensibles</th></tr>
	<tbody>
		<tr><td class="tablerow1 center" colspan="2">La modification du courriel entraîne obligatoirement la modification du mot de passe</td></tr>
		<tr><td class="tablerow1">Courriel</td><td class="tablerow2"><input maxlength="50" type="text" name="user_mailaddress" value="<?php echo @$user['user_mailaddress']; ?>" /></td></tr>
		<tr><td class="tablerow1">Mot de passe</td><td class="tablerow2"><input type="text" name="user_password" value="" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($user['user_id']) && !empty($user['user_id'])) { echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />'; } ?>
			<input type="submit" value="Modifier" />
		</td></tr>
	</tbody></table>
</form>

<?php

} else {


if (isset($_POST['user_mailaddress'])) {

	$user['user_lastname'] = @$_POST['user_lastname'];
	$user['user_firstname'] = @$_POST['user_firstname'];
	$user['user_mailaddress'] = @$_POST['user_mailaddress'];
	$user['user_admin'] = @$_POST['user_admin'];
	$user['user_password'] = @$_POST['user_password'];
	
	// compteur d'erreur avec message
	$error_msg = array();
	
	if (empty($user['user_lastname'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
	if (empty($user['user_firstname'])) { $error_msg[] = 'le prénom doit obligatoirement être renseigné'; }
	if (empty($user['user_mailaddress']) || !preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,6})$`', $user['user_mailaddress'])) { $error_msg[] = 'le courriel doit obligatoirement être renseigné et valide'; }
	if (empty($user['user_password'])) { $error_msg[] = 'le mot de passe doit obligatoirement être renseigné'; }
	
	if (count($error_msg) < 1) {
	
		$sql = @mysql_query("INSERT INTO `users`(`id`, `lastname`, `firstname`, `mailaddress`, `is_admin`, `password`) VALUES(NULL, '".mysql_real_escape_string(strtoupper($user['user_lastname']))."', '".mysql_real_escape_string(ucfirst(strtolower($user['user_firstname'])))."', '".mysql_real_escape_string(strtolower($user['user_mailaddress']))."', '".mysql_real_escape_string($user['user_admin'])."', '".mysql_real_escape_string(sha1(strtolower($user['user_mailaddress']).md5($user['user_password'])))."')");
		if ($sql) {
			header("Location:".$config['site_http'].'/admin.php?act=auser&edit='.@mysql_insert_id().'&ok');		
		} else {
			echo '<div class="div_error">Impossible d\'ajouter l\'utilisateur. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
		}
		
	} else {
		echo '<div class="div_error"><ul class="block">';
		foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
		echo '</ul></div>';
	}
	
}

?>

<form method="post">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader">Ajout d'un utilisateur</th></tr>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_lastname" value="<?php echo @$user['user_lastname']; ?>" /></td></tr>
		<tr><td class="tablerow1">Prénom</td><td class="tablerow2"><input maxlength="50" type="text" name="user_firstname" value="<?php echo @$user['user_firstname']; ?>" /></td></tr>
		<tr><td class="tablerow1">Niveau d'administration</td><td class="tablerow2"><select name="user_admin">
			<option value="0"<?php echo ((@$user['user_admin'] == 0)?' selected="selected"':''); ?>>Utilisateur</option>
			<option value="1"<?php echo ((@$user['user_admin'] == 1)?' selected="selected"':''); ?>>Administrateur d'expositions</option>
			<option value="2"<?php echo ((@$user['user_admin'] == 2)?' selected="selected"':''); ?>>Super administrateur</option>
		</select></td></tr>
		<tr><td class="tablerow1">Courriel</td><td class="tablerow2"><input maxlength="50" type="text" name="user_mailaddress" value="<?php echo @$user['user_mailaddress']; ?>" /></td></tr>
		<tr><td class="tablerow1">Mot de passe</td><td class="tablerow2"><input type="text" name="user_password" value="<?php echo @$user['user_password']; ?>" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($user['user_id']) && !empty($user['user_id'])) { echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />'; } ?>
			<input type="submit" value="Ajouter" />
		</td></tr>
	</tbody></table>
</form>

<?php

}

?>