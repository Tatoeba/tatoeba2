--
-- Table structure for table `wall_threads_last_message`
--
-- TODO for Allan
--

CREATE TABLE IF NOT EXISTS `wall_threads_last_message` (
  `id` int(11) NOT NULL,
  `last_message_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;