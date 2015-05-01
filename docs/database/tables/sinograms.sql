--
-- Table structure for table `sinograms`
--
-- TODO for Allan
--

DROP TABLE IF EXISTS `sinograms`;
CREATE TABLE `sinograms` (
  `id` int(11) NOT NULL,
  `utf` varchar(8) COLLATE utf8_unicode_ci NOT NULL COMMENT 'code utf8 du caractere',
  `glyph` char(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'caractère en lui même',
  `strokes` tinyint(3) unsigned DEFAULT NULL COMMENT 'nombre de trait composant le caractère',
  `english` text COLLATE utf8_unicode_ci COMMENT 'traduction du caractère',
  `chin-trad` char(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'équivalent traditionelle du caractère',
  `chin-simpl` char(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'équivalent simplifié du caractère',
  `chin-pinyin` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'pinyin (chinois) du caractère',
  `jap-on` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'prononciation On du caractère',
  `jap-kun` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'prononciation Kun du caractère',
  `frequency` double NOT NULL DEFAULT '0' COMMENT 'frequence du caractère',
  `checked` tinyint(1) NOT NULL,
  `subcharacterslist` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usedByList` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `glyph_index` (`glyph`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;