<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/incs/header.php');

if (!isset($_GET['act']) || empty($_GET['act'])) {
	$_GET['act'] = 'infos'; 
}

?>

<table><tbody>
	<tr><td id="sidebar">
		<ul><li><a href="<?php echo $config['site_http']; ?>/profil.php">Modifier mon profil</a>
			<ul>
				<li><a<?php if ($_GET['act']=='infos') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/profil.php?act=infos">Informations</a></li>
				<li><a<?php if ($_GET['act']=='passwd') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/profil.php?act=passwd">Mot de passe</a></li>
				<li><a<?php if ($_GET['act']=='mail') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/profil.php?act=mail">Courriel</a></li>
			</ul>
		</li></ul>
	</td>
	<td id="main">
	<?php

	if (is_file(dirname(__FILE__).'/incs/profil.'. $_GET['act'] .'.php')) {
		require_once(dirname(__FILE__).'/incs/profil.'. $_GET['act'] .'.php');
	} else {
		// Message d'erreur en cas de page non-trouvée
		echo '<h3 class="error">Désolé mais la page demandée n\'existe pas.</h3>';
	}


	?>
	</td></tr>
</tbody></table>

<?php

require_once(dirname(__FILE__).'/incs/footer.php');

?>
