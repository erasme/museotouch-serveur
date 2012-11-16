<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

header('Charset: utf-8');

$out = array();

$sql = @mysql_query("SELECT i.`id`, e.`name`, e.`id` AS idexpo FROM `items` AS i LEFT JOIN `expo` AS e ON e.`id`=i.`expo_id` LEFT JOIN `cart_items` AS ci ON ci.`items_id`=i.`id` LEFT JOIN `cart` AS c ON ci.`cart_id`=c.`id` WHERE ci.`cart_id`='".mysql_real_escape_string($_GET['id'])."' AND c.`url`='".mysql_real_escape_string($_GET['code'])."'");

if (@mysql_num_rows($sql) > 0) {
	
	while ($don = @mysql_fetch_assoc($sql)) {
	
	    $main_file = '';
        $md5_main_file = '';
	
		$data = array();
		if (@is_dir('../uploads/objets/'.@$don['id'].'/raw/')) {
			if ($dir = @opendir('../uploads/objets/'.$don['id'].'/raw/')) {
				while (($file = @readdir($dir)) !== false) {
					if ($file !== '.' && $file !== '..' && is_file('../uploads/objets/'.$don['id'].'/raw/'.$file)) {
						$data[] = array('fichier' => $config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$file);
				        if (strpos($file, $don['id'].'.') === 0) {
				            $main_file = 'uploads/objets/'.$don['id'].'/raw/'.$file;
				            $md5_main_file = @md5_file('../'.$main_file);
				        }
					}
				}
				@closedir($dir);
			}
		}
	
		// vérification que le fichier existe bien
		if (is_file('../'.$main_file)) {
			$don['fichier'] = $config['site_http'].'/'.$main_file;
			$don['fichier_md5'] = $md5_main_file;
		} else {
			$don['fichier'] = '';
			$don['fichier_md5'] = '';
		}
		
		$kw = array();
		$rqt = @mysql_query("SELECT ek.`name` FROM `expo_keywords` ek LEFT JOIN `items_rkeywords` ir ON ir.`expokeywords_id`=ek.`id` WHERE ir.`items_id`='".mysql_real_escape_string($don['id'])."' ORDER BY ek.`name`");
		while ($key = @mysql_fetch_assoc($rqt)) {
			$kw[] = $key['name'];
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
	
	}
} else {
	$out[] = 'Erreur : aucun objet dans le panier.';
}

echo json_encode($out);
	
?>
