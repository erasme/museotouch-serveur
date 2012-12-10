<?php

if (!defined('INDEX_ADMIN')) { header("Location:../index.php"); }

// NE PAS TOUCHER À CETTE LIGNE !!
$config = array();

$config['bdd_host'] = 'localhost';	// serveur
$config['bdd_name'] = 'museotouch';		// base de données
$config['bdd_user'] = 'root';		// utilisateur
$config['bdd_pass'] = 'delorean1';		// mot de passe de l'utilisateur

$config['site_name'] = 'Erasme';	// nom du site
$config['site_http'] = 'http://biinlab.com/museotouch';	// base URL du backoffice
$config['site_mail'] = 'antoine@biin.fr';	// mail d'envoi des courriels de mot de passe perdus

$config['cart_mail'] = 'antoine@biin.fr';	// mail d'envoi des courriels des url de paniers
// contenu du mail contenant l'URL du panier
// version plain/text
$config['cart_body_text'] = "Je vous invite à découvrir le territoire stéphanois comme vous ne l’avez jamais vu ! Nouveaux projets urbains, nouvelles ambitions, nouvelle image… 
\r\nVisitez Saint-Étienne Atelier Visionnaire www.saintetienneateliervisionnaire.com
\r\n%s\r\n\Cliquez ou copiez-collez le lien ci-dessus et partagez-le avec vos amis !
r\n\r\nCordialement.";
// version html
$config['cart_body_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr"> 
<head>
	<title>'.$config['site_name'].'</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /> 
	<style type="text/css">
		body { background-color:#F0F0F0; margin:auto; font-size:13px; line-height:21px; font-family:\'Lucida Grande\', Arial, sans-serif; }
		div.content { margin:10px auto; background-color:#FFF; border:1px solid #C2C2C2; width:600px; padding:10px; color:#333; }
		div.logo { background:url(\''.$config['site_http'].'/imgs/logo.png\') no-repeat top left; height:50px; margin:10px 0px 10px; }
	</style>
</head>
<body>
<div class="content">
<div class="logo"></div>
'.nl2br($config['cart_body_text']).'
</div>
</body>
</html>';

?>
