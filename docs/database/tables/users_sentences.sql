--
-- Table structure for table `users_sentences`
--
-- With this new table we will shift from a system where Tatoeba is one single corpus
-- towards a system where each user is building their own corpus within Tatoeba.
-- This table represents each user's corpus.
--
-- id
-- user_id     ID of the user.
-- sentence_id ID of the sentence.
-- correctness Evaluation of the correctness of the sentence by the user.
-- created
-- modified
-- dirty       Default false. Is set to true when the sentence has been edited.
--

DROP TABLE IF EXISTS `users_sentences`;
CREATE TABLE `users_sentences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  `correctness` smallint(1) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `dirty` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_sentence` (`user_id`,`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;