<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id'])) {

	// vérification que le panier existe bien
	$sql = @mysql_query("SELECT `id`, `type_action` FROM `scenarios` WHERE `id_rfid`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {

		$don = @mysql_fetch_assoc($sql);
		if ($don['type_action'] == 0) {
			$out = array(0);
		} else {
			$up = array();
			if (@is_dir('../uploads/scenarios/'.@$don['id'].'/')) {
				if ($dir = @opendir('../uploads/scenarios/'.$don['id'].'/')) {
					while (($file = @readdir($dir)) !== false) {
						if ($file !== '.' && $file !== '..' && is_file('../uploads/scenarios/'.$don['id'].'/'.$file)) {
							$up[] = urlencode($config['site_http'].'/uploads/scenarios/'.$don['id'].'/'.$file);
						}
					}
					@closedir($dir);
				}
			}
			
			$items = array();
			$rqt = @mysql_query("SELECT `items_id` FROM `scenarios_items` WHERE `scenarios_id`='".mysql_real_escape_string($don['id'])."'");
			while ($it = @mysql_fetch_assoc($rqt)) {
				$items[] = $it['items_id'];
			}
			
			$out = array('action' => $don['type_action'], 'data' => $up, 'items' => $items);
		}

	} else {
		$out[] = urlencode('Erreur : le scénario n\'existe pas.');
	}

} else {
	$out[] = urlencode('Erreur : scénario non-défini.');
}	

echo json_encode($out);
	
?>