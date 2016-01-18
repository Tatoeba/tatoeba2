--
-- Table structure for table `wall`
--
-- TODO for Allan
--

DROP TABLE IF EXISTS `wall`;
CREATE TABLE `wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;