--
-- Table structure for table `sentence_annotations`
--
-- This table currently stores the indices for Japanese sentences and is used only
-- by Jim Breen and his peers. It may have to be renamed into 'sentence_indices'
-- if someday we implement a feature for anyone to annotate sentences other than
-- through the comments.
--
-- id          Id of the annotation.
-- sentence_id Id of the sentence annotated.
-- meaning_id  Id of the sentence used to disambiguate the meaning of the annotated
--               sentence.
-- text        Text of the annotation.
--

DROP TABLE IF EXISTS `sentence_annotations`;
CREATE TABLE `sentence_annotations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sentence_id` int(11) NOT NULL,
  `meaning_id` int(11) NOT NULL,
  `text` varbinary(2000) NOT NULL,
  `modified` datetime NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sentence_id` (`sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
