-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Dim 07 Avril 2013 à 14:47
-- Version du serveur: 5.5.16
-- Version de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `tatoeba_2`
--

-- --------------------------------------------------------

--
-- Structure de la table `aros`
--

DROP TABLE IF EXISTS `aros`;
CREATE TABLE `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Group', 1, 'group_admin', 1, 4),
(2, NULL, 'Group', 2, 'group_moderator', 5, 8),
(3, NULL, 'Group', 3, 'group_trusted_user', 9, 12),
(4, NULL, 'Group', 4, 'group_user', 13, 16),
(5, NULL, 'Group', 5, 'group_inactive', 17, 20),
(6, NULL, 'Group', 6, 'group_spammer', 21, 24),
(7, 1, 'User', 1, 'user_admin', 2, 3),
(8, 2, 'User', 2, 'user_moderator', 6, 7),
(9, 3, 'User', 3, 'user_trusted_user', 10, 11),
(10, 4, 'User', 4, 'user_user', 14, 15),
(11, 5, 'User', 5, 'user_inactive', 18, 19),
(12, 6, 'User', 6, 'user_spammer', 22, 23);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
