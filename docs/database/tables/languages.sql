--
-- Table structure for table `languages`
--
-- Table that lists the supported languages in Tatoeba and stores the statisitics on
-- the number of sentences per language.
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` smallint(3) NOT NULL DEFAULT '0',
  `code` varchar(4) CHARACTER SET utf8 DEFAULT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;