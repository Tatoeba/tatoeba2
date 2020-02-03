--
-- Table structure for table `transcriptions`
--

-- Contains all the transcriptions and alternative scripts associated
-- to the sentences using the sentence_id foreign key.
-- See also app/model/transcriptions.php

DROP TABLE IF EXISTS `transcriptions`;
CREATE TABLE `transcriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `script` varchar(4) NOT NULL,
  `text` varbinary(10000) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `needsReview` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_transcriptions` (`sentence_id`,`script`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
