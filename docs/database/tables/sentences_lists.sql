--
-- Table structure for table `sentences_lists`
--
-- Table that stores the lists of sentences.
--
-- is_public Status of the list (public means all the other members can edit it).
-- name      Name of the list.
-- user_id   Owner of the list.
-- created   Date when the list was created.
-- modified  Date when the list was modified. Note: this date doesn't update if the
--             content of the list changes, only if the name of the list or the
--             status of the list has changed.
--

CREATE TABLE IF NOT EXISTS `sentences_lists` (
  `id` int(11) NOT NULL auto_increment,
  `is_public` tinyint(1) NOT NULL default '0',
  `name` varchar(150) character set utf8 collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;