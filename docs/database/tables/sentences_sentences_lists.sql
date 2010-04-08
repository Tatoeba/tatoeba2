--
-- Table structure for table `sentences_sentences_lists`
--
-- This table indicates which sentences are the lists composed of.
--
-- sentences_list_id Id of the list.
-- sentence_id       Id of the sentence in that list.
--

CREATE TABLE IF NOT EXISTS `sentences_sentences_lists` (
  `sentences_list_id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  UNIQUE KEY `list_id` (`sentences_list_id`,`sentence_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;