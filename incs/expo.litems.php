<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }

// test si l'utilisateur a les accès à l'exposition
aaccess();


if (isset($_GET['del']) && !empty($_GET['del'])) {
	if ($_SESSION['is_admin'] < 2) {
		$sql = @mysql_query("SELECT i.`id` FROM `items` AS i LEFT JOIN `expo_admin` AS ea ON ea.`expo_id`=i.`expo_id` WHERE ea.`users_id`='".mysql_real_escape_string($_SESSION['id'])."' AND i.`id`='".mysql_real_escape_string($_GET['del'])."'");
	} else {
		$sql = @mysql_query("SELECT `id` FROM `items` WHERE `id`='".mysql_real_escape_string($_GET['del'])."'");	
	}
	if (@mysql_num_rows($sql)>0) {
		$sql = @mysql_query("DELETE i, ir, si, ci FROM `items` AS i LEFT JOIN `items_rkeywords` AS ir ON i.`id`=ir.`items_id` LEFt JOIN `scenarios_items` AS si ON si.`items_id`=i.`id` LEFT JOIN `cart_items` AS ci ON ci.`items_id`=i.`id` WHERE i.`id`='".mysql_real_escape_string($_GET['del'])."'");
		$escapeddel = mysql_real_escape_string($_GET['del']);
		@mysql_query("DELETE FROM item_fields WHERE items_id='$escapeddel';");

		if (@is_dir('uploads/objets/'.$_GET['del'].'/compressed/')) {
			$base = @opendir('uploads/objets/'.$_GET['del'].'/compressed/');
			while (false !== ($dir = readdir($base))) {
				if (is_dir('uploads/objets/'.$_GET['del'].'/compressed/'.$dir) && ($dir !== ".") && ($dir !== "..")) {
					$basedir = @opendir('uploads/objets/'.$_GET['del'].'/compressed/'.$dir);	
					while (false !== ($file = readdir($basedir))) {
						if (($file !== ".") && ($file !== "..")) {
							@unlink('uploads/objets/'.$_GET['del'].'/compressed/'.$dir.'/'.$file);
						}
					}
					@closedir($basedir);
					// puis le dossier
					@rmdir('uploads/objets/'.$_GET['del'].'/compressed/'.$dir);
				}
			}
			@closedir($base);
			// puis le dossier
			@rmdir('uploads/objets/'.$_GET['del'].'/compressed/');
		}
		if (@is_dir('uploads/objets/'.$_GET['del'].'/raw/')) {
			$dir = @opendir('uploads/objets/'.$_GET['del'].'/raw/');
			while (false !== ($file = readdir($dir))) {
				if (($file !== ".") && ($file !== "..")) {
					@unlink('uploads/objets/'.$_GET['del'].'/raw/'.$file);
				}
			}
			@closedir($dir);
			// puis le dossier
			@rmdir('uploads/objets/'.$_GET['del'].'/raw/');
		}
		@rmdir('uploads/objets/'.$_GET['del'].'/');

	} else {
		header("Location:".$config['site_http'].'/expo.php?act=litems&expo='.$_GET['expo']);
	}
	if ($sql) {
		echo '<div class="div_valid">L\'objet a correctement été supprimé.</div>';	
	} else {
		echo '<div class="div_error">Impossible de supprimer l\'objet. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
	}
	
}

?>
<div><form method="get" action="<?php echo $config['site_http']; ?>/expo.php">
<input type="hidden" name="act" value="litems" />
<input type="hidden" name="expo" value="<?php echo $_GET['expo']; ?>" />
<input name="search" value="<?php echo @$_GET['search']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
</form></div><br />
<table class="tableborder">
	<thead><tr><th class="tablesubheader" width="90%">Nom de l'objet</th><th class="tablesubheader" width="5%">Modification</th><th class="tablesubheader" width="5%">Suppression</th></tr></thead>
	<tbody>
<?php

// recherche
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
	$search = "AND item_fields.content LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
}

// calcul de pagination 
$sql = @mysql_fetch_array(@mysql_query("SELECT COUNT(`id`) AS nbre FROM `items` WHERE `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search"));
$limit_page = 30;
if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
if (isset($_GET['page']) && !empty($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }
if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };

//$sql = @mysql_query("SELECT `id` FROM `items` WHERE `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search ORDER BY `id` LIMIT $from, $limit_page");

$sql = @mysql_query(
	"SELECT items.id, item_fields.content FROM items 
	INNER JOIN item_fields ON items.id = item_fields.items_id
	INNER JOIN expo_fields ON expo_fields.id = item_fields.expo_fields_id
	INNER JOIN fields_type ON expo_fields.fields_type_id = fields_type.id
	WHERE fields_type.id=1
	AND items.expo_id='".mysql_real_escape_string($_GET['expo'])."' $search ORDER BY items.id LIMIT $from, $limit_page ;"
);

#$sql = @mysql_query("SELECT items.id, item_fields.content FROM items INNER JOIN item_fields ON items.id=item_fields.items_id WHERE item_fields.expo_fields_id=1 AND `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search ORDER BY items.id LIMIT $from, $limit_page ;");
#echo "SELECT items.id, item_fields.content FROM items INNER JOIN item_fields ON items.id=item_fields.items_id WHERE item_fields.expo_fields_id=1 AND `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search ORDER BY items.id LIMIT $from, $limit_page ;";

if (@mysql_num_rows($sql) >0) {
	while ($don = @mysql_fetch_assoc($sql)) {
		echo '<tr class="height-20">
			<td class="tablerow1">'.$don['content'].'</td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=aitems&amp;expo='.$_GET['expo'].'&amp;edit='.$don['id'].(isset($_GET['page'])?'&amp;page='.$_GET['page']:'').'">Modifier</a></td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=litems&amp;expo='.$_GET['expo'].'&amp;del='.$don['id'].(isset($_GET['page'])?'&amp;page='.$_GET['page']:'').'">Supprimer</a></td>
		</tr>';
	}
	
	// pagination
	// 1ere page
	echo '<tr><td class="tablesubheader" colspan="6" align="center" valign="middle">';
	if($page - $pageavant > 1) {
		echo '&nbsp;<a href="expo.php?act=litems&amp;expo='.$_GET['expo'].'&amp;page=1">1</a>&nbsp;...';
	}
	// pages intermédiaires
	for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
		if ($i == $page) {
			echo '&nbsp;<b>['.$i.']</b>&nbsp;';
		} else {
			echo '&nbsp;<a href="expo.php?act=litems&amp;expo='.$_GET['expo'].'&amp;page='.$i.'">'.$i.'</a>&nbsp;';
		}
	}
	// dernière page
	if($page + $pageapres < $nb_pages) {
		echo '...&nbsp;<a href="expo.php?act=litems&amp;expo='.$_GET['expo'].'&amp;page='.$nb_pages.'">'.$nb_pages.'</a>&nbsp;';
	}
	echo '</td></tr>';
	
	
} else {
	if (isset($_GET['search']) && !empty($_GET['search'])) {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément pour la recherche. <a href="'.$config['site_http'].'/expo.php?act=litems&amp;expo='.$_GET['expo'].'">Voir tous les résultats</a>.</td></tr>';
	} else {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément existant.</td></tr>';
	}
}


?>
	</tbody>
</table>
