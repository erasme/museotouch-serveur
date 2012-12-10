<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

$sql = @mysql_query("SELECT `id` FROM `items` ORDER BY `id`");

if (mysql_num_rows($sql)) {
	while ($don = mysql_fetch_assoc($sql)) {
	
        // recuperation main_file et de son md5 :
        $main_file = '';
        $md5_main_file = '';
        if (@is_dir('../uploads/objets/'.$don['id'].'/raw/')) {
            if ($dir = @opendir('../uploads/objets/'.$don['id'].'/raw/')) {
                while (($file = @readdir($dir)) !== false) {
                    if (strpos($file, $don['id'].'.') === 0 && is_file('../uploads/objets/'.$don['id'].'/raw/'.$file)) {
                        $main_file = 'uploads/objets/'.$don['id'].'/raw/'.$file;
                        $md5_main_file = @md5_file('../'.$main_file);
                        break;
                    }
                }
                @closedir($dir);
            } else {
                print "troloul";
            }
        } // fin recup main_file et md5
        else {
            print "trolol";
        }
        
        // ajouter au tableau :
        $out[] = array(
            'id' => urlencode($don['id']), 
            'fichier' => urlencode($main_file), 
            'fichier_md5' => urlencode($md5_main_file)
        );
	}
} else {
	$out[] = urlencode('Erreur : aucun fichier existant.');
}

echo json_encode($out);
	
	
?>
