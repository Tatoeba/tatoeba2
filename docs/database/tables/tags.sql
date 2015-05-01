--
-- Table structure for table `tags`
--
-- This table stores the tags.
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `nbrOfSentences` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `nbr_sentences_idx` (`nbrOfSentences`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
