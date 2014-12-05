--
-- Table structure for table `countries`
--
-- This table lists the countries on the planet. It is used in the profile.
--
-- NOTE: Salem added it, I don't know where he took it from.
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` char(2) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;