-- 
-- This script will create the table langStats, which contains
-- the list of supported languages in Tatoeba as well as the
-- number of sentence in each language.
--

-- Deleting and creating the table again.
DROP TABLE IF EXISTS `langStats`;
CREATE TABLE `langStats` (
  `lang` varchar(4) CHARACTER SET utf8 NOT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Making sure that there's no entry in the "sentences" table
-- that has lang as an empty string.
UPDATE sentences SET lang = NULL WHERE lang = '';

-- Inserting the stats into langStats
INSERT INTO langStats (lang, numberOfSentences)
    SELECT lang , count(*) FROM sentences GROUP BY lang;
