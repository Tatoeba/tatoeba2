--
-- Table structure for table `langStats`
--
-- Table that stores the statisitics on the sentences. Used for optimization purpose.
--

CREATE TABLE IF NOT EXISTS `langStats` (
  `lang` varchar(4) CHARACTER SET utf8 NOT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;