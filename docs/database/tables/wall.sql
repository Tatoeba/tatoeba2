--
-- Table structure for table `wall`
--
-- TODO for Allan
--

CREATE TABLE IF NOT EXISTS `wall` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL,
  `parent_id` int(11) default NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=437 ;