
CREATE TABLE IF NOT EXISTS `tags_sentences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `user_id` int(11) default NULL,
  `sentence_id` int(11) default NULL,
  `added_time` datetime default NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`),
  KEY `sentence_id` (`sentence_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
