--
-- Table structure for table `sentences`
--
-- This table stores the sentences of the corpus.
--
-- lang        Language of the sentence.
-- text        Text of the sentence. Using varbinary in order to support UTF-8
--               charaters encoded on 4 bytes.
-- correctness Indicates how reliable is the sentence. Currently not used.
-- user_id     Id of the owner of the sentence.
-- created     Date and time when the sentence was added.
-- modified    Date and time when the sentence was modified.
-- dico_id     Id of the sentence in the previous version of Tatoeba (v1). It was
--               kept here due to some dependancy with the Tanaka Corpus but it will
--               soon not be needed anymore.
--

DROP TABLE IF EXISTS `sentences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sentences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(4) DEFAULT NULL,
  `text` varbinary(1500) NOT NULL,
  `correctness` tinyint(2) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `dico_id` int(11) DEFAULT NULL,
  `lang_id` tinyint(3) unsigned DEFAULT NULL,
  `script` varchar(4) DEFAULT NULL,
  `hash` BINARY(16) NOT NULL,
  `license` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'CC BY 2.0 FR',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `dico_id` (`dico_id`),
  KEY `lang` (`lang`),
  KEY `modified_idx` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
