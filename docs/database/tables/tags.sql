--
-- Table structure for table `tags`
--
-- This table stores the tags.
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `internal_name` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) ,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
