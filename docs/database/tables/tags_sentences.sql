--
-- TODO
--

DROP TABLE IF EXISTS `tags_sentences`;
CREATE TABLE `tags_sentences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sentence_id` int(11) DEFAULT NULL,
  `added_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
