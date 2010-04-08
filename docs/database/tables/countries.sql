--
-- Table structure for table `countries`
--
-- This table lists the countries on the planet. It is used in the profile.
--
-- NOTE: Salem added it, I don't know where he took it from.
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` char(2) NOT NULL,
  `iso3` char(3) default NULL,
  `numcode` smallint(6) default NULL,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;