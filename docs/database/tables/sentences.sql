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

CREATE TABLE IF NOT EXISTS `sentences` (
  `id` int(11) NOT NULL auto_increment,
  `lang` varchar(4) default NULL,
  `text` varbinary(1500) NOT NULL,
  `correctness` smallint(2) default NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `dico_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `dico_id` (`dico_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=377096 ;