--
-- ATTENTION: if you only want to update the count of the sentences,
-- do NOT use this script, but the script:
--   update_number_of_sentences.sql
-- 
-- 
-- This script will create the table 'languages', which contains
-- the list of supported languages in Tatoeba as well as the
-- number of sentence in each language.
--

-- Deleting and creating the table again.
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) CHARACTER SET utf8 DEFAULT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Making sure that there's no entry in the "sentences" table
-- that has lang as an empty string.
UPDATE sentences SET lang = NULL WHERE lang = '';

-- Inserting the stats into 'languages'
INSERT INTO languages (code, numberOfSentences)
    SELECT lang , count(*) FROM sentences GROUP BY lang;
