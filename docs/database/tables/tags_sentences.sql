
CREATE TABLE IF NOT EXISTS `tags_sentences` (
  `tag_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `sentence_id` int(11) default NULL,
  `added_time` datetime default NULL,
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`),
  KEY `sentence_id` (`sentence_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
