-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 22, 2008 at 06:12 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `tatoeba_3`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default NULL,
  `foreign_key` int(10) default NULL,
  `alias` varchar(255) default NULL,
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, NULL, NULL, 'controllers', 1, 72),
(2, 1, NULL, NULL, 'Pages', 2, 5),
(3, 2, NULL, NULL, 'display', 3, 4),
(4, 1, NULL, NULL, 'Groups', 6, 17),
(5, 4, NULL, NULL, 'index', 7, 8),
(6, 4, NULL, NULL, 'view', 9, 10),
(7, 4, NULL, NULL, 'add', 11, 12),
(8, 4, NULL, NULL, 'edit', 13, 14),
(9, 4, NULL, NULL, 'delete', 15, 16),
(10, 1, NULL, NULL, 'Sentences', 18, 33),
(11, 10, NULL, NULL, 'index', 19, 20),
(12, 10, NULL, NULL, 'show', 21, 22),
(13, 10, NULL, NULL, 'add', 23, 24),
(14, 10, NULL, NULL, 'delete', 25, 26),
(15, 10, NULL, NULL, 'edit', 27, 28),
(16, 10, NULL, NULL, 'translate', 29, 30),
(17, 10, NULL, NULL, 'save_translation', 31, 32),
(18, 1, NULL, NULL, 'Translations', 34, 35),
(19, 1, NULL, NULL, 'Users', 36, 53),
(20, 19, NULL, NULL, 'index', 37, 38),
(21, 19, NULL, NULL, 'view', 39, 40),
(22, 19, NULL, NULL, 'add', 41, 42),
(23, 19, NULL, NULL, 'edit', 43, 44),
(24, 19, NULL, NULL, 'delete', 45, 46),
(25, 19, NULL, NULL, 'login', 47, 48),
(26, 19, NULL, NULL, 'logout', 49, 50),
(27, 19, NULL, NULL, 'initDB', 51, 52),
(28, 1, NULL, NULL, 'SuggestedModifications', 54, 67),
(29, 28, NULL, NULL, 'index', 55, 56),
(30, 28, NULL, NULL, 'view', 57, 58),
(31, 28, NULL, NULL, 'add', 59, 60),
(32, 28, NULL, NULL, 'edit', 61, 62),
(33, 28, NULL, NULL, 'delete', 63, 64),
(34, 28, NULL, NULL, 'save_suggestion', 65, 66),
(35, 1, NULL, NULL, 'LatestActivities', 68, 71),
(36, 35, NULL, NULL, 'index', 69, 70);

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default NULL,
  `foreign_key` int(10) default NULL,
  `alias` varchar(255) default NULL,
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Group', 1, NULL, 1, 6),
(2, NULL, 'Group', 2, NULL, 7, 12),
(3, NULL, 'Group', 3, NULL, 13, 22),
(4, 1, 'User', 1, NULL, 2, 3),
(5, 2, 'User', 2, NULL, 8, 9),
(6, 3, 'User', 3, NULL, 14, 15),
(7, 1, 'User', 1, NULL, 4, 5),
(8, 2, 'User', 2, NULL, 10, 11),
(9, 3, 'User', 3, NULL, 16, 17),
(10, NULL, 'Group', 4, NULL, 23, 26),
(11, 10, 'User', 4, NULL, 24, 25),
(13, 3, 'User', 6, NULL, 18, 19),
(14, 3, 'User', 7, NULL, 20, 21),
(15, NULL, 'Group', 1, NULL, 27, 28),
(16, NULL, 'Group', 2, NULL, 29, 30),
(17, NULL, 'Group', 3, NULL, 31, 40),
(18, NULL, 'Group', 4, NULL, 41, 42),
(19, 17, 'User', 1, NULL, 32, 33),
(20, 17, 'User', 2, NULL, 34, 35),
(21, 17, 'User', 3, NULL, 36, 37),
(22, 17, 'User', 4, NULL, 38, 39);

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) NOT NULL auto_increment,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL default '0',
  `_read` varchar(2) NOT NULL default '0',
  `_update` varchar(2) NOT NULL default '0',
  `_delete` varchar(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 1, 1, '1', '1', '1', '1'),
(2, 2, 1, '-1', '-1', '-1', '-1'),
(3, 2, 10, '1', '1', '1', '1'),
(4, 3, 1, '-1', '-1', '-1', '-1'),
(5, 3, 12, '1', '1', '1', '1'),
(6, 3, 16, '1', '1', '1', '1'),
(7, 15, 1, '1', '1', '1', '1'),
(8, 16, 1, '-1', '-1', '-1', '-1'),
(9, 16, 10, '1', '1', '1', '1'),
(10, 16, 28, '1', '1', '1', '1'),
(11, 17, 1, '-1', '-1', '-1', '-1'),
(12, 17, 10, '1', '1', '1', '1'),
(13, 18, 1, '-1', '-1', '-1', '-1');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created`, `modified`) VALUES
(1, 'admin', '2008-11-22 17:45:45', '2008-11-22 17:45:45'),
(2, 'moderator', '2008-11-22 17:45:53', '2008-11-22 17:45:53'),
(3, 'trusted_user', '2008-11-22 17:46:01', '2008-11-22 17:46:01'),
(4, 'user', '2008-11-22 17:46:08', '2008-11-22 17:46:08');

-- --------------------------------------------------------

--
-- Stand-in structure for view `latest_activities`
--
CREATE TABLE IF NOT EXISTS `latest_activities` (
`sentence_id` int(11)
,`sentence_lang` varchar(2)
,`translation_id` varbinary(11)
,`translation_lang` varchar(2)
,`text` varchar(500)
,`action` varchar(7)
,`user_id` int(11)
,`datetime` datetime
);
-- --------------------------------------------------------

--
-- Table structure for table `sentences`
--

CREATE TABLE IF NOT EXISTS `sentences` (
  `id` int(11) NOT NULL auto_increment,
  `lang` varchar(2) NOT NULL,
  `text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `correctness` smallint(2) default NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sentences`
--

INSERT INTO `sentences` (`id`, `lang`, `text`, `correctness`, `user_id`, `created`, `modified`) VALUES
(1, 'fr', 'Ceci est une phrase ajoutée par un utilisateur anonyme.', 1, NULL, '2008-11-22 18:07:07', '2008-11-22 18:07:07'),
(2, 'fr', 'Ceci est une phrase ajoutée par un admin.', 5, NULL, '2008-11-22 18:09:59', '2008-11-22 18:09:59'),
(3, 'fr', 'Ceci est une phrase ajoutée par un modérateur.', 4, NULL, '2008-11-22 18:10:28', '2008-11-22 18:10:28'),
(4, 'fr', 'Ceci est une phrase ajoutée par un trusted user.', 3, NULL, '2008-11-22 18:10:54', '2008-11-22 18:10:54'),
(5, 'fr', 'Ceci est une phrase ajoutée par un simple user.', 2, NULL, '2008-11-22 18:11:32', '2008-11-22 18:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `sentences_translations`
--

CREATE TABLE IF NOT EXISTS `sentences_translations` (
  `sentence_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `correctness` smallint(2) NOT NULL,
  UNIQUE KEY `sentence_id` (`sentence_id`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sentences_translations`
--


-- --------------------------------------------------------

--
-- Table structure for table `sentence_logs`
--

CREATE TABLE IF NOT EXISTS `sentence_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) NOT NULL,
  `sentence_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `action` enum('insert','update','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sentence_logs`
--

INSERT INTO `sentence_logs` (`id`, `sentence_id`, `sentence_lang`, `sentence_text`, `action`, `user_id`, `datetime`) VALUES
(1, 1, 'fr', 'Ceci est une phrase ajoutée par un utilisateur anonyme.', 'insert', NULL, '2008-11-22 18:07:07'),
(2, 2, 'fr', 'Ceci est une phrase ajoutée par un admin.', 'insert', 1, '2008-11-22 18:09:59'),
(3, 3, 'fr', 'Ceci est une phrase ajoutée par un modérateur.', 'insert', 2, '2008-11-22 18:10:28'),
(4, 4, 'fr', 'Ceci est une phrase ajoutée par un trusted user.', 'insert', 3, '2008-11-22 18:10:54'),
(5, 5, 'fr', 'Ceci est une phrase ajoutée par un simple user.', 'insert', 4, '2008-11-22 18:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `suggested_modifications`
--

CREATE TABLE IF NOT EXISTS `suggested_modifications` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) NOT NULL,
  `correction_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `submit_user_id` int(11) default NULL,
  `submit_datetime` datetime NOT NULL,
  `apply_user_id` int(11) NOT NULL,
  `apply_datetime` datetime NOT NULL,
  `was_applied` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `suggested_modifications`
--


-- --------------------------------------------------------

--
-- Table structure for table `translation_logs`
--

CREATE TABLE IF NOT EXISTS `translation_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `translation_lang` varchar(2) NOT NULL,
  `translation_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `action` enum('insert','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `translation_logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) collate utf8_unicode_ci NOT NULL,
  `password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `lang` varchar(2) collate utf8_unicode_ci NOT NULL default 'en',
  `since` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL default '0',
  `level` tinyint(4) NOT NULL default '1',
  `group_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `lang`, `since`, `last_time_active`, `level`, `group_id`) VALUES
(1, 'trang', '14390ccca0d51157846829eb5b9831f9', 'tranglich@gmail.com', 'fr', '2008-11-22 17:46:50', 0, 1, 1),
(2, 'trang1', '14390ccca0d51157846829eb5b9831f9', 'tranglich@hotmail.com', 'en', '2008-11-22 17:47:08', 0, 1, 2),
(3, 'trang2', '14390ccca0d51157846829eb5b9831f9', 'hognocph@etu.utc.fr', 'fr', '2008-11-22 17:47:29', 0, 1, 3),
(4, 'trang3', '14390ccca0d51157846829eb5b9831f9', 'trang@babbel.com', 'fr', '2008-11-22 17:47:50', 0, 1, 4);

-- --------------------------------------------------------

--
-- Structure for view `latest_activities`
--
DROP TABLE IF EXISTS `latest_activities`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tatoeba_3`.`latest_activities` AS select `tatoeba_3`.`translation_logs`.`sentence_id` AS `sentence_id`,`tatoeba_3`.`translation_logs`.`sentence_lang` AS `sentence_lang`,`tatoeba_3`.`translation_logs`.`translation_id` AS `translation_id`,`tatoeba_3`.`translation_logs`.`translation_lang` AS `translation_lang`,`tatoeba_3`.`translation_logs`.`translation_text` AS `text`,`tatoeba_3`.`translation_logs`.`action` AS `action`,`tatoeba_3`.`translation_logs`.`user_id` AS `user_id`,`tatoeba_3`.`translation_logs`.`datetime` AS `datetime` from `tatoeba_3`.`translation_logs` union select `tatoeba_3`.`sentence_logs`.`sentence_id` AS `sentence_id`,`tatoeba_3`.`sentence_logs`.`sentence_lang` AS `sentence_lang`,_utf8'' AS ``,_utf8'' AS ``,`tatoeba_3`.`sentence_logs`.`sentence_text` AS `sentence_text`,`tatoeba_3`.`sentence_logs`.`action` AS `action`,`tatoeba_3`.`sentence_logs`.`user_id` AS `user_id`,`tatoeba_3`.`sentence_logs`.`datetime` AS `datetime` from `tatoeba_3`.`sentence_logs` union select `tatoeba_3`.`suggested_modifications`.`sentence_id` AS `sentence_id`,`tatoeba_3`.`suggested_modifications`.`sentence_lang` AS `sentence_lang`,_utf8'' AS ``,_utf8'' AS ``,`tatoeba_3`.`suggested_modifications`.`correction_text` AS `correction_text`,_utf8'suggest' AS `suggest`,`tatoeba_3`.`suggested_modifications`.`submit_user_id` AS `submit_user_id`,`tatoeba_3`.`suggested_modifications`.`submit_datetime` AS `submit_datetime` from `tatoeba_3`.`suggested_modifications` order by `datetime`;
