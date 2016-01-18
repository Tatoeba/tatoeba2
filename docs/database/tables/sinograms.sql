--
-- Table structure for table `sinograms`
--

DROP TABLE IF EXISTS `sinograms`;
CREATE TABLE `sinograms` (
  `id` int(11) NOT NULL,
  `utf` varchar(8) collate utf8_unicode_ci NOT NULL COMMENT 'code utf8 du caractere',
  `glyph` char(10) collate utf8_unicode_ci NOT NULL COMMENT 'caractère en lui-même',
  `strokes` tinyint(3) unsigned default NULL COMMENT 'nombre de traits composant le caractère',
  `english` text collate utf8_unicode_ci COMMENT 'traduction du caractère',
  `chin-trad` char(10) collate utf8_unicode_ci default NULL COMMENT 'équivalent traditionel du caractère',
  `chin-simpl` char(10) collate utf8_unicode_ci default NULL COMMENT 'équivalent simplifié du caractère',
  `chin-pinyin` varchar(255) character set latin1 default NULL COMMENT 'pinyin (chinois) du caractère',
  `jap-on` varchar(255) character set latin1 default NULL COMMENT 'prononciation On du caractère',
  `jap-kun` varchar(255) character set latin1 default NULL COMMENT 'prononciation Kun du caractère',
  `frequency` double NOT NULL default '0' COMMENT 'fréquence du caractère',
  `checked` tinyint(1) NOT NULL,
  `subcharacterslist` varchar(255) collate utf8_unicode_ci default NULL,
  `usedByList` varchar(255) collate utf8_unicode_ci default NULL,
  KEY `glyph_index` (`glyph`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
