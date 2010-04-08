--
-- Table structure for table `groups`
--
-- This table lists the various possible groups in Tatoeba. It was auto-generated
-- from CakePHP's 'bake' command.
-- For now, only three groups are actually in use: admin, user and pending_user.
-- 
-- name     Name of the group.
-- created  Date of creation of the group. This field is actually useless.
-- modified Date of modification of the group. Field useless too.
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;