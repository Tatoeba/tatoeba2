SET SESSION autocommit = 0;
SET SESSION unique_checks = 0;
SET SESSION foreign_key_checks = 0;

SET @_general_log = @@global.general_log;
SET @_general_log_file = @@global.general_log_file;
SET @@global.general_log = 1;
SET @@global.general_log_file = "/tmp/tatoeba_import.log";
COMMIT;

-- Sentences
DELETE FROM sentences;
LOAD DATA INFILE "/tmp/sentences.csv"
	INTO TABLE sentences
	FIELDS TERMINATED BY '\t'
	LINES TERMINATED BY '\n'
	(id, lang, text);
COMMIT;

-- Links
DELETE FROM sentences_translations;
-- LOAD DATA INFILE "/tmp/links.csv"
-- 	INTO TABLE sentences_translations
-- 	FIELDS TERMINATED BY '\t'
-- 	LINES TERMINATED BY '\n'
-- 	(@id1, @id2)
-- 	SET sentence_id = @id1, translation_id = @id2, 
-- 	sentence_lang = (SELECT lang FROM sentences WHERE id = @id1),
-- 	translation_lang = (SELECT lang FROM sentences WHERE id = @id2);
LOAD DATA INFILE "/tmp/links.csv"
	INTO TABLE sentences_translations
	FIELDS TERMINATED BY '\t'
	LINES TERMINATED BY '\n'
	(sentence_id, translation_id);
COMMIT;

-- Tags
DELETE FROM tags;
LOAD DATA INFILE "/tmp/tag_metadata.csv"
	INTO TABLE tags
	FIELDS TERMINATED BY '\t'
	LINES TERMINATED BY '\n'
	(id, @name, user_id, created)
	SET internal_name = lower(replace(@name, ' ', '_')), name = @name;
COMMIT;

DELETE FROM tags_sentences;
LOAD DATA INFILE "/tmp/tags_detailed.csv"
	INTO TABLE tags_sentences
	FIELDS TERMINATED BY '\t'
	LINES TERMINATED BY '\n'
	(tag_id, sentence_id, user_id, added_time);
COMMIT;

-- Deleting and creating the table again.
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

-- Making sure that there's no entry in the "sentences" table
-- that has lang as an empty string.
UPDATE sentences SET lang = NULL WHERE lang = '';

-- Inserting the stats into languages table
INSERT INTO languages (code, sentences)
    SELECT lang , count(*) FROM sentences GROUP BY lang;

SET @@global.general_log = @_general_log;
SET @@global.general_log_file = @_general_log_file;
