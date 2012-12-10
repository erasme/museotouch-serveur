<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id'])) {

	// vérification que l'objet existe bien
	$sql = @mysql_query("SELECT `id`, `nom`, `date_acqui`, `date_crea`, `datation`, `orig_geo`, `orig_geo_prec`, `taille`, `cartel`, `freefield`, `fichier`, `fichier_md5` FROM `items` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {

		$don = @mysql_fetch_assoc($sql);
		// print_r($don);
		
		$data = array();
		if (@is_dir('../uploads/objets/'.@$don['id'].'/raw/')) {
			if ($dir = @opendir('../uploads/objets/'.$don['id'].'/raw/')) {
				while (($file = @readdir($dir)) !== false) {
					if ($file !== '.' && $file !== '..' && is_file('../uploads/objets/'.$don['id'].'/raw/'.$file)) {
						$data[] = array('fichier' => urlencode($config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$file));
					}
				}
				@closedir($dir);
			}
		}
		
				
		$kw = array();
		$rqt = @mysql_query("SELECT ek.`id` FROM `expo_keywords` ek LEFT JOIN `items_rkeywords` ir ON ir.`expokeywords_id`=ek.`id` WHERE ir.`items_id`='".mysql_real_escape_string($_GET['id'])."' ORDER BY ek.`name`");
		while ($key = @mysql_fetch_assoc($rqt)) {
			$kw[] = urlencode($key['id']);
		}
		
		// vérification que le fichier existe bien
		if (is_file('../uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'])) {
			$don['fichier'] = $config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'];
		} else {
			$don['fichier'] = '';
			$don['fichier_md5'] = '';
		}
		
		$out = array('id' => urlencode($don['id']), 'nom' => urlencode($don['nom']), 'date_acqui' => urlencode($don['date_acqui']), 'date_crea' => urlencode($don['date_crea']), 'datation' => urlencode($don['datation']), 'orig_geo' => urlencode($don['orig_geo']), 'orig_geo_prec' => urlencode($don['orig_geo_prec']), 'taille' => urlencode($don['taille']), 'cartel' => urlencode($don['cartel']), 'freefield' => urlencode($don['freefield']), 'fichier' => urlencode($don['fichier']), 'fichier_md5' => urlencode($don['fichier_md5']), 'keywords' => $kw, 'data' => $data);

	} else {
		$out[] = urlencode('Erreur : l\'objet n\'existe pas.');
	}

} else {
	$out[] = urlencode('Erreur : objet non-défini.');
}

echo json_encode($out);
	
	
?>