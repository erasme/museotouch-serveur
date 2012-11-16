<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }


if (isset($_GET['del']) && !empty($_GET['del'])) {

	// si pas su
	if ($_SESSION['is_admin'] < 2) {
		// vérification que l'utilisateur a bien les droits sur l'exposition sinon, redirection vers la liste des expositions
		$sql = @mysql_query("SELECT e.`id` FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' AND e.`id`='".mysql_real_escape_string($_GET['del'])."'");
		if (@mysql_num_rows($sql) < 1) {
			header("Location:".$config['site_http']."/expo.php?act=lexpos");
		}
	}

	// suppression des fichiers des données associées à l'expo
	// suppression des fichiers des objets
	$sql = @mysql_query("SELECT `id` FROM `items` WHERE `expo_id`='".mysql_real_escape_string($_GET['del'])."'");
	if (@mysql_num_rows($sql)>0) {
		while ($don = @mysql_fetch_assoc($sql)) {
			if (@is_dir('uploads/objets/'.$don['id'].'/compressed/')) {
				$base = @opendir('uploads/objets/'.$don['id'].'/compressed/');
				while (false !== ($dir = readdir($base))) {
					if (is_dir('uploads/objets/'.$don['id'].'/compressed/'.$dir) && ($dir !== ".") && ($dir !== "..")) {
						$basedir = @opendir('uploads/objets/'.$don['id'].'/compressed/'.$dir);	
						while (false !== ($file = readdir($basedir))) {
							if (($file !== ".") && ($file !== "..")) {
								@unlink('uploads/objets/'.$don['id'].'/compressed/'.$dir.'/'.$file);
							}
						}
						@closedir($basedir);
						// puis le dossier
						@rmdir('uploads/objets/'.$don['id'].'/compressed/'.$dir);
					}
				}
				@closedir($base);
				// puis le dossier
				@rmdir('uploads/objets/'.$don['id'].'/compressed/');
			}
			if (@is_dir('uploads/objets/'.$don['id'].'/raw/')) {
				$dir = @opendir('uploads/objets/'.$don['id'].'/raw/');
				while (false !== ($file = readdir($dir))) {
					if (($file !== ".") && ($file !== "..")) {
						@unlink('uploads/objets/'.$don['id'].'/raw/'.$file);
					}
				}
				// puis le dossier
				@rmdir('uploads/objets/'.$don['id'].'/raw/');
			}
			@rmdir('uploads/objets/'.$don['id'].'/');
		}
	}
	
	// suppression des fichiers des scenarios
	$sql = @mysql_query("SELECT `id` FROM `scenarios` WHERE `expo_id`='".mysql_real_escape_string($_GET['del'])."'");
	if (@mysql_num_rows($sql)>0) {
		while ($don = @mysql_fetch_assoc($sql)) {
			if (@is_dir('uploads/scenarios/'.$don['id'].'/')) {
				$dir = @opendir('uploads/scenarios/'.$don['id'].'/');
				while (false !== ($file = readdir($dir))) {
					if (($file !== ".") && ($file !== "..")) {
						@unlink('uploads/scenarios/'.$don['id'].'/'.$file);
					}
				}
				// puis le dossier
				@rmdir('uploads/scenarios/'.$don['id'].'/');
			}
		}
	}
	
	// suppression de l'expo et de toutes les données associées
	$sql = @mysql_query("DELETE ci, e, ea, eg, ek, i, ir, s, si FROM `expo` AS e LEFT JOIN `scenarios` AS s ON s.`expo_id`=e.`id` LEFT JOIN `scenarios_items` AS si ON si.`scenarios_id`=s.`id` LEFT JOIN `items` AS i ON i.`expo_id`=e.`id` LEFT JOIN `items_rkeywords` AS ir ON ir.`items_id`=i.`id` LEFT JOIN `cart_items` AS ci ON ci.`items_id`=i.`id` LEFT JOIN `expo_admin` AS ea ON ea.`expo_id`=e.`id` LEFT JOIN `expo_gkeywords` AS eg ON eg.`expo_id`=e.`id` LEFT JOIN `expo_keywords` AS ek ON ek.`expogkeywords_id`=eg.`id` WHERE e.`id`='".mysql_real_escape_string($_GET['del'])."'");
	
	if (@is_dir('uploads/expos/'.$_GET['del'].'/raw/')) {
		$dir = @opendir('uploads/expos/'.$_GET['del'].'/raw/');
		while (false !== ($file = readdir($dir))) {
			if (($file !== ".") && ($file !== "..")) {
				@unlink('uploads/expos/'.$_GET['del'].'/raw/'.$file);
			}
		}
		@closedir($dir);
		@rmdir('uploads/expos/'.$_GET['del'].'/raw/');
	}
	
	if (@is_dir('uploads/expos/'.$_GET['del'].'/compressed/')) {
		$dir = @opendir('uploads/expos/'.$_GET['del'].'/compressed/');
		while (false !== ($file = readdir($dir))) {
			if (($file !== ".") && ($file !== "..")) {
				@unlink('uploads/expos/'.$_GET['del'].'/compressed/'.$file);
			}
		}
		@closedir($dir);
		@rmdir('uploads/expos/'.$_GET['del'].'/compressed/');
	}
	@rmdir('uploads/expos/'.$_GET['del'].'/');
		
	if ($sql) {
		echo '<div class="div_valid">L\'exposition a correctement été supprimée.</div>';	
	} else {
		echo '<div class="div_error">Impossible de supprimer l\'exposition. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
	}
		
}

if (isset($_GET['update']) && !empty($_GET['update'])) {
	$sql = @mysql_query("UPDATE `expo` SET `last_publication`=CURRENT_TIMESTAMP WHERE `id`='".mysql_real_escape_string($_GET['update'])."'");
	
	if ($sql) {
		echo '<div class="div_valid">L\'exposition a été annoncée comme mise à jour.</div>';	
	} else {
		echo '<div class="div_error">Impossible de publier la mise à jour de l\'exposition. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
	}
}

?>
<div><form method="get" action="<?php echo $config['site_http']; ?>/expo.php">
<input type="hidden" name="act" value="lexpos" />
<input name="search" value="<?php echo @$_GET['search']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
</form></div><br />
	<table class="tableborder">
	<thead><tr><th class="tablesubheader" width="85%">Nom de l'exposition</th><th class="tablesubheader" width="5%">Mise à jour</th><th class="tablesubheader" width="5%">Modification</th>
	<!--	<th class="tablesubheader" width="5%">Suppression</th> -->
	</tr></thead>
	<tbody>
<?php

// recherche
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
	if ($_SESSION['is_admin'] < 2) {
		$search = "AND e.`name` LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
	} else {
		$search = "WHERE `name` LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
	}
}

// calcul de pagination
if ($_SESSION['is_admin'] < 2) {
	$sql = @mysql_fetch_assoc(mysql_query("SELECT count(e.`id`) as nbre FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' $search ORDER BY e.`name`"));
} else {
	$sql = @mysql_fetch_assoc(mysql_query("SELECT count(`id`) as nbre FROM `expo` $search ORDER BY `name`"));
}

$limit_page = 30;
if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
if (isset($_GET['page']) && !empty($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }
if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };


if ($_SESSION['is_admin'] < 2) {
	$sql = @mysql_query("SELECT e.`id` as id, e.`name` as name FROM `expo` e LEFT JOIN `expo_admin` ea  ON ea.`expo_id`=e.`id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' $search ORDER BY e.`name` LIMIT $from, $limit_page");
} else {
	$sql = @mysql_query("SELECT `id`, `name` FROM `expo` $search ORDER BY `name` LIMIT $from, $limit_page");
}

if (@mysql_num_rows($sql) >0) {
	while ($don = @mysql_fetch_assoc($sql)) {
		echo '<tr class="height-20">
			<td class="tablerow1">'.$don['name'].'</td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=lexpos&amp;update='.$don['id'].'">Publier</a></td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=aexpos&amp;expo='.$don['id'].'">Modifier</a></td>
		<!--
		 	<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=lexpos&amp;del='.$don['id'].(isset($_GET['page'])?'&amp;page='.$_GET['page']:'').'">Supprimer</a></td>
		-->
		</tr>';
	}
	
	// pagination
	// 1ere page
	echo '<tr><td class="tablesubheader" colspan="6" align="center" valign="middle">';
	if($page - $pageavant > 1) {
		echo '&nbsp;<a href="expo.php?act=lexpos&amp;page=1">1</a>&nbsp;...';
	}
	// pages intermédiaires
	for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
		if ($i == $page) {
			echo '&nbsp;<b>['.$i.']</b>&nbsp;';
		} else {
			echo '&nbsp;<a href="expo.php?act=lexpos&amp;page='.$i.'">'.$i.'</a>&nbsp;';
		}
	}
	// dernière page
	if($page + $pageapres < $nb_pages) {
		echo '...&nbsp;<a href="expo.php?act=lexpos&amp;page='.$nb_pages.'">'.$nb_pages.'</a>&nbsp;';
	}
	echo '</td></tr>';
	
	
} else {
	if (isset($_GET['search']) && !empty($_GET['search'])) {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément pour la recherche. <a href="'.$config['site_http'].'/expo.php?act=lexpos">Voir tous les résultats</a>.</td></tr>';
	} else {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément existant.</td></tr>';
	}
}


?>
	</tbody>
</table>
