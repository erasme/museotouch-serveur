<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/config.inc.php');
require_once(dirname(__FILE__).'/connect.inc.php');


if (isset($_GET['q'])) {
	$sql = @mysql_query("SELECT `id`, `orig_geo` FROM `items` WHERE LOWER(`orig_geo`) LIKE '".mysql_real_escape_string(strtolower($_GET['q']))."%' GROUP BY `orig_geo` ORDER BY `orig_geo`");
	while($don = @mysql_fetch_assoc($sql)) {
		echo $don['orig_geo']."\n";
	}

}

?>