<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

$code_url = md5(microtime().newpasswd());

$sql = @mysql_query("INSERT INTO `cart`(`id`, `url`) VALUES(NULL, '".mysql_real_escape_string($code_url)."')");

$last_id = @mysql_insert_id();

$out = array('id' => urlencode($last_id), 'code_url' => urlencode($code_url), 'url' => urlencode($config['site_http'].'/cart.php?id='.$last_id.'&code='.$code_url));

echo json_encode($out);

	
	
?>