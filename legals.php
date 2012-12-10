<?php
session_start();
define('INDEX_ADMIN', true);

require_once(dirname(__FILE__).'/incs/config.inc.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $config['site_name']; ?> : inscription</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="content/css/login.css" media="screen" />
	<script type="text/javascript" src="content/js/cufon.js"></script>
	<script type="text/javascript" src="content/js/cufon.liberation.js"></script>
	<script type="text/javascript">Cufon.replace(".cufon");</script>
</head>
<body>
<a class="center" href="javascript:history.go(-1);">« Revenir à la page précédente</a>
<div class="mentions">
<h1 class="cufon">Mentions légales</h1>
<h2 class="cufon">Informations éditeur</h2>
	<p>!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! A COMPLETER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!</p>
	<p>Le présent site a fait l’objet d’une déclaration auprès de la Commission Nationale de l’Informatique et des Libertés (CNIL). Le numéro de déclaration est le XXXXXX.</p>
	<p>&nbsp;</p>

<h2 class="cufon">Utilisation de l’information et droits d’auteur</h2>
	<p>Conformément aux dispositions du Code de la propriété intellectuelle et des traités et accords internationaux, toute reproduction, divulgation, distribution, représentation, traduction, diffusion, modification, transcription, partielle ou totale, quel que soit le support et quel que soit le procédé utilisé est formellement interdite sauf autorisation express du responsable de la publication. Les documents ne peuvent faire l’objet de copies.</p>
	<p>Toute contravention aux dispositions du présent article est constitutive du délit de contrefaçon et engage à ce titre la responsabilité civile et pénale de son auteur ( article L335-2 et suivants du Code de Propriété Intellectuelle).</p>
	<p>&nbsp;</p>

<h2 class="cufon">Données nominatives</h2>
	<p>Aucune donnée personnelle n’est collectée à votre insu. Les données nominatives demandées sur le site sont destinées exclusivement à l'annuaire de la Ville de Lille et en aucun cas ne seront transmises à des tiers.</p>
	<p>Conformément à l’article 34 de loi « Informatique et Libertés », loi n° 78-17 du 6 janvier 1978, modifiée par la loi du 6 août 2004 relative à l’informatique, aux fichiers et aux libertés vous disposez d’un droit d’accès de modification, de rectification et de suppression des données vous concernant. Ce droit peut être exercé par courriel à .</p>
	<p>Pour toute information sur la protection des données personnelles vous pouvez consulter le site de la commission Informatique et Liberté (http://www.cnil.fr).</p>
	<p>&nbsp;</p>

<h2 class="cufon">Cookies (PAS DE COOKIE, JUSTE DES SESSION, DONC À VOIR)</h2>
	<p>Le site Museotouch implante un cookie dans votre ordinateur.</p>
	<p>Ce cookie enregistre des informations relatives à votre navigation sur notre site, et stocke des informations que vous avez saisies durant votre visite : nom, prénom, courriel et mot de passe crypté servant à votre connexion au site.</p>
	<p>La durée de conservation de ces informations dans votre ordinateur est de 30 minutes.</p>
	<p>Ainsi, vous n'aurez pas besoin, lors de votre prochaine visite, de les saisir à nouveau. Nous pourrons les consulter lors de vos prochaines visites.</p>
	<p>Nous vous informons que vous pouvez vous opposer à l'enregistrement de &quot;cookies&quot; en configurant votre navigateur de la manière suivante :</p>
	<h4 class="cufon">Pour Mozilla firefox :</h4>
		<ol><li>Choisissez le menu "outil " puis "Options"</li>
		<li>Cliquez sur l'icône "vie privée"</li>
		<li>Repérez le menu "cookie" et sélectionnez les options qui vous conviennent</li></ol>
	<h4 class="cufon">Pour Microsoft Internet Explorer :</h4>
		<ol><li>choisissez le menu "Outils"puis "Options Internet"</li>
		<li>cliquez sur l'onglet "Confidentialité"</li>
		<li>sélectionnez le niveau souhaité à l'aide du curseur.</li></ol>
	<h4 class="cufon">Pour Opéra 6.0 et au-delà :</h4>
		<ol><li>choisissez le menu "Fichier">"Préférences"</li>
		<li>Vie Privée</li></ol>
</div>
</body>
</html>