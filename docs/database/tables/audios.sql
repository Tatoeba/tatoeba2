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
  `external` varchar(500) DEFAULT NULL, -- we use either `user_id` or store metadata as JSON in `external`
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
