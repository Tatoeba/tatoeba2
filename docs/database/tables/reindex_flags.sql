DROP TABLE IF EXISTS `reindex_flags`;
CREATE TABLE `reindex_flags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `lang` varchar(4) DEFAULT NULL,
  `indexed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_sentence_id` (`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
