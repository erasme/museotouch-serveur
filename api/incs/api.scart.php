<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['mail']) && !empty($_GET['mail'])) {
	if (!isset($_GET['from']) || empty($_GET['from'])) {
		$from = $config['site_mail'];
	} else {
		$from = $_GET['from'];
	}

	if (preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,6})$`', $_GET['mail'])) {

		$sql = @mysql_query("SELECT `id`, `url` FROM `cart` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");
		if (@mysql_num_rows($sql) > 0) {
		
			$don = @mysql_fetch_assoc($sql);
			$url = $config['site_http'].'/cart.php?id='.$don['id'].'&code='.$don['url'];
			
			$headers = 'From: '.$from."\r\n".'Reply-To: '.$from;
			
			if (mail($_GET['mail'], $config['site_name'].' : partage de panier.', sprintf($config['cart_body_text'], $url), $headers)) {
				$out[] = array('achieved' => true);
			} else {
				phpinfo();
			}
			
		} else {
			$out[] = urlencode('Erreur : le panier n\'existe pas.');
		}

	} else {
		$out[] = urlencode('Erreur : le courriel renseigné n\'est pas un courriel correct.');
	}

} else {
	$out[] = urlencode('Erreur : données incorrectes.');
}

echo json_encode($out);
	
	
?>