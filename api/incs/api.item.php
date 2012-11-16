<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id'])) {

	// vérification que l'objet existe bien
	$sql = @mysql_query("SELECT `id` FROM `items` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {

        
		$don = @mysql_fetch_assoc($sql);
		$main_file = '';
		$md5_main_file = '';
		// print_r($don);
		
		$data = array();
		if (@is_dir('../uploads/objets/'.@$don['id'].'/raw/')) {
			if ($dir = @opendir('../uploads/objets/'.$don['id'].'/raw/')) {
				while (($file = @readdir($dir)) !== false) {
					if ($file !== '.' && $file !== '..' && is_file('../uploads/objets/'.$don['id'].'/raw/'.$file)) {
						$data[] = array('fichier' => urlencode($config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$file));
						if (strpos($file, $don['id'].'.') === 0) {
						    $main_file = 'uploads/objets/'.$don['id'].'/raw/'.$file;
						    $md5_main_file = @md5_file('../'.$main_file);
						}
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
		if (is_file('../'.$main_file)) {
			$don['fichier'] = $config['site_http'].'/'.$main_file;
			$don['fichier_md5'] = $md5_main_file;
		} else {
			$don['fichier'] = '';
			$don['fichier_md5'] = '';
		}

		$info_array = array();
		
		$sql2 = @mysql_query("SELECT ft.slug, itf.content FROM fields_type ft, expo_fields ef, item_fields itf WHERE ft.id = ef.fields_type_id AND ef.id = itf.expo_fields_id AND itf.items_id = '".$don['id']."';");
		while ($don2 = @mysql_fetch_assoc($sql2)) {
			//print_r($don2);
			//$don[$don2['slug']] = $don2['content'];
			$info_array[$don2['slug']] = urlencode($don2['content']);
		}
		
        $info_array['id'] = urlencode($don['id']);
        $info_array['fichier'] = urlencode($don['fichier']);
        $info_array['fichier_md5'] = urlencode($don['fichier_md5']);
        $info_array['data'] = $data;
        $info_array['keywords'] = $kw;

        $out[] = $info_array;

	} else {
		$out[] = urlencode('Erreur : l\'objet n\'existe pas.');
	}

} else {
	$out[] = urlencode('Erreur : objet non-défini.');
}

echo json_encode($out);
	
	
?>
