<?php

if (!defined('INDEX_ADMIN')) { header("Location:../admin.php"); }


if (isset($_GET['id']) && !empty($_GET['id'])) {

	// affichage du nom de l'expo en référence
	$expo = @mysql_fetch_assoc(mysql_query("SELECT `id`, `name` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1"));
	echo '<h2 class="cufon">Gestion des droits de l\'exposition : '.$expo['name'].'</h2><br />';
	

	// suppression des droits d'administrateur de l'exposition à un utilisateur
	if (isset($_GET['del']) && !empty($_GET['del'])) {
		$sql = @mysql_query("DELETE FROM `expo_admin` WHERE `users_id`='".mysql_real_escape_string($_GET['del'])."' AND `expo_id`='".mysql_real_escape_string($expo['id'])."'");
		if (!$sql) {
			echo '<div class="error_valid">Une erreur s\'est produite. Si l\'erreur se répète, veuillez contacter un administrateur.</div>';		
		}
	}

	
	// ajout des droits d'administrateur de l'exposition à un utilisateur
	if (isset($_GET['add']) && !empty($_GET['add'])) {
		$sql = @mysql_query("SELECT `users_id` FROM `expo_admin` WHERE `users_id`='".mysql_real_escape_string($_GET['add'])."' AND `expo_id`='".mysql_real_escape_string($_GET['id'])."'");
		if (@mysql_num_rows($sql) < 1) {
			$sql = @mysql_query("INSERT INTO `expo_admin`(`users_id`, `expo_id`) VALUES('".mysql_real_escape_string($_GET['add'])."', '".mysql_real_escape_string($expo['id'])."')");
			if (!$sql) {
				echo '<div class="error_valid">Une erreur s\'est produite. Si l\'erreur se répète, veuillez contacter un administrateur.</div>';		
			}
		}
	}
	
	?>

<table width="100%"><tr><td width="50%">
	<h3 class="cufon inline">Administrateurs de l'exposition :</h3><br /><br />
	<table class="tableborder">
	<thead><tr><th class="tablesubheader center" width="10%">Retirer</th><th class="tablesubheader" width="90%">Utilisateur</th></tr></thead>
	<tbody>
	
	<?php
	
	$sql = @mysql_query("SELECT u.`lastname`, u.`firstname`, u.`mailaddress`, u.`id` FROM `users` u LEFT JOIN `expo_admin` ea ON ea.`users_id`=u.`id` WHERE ea.`expo_id`='".mysql_real_escape_string($expo['id'])."' ORDER BY u.`lastname`, u.`firstname`, u.`mailaddress`");
	if (@mysql_num_rows($sql) > 0) {
		while ($don = @mysql_fetch_assoc($sql)) {
			echo '<tr><td class="tablerow1 center"><a href="'.$config['site_http'].'/admin.php?act=expos&amp;id='.$expo['id'].'&amp;del='.$don['id'].'">[ retirer ]</a></td><td class="tablerow2">'.((!empty($don['lastname']) && !empty($don['firstname']))?$don['lastname'].' '.$don['firstname']:$don['mailaddress']).'</td></tr>';
		}
	} else {
		echo '<tr><td class="tablerow1 center" colspan="2">Il n\'y a aucun administrateur pour l\'exposition (hors super-administrateurs).</td></tr>';
	}
	
	?>
	
	</tbody></table>
</td><td width="50%">
	<h3 class="cufon inline">Ajouter un administrateur à l'exposition :</h3><br /><br />
	<table class="tableborder">
	<thead><tr><th class="tablesubheader center" width="10%">Ajouter</th><th class="tablesubheader" width="90%">Utilisateur</th></tr></thead>
	<tbody>
	
	<?php
	
	$sql = @mysql_query("SELECT `id`, `firstname`, `lastname`, `mailaddress` FROM `users` WHERE `is_admin`='1' AND `id` NOT IN (SELECT u.`id` FROM `users` u LEFT JOIN `expo_admin` ea ON ea.`users_id`=u.`id` WHERE ea.`expo_id`='".mysql_real_escape_string($expo['id'])."') ORDER BY `lastname`, `firstname`, `mailaddress`");
	if (@mysql_num_rows($sql) > 0) {
		while ($don = @mysql_fetch_assoc($sql)) {
			echo '<tr><td class="tablerow1 center"><a href="'.$config['site_http'].'/admin.php?act=expos&amp;id='.$expo['id'].'&amp;add='.$don['id'].'">[ ajouter ]</a></td><td class="tablerow2">'.((!empty($don['lastname']) && !empty($don['firstname']))?$don['lastname'].' '.$don['firstname']:$don['mailaddress']).'</td></tr>';
		}
	} else {
		echo '<tr><td class="tablerow1 center" colspan="2">Aucun administrateur ne peut être ajouté car plus aucun utilisateur ne dispose des droits administrateurs (hors super-administrateurs).</td></tr>';
	}
	?>
</tbody></table></tr></td></table>
	<?php
	
} else {

?>
<div><form method="get" action="<?php echo $config['site_http']; ?>/admin.php">
<input type="hidden" name="act" value="expos" />
<input name="search" value="<?php echo @$_GET['search']; ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Rechercher" />
</form></div><br />
	<table class="tableborder">
	<thead><tr><th class="tablesubheader" width="90%">Nom de l'exposition</th><th class="tablesubheader" width="5%">Modifier</th><th class="tablesubheader" width="5%">Gérer les droits</th></tr></thead>
	<tbody>
<?php

// recherche
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
	$search = "WHERE `name` LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
}

$sql = @mysql_fetch_assoc(@mysql_query("SELECT count(`id`) as nbre FROM `expo` $search ORDER BY `name`"));
$limit_page = 30;
if ($sql['nbre'] > 0) { $nb_pages = ceil($sql['nbre'] / $limit_page); }  else { $nb_pages = 1; }
if (isset($_GET['page']) && !empty($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }
if ($page > $nb_pages) { $from = $nb_pages-1; }  else { $from = ($page - 1) * $limit_page; }
if ($page > 3) { $pageavant = 2; }  else { $pageavant = $page - 1; };
if ($page <= $nb_pages - 2) { $pageapres = 2; } else { $pageapres = $nb_pages - $page; };

$sql = @mysql_query("SELECT `id`, `name` FROM `expo` $search ORDER BY `name` LIMIT $from, $limit_page");

if (@mysql_num_rows($sql) >0) {
	while ($don = @mysql_fetch_assoc($sql)) {
		echo '<tr class="height-20">
			<td class="tablerow1">'.$don['name'].'</td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/expo.php?act=aexpos&amp;edit='.$don['id'].'">Modifier</a></td>
			<td class="tablerow2 center"><a href="'.$config['site_http'].'/admin.php?act=expos&amp;id='.$don['id'].'">Gérer</a></td>
		</tr>';
	}
	
	// pagination
	// 1ere page
	echo '<tr><td class="tablesubheader" colspan="6" align="center" valign="middle">';
	if($page - $pageavant > 1) {
		echo '&nbsp;<a href="admin.php?act=expos&amp;page=1">1</a>&nbsp;...';
	}
	// pages intermédiaires
	for($i = $page - $pageavant; $i <= $page + $pageapres; $i++) {
		if ($i == $page) {
			echo '&nbsp;<b>['.$i.']</b>&nbsp;';
		} else {
			echo '&nbsp;<a href="admin.php?act=expos&amp;page='.$i.'">'.$i.'</a>&nbsp;';
		}
	}
	// dernière page
	if($page + $pageapres < $nb_pages) {
		echo '...&nbsp;<a href="admin.php?act=expos&amp;page='.$nb_pages.'">'.$nb_pages.'</a>&nbsp;';
	}
	echo '</td></tr>';
	
	
} else {
	echo '<tr><td colspan="3" class="tablerow1 center">Aucun élément existant.</td></tr>';
}

?>
	</tbody>
</table>

<?php

}

?>