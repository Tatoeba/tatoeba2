--
-- Table structure for table `contributions`
--
-- This table logs the contributions in Tatoeba. Everytime someone adds a new
-- sentences, translates a sentence, modify a sentence or deletes a sentence,
-- it should be saved in here.
-- 
-- sentences_id     Id of the sentence
-- sentences_lang   Language of the sentence
-- translation_id   Id of the translation, if it's a 'link' type.
-- translation_lang Language of the translation, if it's a 'link' type.
-- text             Text of the contribution, if it's a 'sentence' type.
--                    Using varbinary in order to support UTF-8 charaters encoded 
--                    on 4 bytes.
-- action           Action performed.
-- user_id          Id of the user who performed the action.
-- datetime         Date and time of the contribution.
-- ip               IP of the contributor.
-- type             Nature of the contribution.
--
-- Whenever a translation is added, what actually happens is:
--  1) A new sentences is added
--  2) Two links are added (A-->B and B-->A)
--
-- NOTE 1: It may be better to split this into two tables. One table that logs only
-- the sentences, and another that logs only the links.
-- NOTE 2: We currently don't log the modification of the language, nor the change of
-- owner. It is not critical, but it could be useful information.
--

DROP TABLE IF EXISTS `contributions`;
CREATE TABLE `contributions` (
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(4) DEFAULT NULL,
  `translation_id` int(11) DEFAULT NULL,
  `translation_lang` varchar(4) DEFAULT NULL,
  `script` varchar(4) DEFAULT NULL, /* ISO 15924 code */
  `text` varbinary(1500) NOT NULL,
  `action` enum('insert','update','delete') CHARACTER SET latin1 NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `type` enum('link','sentence','license') CHARACTER SET latin1 NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_desc` (`id`),
  KEY `sentence_id` (`sentence_id`),
  KEY `datetime` (`datetime`),
  KEY `user_id` (`user_id`),
  KEY `sentence_lang` (`sentence_lang`,`type`),
  KEY `translation_id_idx` (`translation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;