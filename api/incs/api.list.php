<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

if (isset($_GET['priv']) && !empty($_GET['priv'])) {
	$search = "WHERE `private`<>''";
} else {
	$search = "WHERE `private`=''";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
	$search .= " AND `name` LIKE '%".mysql_real_escape_string($_GET['search'])."%'";
}

$out = array();

$sql = @mysql_query("SELECT `id`, `name`, `private`, `last_publication` FROM `expo` $search ORDER BY `createdate`");
while ($don = @mysql_fetch_assoc($sql)) {
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
	$out[] = array('id' => urlencode($don['id']), 'name' => urlencode($don['name']), 'private' => urlencode(empty($don['private'])?0:1), 'last_publication' => urlencode($don['last_publication']), 'data' => $up);
}

echo json_encode($out);

?>