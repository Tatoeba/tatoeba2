CREATE TABLE `audios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `external` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users
  ADD `audio_license` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  ADD `audio_attribution_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

INSERT INTO audios
(sentence_id, created, modified)
SELECT
  id as sentence_id,
  0 as created,
  0 as modified
FROM sentences
WHERE hasaudio != 'no';

ALTER TABLE sentences DROP hasaudio;
