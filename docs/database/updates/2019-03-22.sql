ALTER TABLE audios ADD `lang` varchar(4) CHARACTER SET utf8 DEFAULT NULL;
UPDATE audios, sentences SET audios.lang = sentences.lang WHERE audios.sentence_id = sentences.id;