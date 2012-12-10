<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }

// test si l'utilisateur a les accès à l'exposition
aaccess();

// mise à jour d'une désignation d'un mot-clef
if (isset($_POST['keyword_update']) && !empty($_POST['keyword_update'])) {
	@mysql_query("UPDATE `expo_keywords` SET `name`='".mysql_real_escape_string($_POST['keyword_update'])."' WHERE `id`='".mysql_real_escape_string($_POST['keyword_update_id'])."'");
}


// édition
if (isset($_GET['edit']) && !empty($_GET['edit'])) {

	// message d'ajout du scénario (obligation d'affichage dûe à la redirection vers la page d'édition de l'exposition directement après son ajout dans la BDD)
	if (isset($_GET['ok'])) {
		echo '<div class="div_valid">Le scénario a correctement été ajoutée.</div>';
	}

	// récupération des infos à afficher
	$sql = @mysql_query("SELECT `id`, `name`, `expo_id` FROM `expo_gkeywords` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."' AND `expo_id`='".mysql_real_escape_string($_GET['expo'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {
		
		$don = @mysql_fetch_assoc($sql);
		$gkeywords = array();
		$gkeywords['gkeywords_name'] = $don['name'];
		$gkeywords['gkeywords_id'] = $don['id'];
		$gkeywords['gkeywords_expo'] = $don['expo_id'];
	
	} else {
		echo '<div class="div_error">Le groupe de mots-clefs n\'existe pas.</div>';
		return 0;
	}
	
	
	if (isset($_GET['del']) && !empty($_GET['del'])) {
		$sql = @mysql_query("DELETE ir, ek FROM `expo_keywords` AS ek LEFT JOIN `items_rkeywords` AS ir ON ek.`id`=ir.`expokeywords_id` WHERE ek.`expogkeywords_id`='".mysql_real_escape_string($_GET['edit'])."' AND ek.`id`='".mysql_real_escape_string($_GET['del'])."'");
	}
	
	if (isset($_POST['gkeywords_k'])) {
		$sql = @mysql_query("INSERT INTO `expo_keywords`(`id`, `expogkeywords_id`, `name`) VALUES(NULL, '".mysql_real_escape_string($_POST['gkeywords_id'])."', '".mysql_real_escape_string($_POST['gkeywords_k'])."')");
	}

}


if (isset($_POST['gkeywords_name'])) {
	
	$gkeywords['gkeywords_name'] = @$_POST['gkeywords_name'];
	$gkeywords['gkeywords_id'] = @$_POST['gkeywords_id'];
	$gkeywords['gkeywords_expo'] = @$_POST['gkeywords_expo'];
	
	// modification d'un scénario
	if (isset($_POST['gkeywords_id']) && !empty($_POST['gkeywords_id'])) {
				
		// test que le scénario existe
		$sql = @mysql_query("SELECT `id` FROM `expo_gkeywords` WHERE `id`='".mysql_real_escape_string($gkeywords['gkeywords_id'])."' AND `expo_id`='".mysql_real_escape_string($gkeywords['gkeywords_expo'])."' LIMIT 0,1");
		if (@mysql_num_rows($sql) > 0) {
		
			// compteur d'erreur avec message
			$error_msg = array();
			
			if (empty($gkeywords['gkeywords_name'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
			
			if (count($error_msg) < 1) {
					
				$sql = @mysql_query("UPDATE `expo_gkeywords` SET `name`='".mysql_real_escape_string($gkeywords['gkeywords_name'])."' WHERE `id`='".mysql_real_escape_string($gkeywords['gkeywords_id'])."' AND `expo_id`='".mysql_real_escape_string($gkeywords['gkeywords_expo'])."'");
				
				if ($sql) {
					echo '<div class="div_valid">Le groupe de mots-clefs a bien été mis à jour.</div>';
				} else {
					echo '<div class="div_error">Impossible de mettre à jour le groupe de mots-clefs. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';		
				}
				
			} else {
				echo '<div class="div_error"><ul class="block">';
				foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
				echo '</ul></div>';
			}
			
		} else {
			echo '<div class="div_error">Le groupe de mots-clefs n\'existe pas et ne peut donc pas être modifiée.</div>';
			$gkeywords = array();
		}
	
	//	nouveau groupe
	} else {

		// compteur d'erreur avec message
		$error_msg = array();
		
		if (empty($gkeywords['gkeywords_name'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
		
		if (count($error_msg) < 1) {
	
			$sql = @mysql_query("INSERT INTO `expo_gkeywords`(`id`, `expo_id`, `name`) VALUES(NULL, '".mysql_real_escape_string($gkeywords['gkeywords_expo'])."', '".mysql_real_escape_string($gkeywords['gkeywords_name'])."')");
			if ($sql) {
			
				$gkeywords['gkeywords_id'] = @mysql_insert_id();
				
				// redirection vers la page d'édition du groupe avec message d'ajout OK
				header("Location:".$config['site_http']."/expo.php?act=akeywords&expo=".$_GET['expo']."&edit=".$gkeywords['gkeywords_id']."&ok");
				
			} else {
			
				echo '<div class="div_error">Impossible d\'ajouter le groupe de mots-clefs. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
				
			}
			
		} else {
			echo '<div class="div_error"><ul class="block">';
			foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
			echo '</ul></div>';
		}
	}

}


?>
<form method="post" id="gkeywords" action="<?php echo $config['site_http'].'/expo.php?act=akeywords&amp;expo='.$_GET['expo'].((isset($gkeywords['gkeywords_id']) && !empty($gkeywords['gkeywords_id']))?'&amp;edit='.$gkeywords['gkeywords_id']:''); ?>">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader"><?php if (isset($gkeywords['gkeywords_id']) && !empty($gkeywords['gkeywords_id'])) { echo 'Modification'; } else { echo 'Ajout'; } ?> d'un groupe de mots-clefs</th></tr></thead>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input type="text" name="gkeywords_name" value="<?php echo @$gkeywords['gkeywords_name']; ?>" maxlength="100" /></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($gkeywords['gkeywords_id']) && !empty($gkeywords['gkeywords_id'])) { echo '<input type="hidden" name="gkeywords_id" value="'.$gkeywords['gkeywords_id'].'" />'; } ?>
			<input type="hidden" name="gkeywords_expo" value="<?php echo $_GET['expo']; ?>" />
			<input type="submit" value="<?php if (isset($gkeywords['gkeywords_id']) && !empty($gkeywords['gkeywords_id'])) { echo 'Modifier'; } else { echo 'Ajouter'; } ?>" />
		</td></tr>
	</tbody></table>
</form><br />

<?php

if (isset($gkeywords['gkeywords_id']) && !empty($gkeywords['gkeywords_id'])) {

?>
<form method="post" id="addkeyword" action="<?php echo $config['site_http'].'/expo.php?act=akeywords&amp;expo='.$_GET['expo'].'&amp;edit='.$gkeywords['gkeywords_id']; ?>">
<input type="text" maxlength="100" name="gkeywords_k" /> <input type="submit" value="Ajouter le mot-clef" />
<input type="hidden" name="gkeywords_id" value="<?php echo $gkeywords['gkeywords_id']; ?>" />
</form>
<table class="tableborder">
<thead><tr><th class="tablesubheader" width="95%">Mots-clefs</th><th class="tablesubheader" width="5%">Suppression</th></tr></thead>
<tbody>
<?php

$sql = @mysql_query("SELECT `id`, `name` FROM `expo_keywords` WHERE `expogkeywords_id`='".mysql_real_escape_string($gkeywords['gkeywords_id'])."' ORDER BY `name`");
if (@mysql_num_rows($sql)>0) {
	while ($don = @mysql_fetch_assoc($sql)) {
		echo '<tr><td class="tablerow1"><form method="post" action="'.$config['site_http'].'/expo.php?act=akeywords&amp;expo='.$_GET['expo'].'&amp;edit='.$gkeywords['gkeywords_id'].'">
		<input type="submit" value="Mettre à jour" /><input value="'.$don['name'].'" name="keyword_update" /><input type="hidden" name="keyword_update_id" value="'.$don['id'].'" />
		</form>
		</td><td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=akeywords&amp;expo='.$gkeywords['gkeywords_expo'].'&amp;edit='.$gkeywords['gkeywords_id'].'&amp;del='.$don['id'].'">Supprimer</a></td></tr>';
	}
} else {
	echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément existant.</td></tr>';
}

?>
</tbody></table>
<?php

}


?>