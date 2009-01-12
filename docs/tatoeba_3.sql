-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2008 at 08:21 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;


INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES 
(1, NULL, 'Group', 1, NULL, 1, 2),
(2, NULL, 'Group', 2, NULL, 3, 4),
(3, NULL, 'Group', 3, NULL, 5, 6),
(4, NULL, 'Group', 4, NULL, 7, 8),
(5, NULL, 'Group', 5, NULL, 9, 10);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dico`
--

CREATE TABLE IF NOT EXISTS `dico` (
  `id` int(11) NOT NULL default '0',
  `lang` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `sentence` text collate utf8_unicode_ci NOT NULL,
  `romanization` text collate utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `original` tinyint(1) NOT NULL default '0',
  `sentence_id` int(11) default NULL,
  PRIMARY KEY  (`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;


INSERT INTO `groups` (`id`, `name`, `created`, `modified`) VALUES 
(1, 'admin', '2009-01-11 21:23:50', '2009-01-11 21:23:50'),
(2, 'moderator', '2009-01-11 21:24:03', '2009-01-11 21:24:03'),
(3, 'trusted_user', '2009-01-11 21:24:13', '2009-01-11 21:24:13'),
(4, 'user', '2009-01-11 21:24:22', '2009-01-11 21:24:22'),
(5, 'pending_user', '2009-01-11 21:24:29', '2009-01-11 21:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `sentences`
--

CREATE TABLE IF NOT EXISTS `sentences` (
  `id` int(11) NOT NULL auto_increment,
  `lang` varchar(2) default NULL,
  `text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `correctness` smallint(2) default NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `dico_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentence_comments`
--

CREATE TABLE IF NOT EXISTS `sentence_comments` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `lang` varchar(2) collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `last_edit_datetime` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `sentence_logs`
--

CREATE TABLE IF NOT EXISTS `sentence_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `sentence_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `action` enum('insert','update','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentences_translations`
--

CREATE TABLE IF NOT EXISTS `sentences_translations` (
  `sentence_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `distance` smallint(2) NOT NULL default '1',
  UNIQUE KEY `sentence_id` (`sentence_id`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `suggested_modifications`
--

CREATE TABLE IF NOT EXISTS `suggested_modifications` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `correction_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `submit_user_id` int(11) default NULL,
  `submit_datetime` datetime NOT NULL,
  `apply_user_id` int(11) NOT NULL,
  `apply_datetime` datetime NOT NULL,
  `was_applied` tinyint(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translation_logs`
--

CREATE TABLE IF NOT EXISTS `translation_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `translation_id` int(11) NOT NULL,
  `translation_lang` varchar(2) default NULL,
  `action` enum('insert','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE IF NOT EXISTS `visitors` (
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `timestamp` int(11) NOT NULL default '0',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `contributions`
--
DROP VIEW IF EXISTS `contributions`;

CREATE VIEW `contributions` AS
SELECT `sentence_id`
 , `sentence_lang`
 , `translation_id`
 , `translation_lang`
 , '' as `text`
 , `action`
 , `user_id`
 , `datetime` FROM `translation_logs` 
UNION 
SELECT `sentence_id`
 , `sentence_lang`
 , ''
 , ''
 , `sentence_text`
 , `action`
 , `user_id`
 , `datetime` FROM `sentence_logs`
UNION
SELECT `sentence_id`
 , `sentence_lang`
 , ''
 , ''
 , `correction_text`
 , 'suggest'
 , `submit_user_id`
 , `submit_datetime` FROM `suggested_modifications`
ORDER BY `datetime` DESC;


-- --------------------------------------------------------

--
-- Structure for view `users_statistics`
--
DROP VIEW IF EXISTS `users_statistics`;

CREATE VIEW `users_statistics` AS
SELECT user_id, COUNT(*) as quantity, action, translation_id != '' as is_translation
FROM contributions
GROUP BY user_id, action, translation_id = '';