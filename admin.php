<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/incs/header.php');

// Vérification du niveau d'admin
if ($_SESSION['is_admin'] < 2) { header("Location:index.php"); } else {

if (!isset($_GET['act']) || empty($_GET['act'])) {
	$_GET['act'] = 'expos';
}

?>

<table><tbody>
	<tr><td id="sidebar">
		<ul><li><a href="<?php echo $config['site_http']; ?>/admin.php?act=expos">Gestion des expositions</a>
			<ul>
				<li><a<?php if ($_GET['act']=='expos') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/admin.php?act=expos">Gestion des droits</a></li>
			</ul>
		</li><li><a href="<?php echo $config['site_http']; ?>/admin.php?act=luser">Gestion des utilisateurs</a>
			<ul>
				<li><a<?php if ($_GET['act']=='auser') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/admin.php?act=auser">Ajouter</a></li>
				<li><a<?php if ($_GET['act']=='luser') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/admin.php?act=luser">Liste</a></li>
			</ul>
		</li></ul>
	</td>
	<td id="main">
	<?php

	if (is_file(dirname(__FILE__).'/incs/admin.'. $_GET['act'] .'.php')) {
		require_once(dirname(__FILE__).'/incs/admin.'. $_GET['act'] .'.php');
	} else {
		// Message d'erreur en cas de page non-trouvée
		echo '<h3 class="error">Désolé mais la page demandée n\'existe pas.</h3>';
	}


	?>
	</td></tr>
</tbody></table>

<?php

}

require_once(dirname(__FILE__).'/incs/footer.php');

?>