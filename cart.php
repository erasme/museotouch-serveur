<?php
session_start();
define('INDEX_ADMIN', true);

// connexion à la BDD
require_once(dirname(__FILE__).'/incs/config.inc.php');
require_once(dirname(__FILE__).'/incs/connect.inc.php');
require_once(dirname(__FILE__).'/incs/function.inc.php');

// Twig
require_once(dirname(__FILE__).'/content/Twig/Autoloader.php');
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('cart_templates');
$twig = new Twig_Environment($loader, array('cache' => false));
$twig_template = 'error'; // -> error.html will be used if errors.
$twig_data = array(
    'site_http' => $config['site_http'],
    'site_name' => $config['site_name'],
);

// la suite
$sql = @mysql_query("SELECT `id`, `url` FROM `cart` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' AND `url`='".mysql_real_escape_string($_GET['code'])."' LIMIT 0,1");
if (@mysql_num_rows($sql) > 0) {

	$sql = @mysql_query("SELECT i.`id`, e.`name`, e.`id` AS idexpo FROM `items` AS i LEFT JOIN `expo` AS e ON e.`id`=i.`expo_id` LEFT JOIN `cart_items` AS ci ON ci.`items_id`=i.`id` LEFT JOIN `cart` AS c ON ci.`cart_id`=c.`id` WHERE ci.`cart_id`='".mysql_real_escape_string($_GET['id'])."' AND c.`url`='".mysql_real_escape_string($_GET['code'])."'");

	$maxitems = @mysql_num_rows($sql);

	if ($maxitems > 0) {

		$items = array();
		
		while ($don = @mysql_fetch_assoc($sql)) {

		    $main_file = '';
	        $md5_main_file = '';
	        $twig_template = $don['idexpo'];
		
			$data = array();
			if (@is_dir('./uploads/objets/'.@$don['id'].'/raw/')) {
				if ($dir = @opendir('./uploads/objets/'.$don['id'].'/raw/')) {
					while (($file = @readdir($dir)) !== false) {
						if ($file !== '.' && $file !== '..' && is_file('./uploads/objets/'.$don['id'].'/raw/'.$file)) {
							$data[] = array('fichier' => $config['site_http'].'/uploads/objets/'.$don['id'].'/raw/'.$file);
					        if (strpos($file, $don['id'].'.') === 0) {
					            $main_file = 'uploads/objets/'.$don['id'].'/raw/'.$file;
					            $md5_main_file = @md5_file('./'.$main_file);
					        }
						}
					}
					@closedir($dir);
				}
			}

			$kw = array();
			$rqt = @mysql_query("SELECT ek.`name` FROM `expo_keywords` ek LEFT JOIN `items_rkeywords` ir ON ir.`expokeywords_id`=ek.`id` WHERE ir.`items_id`='".mysql_real_escape_string($don['id'])."' ORDER BY ek.`name`");
			while ($key = @mysql_fetch_assoc($rqt)) {
				$kw[] = $key['name'];
			}
			
			$info_array = array();

	        $sql2 = @mysql_query("SELECT ft.slug, itf.content FROM fields_type ft, expo_fields ef, item_fields itf WHERE ft.id = ef.fields_type_id AND ef.id = itf.expo_fields_id AND itf.items_id = '".$don['id']."';");
	        while ($don2 = @mysql_fetch_assoc($sql2)) {
		        $info_array[$don2['slug']] = $don2['content'];
	        }

	        $info_array['id'] = urlencode($don['id']);
	        $info_array['fichier'] = urlencode($don['fichier']);
	        $info_array['fichier_md5'] = urlencode($don['fichier_md5']);
	        $info_array['data'] = $data;
	        $info_array['keywords'] = $kw;

	        $info_array['_main_file'] = $main_file;
	        $info_array['_expo_name'] = $don['name'];

	        $items[] = $info_array;
		}
		$twig_data['items'] = $items;
		
	} else {
		$twig_data['error'] = "Le panier est vide";
	}

} else {
	$twig_data['error'] = "Le panier n'existe pas";
}

echo $twig->render($twig_template.'.html', $twig_data); // affiche le template avec les donnees de $twigarray
?>