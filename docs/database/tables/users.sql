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
-- since             Date of registration.
-- last_time_active  Timestamp of the last time the user logged in.
-- last_contribution  Timestamp of the last time the user added a sentence.
-- level             Currently not in use. I wanted to integrate some game mechanics
--                     in Tatoeba, but it's obviously not easy...
-- group_id          Id of the group in which the user is.
-- send_notifiations Indicates whether the user wants to receive email notifications.
-- name              Real name of the user.
-- birthday          Birthday of the user.
-- description       User's description of himself or herself.
-- settings          User's settings serialized in JSON.
-- homepage          User's personal website.
-- image             User's profile picture.
-- country_id        Country in which the user lives.
-- audio_license     License of the user's audio recordings.
-- audio_attribution_url Attribution URL of the user's audio recordings.
--                     members of Tatoeba.
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(62) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `since` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL DEFAULT '0',
  `last_contribution` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(2) NOT NULL DEFAULT '0',
  `group_id` tinyint(4) NOT NULL,
  `send_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `birthday` datetime DEFAULT NULL,
  `description` blob NOT NULL,
  `settings` blob NOT NULL,
  `homepage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_id` varchar(2) DEFAULT NULL,
  `audio_license` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `audio_attribution_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
