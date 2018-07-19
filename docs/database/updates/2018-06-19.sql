ALTER TABLE sentences
  ADD `license` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  ADD `based_on_id` int(11) DEFAULT NULL;
