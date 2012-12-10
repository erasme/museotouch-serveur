<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id'])) {

	if (!isset($_GET['priv']) || empty($_GET['priv'])) {
		$_GET['priv'] = '';
	}

	// vérification que l'objet existe bien
	$sql = @mysql_query("SELECT `id`, `name`, `private` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");

	if (@mysql_num_rows($sql) > 0) {
	
		$don = @mysql_fetch_assoc($sql);
	
		if ($don['private'] == $_GET['priv']) {
			
			$out = array();
			
			// récupération des fichiers de l'exposition
			$up = array();
			if (@is_dir('../uploads/expos/'.@$don['id'].'/raw/')) {
				if ($dir = @opendir('../uploads/expos/'.$don['id'].'/raw/')) {
					while (($file = @readdir($dir)) !== false) {
						if ($file !== '.' && $file !== '..' && is_file('../uploads/expos/'.$don['id'].'/raw/'.$file)) {
							$up[] = array('fichier' => urlencode($config['site_http'].'/uploads/expos/'.$don['id'].'/raw/'.$file));
						}
					}
					@closedir($dir);
				}
			}
			
			// liste des objets de l'exposition
			$item = array();
			$sql = @mysql_query("SELECT `id`, `nom`, `date_acqui`, `date_crea`, `datation`, `orig_geo`, `orig_geo_prec`, `taille`, `cartel`, `freefield`, `fichier`, `fichier_md5` FROM `items` WHERE `expo_id`='".mysql_real_escape_string($_GET['id'])."'");
			
			while ($it = @mysql_fetch_assoc($sql)) {
			
				$data = array();
				if (@is_dir('../uploads/objets/'.@$it['id'].'/raw/')) {
					if ($dir = @opendir('../uploads/objets/'.$it['id'].'/raw/')) {
						while (($file = @readdir($dir)) !== false) {
							if ($file !== '.' && $file !== '..' && is_file('../uploads/objets/'.$it['id'].'/raw/'.$file)) {
								$data[] = array('fichier' => urlencode($config['site_http'].'/uploads/objets/'.$it['id'].'/raw/'.$file));
							}
						}
						@closedir($dir);
					}
				}
				
				$kw = array();
				$rqt = @mysql_query("SELECT ek.`id` FROM `expo_keywords` ek LEFT JOIN `items_rkeywords` ir ON ir.`expokeywords_id`=ek.`id` WHERE ir.`items_id`='".mysql_real_escape_string($it['id'])."' ORDER BY ek.`name`");
				while ($key = @mysql_fetch_assoc($rqt)) {
					$kw[] = urlencode($key['id']);
				}
		
				// vérification que le fichier existe bien
				if (is_file('../uploads/objets/'.$it['id'].'/raw/'.$it['id'].'.'.$it['fichier'])) {
					$it['fichier'] = $config['site_http'].'/uploads/objets/'.$it['id'].'/raw/'.$it['id'].'.'.$it['fichier'];
				} else {
					$it['fichier'] = '';
					$it['fichier_md5'] = '';
				}
				
				$item[] = array('id' => urlencode($it['id']), 'nom' => urlencode($it['nom']), 'date_acqui' => urlencode($it['date_acqui']), 'date_crea' => urlencode($it['date_crea']), 'datation' => urlencode($it['datation']), 'orig_geo' => urlencode($it['orig_geo']), 'orig_geo_prec' => urlencode($it['orig_geo_prec']), 'taille' => urlencode($it['taille']), 'cartel' => urlencode($it['cartel']), 'freefield' => urlencode($it['freefield']), 'fichier' => urlencode($it['fichier']), 'fichier_md5' => urlencode($it['fichier_md5']), 'data' => $data, 'keywords' => $kw);
			}
			
			$kw = array();
			$rqt1 = @mysql_query("SELECT `id`, `name` FROM `expo_gkeywords` WHERE `expo_id`='".mysql_real_escape_string($_GET['id'])."'");
			$count = 0;
			while ($don1 = @mysql_fetch_assoc($rqt1)) {
				$kw[$count]['group'] = urlencode($don1['name']);
				$rqt2 = @mysql_query("SELECT `id`, `name` FROM `expo_keywords` WHERE `expogkeywords_id`='".mysql_real_escape_string($don1['id'])."'");
				while($don2 = @mysql_fetch_assoc($rqt2)) {
					$kw[$count]['children'][] = array('id' => urlencode($don2['id']), 'name' => urlencode($don2['name']));
				}
				$count++;
			}
			
			$out = array('id' => urlencode($don['id']), 'name' => urlencode($don['name']), 'private' => urlencode(empty($don['private'])?0:1), 'data' => $up, 'keywords' => $kw, 'items' => $item);
		
		} else {
			$out[] = urlencode('Erreur : exposition privée.');
		}
		
	} else {
		$out[] = urlencode('Erreur : l\'exposition n\'existe pas.');
	}
	
} else {
	$out[] = urlencode('Erreur : exposition non-définie.');
}


echo json_encode($out);
	
	
?>