--
-- Table structure for table `private_messages`
--
-- This table stores the private messages.
-- TODO for Etienne
--

DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE `private_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recpt` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `folder` enum('Inbox','Sent','Trash','Drafts') CHARACTER SET utf8 NOT NULL DEFAULT 'Inbox',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `isnonread` tinyint(4) NOT NULL DEFAULT '1',
  `draft_recpts` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `sent` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_recpt` (`recpt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
