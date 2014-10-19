CREATE TABLE `transcriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `script` varchar(4) NOT NULL, /* ISO 15924 code */
  /* Transcriptions for japanese tend to take 300 to 400% the size of the
     original (1500) using the [kanji|reading] format + tokenization spaces.
     So that’s about 5000, and I double it because I suck at estimating. */
  `text` varbinary(10000) NOT NULL,
  `dirty` BOOLEAN NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  CONSTRAINT `unique_transcriptions` UNIQUE (`sentence_id`,`script`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
