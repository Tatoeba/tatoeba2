--
-- Table structure for table `languages`
--
-- Table that lists the supported languages in Tatoeba and stores the statisitics on
-- the number of sentences per language.
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) CHARACTER SET utf8 DEFAULT NULL,
  `sentences` INT(10) unsigned NOT NULL DEFAULT '0',
  `audio` INT(10) NOT NULL DEFAULT 0,
  `group_1` TINYINT(2) NOT NULL DEFAULT 0,
  `group_2` SMALLINT(3) NOT NULL DEFAULT 0,
  `group_3` SMALLINT(4) NOT NULL DEFAULT 0,
  `group_4` INT(10) NOT NULL DEFAULT 0,
  `level_0` INT(10) NOT NULL DEFAULT 0,
  `level_1` INT(10) NOT NULL DEFAULT 0,
  `level_2` INT(10) NOT NULL DEFAULT 0,
  `level_3` INT(10) NOT NULL DEFAULT 0,
  `level_4` INT(10) NOT NULL DEFAULT 0,
  `level_5` INT(10) NOT NULL DEFAULT 0,
  `level_unknown` INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;