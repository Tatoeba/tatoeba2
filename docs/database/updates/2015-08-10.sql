DROP TABLE IF EXISTS `users_sentences`;
CREATE TABLE `users_sentences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sentence_id` int(11) NOT NULL,
  `correctness` smallint(1) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `dirty` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_sentence` (`user_id`,`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;