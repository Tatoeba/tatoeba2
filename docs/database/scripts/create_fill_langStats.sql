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
  `code` varchar(4) CHARACTER SET utf8,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Making sure that there's no entry in the "sentences" table
-- that has lang as an empty string.
UPDATE sentences SET lang = NULL WHERE lang = '';

-- Inserting the stats into 'languages'
INSERT INTO languages (code, numberOfSentences)
    SELECT lang , count(*) FROM sentences GROUP BY lang;
    
-- We need to Update the sentences.lang_id, because re-creating the table will 
-- assign a different id to a code. For instance if the id for 'eng' was 11,
-- and we create again the table, then the new id for 'eng' could be 23. 
-- So the older sentences in the table sentences would have lang_id = 11 while 
-- the newer sentences would have lang_id = 23.
--
-- NOTE: It seems that this doesn't update properly the count for lang = NULL
-- I don't have time to debug that.
UPDATE sentences s JOIN languages ls ON s.lang = ls.lang SET s.lang_id = ls.id;