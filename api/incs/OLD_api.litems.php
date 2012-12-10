<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

$sql = @mysql_query("SELECT `id`, `fichier`, `fichier_md5` FROM `items` WHERE `fichier`<>'' ORDER BY `id`");


if (mysql_num_rows($sql)) {

	while ($don = mysql_fetch_assoc($sql)) {
	
		// vérification que le fichier existe bien
		if (is_file('../uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'])) {
			$don['fichier'] = '/uploads/objets/'.$don['id'].'/raw/'.$don['id'].'.'.$don['fichier'];
		} else {
			$don['fichier'] = '';
			$don['fichier_md5'] = '';
		}
		
		$out[] = array('id' => urlencode($don['id']), 'fichier' => urlencode($don['fichier']), 'fichier_md5' => urlencode($don['fichier_md5']));
	
	}

} else {

	$out[] = urlencode('Erreur : aucun fichier existant.');
	
}

echo json_encode($out);
	
	
?>
