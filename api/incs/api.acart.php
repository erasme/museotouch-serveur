<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['cart']) && !empty($_GET['cart']) && isset($_GET['item']) && !empty($_GET['item'])) {

	// vérification que le panier existe bien
	$sql = @mysql_query("SELECT `id` FROM `cart` WHERE `id`='".mysql_real_escape_string($_GET['cart'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {
	
		$explode = explode(',', $_GET['item']);
		foreach ($explode as $key => $value) {

			// vérification que l'objet existe bien
			$sql = @mysql_query("SELECT `id` FROM `items` WHERE `id`='".mysql_real_escape_string($value)."' LIMIT 0,1");
			if (@mysql_num_rows($sql) > 0) {

				// le panier et l'objet existe, est-ce que l'objet n'est pas déjà présent dans le panier
				$sql = @mysql_query("SELECT `cart_id` FROM `cart_items` WHERE `cart_id`='".mysql_real_escape_string($_GET['cart'])."' && `items_id`='".mysql_real_escape_string($value)."'");
				
				// l'objet est déjà présent dans le panier
				if (@mysql_num_rows($sql) < 1) {
				
					// ajout de l'objet au panier avec "OK" si l'ajout s'est bien effectué
					$sql = @mysql_query("INSERT INTO `cart_items`(`cart_id`, `items_id`) VALUES('".mysql_real_escape_string($_GET['cart'])."', '".mysql_real_escape_string($value)."')");
					if ($sql) {
						$out = array(array('achieved' => true));
					} else {
						$out[] = urlencode('Erreur : impossible d\'ajouter l\'objet au panier. Merci de retenter plus tard. Si le problème persiste, merci de contacter un administrateur.');
						echo json_encode($out);
						return true;
					}
				}

			}
			
		}

		if (!isset($out) || empty($out)) {
			$out = array(array('achieved' => true));
		}

	} else {
		$out[] = urlencode('Erreur : le panier n\'existe pas.');
	}

} else {
	$out[] = urlencode('Erreur : panier ou objet non-définis.');
}

echo json_encode($out);
	
	
?>