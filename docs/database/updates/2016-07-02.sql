DROP TABLE IF EXISTS `vocabulary`;
CREATE TABLE `vocabulary` (
  `id` binary(16) PRIMARY KEY,
  `lang` varchar(4) DEFAULT NULL,
  `text` varbinary(1500) NOT NULL,
  `numSentences` int(10) DEFAULT 0,
  `numAdded` int(10) DEFAULT 0,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users_vocabulary`;
CREATE TABLE `users_vocabulary` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `vocabulary_id` binary(16) NOT NULL,
  `created` datetime DEFAULT NULL,
  UNIQUE KEY `user_vocabulary` (`user_id`,`vocabulary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;