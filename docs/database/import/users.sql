-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Dim 07 Avril 2013 à 12:19
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
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(62) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `since` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(2) NOT NULL DEFAULT '0',
  `group_id` tinyint(4) NOT NULL,
  `send_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `birthday` datetime DEFAULT NULL,
  `description` blob NOT NULL,
  `settings` blob NOT NULL,
  `homepage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_id` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `since`, `last_time_active`, `level`, `group_id`, `send_notifications`, `name`, `birthday`, `description`, `settings`, `homepage`, `image`, `country_id`) VALUES

(1, 'admin', '1 $2a$10$ze.r64Pv1E.CTHUNFpFkVuqxuYlbj00aqqeRDd.kumCTIVtkZ/Q8C', 'admin@fakemail.com', '2013-04-07 12:15:16', 0, 1, 1, 1, '', NULL, '', '', '', '', NULL),
(2, 'corpus_maintainer', '1 $2a$10$HiIXBuvKbZkPOJ3NNegnyO0tEXzI8Q7/.elZyicSbx3b4Q9y3WsRS', 'corpus_maintainer@fakemail.com', '2013-04-07 12:15:50', 0, 1, 2, 1, '', NULL, '', '', '', '', NULL),
(3, 'advanced_contributor', '1 $2a$10$3jg3N9/ig6LTRumZPGPBiexEXFQ/5egPjBh3X/tuuU5R5H2kRX5em', 'advanced_contributor@fakemail.com', '2013-04-07 12:16:37', 0, 1, 3, 1, '', NULL, '', '', '', '', NULL),
(4, 'contributor', '1 $2a$10$lGq/QSz3nwHsDAHubtPC7ebT2dnpVTtcSWCV1LJ/vhOwNuJp3Jxay', 'contributor@fakemail.com', '2013-04-07 12:17:02', 0, 1, 4, 1, '', NULL, '', '', '', '', NULL),
(5, 'inactive', '1 $2a$10$vCE03mwxDuuhISbs9mRnTekuwqPrnTfLjHLoH0zGBg53JE1Z3hl/q', 'inactive@fakemail.com', '2013-04-07 12:17:29', 0, 1, 5, 1, '', NULL, '', '', '', '', NULL),
(6, 'spammer', '1 $2a$10$x68Xtpx56iQubb0LHZN/3ez8auzxe3EnOj8GfY7Xn2DIKjpJIBEry', 'spammer@fakemail.com', '2013-04-07 12:17:54', 0, 1, 6, 1, '', NULL, '', '', '', '', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
