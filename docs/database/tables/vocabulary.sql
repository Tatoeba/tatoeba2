--
-- Table structure for table `vocabulary`
--
-- This table stores the vocabulary items of users.
--
-- id           Contains the hash value of (lang + text).
-- lang         Language of the vocabulary item.
-- text         Text of the vocabulary item. Using varbinary in order to support UTF-8
--                characters encoded on 4 bytes.
-- numSentences Number of search results for the vocabulary item.
-- numAdded     Number of vocabulary lists where the item was added.
-- created      Date and time when the vocabulary item was added.
--

DROP TABLE IF EXISTS `vocabulary`;
CREATE TABLE `vocabulary` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `hash` binary(16) NOT NULL,
  `lang` varchar(4) DEFAULT NULL,
  `text` varbinary(1500) NOT NULL,
  `numSentences` int(10) DEFAULT 0,
  `numAdded` int(10) DEFAULT 0,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
