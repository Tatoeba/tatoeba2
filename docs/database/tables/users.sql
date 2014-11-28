--
-- Table structure for table `users`
--
-- This is the classical users table that stores information about the users. It
-- should probably be split though, so that the users table itself only contains
-- the necessary information for authentication and permissions.
-- The rest can go in 'user_profile','user_settings' and whatever else is needed.
-- 
-- id                Id of the user.
-- username          Username of the user.
-- password          Password of the user.
-- email             Email of the user.
-- lang              Language of the user. It's not necessarily their mother tongue,
--                     but it's a language in which they can communicate. This field
--                     was no more used since Tatoeba v2.
-- since             Date of registration.
-- last_time_active  Timestamp of the last time the user logged in.
-- level             Currently not in use. I wanted to integrate some game mechanics
--                     in Tatoeba, but it's obviously not easy...
-- group_id          Id of the group in which the user is.
-- send_notifiations Indicates whether the user wants to receive email notifications.
-- name              Real name of the user.
-- birthday          Birthday of the user.
-- description       User's description of himself or herself.
-- homepage          User's personal website.
-- image             User's profile picture.
-- country_id        Country in which the user lives.
-- is_public         Indicates whether the profile can be seen by other people than
--                     members of Tatoeba.
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(100) DEFAULT NULL,
  `since` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(2) NOT NULL DEFAULT '0',
  `group_id` tinyint(4) NOT NULL,
  `send_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `birthday` datetime DEFAULT NULL,
  `description` blob NOT NULL,
  `homepage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_id` varchar(2) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;