<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }

$file_name = '';

function expo_has_field_type($expo_id, $field_type_id) {
	$req = "SELECT * FROM `expo_fields` WHERE `expo_id`=".$expo_id." AND `fields_type_id`=".$field_type_id."";
	$sql3 = @mysql_query($req);
	if (@mysql_num_rows($sql3) > 0 ) {
		while ($don2 = @mysql_fetch_assoc($sql3)){
			return $don2;
		}
	} 
	return NULL;
}

if (isset($_GET['expo']) && !empty($_GET['expo'])) {

	// message d'ajout d'exposition (obligation d'affichage dûe à la redirection vers la page d'édition de l'exposition directement après son ajout dans la BDD)
	if (isset($_GET['ok'])) {
		echo '<div class="div_valid">L\'exposition a correctement été ajoutée.</div>';
	}

	// si pas su
	if ($_SESSION['is_admin'] < 2) {
		// vérification que l'utilisateur a bien les droits sur l'exposition sinon, redirection vers la liste des expositions
		$sql = @mysql_query("SELECT e.`id` FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' AND e.`id`='".mysql_real_escape_string($_GET['expo'])."'");
		if (@mysql_num_rows($sql) < 1) {
			header("Location:".$config['site_http']."/expo.php?act=lexpos");
		}
	}

	// suppression d'une image
	if (isset($_GET['deli']) && !empty($_GET['deli'])) {
		@unlink('uploads/expos/'.$_GET['expo'].'/raw/'.urldecode($_GET['deli']));
		@unlink('uploads/expos/'.$_GET['expo'].'/compressed/'.urldecode($_GET['deli']));
	}

	// récupération des infos à afficher
	$sql = @mysql_query("SELECT `id`, `name`, `private` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['expo'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {
		$don = @mysql_fetch_assoc($sql);
		$expo = array();
		$expo['expo_nom'] = $don['name'];
		$expo['expo_priv'] = $don['private'];
		$expo['expo_id'] = $don['id'];
	}	
}



if (isset($_POST['expo_nom'])) {
	
	$expo['expo_nom'] = @$_POST['expo_nom'];
	$expo['expo_priv'] = @$_POST['expo_priv'];
	$expo['expo_id'] = @$_POST['expo_id'];
	
	$expo_fields_names = array();
	$expo_fields = array();
	
	$sql = @mysql_query("SELECT * FROM `fields_type`");
	if (@mysql_num_rows($sql) > 0) {
		while ($don = @mysql_fetch_assoc($sql)) {
			$current = $don['slug'];
			$expo_fields_names[$don['slug']] = @$_POST[$don['slug']];
			$expo_fields[$don['slug']] = @$_POST[$don['slug'].'-checkbox'];
			
			
			// On récupère l'id du tuple fields_type pour les prochaines requetes
			$request = "SELECT `id` FROM `fields_type` WHERE `slug`='".mysql_real_escape_string($don['slug'])."'";
			$sql2 = @mysql_query($request);
			if (@mysql_num_rows($sql2) > 0 ) {
				while (	$row = @mysql_fetch_assoc($sql2)) {
					$field_type_id = $row['id'];
						$checkbox = ($expo_fields[$don['slug']] == 1) ? TRUE : FALSE; // Booléen qui indique si la case est cochee
						// On vérifie que les informations ne sont pas deja presentes dans la table expo_fields
						$req = "SELECT `id` FROM `expo_fields` WHERE `expo_id`=".mysql_real_escape_string($expo['expo_id'])." AND `fields_type_id`=".mysql_real_escape_string($field_type_id)."";
						$sql3 = @mysql_query($req);
						if (@mysql_num_rows($sql3) > 0 ) {
							if ($checkbox == TRUE) {
								// information existe deja -> UPDATE
								$req = "UPDATE `expo_fields` SET `name_field` = '".mysql_real_escape_string($expo_fields_names[$don['slug']])."' WHERE `expo_id`=".mysql_real_escape_string($expo['expo_id'])." AND `fields_type_id`=".$field_type_id;
								$sql3 = @mysql_query($req);
							} else {
								// information existe deja -> DELETE
								while($expo_field = @mysql_fetch_assoc($sql3)) {
									$id_expo_field = $expo_field['id'];
									$req4 = "DELETE FROM `items_fields` WHERE `expo_fields_id` =  ".$id_expo_field;
									$sql4 = @mysql_query($req4);

								}
								$req = "DELETE FROM `expo_fields` WHERE `expo_id` = ".$expo['expo_id']." AND `fields_type_id`=".$field_type_id;
								$sql3 = @mysql_query($req);


							}
						} 
						else {
							// information n'existe pas -> on enregistre
							if ($checkbox == TRUE) {
								$req = "INSERT INTO `expo_fields`(`expo_id`, `fields_type_id`, `name_field`) VALUES('".mysql_real_escape_string($expo['expo_id'])."', '".mysql_real_escape_string($field_type_id)."', '".mysql_real_escape_string($expo_fields_names[$don['slug']])."')";
								$sql4 = @mysql_query($req);
							}
							
							// if ($sql4) {
							// 	echo '<div class="div_error">Impossible d\'ajouter les champs à l\'exposition. Celle-ci a cependant été ajoutée, il est donc inutile de l\'ajouter à nouveau. Merci de contacter un administrateur.</div>';
							// } else {
							// 	echo '<div class="div_error">Impossible d\'ajouter les champs à l\'exposition. Celle-ci a cependant été ajoutée, il est donc inutile de l\'ajouter à nouveau. Merci de contacter un administrateur.</div>';			
							// }	  				
						}
				}
			}


		}
		// print_r($expo_fields['date_crea']);
	}
	
	//print_r($expo_fields);
			
			
	
	// compteur d'erreur avec message
	$error_msg = array();
	
	if (empty($expo['expo_nom'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
	
	if (count($error_msg) < 1) {
		// modification d'une exposition
		if (isset($_POST['expo_id']) && !empty($_POST['expo_id'])) {
		
			// si pas su
			if ($_SESSION['is_admin'] < 2) {
				// vérification que l'utilisateur a bien les droits sur l'exposition sinon, redirection vers la liste des expositions
				$sql = @mysql_query("SELECT e.`id` FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' AND e.`id`='".mysql_real_escape_string($_GET['expo'])."'");
				if (@mysql_num_rows($sql) < 1) {
					header("Location:".$config['site_http']."/expo.php?act=lexpos");
				}
			}
				
			// test que l'exposition existe
			$sql = @mysql_query("SELECT `id` FROM `expo` WHERE `id`='".mysql_real_escape_string($expo['expo_id'])."'");
			if (@mysql_num_rows($sql) > 0) {
				$sql = @mysql_query("UPDATE `expo` SET `name`='".mysql_real_escape_string($expo['expo_nom'])."', `private`='".mysql_real_escape_string($expo['expo_priv'])."' WHERE `id`='".mysql_real_escape_string($expo['expo_id'])."'");

				// si un fichier a été envoyé
				if (is_uploaded_file($_FILES['expo_file']['tmp_name']) && !empty($_FILES['expo_file']['tmp_name'])) {
					// traitement pour l'upload du fichier
					if ($_FILES['expo_file']['error']) {    
						switch ($_FILES['expo_file']['error']) {    
							case 1: // UPLOAD_ERR_INI_SIZE
								echo '<div class="div_error">Le poids du fichier est trop importante. Réduisez le poids de votre fichier ou contacter un administrateur pour qu\'il puisse résoudre votre problème. ERR INI SIZE</div>';
								break;
							case 2: // UPLOAD_ERR_FORM_SIZE
								echo '<div class="div_error">Le poids du fichier est trop importante. Réduisez le poids de votre fichier ou contacter un administrateur pour qu\'il puisse résoudre votre problème.</div>';
							   break;
							case 3: // UPLOAD_ERR_PARTIAL
								echo '<div class="div_error">Une erreur est survenue lors de l\'envoi du fichier. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';
								break;
							case 4: // UPLOAD_ERR_NO_FILE
								echo '<div class="div_error">Le fichier que vous avez envoyé est défectueux. Veuillez en changer et rententez l\'opération.</div>';
								break;
						}
					} else if ((isset($_FILES['expo_file']['name']) && ($_FILES['expo_file']['error'] == UPLOAD_ERR_OK))) {
						if (!is_dir('uploads/expos/'.$expo['expo_id'].'/')) {
							mkdir('uploads/expos/'.$expo['expo_id'].'/');
						}
						if (!is_dir('uploads/expos/'.$expo['expo_id'].'/raw/')) {
							mkdir('uploads/expos/'.$expo['expo_id'].'/raw/');
						}
						if (!is_dir('uploads/expos/'.$expo['expo_id'].'/compressed/')) {
							mkdir('uploads/expos/'.$expo['expo_id'].'/compressed/');
						}
						$file_name = md5(microtime().$_FILES['expo_file']['name']).substr($_FILES['expo_file']['name'], strrpos($_FILES['expo_file']['name'], '.'));
						move_uploaded_file($_FILES['expo_file']['tmp_name'], 'uploads/expos/'.$expo['expo_id'].'/raw/'.$file_name);
					}
				}
				
				if ($sql) {
					echo '<div class="div_valid">L\'exposition a bien été mise à jour.</div>';
				} else {
					echo '<div class="div_error">Impossible de mettre à jour l\'opération. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';			
				}
				
			} else {
				echo '<div class="div_error">L\'exposition n\'existe pas et ne peut donc pas être modifiée.</div>';
				$expo = array();
			}
		
		//	nouvelle exposition
		} else {
			// $sql = @mysql_query("INSERT INTO `expo`(`id`, `name`, `private`, `last_publication`) VALUES(NULL, '".mysql_real_escape_string($expo['expo_nom'])."', '".mysql_real_escape_string($expo['expo_priv'])."')");
			$sql = @mysql_query("INSERT INTO `expo`(`id`, `name`, `private`) VALUES(NULL, '".mysql_real_escape_string($expo['expo_nom'])."', '".mysql_real_escape_string($expo['expo_priv'])."')");
			echo mysql_real_escape_string($expo['expo_nom']);
			echo mysql_real_escape_string($expo['expo_priv']);
			echo 'sql : '.$sql;
			if ($sql) {
			
				$expo['expo_id'] = @mysql_insert_id();
				// si l'utilisateur n'est pas un super-admin, on lui rajoute les droits sur l'exposition
				if ($_SESSION['is_admin'] < 2) {
					$sql = @mysql_query("INSERT INTO `expo_admin`(`expo_id`, `users_id`) VALUES('".mysql_real_escape_string($expo['expo_id'])."', '".mysql_real_escape_string($_SESSION['id'])."')");
					if ($sql) {
					
					} else {
						echo '<div class="div_error">Impossible d\'ajouter les droits à l\'exposition. Celle-ci a cependant été ajoutée, il est donc inutile de l\'ajouter à nouveau. Merci de contacter un administrateur.</div>';			
					}
				}
				
				// si un fichier a été envoyé
				if (@is_uploaded_file($_FILES['expo_file']['tmp_name'])) {
					// traitement pour l'upload du fichier
					if ($_FILES['expo_file']['error']) {    
						switch ($_FILES['expo_file']['error']) {    
							case 1: // UPLOAD_ERR_INI_SIZE
							case 2: // UPLOAD_ERR_FORM_SIZE
								echo '<div class="div_error">Le poids du fichier est trop importante. Réduisez le poids de votre fichier ou contacter un administrateur pour qu\'il puisse résoudre votre problème.</div>';
							   break;
							case 3: // UPLOAD_ERR_PARTIAL
								echo '<div class="div_error">Une erreur est survenue lors de l\'envoi du fichier. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';
								break;
							case 4: // UPLOAD_ERR_NO_FILE
								echo '<div class="div_error">Le fichier que vous avez envoyé est défectueux. Veuillez en changer et rententez l\'opération.</div>';
								break;
						}
					} else if ((isset($_FILES['expo_file']['name']) && ($_FILES['expo_file']['error'] == UPLOAD_ERR_OK))) {
						mkdir('uploads/expos/'.$expo['expo_id'].'/');
						mkdir('uploads/expos/'.$expo['expo_id'].'/raw/');
						mkdir('uploads/expos/'.$expo['expo_id'].'/compressed/');
						mkdir('uploads/expos/'.$expo['expo_id'].'/compressed/dds/');
						move_uploaded_file($_FILES['expo_file']['tmp_name'], 'uploads/expos/'.$expo['expo_id'].'/raw/'.md5(microtime().$_FILES['expo_file']['name']).substr($_FILES['expo_file']['name'], strrpos($_FILES['expo_file']['name'], '.')));
					}
				}
				// redirection vers la page d'édition de l'expo avec message d'ajout OK
				header("Location:".$config['site_http']."/expo.php?act=aexpos&expo=".$expo['expo_id']."&ok");
				
			} else {
				echo '<div class="div_error">Impossible d\'ajouter l\'exposition. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
			}
		}
	} else {
		echo '<div class="div_error"><ul class="block">';
		foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
		echo '</ul></div>';
	}
}


?>
<form method="post" id="items" enctype="multipart/form-data" action="<?php echo $config['site_http'].'/expo.php?act=aexpos'.((isset($expo['expo_id']) && !empty($expo['expo_id']))?'&amp;expo='.$expo['expo_id']:''); ?>">
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader"><?php if (isset($expo['expo_id']) && !empty($expo['expo_id'])) { echo 'Modification'; } else { echo 'Ajout'; } ?> d'une exposition</th></tr></thead>
	<tbody>
		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input type="text" name="expo_nom" maxlength="100" value="<?php echo @$expo['expo_nom']; ?>" /></td></tr>
		<tr><td class="tablerow1">Clef privée (si vide, exposition publique)</td><td class="tablerow2"><input type="text" maxlength="10" name="expo_priv" value="<?php echo @$expo['expo_priv']; ?>" /></td></tr>
		<tr><td class="tablerow1">Données</td><td class="tablerow2">
		<?php
			// liste des fichiers dans le dossier de l'exposition si édition
			if (isset($_GET['expo']) && !empty($_GET['expo'])) {
				if (@is_dir('uploads/expos/'.@$expo['expo_id'].'/raw/')) {
					if ($dir = @opendir('uploads/expos/'.$expo['expo_id'].'/raw/')) {
						while (($file = @readdir($dir)) !== false) {
							if ($file !== '.' && $file !== '..' && is_file('uploads/expos/'.$expo['expo_id'].'/raw/'.$file)) {
								echo '<a href="'.$config['site_http'].'/uploads/expos/'.@$expo['expo_id'].'/raw/'.$file.'"'.(($file==$file_name)?' class="new_item"':'').'>'.$file.'</a> | <a href="'.$config['site_http'].'/expo.php?act=aexpos&amp;expo='.@$expo['expo_id'].'&amp;deli='.urlencode($file).'">Supprimer</a><br />';
							}
						}
						@closedir($dir);
					}
				}
			}
			echo '<br />';
		?>
		<input type="file" name="expo_file" /> (taille maximale : <?php echo ini_get('post_max_size'); ?>)
		</td></tr>
		<tr>
			<td class="tablerow1">
				Champs de description des items 
			</td>
			<td class="tablerow2">
				<table class="tableborder">
					<tbody>
				<?php
					$sql = @mysql_query("SELECT * FROM `fields_type`");
					if (@mysql_num_rows($sql) >0) {
						while ($don = @mysql_fetch_assoc($sql)) {
							echo '<tr><td class="tablerow1"><input type="checkbox" name="'.$don['slug'].'-checkbox" value="1"';
									// if ($expo_fields[$don['slug']] == 1) {  echo 'checked="checked"';}
									$currentField = expo_has_field_type($expo['expo_id'], $don['id']);
									if ($currentField != NULL ){ 
										echo 'checked="checked"';
										// print_r($currentField);
									}
									echo '/>';
							echo $don['name'].'</td>';
							$currentFieldName = ($currentField != NULL) ? $currentField['name_field'] : $don['name'];
							echo '<td class="tablerow2"><input type="text" name="'.$don['slug'].'" maxlength="50" value="'.$currentFieldName.'" /></td></tr>';
							//echo '<label><input type="checkbox" name="'.$don['slug'].'" value="1" />';
							//echo $don['name'].'</label></br>';
							// echo $don['slug'];
							// $sqltemp = expo_has_field_type($expo['expo_id'], $don['id']);
							// $req = "SELECT * FROM `expo_fields` WHERE `expo_id`=".$expo['expo_id']." AND `fields_type_id`=".$don['id']."";
							// 
							// // echo 'requete : '.$req;
							// $sqltemp = mysql_query($req);
							// // echo 'requete : ';
							// if (@mysql_num_rows($sqltemp) > 0) {
							// 	echo 'checked';
							// 	while ($don2 = @mysql_fetch_assoc($sqltemp)){
							// 		// print_r($don2);
							// 	}
							// }
						}
					}
					
					/* <input type="checkbox" name="item_private" value="1"<?php echo (@$item['item_private']?' checked="checked"':''); ?> />*/
				?>
					</tdody>
				</table>
			</td>
		</tr>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($expo['expo_id']) && !empty($expo['expo_id'])) { echo '<input type="hidden" name="expo_id" value="'.$expo['expo_id'].'" />'; } ?>
			<input type="submit" value="<?php if (isset($expo['expo_id']) && !empty($expo['expo_id'])) { echo 'Modifier'; } else { echo 'Ajouter'; } ?>" />
		</td></tr>
	</tbody></table>
</form>
