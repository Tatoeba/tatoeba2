--
-- Table structure for table `users_vocabulary`
--
-- This table represents each user's vocabulary list.
--
-- id
-- user_id       ID of the user.
-- vocabulary_id ID of the vocabulary item.
-- created       Date when the vocabulary item was added.
--

DROP TABLE IF EXISTS `users_vocabulary`;
CREATE TABLE `users_vocabulary` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `hash` binary(16) NOT NULL,
  `vocabulary_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  UNIQUE KEY `user_vocabulary` (`user_id`,`vocabulary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;