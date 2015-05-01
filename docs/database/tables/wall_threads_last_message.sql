--
-- Table structure for table `wall_threads_last_message`
--
-- TODO for Allan
--

DROP TABLE IF EXISTS `wall_threads_last_message`;
CREATE TABLE `wall_threads_last_message` (
  `id` int(11) NOT NULL,
  `last_message_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;