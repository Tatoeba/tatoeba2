--
-- Stores the stats about the number of sentences contributed.
--

DROP TABLE IF EXISTS `contributions_stats`;
CREATE TABLE `contributions_stats` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` DATE DEFAULT NULL ,
  `lang` VARCHAR(4) DEFAULT NULL,
  `sentences` INT(11) DEFAULT NULL,
  `action` ENUM('insert','update','delete') CHARACTER SET latin1 NOT NULL,
  `type` ENUM('link','sentence','license') CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;