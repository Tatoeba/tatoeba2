--
-- Table structure for table `audios`
--

-- Contains all the audio recordings associated
-- to the sentences using the sentence_id foreign key.
-- See also app/model/audio.php

DROP TABLE IF EXISTS `audios`;
CREATE TABLE `audios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL, -- we use either user_id or author to identify the contributor of the recording
  `licence_id` int(4) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
