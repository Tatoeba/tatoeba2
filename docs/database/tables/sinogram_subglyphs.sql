--
-- Table structure for table `sinogram_subglyphs`
--
-- TODO for Allan
--

DROP TABLE IF EXISTS `sinogram_subglyphs`;
CREATE TABLE `sinogram_subglyphs` (
  `sinogram_id` int(11) NOT NULL,
  `glyph` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `subglyph` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
