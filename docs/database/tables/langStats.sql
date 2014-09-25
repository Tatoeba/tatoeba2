--
-- Table structure for table `languages`
--
-- Table that lists the supported languages in Tatoeba and stores the statisitics on
-- the number of sentences per language.
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) CHARACTER SET utf8 NOT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;