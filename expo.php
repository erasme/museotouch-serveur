<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/incs/header.php');

// Vérification du niveau d'admin
if ($_SESSION['is_admin']<1) { header("Location:index.php"); } else {

if (!isset($_GET['act']) || empty($_GET['act'])) {
	$_GET['act'] = 'lexpos';
}

?>

<table><tbody>
	<tr><td id="sidebar">
		<ul>
		<li><a href="<?php echo $config['site_http']; ?>/expo.php?act=lexpos">Gestion des expositions</a>
			<ul>
				<li><a<?php if ($_GET['act']=='aexpos' && (!isset($_GET['expo']) || empty($_GET['expo']))) { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=aexpos">Ajouter</a></li>
				<li><a<?php if ($_GET['act']=='lexpos') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=lexpos">Liste</a></li>
			</ul>
		</li>
		<?php if (isset($_GET['expo']) && !empty($_GET['expo'])) {
		// test si l'expo existe
		$sql = @mysql_query("SELECT `id` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['expo'])."'");
		if (@mysql_num_rows($sql) > 0) {
		?>
		<li><a href="<?php echo $config['site_http']; ?>/expo.php?act=aexpos&amp;expo=<?php echo $_GET['expo']; ?>">Gérer l'exposition</a>
			<ul>
				<li><a<?php if ($_GET['act']=='aexpos' && isset($_GET['expo']) && !empty($_GET['expo'])) { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=aexpos&amp;expo=<?php echo $_GET['expo']; ?>">Gérer</a></li>
			</ul>
		</li>
		<li><a href="<?php echo $config['site_http']; ?>/expo.php?act=lkeywords&amp;expo=<?php echo $_GET['expo']; ?>">Gestion des mots-clefs</a>
			<ul>
				<li><a<?php if ($_GET['act']=='akeywords') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=akeywords&amp;expo=<?php echo $_GET['expo']; ?>">Ajouter</a></li>
				<li><a<?php if ($_GET['act']=='lkeywords') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=lkeywords&amp;expo=<?php echo $_GET['expo']; ?>">Liste</a></li>
			</ul>
		</li>
		<li><a href="<?php echo $config['site_http']; ?>/expo.php?act=litems&amp;expo=<?php echo $_GET['expo']; ?>">Gestion des objets</a>
			<ul>
				<li><a<?php if ($_GET['act']=='aitems') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=aitems&amp;expo=<?php echo $_GET['expo']; ?>">Ajouter</a></li>
				<li><a<?php if ($_GET['act']=='litems') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=litems&amp;expo=<?php echo $_GET['expo']; ?>">Liste</a></li>
			</ul>
		</li>
		<li><a href="<?php echo $config['site_http']; ?>/expo.php?act=lscenario&amp;expo=<?php echo $_GET['expo']; ?>">Gestion des scénarios</a>
			<ul>
				<li><a<?php if ($_GET['act']=='ascenario') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=ascenario&amp;expo=<?php echo $_GET['expo']; ?>">Ajouter</a></li>
				<li><a<?php if ($_GET['act']=='lscenario') { echo ' class="hover"'; } ?> href="<?php echo $config['site_http']; ?>/expo.php?act=lscenario&amp;expo=<?php echo $_GET['expo']; ?>">Liste</a></li>
			</ul>
		</li>
		<?php } } ?>
		</ul>
	</td>
	<td id="main">
	<?php

	if (is_file(dirname(__FILE__).'/incs/expo.'. $_GET['act'] .'.php')) {
		require_once(dirname(__FILE__).'/incs/expo.'. $_GET['act'] .'.php');
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