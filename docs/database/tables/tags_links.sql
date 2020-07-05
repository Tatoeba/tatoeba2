--
-- Table structure for table `tags_links`
--
-- This table stores the tags relations.
--

DROP TABLE IF EXISTS `tags_links`;
CREATE TABLE `tags_links` (
    `parent` int(11) NOT NULL,
    `child` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `added_time` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`parent`, `child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
