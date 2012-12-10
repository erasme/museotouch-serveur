<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }

// test si l'utilisateur a les accès à l'exposition
aaccess();

$file_name = '';

// édition
if (isset($_GET['edit']) && !empty($_GET['edit'])) {

	// message d'ajout du scénario (obligation d'affichage dûe à la redirection vers la page d'édition de l'exposition directement après son ajout dans la BDD)
	if (isset($_GET['ok'])) {
		echo '<div class="div_valid">Le scénario a correctement été ajoutée.</div>';
	}

	// suppression d'une image
	if (isset($_GET['deli']) && !empty($_GET['deli'])) {
		@unlink('uploads/scenarios/'.$_GET['edit'].'/'.urldecode($_GET['deli']));
	}


	// récupération des infos à afficher
	$sql = @mysql_query("SELECT `id`, `name`, `type_action`, `id_rfid`, `expo_id` FROM `scenarios` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."' AND `expo_id`='".mysql_real_escape_string($_GET['expo'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {
		
		$don = @mysql_fetch_assoc($sql);
		$scenario = array();
		$scenario['scenario_nom'] = $don['name'];
		$scenario['scenario_expo'] = $don['expo_id'];
		$scenario['scenario_rfid'] = $don['id_rfid'];
		$scenario['scenario_action'] = $don['type_action'];
		$scenario['scenario_id'] = $don['id'];
	
	} else {
		echo '<div class="div_error">Le scénario n\'existe pas.</div>';
		return 0;
	}
	
	if ($scenario['scenario_action'] == 1) {
		// si un fichier a été envoyé
		if (@is_uploaded_file($_FILES['scenario_file']['tmp_name']) && !@empty($_FILES['scenario_file']['tmp_name'])) {
			// traitement pour l'upload du fichier
			if ($_FILES['scenario_file']['error']) {    
				switch ($_FILES['scenario_file']['error']) {    
					case 1: // UPLOAD_ERR_INI_SIZE
					case 2: // UPLOAD_ERR_FORM_SIZE
						echo '<div class="div_error">Le poids du fichier est trop important. Réduisez le poids de votre fichier ou contacter un administrateur pour qu\'il puisse résoudre votre problème.</div>';
					   break;
					case 3: // UPLOAD_ERR_PARTIAL
						echo '<div class="div_error">Une erreur est survenue lors de l\'envoi du fichier. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';
						break;
					case 4: // UPLOAD_ERR_NO_FILE
						echo '<div class="div_error">Le fichier que vous avez envoyé est défectueux. Veuillez en changer et rententez l\'opération.</div>';
						break;
				}
			} else if ((isset($_FILES['scenario_file']['name']) && ($_FILES['scenario_file']['error'] == UPLOAD_ERR_OK))) {
				if (!is_dir('uploads/scenarios/'.$scenario['scenario_id'].'/')) {
					mkdir('uploads/scenarios/'.$scenario['scenario_id'].'/');
				}
				$file_name = md5(microtime().$_FILES['scenario_file']['name']).substr($_FILES['scenario_file']['name'], strrpos($_FILES['scenario_file']['name'], '.'));
				move_uploaded_file($_FILES['scenario_file']['tmp_name'], 'uploads/scenarios/'.$scenario['scenario_id'].'/'.$file_name);
			}
		}
	}
	
	
	// suppression de l'objet à l'exposition
	if (isset($_GET['del']) && !empty($_GET['del'])) {
		$sql = @mysql_query("DELETE FROM `scenarios_items` WHERE `items_id`='".mysql_real_escape_string($_GET['del'])."' AND `scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."'");
		if (!$sql) {
			echo '<div class="error_valid">Une erreur s\'est produite. Si l\'erreur se répète, veuillez contacter un administrateur.</div>';		
		}
	}

	
	// ajout de l'objet à l'exposition
	if (isset($_GET['add']) && !empty($_GET['add'])) {
		$sql = @mysql_query("SELECT `items_id` FROM `scenarios_items` WHERE `items_id`='".mysql_real_escape_string($_GET['add'])."' AND `scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."' LIMIT 0,1");
		if (@mysql_num_rows($sql) < 1) {
			$sql = @mysql_query("INSERT INTO `scenarios_items`(`items_id`, `scenarios_id`) VALUES('".mysql_real_escape_string($_GET['add'])."', '".mysql_real_escape_string($scenario['scenario_id'])."')");
			if (!$sql) {
				echo '<div class="error_valid">Une erreur s\'est produite. Si l\'erreur se répète, veuillez contacter un administrateur.</div>';		
			}
		}
	}

}


if (isset($_POST['scenario_nom'])) {
	
	$scenario['scenario_nom'] = @$_POST['scenario_nom'];
	$scenario['scenario_expo'] = @$_POST['scenario_expo'];
	$scenario['scenario_rfid'] = @$_POST['scenario_rfid'];
	$scenario['scenario_action'] = @$_POST['scenario_action'];
	$scenario['scenario_id'] = @$_POST['scenario_id'];
	
	// modification d'un scénario
	if (isset($_POST['scenario_id']) && !empty($_POST['scenario_id'])) {
				
		// test que le scénario existe
		$sql = @mysql_query("SELECT `id`, `type_action` FROM `scenarios` WHERE `id`='".mysql_real_escape_string($scenario['scenario_id'])."' AND `expo_id`='".mysql_real_escape_string($scenario['scenario_expo'])."' LIMIT 0,1");
		if (@mysql_num_rows($sql) > 0) {
		
			// compteur d'erreur avec message
			$error_msg = array();
			
			if (empty($scenario['scenario_nom'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
			if (empty($scenario['scenario_rfid'])) { $error_msg[] = 'l\'identifiant RFID doit obligatoirement être renseigné'; }
			
			if (count($error_msg) < 1) {
		
				$don = @mysql_fetch_assoc($sql);
				// le scénario passe d'un type "image" à "reboot", on supprime les fichiers
				if ($don['type_action'] == 1 && $scenario['scenario_action'] == 0) {
					// supprime le contenu du dossier compressed
					if (@is_dir('uploads/scenarios/'.$_GET['del'].'/')) {
						$dir = @opendir('uploads/scenarios/'.$_GET['del'].'/');
						while (false !== ($file = readdir($dir))) {
							if (($file !== ".") && ($file !== "..")) {
								@unlink('uploads/scenarios/'.$_GET['del'].'/'.$file);
							}
						}
						// puis le dossier
						@rmdir('uploads/scenarios/'.$_GET['del'].'/');
					}
				}
			
				$sql = @mysql_query("UPDATE `scenarios` SET `name`='".mysql_real_escape_string($scenario['scenario_nom'])."', `id_rfid`='".mysql_real_escape_string($scenario['scenario_rfid'])."', `type_action`='".mysql_real_escape_string($scenario['scenario_action'])."' WHERE `id`='".mysql_real_escape_string($scenario['scenario_id'])."' AND `expo_id`='".mysql_real_escape_string($scenario['scenario_expo'])."'");
				
				if ($sql) {
					echo '<div class="div_valid">Le scénario a bien été mis à jour.</div>';
				} else {
					echo '<div class="div_error">Impossible de mettre à jour le scénario. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';		
				}
				
			} else {
				echo '<div class="div_error"><ul class="block">';
				foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
				echo '</ul></div>';
			}
			
		} else {
			echo '<div class="div_error">Le scénario n\'existe pas et ne peut donc pas être modifiée.</div>';
			$scenario = array();
		}
	
	//	nouveau scénario
	} else {

		// compteur d'erreur avec message
		$error_msg = array();
		
		if (empty($scenario['scenario_nom'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
		if (empty($scenario['scenario_rfid'])) { $error_msg[] = 'l\'identifiant RFID doit obligatoirement être renseigné'; }
		
		if (count($error_msg) < 1) {
	
			$sql = @mysql_query("INSERT INTO `scenarios`(`id`, `expo_id`, `name`, `id_rfid`, `type_action`) VALUES(NULL, '".mysql_real_escape_string($scenario['scenario_expo'])."', '".mysql_real_escape_string($scenario['scenario_nom'])."', '".mysql_real_escape_string($scenario['scenario_rfid'])."', '".mysql_real_escape_string($scenario['scenario_action'])."')");
			if ($sql) {
			
				$scenario['scenario_id'] = @mysql_insert_id();
				
				// redirection vers la page d'édition de lu scénario avec message d'ajout OK
				header("Location:".$config['site_http']."/expo.php?act=ascenario&expo=".$_GET['expo']."&edit=".$scenario['scenario_id']."&ok");
				
			} else {
			
				echo '<div class="div_error">Impossible d\'ajouter le scénario. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
				
			}
			
		} else {
			echo '<div class="div_error"><ul class="block">';
			foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
			echo '</ul></div>';
		}
	}

}


?>
<form method="post" id="scenario" action="<?php echo $config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].((isset($scenario['scenario_id']) && !empty($scenario['scenario_id']))?'&amp;edit='.$scenario['scenario_id']:''); ?>">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader"><?php if (isset($scenario['scenario_id']) && !empty($scenario['scenario_id'])) { echo 'Modification'; } else { echo 'Ajout'; } ?> d'un scénario</th></tr></thead>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input type="text" name="scenario_nom" value="<?php echo @$scenario['scenario_nom']; ?>" maxlength="50" /></td></tr>
		<tr><td class="tablerow1">ID RFID</td><td class="tablerow2"><input type="text" name="scenario_rfid" value="<?php echo @$scenario['scenario_rfid']; ?>" maxlength="14" /></td></tr>
		<tr><td class="tablerow1">Type d'action</td><td class="tablerow2"><select name="scenario_action">
			<option value="0"<?php echo ((@$scenario['scenario_action']==0)?' selected="selected"':''); ?>>Redémarrage</option>
			<option value="1"<?php echo ((@$scenario['scenario_action']==1)?' selected="selected"':''); ?>>Affichage d'images</option>
		</select></td></tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($scenario['scenario_id']) && !empty($scenario['scenario_id'])) { echo '<input type="hidden" name="scenario_id" value="'.$scenario['scenario_id'].'" />'; } ?>
			<input type="hidden" name="scenario_expo" value="<?php echo $_GET['expo']; ?>" />
			<input type="submit" value="<?php if (isset($scenario['scenario_id']) && !empty($scenario['scenario_id'])) { echo 'Modifier'; } else { echo 'Ajouter'; } ?>" />
		</td></tr>
	</tbody></table>
</form><br />

<?php

if (@$scenario['scenario_action'] == 1) {

	?>
	<form method="post" id="upscenario" enctype="multipart/form-data" action="<?php echo $config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].((isset($scenario['scenario_id']) && !empty($scenario['scenario_id']))?'&amp;edit='.$scenario['scenario_id']:''); ?>">
	<input type="file" name="scenario_file" /> (taille maximale : <?php echo ini_get('post_max_size'); ?>) <input type="submit" value="Ajouter un fichier" />

	</form>
	<?php
	// liste des fichiers dans le dossier du scénario
	if (@is_dir('uploads/scenarios/'.@$scenario['scenario_id'].'/')) {
		if ($dir = @opendir('uploads/scenarios/'.$scenario['scenario_id'].'/')) {
			while (($file = @readdir($dir)) !== false) {
				if ($file !== '.' && $file !== '..' && is_file('uploads/scenarios/'.$scenario['scenario_id'].'/'.$file)) {
					echo '<a href="'.$config['site_http'].'/uploads/scenarios/'.@$scenario['scenario_id'].'/'.$file.'"'.(($file==$file_name)?' class="new_item"':'').'>'.$file.'</a> | <a href="'.$config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.@$scenario['scenario_id'].'&amp;deli='.urlencode($file).'">Supprimer</a><br />';
				}
			}
			@closedir($dir);
		}
	}
?>
<br /><br /><br /><br />
	<table width="100%"><tr><td width="50%">
		<h3 class="cufon inline">Objets associés aux scénario :</h3>
		<form class="right" method="get" action="<?php echo $config['site_http']; ?>/expo.php">
			<input type="hidden" name="act" value="ascenario" />
			<input type="hidden" name="edit" value="<?php echo $scenario['scenario_id']; ?>" />
			<input type="hidden" name="paged" value="<?php echo @$_GET['paged']; ?>" />
			<input name="searchg" value="<?php echo @$_GET['searchg']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
		</form>
		<table class="tableborder">
		<thead><tr><th class="tablesubheader center" width="10%">Retirer</th><th class="tablesubheader" width="90%">Nom de l'objet</th></tr></thead>
		<tbody>
		<?php
		// recherche
		$search = '';
		if (isset($_GET['searchg']) && !empty($_GET['searchg'])) {
			$search = "i.`nom` LIKE '%".mysql_real_escape_string($_GET['searchg'])."%' AND";
		}

		$sql = @mysql_fetch_assoc(@mysql_query("SELECT count(i.`id`) as nbre FROM `items` i LEFT JOIN `scenarios_items` si ON i.`id`=si.`items_id` WHERE $search i.`expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND si.`scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."'"));
		$limit_page = 30;
		if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
		if (isset($_GET['pageg']) && !empty($_GET['pageg'])) { $page = intval($_GET['pageg']); } else { $page = 1; }
		if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
		if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
		if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };

		$sql = @mysql_query("SELECT i.`id`, i.`nom` FROM `items` i LEFT JOIN `scenarios_items` si ON i.`id`=si.`items_id` WHERE $search i.`expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND si.`scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."' ORDER BY i.`nom` LIMIT $from,$limit_page");
		if (@mysql_num_rows($sql) > 0) {
			while ($don = @mysql_fetch_assoc($sql)) {
				echo '<tr><td class="tablerow1 center"><a href="'.$config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;del='.$don['id'].'">[ retirer ]</a></td><td class="tablerow2">'.$don['nom'].'</td></tr>';
			}
		} else {
			if (!empty($search)) {
				echo '<tr><td colspan="2" class="tablerow1 center">Aucun résultat pour la recherche.</td></tr>';
			} else {
				echo '<tr><td colspan="2" class="tablerow1 center">Aucun objet ne peut être ajouté.</td></tr>';
			}
		}
		
		// pagination
		// 1ere page
		echo '<tr><td class="tablesubheader" colspan="2" align="center" valign="middle">';
		if($page - $pageavant > 1) {
			echo '&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg=1&amp;paged='.@$_GET['paged'].'">1</a>&nbsp;...';
		}
		// pages intermédiaires
		for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
			if ($i == $page) {
				echo '&nbsp;<b>['.$i.']</b>&nbsp;';
			} else {
				echo '&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg='.$i.'&amp;paged='.@$_GET['paged'].'">'.$i.'</a>&nbsp;';
			}
		}
		// dernière page
		if($page + $pageapres < $nb_pages) {
			echo '...&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg='.$nb_pages.'&amp;paged='.@$_GET['paged'].'">'.$nb_pages.'</a>&nbsp;';
		}
		echo '</td></tr>';
		
		?>
		</tbody></table>
	</td><td width="50%">
		<h3 class="cufon inline">Associer un objet au scénario :</h3>
		<form class="right" method="get" action="<?php echo $config['site_http']; ?>/expo.php">
			<input type="hidden" name="act" value="ascenario" />
			<input type="hidden" name="edit" value="<?php echo $scenario['scenario_id']; ?>" />
			<input type="hidden" name="pageg" value="<?php echo @$_GET['pageg']; ?>" />
			<input name="searchd" value="<?php echo @$_GET['searchd']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
		</form>
		<table class="tableborder">
		<thead><tr><th class="tablesubheader center" width="10%">Ajouter</th><th class="tablesubheader" width="90%">Nom de l'objet</th></tr></thead>
		<tbody>
		<?php
		// recherche
		$search = '';
		if (isset($_GET['searchd']) && !empty($_GET['searchd'])) {
			$search = "`nom` LIKE '%".mysql_real_escape_string($_GET['searchd'])."%' AND";
		}
		
		$sql = @mysql_fetch_assoc(@mysql_query("SELECT count(`id`) as nbre FROM `items` WHERE $search `expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND `id` NOT IN (SELECT i.`id` FROM `items` i LEFT JOIN `scenarios_items` si ON i.`id`=si.`items_id` WHERE i.`expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND si.`scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."')"));
		$limit_page = 30;
		if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
		if (isset($_GET['paged']) && !empty($_GET['paged'])) { $page = intval($_GET['paged']); } else { $page = 1; }
		if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
		if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
		if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };
		
		$sql = @mysql_query("SELECT `id`, `nom` FROM `items` WHERE $search `expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND `id` NOT IN (SELECT i.`id` FROM `items` i LEFT JOIN `scenarios_items` si ON i.`id`=si.`items_id` WHERE i.`expo_id`='".mysql_real_escape_string($_GET['expo'])."' AND si.`scenarios_id`='".mysql_real_escape_string($scenario['scenario_id'])."') ORDER BY `nom` LIMIT $from,$limit_page");
		if (@mysql_num_rows($sql) > 0) {
			while ($don = @mysql_fetch_assoc($sql)) {
				echo '<tr><td class="tablerow1 center"><a href="'.$config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;add='.$don['id'].'">[ ajouter ]</a></td><td class="tablerow2">'.$don['nom'].'</td></tr>';
			}
		} else {
			if (!empty($search)) {
				echo '<tr><td colspan="2" class="tablerow1 center">Aucun résultat pour la recherche.</td></tr>';
			} else {
				echo '<tr><td colspan="2" class="tablerow1 center">Aucun objet ne peut être ajouté.</td></tr>';
			}
		}
		
		// pagination
		// 1ere page
		echo '<tr><td class="tablesubheader" colspan="2" align="center" valign="middle">';
		if($page - $pageavant > 1) {
			echo '&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg='.@$_GET['pageg'].'&amp;paged=1">1</a>&nbsp;...';
		}
		// pages intermédiaires
		for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
			if ($i == $page) {
				echo '&nbsp;<b>['.$i.']</b>&nbsp;';
			} else {
				echo '&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg='.@$_GET['paged'].'&amp;paged='.$i.'">'.$i.'</a>&nbsp;';
			}
		}
		// dernière page
		if($page + $pageapres < $nb_pages) {
			echo '...&nbsp;<a href="expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$scenario['scenario_id'].'&amp;pageg='.@$_GET['paged'].'&amp;paged='.$nb_pages.'">'.$nb_pages.'</a>&nbsp;';
		}
		echo '</td></tr>';
		
		?>
		</tbody></table>
	</td></tr></table>

<?php

}

?>