<?php

if (!defined('INDEX_ADMIN')) { header("Location:../expo.php"); }


// test si l'utilisateur a les accès à l'exposition
aaccess();


if (isset($_GET['del']) && !empty($_GET['del'])) {

	// suppression de l'expo, des droits sur celle-ci, des objets liés (juste la liaison objets / expo) et le dossier ainsi que les images
	$sql = @mysql_query("DELETE s, si FROM `scenarios` AS s LEFT JOIN `scenarios_items` AS si ON si.`scenarios_id`=s.`id` WHERE s.`id`='".mysql_real_escape_string($_GET['del'])."'");
	if ($sql) {
		echo '<div class="div_valid">Le scénario a correctement été supprimé.</div>';	
	} else {
		echo '<div class="div_error">Impossible de supprimer le scénario. Veuillez reessayer dans quelques instants. Si cela ne fonctionne toujours pas, merci de contacter un administrateur.</div>';
	}
		
}

?>
<div><form method="get" action="<?php echo $config['site_http']; ?>/expo.php">
<input type="hidden" name="act" value="lscenario" />
<input type="hidden" name="expo" value="<?php echo $_GET['expo']; ?>" />
<input name="search" value="<?php echo @$_GET['search']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
</form></div><br />
	<table class="tableborder">
	<thead><tr><th class="tablesubheader" width="90%">Scénario</th><th class="tablesubheader" width="5%">Modification</th><th class="tablesubheader" width="5%">Suppression</th></tr></thead>
	<tbody>
<?php

// recherche
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
	$search = "AND `name` LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
}

// calcul de pagination
$sql = @mysql_fetch_assoc(@mysql_query("SELECT count(`id`) as nbre FROM `scenarios` WHERE `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search"));
$limit_page = 30;
if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
if (isset($_GET['page']) && !empty($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }
if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };


$sql = @mysql_query("SELECT `id`, `name` FROM `scenarios` WHERE `expo_id`='".mysql_real_escape_string($_GET['expo'])."' $search ORDER BY `name` LIMIT $from, $limit_page");
if (@mysql_num_rows($sql) >0) {
	while ($don = @mysql_fetch_assoc($sql)) {
		echo '<tr class="height-20">
			<td class="tablerow1">'.$don['name'].'</td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=ascenario&amp;expo='.$_GET['expo'].'&amp;edit='.$don['id'].'">Modifier</a></td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=lscenario&amp;expo='.$_GET['expo'].'&amp;del='.$don['id'].(isset($_GET['page'])?'&amp;page='.$_GET['page']:'').'">Supprimer</a></td>
		</tr>';
	}
	
	// pagination
	// 1ere page
	echo '<tr><td class="tablesubheader" colspan="3" align="center" valign="middle">';
	if($page - $pageavant > 1) {
		echo '&nbsp;<a href="expo.php?act=lscenario&amp;expo='.$_GET['expo'].'&amp;page=1">1</a>&nbsp;...';
	}
	// pages intermédiaires
	for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
		if ($i == $page) {
			echo '&nbsp;<b>['.$i.']</b>&nbsp;';
		} else {
			echo '&nbsp;<a href="expo.php?act=lscenario&amp;expo='.$_GET['expo'].'&amp;page='.$i.'">'.$i.'</a>&nbsp;';
		}
	}
	// dernière page
	if($page + $pageapres < $nb_pages) {
		echo '...&nbsp;<a href="expo.php?act=lscenario&amp;expo='.$_GET['expo'].'&amp;page='.$nb_pages.'">'.$nb_pages.'</a>&nbsp;';
	}
	echo '</td></tr>';
	
	
} else {
	if (isset($_GET['search']) && !empty($_GET['search'])) {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément pour la recherche. <a href="'.$config['site_http'].'/expo.php?act=lscenario">Voir tous les résultats</a>.</td></tr>';
	} else {
		echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément existant.</td></tr>';
	}
}


?>
	</tbody>
</table>
