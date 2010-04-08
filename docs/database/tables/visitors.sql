--
-- Table structure for table `visitors`
--
-- Stores the ip of the last visitors. This gives an idea of how many "people"
-- (that could include bots) are using Tatoeba at a given time.
--
-- ip        IP of the visitor.
-- timestamp Time of the visit.
--

CREATE TABLE IF NOT EXISTS `visitors` (
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `timestamp` int(11) NOT NULL default '0',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;