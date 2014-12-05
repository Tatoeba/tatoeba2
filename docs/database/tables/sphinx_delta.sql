--
-- TODO
--

DROP TABLE IF EXISTS `sphinx_delta`;
CREATE TABLE `sphinx_delta` (
  `lang_id` int(11) NOT NULL,
  `index_start_date` datetime DEFAULT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;