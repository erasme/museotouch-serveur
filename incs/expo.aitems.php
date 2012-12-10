<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }

// test si l'utilisateur a les accès à l'exposition
aaccess();

include('incs/SimpleImage.php');

$file_name = '';
$iditem = '';
	
	//This function reads the extension of the file. It is used to determine if the
	// file  is an image by checking the extension.
	 function getExtension($str) {
	         $i = strrpos($str,".");
	         if (!$i) { return ""; }
	         $l = strlen($str) - $i;
	         $ext = substr($str,$i+1,$l);
	         return $ext;
	 }

	// Retourne un tuple de la table fields type en fonction d'un id : cela permet de récupérer les informations sur le type de champ à générer
	function get_type_field_by_id($id_type_field){
		$sql = @mysql_query("SELECT * FROM `fields_type` WHERE `id`=".$id_type_field);
		// echo "SELECT * FROM `field_type` WHERE `id`=".$id_type_field;
		if(@mysql_num_rows($sql) > 0){
			return @mysql_fetch_assoc($sql);
		}
	}
	
	//répuration des champs
	$sql = @mysql_query("SELECT * FROM `expo_fields` WHERE `expo_id`=".mysql_real_escape_string($_GET['expo']));
	if (@mysql_num_rows($sql) > 0) {
		$fields = array();
		while($don = @mysql_fetch_assoc($sql)) {
			// print '<pre>';
			// print_r( $don);
			// print '';
			$don['options'] = get_type_field_by_id($don['fields_type_id']);
			array_push($fields, $don);
			// print_r($fields);
			// echo count($fields);
		}
	}
	// print_r($fields[0]);

$update = '0';
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
	$update = '1';
	// message d'ajout d'objet (obligation d'affichage dûe à la redirection vers la page d'édition de l'objet directement après son ajout dans la BDD)
	if (isset($_GET['ok'])) {
		echo '<div class="div_valid">L\'objet a correctement été ajoutée.</div>';
	}
	
    $iditem = @mysql_real_escape_string($_GET['edit']);
    $sql = @mysql_query("select * from item_fields where items_id='$iditem';");

	// récupération des infos
	if ($sql) {
        $champs = array();
        while ($don = @mysql_fetch_assoc($sql)) {
            //print $don['expo_fieds_id']." --> ".$don['content']."<br .>";
            $champs[$don['expo_fields_id']] = $don['content'];
        }
	/*$sql = @mysql_query("SELECT `id`, `expo_id`, `nom`, `date_acqui`, `date_crea`, `datation`, `orig_geo`, `orig_geo_prec`, `taille`, `cartel`, `freefield`, `private`, `fichier` FROM `items` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."' AND `expo_id`='".mysql_real_escape_string($_GET['expo'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {
		$don = @mysql_fetch_assoc($sql);
		$item = array();
		$item['item_nom'] = $don['nom'];
		$item['item_date_acqui'] = $don['date_acqui'];
		$item['item_data_crea'] = $don['date_crea'];
		$item['item_date'] = $don['datation'];
		$item['item_conti'] = $don['orig_geo'];
		$item['item_count'] = $don['orig_geo_prec'];
		$item['item_taille'] = $don['taille'];
		$item['item_cartel'] = $don['cartel'];
		$item['item_id'] = $don['id'];
		$item['item_id_expo'] = $don['expo_id'];
		$item['item_freefield'] = $don['freefield'];
		$item['item_private'] = $don['private'];
		$item['item_file'] = $don['fichier'];*/
	} else {
		header("Location:".$config['site_http'].'/expo.php?act=litems&expo='.$_GET['expo']);
	}

	// suppression d'une image
	if (isset($_GET['deli']) && !empty($_GET['deli'])) {
		@unlink('uploads/objets/'.$iditem.'/raw/'.urldecode($_GET['deli']));
		$basedir = opendir('uploads/objets/'.$iditem.'/compressed/');
		while($dir = @readdir($basedir)) {
			if(is_dir('uploads/objets/'.$iditem.'/compressed/'.$dir)&& $dir != '.' && $dir != '..') {
				@unlink('uploads/objets/'.$iditem.'/compressed/'.$dir.'/'.urldecode($_GET['deli']));
			}
		}
		closedir($basedir);
	}
	
	
	// si un fichier a été envoyé
	if (@is_uploaded_file($_FILES['item_fichiersup']['tmp_name']) && !empty($_FILES['item_fichiersup']['tmp_name'])) {
		// traitement pour l'upload du fichier
		if ($_FILES['item_fichiersup']['error']) {    
			switch ($_FILES['item_fichiersup']['error']) {    
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
		} else if ((isset($_FILES['item_fichiersup']['name']) && ($_FILES['item_fichiersup']['error'] == UPLOAD_ERR_OK))) {
			$file_name = md5(microtime().$_FILES['item_fichiersup']['name']).substr($_FILES['item_fichiersup']['name'], strrpos($_FILES['item_fichiersup']['name'], '.'));
			move_uploaded_file($_FILES['item_fichiersup']['tmp_name'], 'uploads/objets/'.$iditem.'/raw/'.$file_name);
		}
	} 
}

/*
$sql = @mysql_query("select * from users;");
while ($don = @mysql_fetch_assoc($sql)) { // pas besoin de while pour une seule ligne
    foreach ($don as $clef => $valeur) {
        echo $clef." ==> ".$valeur."<br />";
    }
}
*/

$idexpo =  @$_GET['expo'];
$champs_voulus_noms = array();
$all_fields = array();
 
// $sql0 = @mysql_query("SELECT id, fields_type_id FROM expo_fields INNER JOIN fields_type ON expo_fields.fields_type_id = fields_type.id WHERE expo_fields.expo_id = $idexpo AND fields_type.widget_filter = 1;");
$sql0 = @mysql_query("SELECT expo_fields.id, expo_fields.fields_type_id, expo_fields.name_field FROM expo_fields INNER JOIN fields_type ON expo_fields.fields_type_id = fields_type.id WHERE expo_fields.expo_id = $idexpo AND fields_type.widget_filter = 1;");

while ($don = @mysql_fetch_assoc($sql0)) {
	$champs_voulus_noms[] = $don['name_field'];
	$champs_voulus[] = $don['id'];
}

$sql = @mysql_query("select id,fields_type_id from expo_fields where expo_id=$idexpo;");
while ($don = @mysql_fetch_assoc($sql)) {
	$all_fields[] = $don['id'];
}

// $sql = @mysql_query("select id,fields_type_id from expo_fields where expo_id=$idexpo;");
// $champs_voulus = array();
// $champs_voulus_defined = true;
// while ($don = @mysql_fetch_assoc($sql)) {
//     //print "ftid = ".$don['fields_type_id']."<br .>";
//     // $id = $don['id'];
//     // $sql2 = @mysql_query("SELECT widget_filter FROM fields_type WHERE id = $id;");
//     // while ($don2 = @mysql_fetch_assoc($sql2)) {
//     // 	if ($don['widget_filter'] == 1 ) {
//     // 		$champs_voulus[] = $don['id'];
//     // 	}
//     // }
//     $champs_voulus[] = $don['id'];
// }
// print_r($_POST);
// print_r($champs_voulus);

$champs_voulus_defined = true;
foreach($champs_voulus as $ch) {
    if (! isset($_POST[$ch])) {
        $champs_voulus_defined = false;
        break;
    }
}

if ($champs_voulus_defined) {

    /*
	$item['item_nom'] = @$_POST['item_nom'];
	$item['item_date_acqui'] = @$_POST['item_date_acqui'];
	$item['item_data_crea'] = @$_POST['item_data_crea'];
	$item['item_date'] = @$_POST['item_date'];
	$item['item_conti'] = @$_POST['item_conti'];
	$item['item_count'] = @$_POST['item_count'];
	$item['item_taille'] = @$_POST['item_taille'];
	$item['item_cartel'] = @$_POST['item_cartel'];
	$item['item_private'] = @$_POST['item_private'];
	$item['item_id'] = @$_POST['item_id'];
	$item['item_id_expo'] = @$_POST['item_expo'];
	$item['item_freefield'] = @$_POST['item_freefield'];
	*/
	$item = $_POST;
	// compteur d'erreur avec message
	$error_msg = array();
	
	//if (empty($item['item_nom'])) { $error_msg[] = 'le nom doit obligatoirement être renseigné'; }
	//if (empty($_FILES['item_fichier']['tmp_name']) && empty($item['item_file'])) { $error_msg[] = 'Un "fichier associé" doit obligatoirement être présent.'; }
	
	if (count($error_msg) < 1) {
		// édition	
		if (isset($_POST['update']) && $_POST['update']==1 && isset($_GET['edit']) && !empty($_GET['edit'])) {
		    $iditem = @mysql_real_escape_string($_GET['edit']);
		    $item['item_id'] = $iditem;
		    
			$sql = @mysql_query("SELECT `id` FROM `items` WHERE `id`='$iditem';");
			// l'objet existe
			if (@mysql_num_rows($sql) > 0) {
			
				// maj des mots-clefs
				$sql = @mysql_query("DELETE FROM `items_rkeywords` WHERE `items_id`='".mysql_real_escape_string($item['item_id'])."'");
				foreach($_POST['items_keywords'] as $key => $value) {
					@mysql_query("INSERT INTO `items_rkeywords`(`items_id`, `expokeywords_id`) VALUES('".mysql_real_escape_string($item['item_id'])."', '".mysql_real_escape_string($value)."')");
				}
				
				
				// si un fichier a été envoyé
				if (is_uploaded_file($_FILES['item_fichier']['tmp_name']) && !empty($_FILES['item_fichier']['tmp_name'])) {
					// traitement pour l'upload du fichier
					if ($_FILES['item_fichier']['error']) {    
						switch ($_FILES['item_fichier']['error']) {    
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
					} else if ((isset($_FILES['item_fichier']['name']) && ($_FILES['item_fichier']['error'] == UPLOAD_ERR_OK))) {
						// Création de l'arborescence de l'objet
						if(is_dir('uploads/objets/'.$item['item_id']) == False){
							mkdir('uploads/objets/'.$item['item_id']);
						}
						if(is_dir('uploads/objets/'.$item['item_id'].'/raw/') == False){
							mkdir('uploads/objets/'.$item['item_id'].'/raw/');
						}
						if(is_dir('uploads/objets/'.$item['item_id'].'/compressed/') == False){
							mkdir('uploads/objets/'.$item['item_id'].'/compressed/');
						}
						if(is_dir('uploads/objets/'.$item['item_id'].'/compressed/dds/') == False){
							mkdir('uploads/objets/'.$item['item_id'].'/compressed/dds/');
						}

						$item['item_file'] = substr($_FILES['item_fichier']['name'], strrpos($_FILES['item_fichier']['name'], '.') + 1);
						$filename = $_FILES['item_fichier']['tmp_name'];
						if ($item['item_file'] = 'jpeg') {
							$item['item_file'] = 'jpg';
						}
						$destination = 'uploads/objets/'.$item['item_id'].'/raw/'.$item['item_id'].'.'.$item['item_file'];
						$extension = getExtension($_FILES['item_fichier']['name']);
						$size=filesize($_FILES['item_fichier']['tmp_name']);
						$error = 0;

						if ($extension != 'jpg' && $extension != 'jpeg') {
							echo '<div class="div_error"> Mauvais format d\'image ! Uniquement jpg ou jpeg. </div>';
							$error = 1;
						} 
						if ($size > 5000*1024) { // Limite de 5 mo par image
							echo '<div class="div_error"> Le fichier ne doit pas faire plus de 5 mo ! </div>';
							$error = 1;
						}
						if ($error == 0){
							$image = new SimpleImage();
							$image->load($filename);
							$width = $image->getWidth();
							$height= $image->getHeight();
							if($width > 100 && $height > 100) {
								$iwidth = round($width/100) * 100;
								$iheight = round($height/100) * 100;
							}
							$image->resize($iwidth, $iheight);
							$image->save($destination);
						}
						
						// $result = move_uploaded_file($filename, $destination);
						// move_uploaded_file($_FILES['item_fichier']['tmp_name'], 'uploads/objets/'.$item['item_id'].'/raw/'.$item['item_id'].'.'.$item['item_file']);
						// $md5 = md5_file('uploads/objets/raw/'.$item['item_id'].'.'.$item['item_file']);

						// if($result == True){
						// 	// echo 'Vrai';
						// } else if($result == False) {
						// 	echo '<div class="div_error"> Echec de l\'envoi du fichier ! </div>';
						// }
						// $updateitem = ", `fichier`='".mysql_real_escape_string($item['item_file'])."', `fichier_md5`='".mysql_real_escape_string($md5)."'";
					}
				}

	            $item['item_id'] = $iditem;
	            foreach($all_fields as $ch) {
	                $efid = mysql_real_escape_string($ch);
	                $content = mysql_real_escape_string($item[$efid]);
                    // $sql = @mysql_query("UPDATE item_fields SET content='$content' WHERE items_id='$iditem' and expo_fields_id='$efid';");
                    $req = "UPDATE item_fields SET content='$content' WHERE items_id='$iditem' and expo_fields_id='$efid';";
                    $sql = @mysql_query($req);
                    $champs[$efid] = $content;		        
	            }
			    /*
				$sql = @mysql_query("UPDATE `items` SET `nom`='".mysql_real_escape_string($item['item_nom'])."', `date_acqui`='".mysql_real_escape_string($item['item_date_acqui'])."', `date_crea`='".mysql_real_escape_string($item['item_data_crea'])."', `datation`='".mysql_real_escape_string($item['item_date'])."', `orig_geo`='".mysql_real_escape_string($item['item_conti'])."', `orig_geo_prec`='".mysql_real_escape_string($item['item_count'])."', `taille`='".mysql_real_escape_string($item['item_taille'])."', `cartel`='".mysql_real_escape_string($item['item_cartel'])."', `freefield`='".mysql_real_escape_string($item['item_freefield'])."', `private`='".mysql_real_escape_string($item['item_private'])."' $updateitem WHERE `id`='".mysql_real_escape_string($item['item_id'])."' AND `expo_id`='".mysql_real_escape_string($item['item_id_expo'])."'");
				*/
				
				if ($sql) {
					echo '<div class="div_valid">L\'objet a correctement été modifié.</div>';
				} else {
					echo '<div class="div_error">Impossible de modifier l\'objet. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
				}
				
			// l'objet n'existe pas
			} else {
				echo '<div class="div_error">L\'objet n\'existe pas et ne peut donc pas être modifié.</div>';
				$item = array();
			}
		// ajout
		} else if (isset($_POST['update']) && $_POST['update']==0) {		
		    $sql = @mysql_query("INSERT INTO items(expo_id) VALUES(".mysql_real_escape_string($idexpo).");");
		    $newid = @mysql_insert_id();
		    $item['item_id'] = $newid;
		    // foreach($champs_voulus as $ch) {
		    foreach($all_fields as $ch) {
		        $efid = mysql_real_escape_string($ch);
		        $content = mysql_real_escape_string($item[$efid]);
                @mysql_query("INSERT INTO item_fields(items_id, expo_fields_id, content) VALUES('$newid', '$efid', '$content');");		        
		    }
		    
		    /*
			$sql = @mysql_query("INSERT INTO `items`(`id`, `expo_id`, `nom`, `date_acqui`, `date_crea`, `datation`, `orig_geo`, `orig_geo_prec`, `taille`, `cartel`, `freefield`, `private`) VALUES(NULL, '".mysql_real_escape_string($item['item_id_expo'])."', '".mysql_real_escape_string($item['item_nom'])."', '".mysql_real_escape_string($item['item_date_acqui'])."', '".mysql_real_escape_string($item['item_data_crea'])."', '".mysql_real_escape_string($item['item_date'])."', '".mysql_real_escape_string($item['item_conti'])."', '".mysql_real_escape_string($item['item_count'])."', '".mysql_real_escape_string($item['item_taille'])."', '".mysql_real_escape_string($item['item_cartel'])."', '".mysql_real_escape_string($item['item_freefield'])."', '".mysql_real_escape_string($item['item_private'])."')") or die(mysql_error());
			$item['item_id'] = @mysql_insert_id();
			*/

			// Création de l'arborescence de l'objet
			mkdir('uploads/objets/'.$item['item_id']);
			mkdir('uploads/objets/'.$item['item_id'].'/raw/');
			mkdir('uploads/objets/'.$item['item_id'].'/compressed/');
			mkdir('uploads/objets/'.$item['item_id'].'/compressed/dds/');
						
			// si un fichier a été envoyé
			if (@is_uploaded_file($_FILES['item_fichier']['tmp_name']) && !empty($_FILES['item_fichier']['tmp_name'])) {
				// traitement pour l'upload du fichier
				if ($_FILES['item_fichier']['error']) {    
					switch ($_FILES['item_fichier']['error']) {    
						case 1: // UPLOAD_ERR_INI_SIZE
						case 2: // UPLOAD_ERR_FORM_SIZE
							echo '<div class="div_error">Le poids du fichier est trop importante. Réduisez le poids de votre fichier ou contacter un administrateur pour qu\'il puisse résoudre votre problème.</div>';
						   break;
						case 3: // UPLOAD_ERR_PARTIAL
							echo '<div class="div_error">Une erreur est survenue lors de l\'envoi du fichier. Veuillez retenter l\'opération. Si le problème persiste, veuillez contacter un administrateur.</div>';
							break;
						case 4: // UPLOAD_ERR_NO_FILE
							echo '<div class="div_error">Le fichier que vous avez envoyé est défectueux. Veuillez changer et rententer l\'opération.</div>';
							break;
					}
				} else if ((isset($_FILES['item_fichier']['name']) && ($_FILES['item_fichier']['error'] == UPLOAD_ERR_OK))) {
					$item['item_file'] = substr($_FILES['item_fichier']['name'], strrpos($_FILES['item_fichier']['name'], '.') + 1);

					move_uploaded_file($_FILES['item_fichier']['tmp_name'], 'uploads/objets/'.$item['item_id'].'/raw/'.$item['item_id'].'.'.$item['item_file']);
					$md5 = md5_file('uploads/objets/'.$item['item_id'].'/raw/'.$item['item_id'].'.'.$item['item_file']);
					@mysql_query("UPDATE `items` SET `fichier`='".mysql_real_escape_string($item['item_file'])."', `fichier_md5`='".mysql_real_escape_string($md5)."' WHERE `id`='".mysql_real_escape_string($item['item_id'])."'");
				}
			}
						
			if ($sql) {
				header("Location:".$config['site_http'].'/expo.php?act=aitems&expo='.$_GET['expo'].'&edit='.$item['item_id'].'&ok');
			} else {
				echo '<div class="div_error">Impossible d\'ajouter l\'objet. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
			}
		}
	} else {
		echo '<div class="div_error"><ul class="block">';
		foreach ($error_msg as $key => $value) { echo '<li>'.$value.'</li>'; }
		echo '</ul></div>';
	}
} else {
	if ($_POST) {
	    echo '<div class="div_error">Vous devez définir les champs suivants : </br>';
	    foreach ($champs_voulus_noms as $nom){
	    	echo $nom;
	    	echo '</br>';
	    }
	    echo '</div>';
	}
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $("#item_conti").autocomplete(
        "incs/ajax.item.geo.php",
        {
            delay:10,
            matchSubset:1,
            matchContains:1,
            cacheLength:10,
            autoFill:true
        }
    );
    $("#item_count").autocomplete(
        "incs/ajax.item.geo_prec.php",
        {
            delay:10,
            matchSubset:1,
            matchContains:1,
            cacheLength:10,
            autoFill:true
        }
    );
});
</script>

<form method="post" id="items" enctype="multipart/form-data" action="<?php echo $config['site_http'].'/expo.php?act=aitems&amp;expo='.$_GET['expo'].((isset($iditem) && !empty($iditem))?'&amp;edit='.$iditem : ''); ?>">
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
	<table class="tableborder">
	<thead><tr><th colspan="2" class="tablesubheader"><?php if (isset($iditem) && !empty($iditem)) { echo 'Modification'; } else { echo 'Ajout'; } ?> d'un objet</th></tr></thead>
	<tbody>
<!-- 		<tr><td class="tablerow1">Nom</td><td class="tablerow2"><input type="text" maxlength="100" name="item_nom" value="<?php echo @$item['item_nom']; ?>" /></td></tr>
		<tr><td class="tablerow1">Date d'acquisition</td><td class="tablerow2"><input type="text" maxlength="4" name="item_date_acqui" value="<?php echo @$item['item_date_acqui']; ?>" /></td></tr>
		<tr><td class="tablerow1">* Date de création</td><td class="tablerow2"><input type="text" maxlength="4" name="item_data_crea" value="<?php echo @$item['item_data_crea']; ?>" /></td></tr>
		<tr><td class="tablerow1">Datation</td><td class="tablerow2"><input type="text" maxlength="50" name="item_date" value="<?php echo @$item['item_date']; ?>" /></td></tr>
		<tr><td class="tablerow1">* Origine</td><td class="tablerow2"><input type="text" maxlength="50" name="item_conti" id="item_conti" value="<?php echo @$item['item_conti']; ?>" /></td></tr>
		<tr><td class="tablerow1">Origine précise</td><td class="tablerow2"><input type="text" maxlength="50" name="item_count" id="item_count" value="<?php echo @$item['item_count']; ?>" /></td></tr>
		<tr><td class="tablerow1">* Taille</td><td class="tablerow2"><input type="text" maxlength="11" name="item_taille" value="<?php echo @$item['item_taille']; ?>" /></td></tr>
		<tr><td class="tablerow1">Privé</td><td class="tablerow2"><input type="checkbox" name="item_private" value="1"<?php echo (@$item['item_private']?' checked="checked"':''); ?> /></td></tr>
		<tr><td class="tablerow1">Cartel</td><td class="tablerow2"><textarea name="item_cartel"><?php echo @$item['item_cartel']; ?></textarea></td></tr>
		<tr><td class="tablerow1">Champ libre</td><td class="tablerow2"><textarea name="item_freefield"><?php echo @$item['item_freefield']; ?></textarea></td></tr>
-->
		<?php
			
			foreach($fields as $field) {
				echo '<tr><td class="tablerow1">';
				echo $field['name_field'];
				echo '</td><td class="tablerow2">';
				echo $field['options']['form_type'];
				echo ' maxlength="';
				echo $field['options']['max_length'];
				$value = '';
				if (isset($champs)) {
				    $value = $champs[$field['id']];
				}
				echo '" name="'.$field['id'].'" value="'.$value.'" data-slug="'.$field['options']['slug'].'" data-id="'.$field['options']['id'].'"';

				if(strpos($field['options']['form_type'], 'textarea') !== false) {
					echo '" name="'.$field['id'].'" data-slug="'.$field['options']['slug'].'" data-id="'.$field['options']['id'].'"';
					echo '>';
					echo stripslashes($value);
					echo '</textarea>';
				} else {
					echo '" name="'.$field['id'].'" value="'.$value.'" data-slug="'.$field['options']['slug'].'" data-id="'.$field['options']['id'].'"';
					echo '/>';
				}
				echo '</td></tr>';
									
			}

		?>

		<tr><td class="tablerow1">Image principale</td><td class="tablerow2"> 

					<?php
			// liste des fichiers dans le dossier de l'exposition si édition
			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
			
			    // recuperation main_file
			    $main_file = '';
	            if (@is_dir('uploads/objets/'.$iditem.'/raw/')) {
		            if ($dir = @opendir('uploads/objets/'.$iditem.'/raw/')) {
			            while (($file = @readdir($dir)) !== false) {
				            if (strpos($file, $iditem.'.') === 0 && is_file('uploads/objets/'.$iditem.'/raw/'.$file)) {
				                $main_file = $file;
				                break;
				            }
			            }
			            @closedir($dir);
		            }
	            }
			    
				if (!empty($main_file)) {
					echo '<a href="'.$config['site_http'].'/uploads/objets/'.$iditem.'/raw/'.$main_file.'" target="_blank"><img src="'.$config['site_http'].'/uploads/objets/'.$iditem.'/raw/'.$main_file.'" class="mainPict"/></a>';
					/*echo '<a href="'.$config['site_http'].'/uploads/objets/'.$iditem.'/raw/'.$main_file.'">Fichier associé</a><br />';*/
				} else {
					echo 'Aucun fichier associé pour le moment.';
				}
			}
			?>
			<span class="addPict">
				<input type="file" name="item_fichier" /> 
				- Uniquement jpg ou jpeg. <br/> 
				- Taille maximale : 5 mo.
				<br />
				<br />Le fait d'envoyer un fichier écrase le précédent.<br /> 
			</span>
		</td></tr>
		<?php if (isset($_GET['expo']) && !empty($_GET['expo']) && isset($iditem) && !empty($iditem)) { ?>
		<tr><td class="tablerow1">* Mots-clefs</td><td class="tablerow2">
		<?php		
		// récupération de la liste des id des mots-clefs liés à l'objet
		$lst_keywords = array();
		$sql = @mysql_query("SELECT `expokeywords_id` FROM `items_rkeywords` WHERE `items_id`='".mysql_real_escape_string($iditem)."'");
		while ($don = @mysql_fetch_assoc($sql)) {
			$lst_keywords[] = $don['expokeywords_id'];
		}
		
		// affichage des différents groupes de mots-clefs		
		$sql = @mysql_query("SELECT `name`, `id` FROM `expo_gkeywords` WHERE `expo_id`='".mysql_real_escape_string($_GET['expo'])."' ORDER BY `name`");
		if (@mysql_num_rows($sql)>0) {
			while ($don = @mysql_fetch_assoc($sql)) {
				echo '<h3>'.$don['name'].'</h3>';
				// affichage des mots-clefs de chaque groupe
				$rqt = @mysql_query("SELECT `id`, `name` FROM `expo_keywords` WHERE `expogkeywords_id`='".mysql_real_escape_string($don['id'])."' ORDER BY `name`");
				while ($data = @mysql_fetch_assoc($rqt)) {
					echo '<label><input type="checkbox" value="'.$data['id'].'" name="items_keywords[]"'.((in_array($data['id'], $lst_keywords))?' checked="checked"':'').' /> '.$data['name'].'</label>';
				}
			}
		} else {
			echo 'Aucun groupe de mots-clefs n\'est associé à cette exposition.';
		}
		?>	
		</td></tr>
		<?php } ?>
		<tr><td class="tablefooter center" colspan="2">
			<?php if (isset($iditem) && !empty($iditem)) { echo '<input type="hidden" name="item_id" value="'.$iditem.'" />'; } ?>
			<input type="hidden" name="item_expo" value="<?php echo $_GET['expo']; ?>" />
			<input type="submit" value="<?php if (isset($iditem) && !empty($iditem)) { echo 'Modifier'; } else { echo 'Ajouter'; } ?>" />
		</td></tr>
	</tbody></table>
</form>


<?php

// liste des fichiers dans le dossier de l'objet si édition
if (isset($_GET['expo']) && !empty($_GET['expo']) && isset($iditem) && !empty($iditem)) {

?>

<form method="post" id="itemsup" enctype="multipart/form-data" action="<?php echo $config['site_http'].'/expo.php?act=aitems&amp;expo='.$_GET['expo'].'&amp;edit='.$iditem; ?>">
<input type="file" name="item_fichiersup" /> (taille maximale : <?php echo ini_get('post_max_size'); ?>) <input type="submit" value="Ajouter un fichier" />

</form>

<?php

	if (@is_dir('uploads/objets/'.@$iditem.'/raw/')) {
		if ($dir = @opendir('uploads/objets/'.$iditem.'/raw/')) {
			while (($file = @readdir($dir)) !== false) {
				if ($file !== '.' && $file !== '..' && strpos($file, $iditem.'.') !== 0 && is_file('uploads/objets/'.$iditem.'/raw/'.$file)) {
					echo '<a href="'.$config['site_http'].'/uploads/objets/'.@$iditem.'/raw/'.$file.'"'.(($file==$file_name)?' class="new_item"':'').'>'.$file.'</a> | <a href="'.$config['site_http'].'/expo.php?act=aitems&amp;expo='.$_GET['expo'].'&amp;edit='.@$iditem.'&amp;deli='.urlencode($file).'">Supprimer</a><br />';
				}
			}
			@closedir($dir);
		}
		echo '<br />';
	}
}
