--
-- Table structure for table `sentences_sentences_lists`
--
-- This table indicates which sentences are the lists composed of.
--
-- sentences_list_id Id of the list.
-- sentence_id       Id of the sentence in that list.
--

DROP TABLE IF EXISTS `sentences_sentences_lists`;
CREATE TABLE `sentences_sentences_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentences_list_id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list_id` (`sentences_list_id`,`sentence_id`),
  KEY `sentences_list_id` (`sentences_list_id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
