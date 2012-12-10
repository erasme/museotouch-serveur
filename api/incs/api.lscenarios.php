<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['id']) && !empty($_GET['id'])) {

	// vérification que l'exposition existe bien
	$sql = @mysql_query("SELECT `id` FROM `expo` WHERE `id`='".mysql_real_escape_string($_GET['id'])."' LIMIT 0,1");
	if (@mysql_num_rows($sql) > 0) {

		$rqt = @mysql_query("SELECT `id`, `id_rfid`, `type_action` FROM `scenarios` WHERE `expo_id`='".mysql_real_escape_string($_GET['id'])."'");
		if (mysql_num_rows($rqt) > 0) {
		
			while ($don = @mysql_fetch_assoc($rqt)) {
			
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
					$req = @mysql_query("SELECT `items_id` FROM `scenarios_items` WHERE `scenarios_id`='".mysql_real_escape_string($don['id'])."'");
					while ($it = @mysql_fetch_assoc($req)) {
						$items[] = $it['items_id'];
					}
					
					$out[] = array('id_rfid' => $don['id_rfid'], 'action' => $don['type_action'], 'data' => $up, 'items' => $items);
				}
				
			}
			
		} else {
			$out[] = urlencode('Erreur : aucun scénario pour cette exposition.');
		}

	} else {
		$out[] = urlencode('Erreur : l\'exposition n\'existe pas.');
	}

} else {
	$out[] = urlencode('Erreur : exposition non-défini.');
}	

echo json_encode($out);
	
?>