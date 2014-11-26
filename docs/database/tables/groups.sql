--
-- Table structure for table `groups`
--
-- This table lists the various possible groups in Tatoeba. It was auto-generated
-- from CakePHP's 'bake' command.
-- For now, only three groups are actually in use: admin, user and pending_user.
-- 
-- name     Name of the group.
-- created  Date of creation of the group. This field is actually useless.
-- modified Date of modification of the group. Field useless too.
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;