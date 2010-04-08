--
-- Table structure for table `langStats`
--
-- Table that stores the statisitics on the sentences. Used for optimization purpose.
--

CREATE TABLE IF NOT EXISTS `langStats` (
  `lang` varchar(4) collate utf8_unicode_ci default NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;