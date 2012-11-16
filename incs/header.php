<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

require_once('config.inc.php');
require_once('connect.inc.php');
require_once('logged.inc.php');
require_once('function.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head profile="http://gmpg.org/xfn/11"> 
	<title><?php echo $config['site_name']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $config['site_http']; ?>/content/css/jquery.autocomplete.css" media="screen" />
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.js"></script>
	<script type="text/javascript" src="<?php echo $config['site_http']; ?>/content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
		<!-- jquery ui for datetimepicker -->
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.js"></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/cupertino/jquery-ui.css" type="text/css" />
		<style>
			/* css for timepicker */
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
		</style>
		<script src="./content/js/jquery-ui-timepicker.js"></script>
		<script src="./content/js/jquery-ui-timepicker-exec.js"></script>
</head>
<body>
<nav>
	<div>
		<ul>
			<a href="<?php echo $config['site_http']; ?>/index.php"><li class="logo"><!-- <img src="<?php echo $config['site_http']; ?>/imgs/logo.png" alt="Index" /> -->Accueil</li></a>
			<?php if ($_SESSION['is_admin']>1) { echo '<a href="'.$config['site_http'].'/admin.php"><li>Administration</li></a>'; } ?>
			<?php if ($_SESSION['is_admin']>0) { echo '<a href="'.$config['site_http'].'/expo.php"><li>Exposition</li></a>'; } ?>
			<a href="<?php echo $config['site_http']; ?>/profil.php"><li class="borderRight">Profil</li></a>
			<a href="<?php echo $config['site_http']; ?>/logout.php"><li class="right logout"></li></a>
			<li class="right"><?php echo $_SESSION['user_seen']; ?>&nbsp;</li>
		</ul>
	</div>	
</nav>

<!-- <div id="header">
	<a class="logo" href="<?php echo $config['site_http']; ?>/index.php"><img src="<?php echo $config['site_http']; ?>/imgs/logo.png" alt="Index" /></a>
	<ul id="menu">
		<li><a href="<?php echo $config['site_http']; ?>/profil.php">PROFIL<a></li>
		<?php if ($_SESSION['is_admin']>0) { echo '<li><a href="'.$config['site_http'].'/expo.php">EXPOSITION<a></li>'; } ?>
		<?php if ($_SESSION['is_admin']>1) { echo '<li><a href="'.$config['site_http'].'/admin.php">ADMINISTRATION<a></li>'; } ?>
	</ul>
	<div class="connected">
		<?php echo $_SESSION['user_seen']; ?>&nbsp;
		<a href="<?php echo $config['site_http']; ?>/logout.php">Se déconnecter</a>
	</div>
</div> -->
<div id="content" class="col-full">