--
-- Table structure for table `sentence_comments`
--
-- This table stores the comments posted on sentences. Each comment can only be
-- associated to one sentence for now. In the future it could be useful to
-- associate a comment to several sentences. This would make more sense in case
-- a comment is made on how a translation is not very accurate or incorrect.
-- 
-- id          Id of the comment.
-- sentence_id Id of the sentence that is commented.
-- lang        Language of the comment.
-- text        Text of the comment. Using varbinary in order to support UTF-8
--               charaters encoded on 4 bytes.
-- user_id     Id of the user who posted the comment.
-- created     Date and time when the comment was posted.
-- modified    Date and time when the comment was last modified.
--

CREATE TABLE IF NOT EXISTS `sentence_comments` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `lang` varchar(4) default NULL,
  `text` blob NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2511 ;