<?php

/*

API des expositions

Mode d'entrée  : _GET
Mode de sortie : JSON
Actions (act)  :
	list(D)		= liste des expos
	expo 		= données d'une expo
	cart		= création d'un panier
	acart		= ajoute un objet au panier (il faut d'abord créer le panier pour récupérer son ID)
	dcart		= retire un objet au panier (il faut d'abord créer le panier pour récupérer son ID)
	scart		= envoie le lien publique de consultation du panier
	item		= informations sur un objet
	scenarios 	= information sur un scénario (type, objets associés, fichiers associés)
	lscenarios	= liste des scénarios d'une exposition avec ses infos
	litems		= liste de tous les objets avec les données ID, le fichier par défaut et le md5 associé
	lcart		= liste de tous les objets d'un panier avec toutes les données en fonction de l'ID

Liste des expositions :
api/index.php[?search=RECHERCHE]

Liste des expositions privées :
api/index.php?priv=1[&search=RECHERCHE]

Liste des objets (tous les champs.) d'une exposition :
api/index.php?act=expo&id=ID_EXPO[&key=CLEF_EXPO_PRIV]

Créer un panier et récupérer son ID, sa clef URL ainsi que son URL publique :
api/index.php?act=cart

Ajout d'un objet au panier :
api/index.php?act=acart&item=ID_OBJET&cart=ID_PANIER

Suppression d'un objet du panier :
api/index.php?act=dcart&item=ID_OBJET&cart=ID_PANIER

Envoi du panier par mail :
api/index.php?act=scart&id=ID_PANIER&mail=MAIL

Informations sur un objet :
api/index.php?act=item&id=ID_OBJET

Scénario :
api/index.php?act=scenarios&id=RFID

Liste des scénarios :
api/index.php?act=lscenarios&id=ID_EXPO

Liste des objets :
api/index.php?act=litems

Liste des objets d'un panier :
api/index.php?act=lcart&id=ID_CART&code=CODE_URL

*/

session_start();
define('INDEX_ADMIN', true);

header('Content-Type: application/json');

require_once('../incs/config.inc.php');
require_once('../incs/connect.inc.php');
require_once('../incs/function.inc.php');


if (!isset($_GET['act']) || empty($_GET['act'])) {
	$_GET['act'] = 'list';
}

if (is_file(dirname(__FILE__).'/incs/api.'. $_GET['act'] .'.php')) {
	require_once(dirname(__FILE__).'/incs/api.'. $_GET['act'] .'.php');
} else {
	// Message d'erreur en cas de page non-trouvée
	echo json_encode(array('Erreur : erreur d\'appel de l\'API.'));
}


@mysql_close();

?>