--
-- Table structure for table `sentences_translations`
-- 
-- This table indicates which sentence is translation of which other sentence.
--
-- sentence_id      Id of the "original" sentence. 
-- translation_id   Id of the translation. 
-- sentence_lang    Language of the "original" sentence. Note that is is redundant
--                    information, but it is necessary for the search engine.
-- translation_lang Language of the translation.
-- distance         Not in use. This could however be used in the future. Sometimes
--                    there are several ways to translate a sentence. In such cases,
--                    the distance will indicate how close a translation is
--                    compared to the other ones.
--

DROP TABLE IF EXISTS `sentences_translations`;
CREATE TABLE `sentences_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `sentence_lang` varchar(4) DEFAULT NULL,
  `translation_lang` varchar(4) DEFAULT NULL,
  `distance` smallint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sentence_id` (`sentence_id`,`translation_id`),
  KEY `translation_id` (`translation_id`),
  KEY `sentence_lang` (`sentence_lang`),
  KEY `translation_lang` (`translation_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;