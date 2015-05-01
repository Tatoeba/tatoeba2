DROP TABLE IF EXISTS `users_languages`;
CREATE TABLE `users_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `of_user_id` int(11) NOT NULL,
  `by_user_id` int(11) NOT NULL,
  `language_code` varchar(4) NOT NULL,
  `level` tinyint(2) DEFAULT '0',
  `level_approval_status` enum('approved','pending','unapproved') NOT NULL DEFAULT 'pending',
  `details` blob NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_lang` (`of_user_id`,`by_user_id`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;