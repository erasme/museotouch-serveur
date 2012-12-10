<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

header('Charset: utf-8');

$out = array();

$sql = @mysql_query("SELECT i.`id`, i.`nom`, i.`date_acqui`, i.`date_crea`, i.`datation`, i.`orig_geo`, i.`orig_geo_prec`, i.`taille`, i.`cartel`, i.`freefield`, i.`fichier`, i.`fichier_md5`, e.`name`, e.`id` AS idexpo FROM `items` AS i LEFT JOIN `expo` AS e ON e.`id`=i.`expo_id` LEFT JOIN `cart_items` AS ci ON ci.`items_id`=i.`id` LEFT JOIN `cart` AS c ON ci.`cart_id`=c.`id` WHERE ci.`cart_id`='".mysql_real_escape_string($_GET['id'])."' AND c.`url`='".mysql_real_escape_string($_GET['code'])."' AND i.`private`=0");

if (@mysql_num_rows($sql) > 0) {
	
	while ($don = @mysql_fetch_assoc($sql)) {
	
		$data = array();
		if (@is_dir('../uploads/objets/'.@$don['id'].'/raw/')) {
			if ($dir = @opendir('../uploads/objets/'.$don['id'].'/raw/')) {
				while (($file = @readdir($dir)) !== false) {
					if ($file !== '.' && $file !== '..' && is_file('../uploads/objets/'.$don['id'].'/raw/'.$file)) {
						$data[] = array('fichier' => $config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$file);
					}
				}
				@closedir($dir);
			}
		}
	
		// vérification que le fichier existe bien
		if (is_file('../uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'])) {
			$don['fichier'] = $config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'];
		} else {
			$don['fichier'] = '';
			$don['fichier_md5'] = '';
		}
		
		$kw = array();
		$rqt = @mysql_query("SELECT ek.`name` FROM `expo_keywords` ek LEFT JOIN `items_rkeywords` ir ON ir.`expokeywords_id`=ek.`id` WHERE ir.`items_id`='".mysql_real_escape_string($don['id'])."' ORDER BY ek.`name`");
		while ($key = @mysql_fetch_assoc($rqt)) {
			$kw[] = $key['name'];
		}
	
		$out[] = array(
			'expo' => array('id' => $don['idexpo'], 'name' => $don['name']),
			'id' => $don['id'],
			'nom' => $don['nom'],
			'date_acqui' => $don['date_acqui'],
			'date_crea' => $don['date_crea'],
			'datation' => $don['datation'],
			'orig_geo' => $don['orig_geo'],
			'orig_geo_prec' => $don['orig_geo_prec'],
			'taille' => $don['taille'],
			'cartel' => $don['cartel'],
			'freefield' => $don['freefield'],
			'fichier' => $don['fichier'],
			'fichier_md5' => $don['fichier_md5'],
			'keywords' => $kw,
			'data' => $data
		);
	
	}
} else {
	$out[] = 'Erreur : aucun objet dans le panier.';
}

echo json_encode($out);
	
?>