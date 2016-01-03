--
-- Table structure for table `sentences_lists`
--
-- Table that stores the lists of sentences.
--
-- is_public Status of the list (public means all the other members can edit it).
-- name      Name of the list.
-- user_id   Owner of the list.
-- created   Date when the list was created.
-- modified  Date when the list was modified. Note: this date doesn't update if the
--             content of the list changes, only if the name of the list or the
--             status of the list has changed.
--

DROP TABLE IF EXISTS `sentences_lists`;
CREATE TABLE `sentences_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `name` VARCHAR(450) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `numberOfSentences` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `visibility` enum('private', 'public') NOT NULL DEFAULT 'private',
  `editable_by` enum('creator', 'anyone') NOT NULL DEFAULT 'creator',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3961 DEFAULT CHARSET=latin1;