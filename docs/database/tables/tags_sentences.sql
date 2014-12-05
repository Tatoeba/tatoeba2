--
-- TODO
--

DROP TABLE IF EXISTS `tags_sentences`;
CREATE TABLE `tags_sentences` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sentence_id` int(11) DEFAULT NULL,
  `added_time` datetime DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
