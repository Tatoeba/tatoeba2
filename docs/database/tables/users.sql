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

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
  `password` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `lang` varchar(4) default NULL,
  `since` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL default '0',
  `level` tinyint(4) NOT NULL default '1',
  `group_id` tinyint(4) NOT NULL,
  `send_notifications` tinyint(1) NOT NULL default '1',
  `name` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `birthday` timestamp NOT NULL default '0000-00-00 00:00:00',
  `description` blob NOT NULL,
  `homepage` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `image` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `country_id` char(2) character set utf8 collate utf8_unicode_ci NOT NULL default '0',
  `is_public` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1357 ;