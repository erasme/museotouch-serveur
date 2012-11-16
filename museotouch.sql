-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 02 Novembre 2011 à 13:30
-- Version du serveur: 5.1.53
-- Version de PHP: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `museotouch2`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(32) NOT NULL COMMENT 'Paramètre d''URL complexe qui sera utilisé pour afficher le panier (voir si utilité du fait de la non-criticité des données)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Panier virtuel partageable' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `cart`
--


-- --------------------------------------------------------

--
-- Structure de la table `cart_items`
--

CREATE TABLE IF NOT EXISTS `cart_items` (
  `cart_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `cart_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `expo`
--

CREATE TABLE IF NOT EXISTS `expo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `private` varchar(10) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_publication` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Liste des expositions' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `expo`
--


-- --------------------------------------------------------

--
-- Structure de la table `expo_admin`
--

CREATE TABLE IF NOT EXISTS `expo_admin` (
  `expo_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Liste des admin d''expos alimenté par des clefs étrangères';

--
-- Contenu de la table `expo_admin`
--


-- --------------------------------------------------------

--
-- Structure de la table `expo_gkeywords`
--

CREATE TABLE IF NOT EXISTS `expo_gkeywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expo_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `expo_gkeywords`
--


-- --------------------------------------------------------

--
-- Structure de la table `expo_keywords`
--

CREATE TABLE IF NOT EXISTS `expo_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expogkeywords_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `expo_keywords`
--


-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expo_id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `date_acqui` int(4) NOT NULL,
  `date_crea` int(4) NOT NULL,
  `datation` varchar(50) NOT NULL,
  `orig_geo` varchar(50) NOT NULL,
  `orig_geo_prec` varchar(50) NOT NULL,
  `taille` int(11) NOT NULL,
  `cartel` text NOT NULL,
  `freefield` text NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `fichier` varchar(3) NOT NULL,
  `fichier_md5` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Liste des objets' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `items`
--


-- --------------------------------------------------------

--
-- Structure de la table `items_rkeywords`
--

CREATE TABLE IF NOT EXISTS `items_rkeywords` (
  `items_id` int(11) NOT NULL,
  `expokeywords_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `items_rkeywords`
--


-- --------------------------------------------------------

--
-- Structure de la table `scenarios`
--

CREATE TABLE IF NOT EXISTS `scenarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expo_id` int(11) NOT NULL,
  `id_rfid` varchar(14) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type_action` int(1) NOT NULL COMMENT '0 = reboot / 1 = plans - images',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `scenarios`
--


-- --------------------------------------------------------

--
-- Structure de la table `scenarios_items`
--

CREATE TABLE IF NOT EXISTS `scenarios_items` (
  `items_id` int(11) NOT NULL,
  `scenarios_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `scenarios_items`
--


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(40) NOT NULL COMMENT 'sha1(MAIL.md5(MDP))',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `mailaddress` varchar(50) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = utilisateur / 1 = admin d''expo / 2 = su',
  `last_connection` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `password`, `firstname`, `lastname`, `mailaddress`, `is_admin`, `last_connection`) VALUES
(1, 'a0f0c542c42ad98bbfff8f9820b6d6da2bd9f79c', 'Admin', 'ADMIN', 'admin@example.com', 2, '2011-11-02 14:18:00');
