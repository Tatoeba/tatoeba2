--
-- Table structure for table `visitors`
--
-- Stores the ip of the last visitors. This gives an idea of how many "people"
-- (that could include bots) are using Tatoeba at a given time.
--
-- ip        IP of the visitor.
-- timestamp Time of the visit.
--

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE `visitors` (
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;