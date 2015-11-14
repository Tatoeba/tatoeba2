DROP TABLE IF EXISTS `contributions_stats`;
CREATE TABLE `contributions_stats` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` DATE DEFAULT NULL ,
  `lang` VARCHAR(4) DEFAULT NULL,
  `sentences` INT(11) DEFAULT NULL,
  `action` ENUM('insert','update','delete') CHARACTER SET latin1 NOT NULL,
  `type` ENUM('link','sentence') CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


INSERT INTO contributions_stats(`date`, `lang`, `sentences`, `action`, `type`)
  SELECT date_format(`datetime`, "%Y-%m-%d") as `day`, NULL, COUNT(*), `action`, `type`
  FROM contributions
  WHERE type = 'sentence' AND action = 'insert'
  GROUP BY `day`;