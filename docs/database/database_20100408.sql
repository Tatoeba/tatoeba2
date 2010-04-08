-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 08, 2010 at 06:29 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tatoeba_4`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=239 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1339 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=90 ;

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE IF NOT EXISTS `contributions` (
  `id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(4) default NULL,
  `translation_id` int(11) default NULL,
  `translation_lang` varchar(4) default NULL,
  `text` varbinary(1500) NOT NULL,
  `action` enum('insert','update','delete') character set latin1 NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) character set latin1 default NULL,
  `type` enum('link','sentence') character set latin1 NOT NULL,
  KEY `sentence_id` (`sentence_id`),
  KEY `datetime` (`datetime`),
  KEY `user_id` (`user_id`),
  KEY `sentence_lang` (`sentence_lang`,`type`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` char(2) NOT NULL,
  `iso3` char(3) default NULL,
  `numcode` smallint(6) default NULL,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `favorites_users`
--

CREATE TABLE IF NOT EXISTS `favorites_users` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `favorite_id` (`favorite_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `followers_users`
--

CREATE TABLE IF NOT EXISTS `followers_users` (
  `follower_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `follower_id` (`follower_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `langStats`
--

CREATE TABLE IF NOT EXISTS `langStats` (
  `lang` varchar(4) collate utf8_unicode_ci default NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE IF NOT EXISTS `private_messages` (
  `id` int(11) NOT NULL auto_increment,
  `recpt` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `date` datetime NOT NULL,
  `folder` enum('Inbox','Sent','Trash') character set utf8 NOT NULL default 'Inbox',
  `title` varchar(255) character set utf8 NOT NULL,
  `content` text character set utf8 NOT NULL,
  `isnonread` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `idx_recpt` (`recpt`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=315 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentences`
--

CREATE TABLE IF NOT EXISTS `sentences` (
  `id` int(11) NOT NULL auto_increment,
  `lang` varchar(4) default NULL,
  `text` varbinary(1500) NOT NULL,
  `correctness` smallint(2) default NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `dico_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `dico_id` (`dico_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=377096 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentences_lists`
--

CREATE TABLE IF NOT EXISTS `sentences_lists` (
  `id` int(11) NOT NULL auto_increment,
  `is_public` tinyint(1) NOT NULL default '0',
  `name` varbinary(450) NOT NULL,
  `user_id` int(11) default NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentences_sentences_lists`
--

CREATE TABLE IF NOT EXISTS `sentences_sentences_lists` (
  `sentences_list_id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  UNIQUE KEY `list_id` (`sentences_list_id`,`sentence_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sentences_translations`
--

CREATE TABLE IF NOT EXISTS `sentences_translations` (
  `sentence_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `sentence_lang` varchar(4) default NULL,
  `translation_lang` varchar(4) default NULL,
  `distance` smallint(2) NOT NULL default '1',
  UNIQUE KEY `sentence_id` (`sentence_id`,`translation_id`),
  KEY `sentence_id_2` (`sentence_id`),
  KEY `translation_id` (`translation_id`),
  KEY `sentence_lang` (`sentence_lang`),
  KEY `translation_lang` (`translation_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sentence_annotations`
--

CREATE TABLE IF NOT EXISTS `sentence_annotations` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `meaning_id` int(11) NOT NULL,
  `dico_id` int(11) NOT NULL,
  `text` varbinary(2000) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `dico_id` (`dico_id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150418 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentence_comments`
--

CREATE TABLE IF NOT EXISTS `sentence_comments` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `lang` varchar(4) default NULL,
  `text` blob NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2511 ;

-- --------------------------------------------------------

--
-- Table structure for table `sinograms`
--

CREATE TABLE IF NOT EXISTS `sinograms` (
  `id` int(11) NOT NULL,
  `utf` varchar(8) collate utf8_unicode_ci NOT NULL COMMENT 'code utf8 du caractere',
  `glyph` char(10) collate utf8_unicode_ci NOT NULL COMMENT 'caractère en lui même',
  `strokes` tinyint(3) unsigned default NULL COMMENT 'nombre de trait composant le caractère',
  `english` text collate utf8_unicode_ci COMMENT 'traduction du caractère',
  `chin-trad` char(10) collate utf8_unicode_ci default NULL COMMENT 'équivalent traditionelle du caractère',
  `chin-simpl` char(10) collate utf8_unicode_ci default NULL COMMENT 'équivalent simplifié du caractère',
  `chin-pinyin` varchar(255) character set latin1 default NULL COMMENT 'pinyin (chinois) du caractère',
  `jap-on` varchar(255) character set latin1 default NULL COMMENT 'prononciation On du caractère',
  `jap-kun` varchar(255) character set latin1 default NULL COMMENT 'prononciation Kun du caractère',
  `frequency` double NOT NULL default '0' COMMENT 'frequence du caractère',
  `checked` tinyint(1) NOT NULL,
  `subcharacterslist` varchar(255) collate utf8_unicode_ci default NULL,
  `usedByList` varchar(255) collate utf8_unicode_ci default NULL,
  KEY `glyph_index` (`glyph`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sinogram_subglyphs`
--

CREATE TABLE IF NOT EXISTS `sinogram_subglyphs` (
  `sinogram_id` int(11) NOT NULL,
  `glyph` varchar(2) character set utf8 collate utf8_unicode_ci default NULL,
  `subglyph` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
  `password` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `lang` varchar(4) default NULL,
  `since` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL default '0',
  `level` tinyint(4) NOT NULL default '1',
  `group_id` tinyint(4) NOT NULL,
  `send_notifications` tinyint(1) NOT NULL default '1',
  `name` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `birthday` timestamp NOT NULL default '0000-00-00 00:00:00',
  `description` blob NOT NULL,
  `homepage` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `image` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `country_id` char(2) character set utf8 collate utf8_unicode_ci NOT NULL default '0',
  `is_public` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1357 ;

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
-- Table structure for table `wall`
--

CREATE TABLE IF NOT EXISTS `wall` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL,
  `parent_id` int(11) default NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=437 ;

-- --------------------------------------------------------

--
-- Table structure for table `wall_threads_last_message`
--

CREATE TABLE IF NOT EXISTS `wall_threads_last_message` (
  `id` int(11) NOT NULL,
  `last_message_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
