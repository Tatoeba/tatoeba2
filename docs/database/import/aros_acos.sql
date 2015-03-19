-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: tatoeba2
-- ------------------------------------------------------
-- Server version   5.5.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL DEFAULT '0',
  `_read` varchar(2) NOT NULL DEFAULT '0',
  `_update` varchar(2) NOT NULL DEFAULT '0',
  `_delete` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aros_acos`
--

LOCK TABLES `aros_acos` WRITE;
/*!40000 ALTER TABLE `aros_acos` DISABLE KEYS */;
INSERT INTO `aros_acos` VALUES (1,2,1,'1','1','1','1'),
(2,3,2,'1','1','1','1'),
(3,3,8,'1','1','1','1'),
(4,3,11,'1','1','1','1'),
(5,3,20,'1','1','1','1'),
(6,3,27,'1','1','1','1'),
(7,3,31,'-1','-1','-1','-1'),
(8,3,32,'-1','-1','-1','-1'),
(9,3,33,'1','1','1','1'),
(10,3,43,'-1','-1','-1','-1'),
(11,3,44,'-1','-1','-1','-1'),
(12,3,45,'1','1','1','1'),
(13,3,54,'1','1','1','1'),
(14,3,59,'1','1','1','1'),
(15,3,68,'1','1','1','1'),
(16,3,69,'-1','-1','-1','-1'),
(17,3,72,'1','1','1','1'),
(18,3,75,'1','1','1','1'),
(19,3,80,'-1','-1','-1','-1'),
(20,3,81,'-1','-1','-1','-1'),
(21,4,2,'1','1','1','1'),
(22,4,8,'1','1','1','1'),
(23,4,11,'1','1','1','1'),
(24,4,20,'1','1','1','1'),
(25,4,27,'1','1','1','1'),
(26,4,31,'-1','-1','-1','-1'),
(27,4,32,'-1','-1','-1','-1'),
(28,4,33,'1','1','1','1'),
(29,4,43,'-1','-1','-1','-1'),
(30,4,44,'-1','-1','-1','-1'),
(31,4,45,'1','1','1','1'),
(32,4,54,'1','1','1','1'),
(33,4,59,'1','1','1','1'),
(34,4,68,'1','1','1','1'),
(35,4,69,'-1','-1','-1','-1'),
(36,4,72,'1','1','1','1'),
(37,4,75,'1','1','1','1'),
(38,4,80,'-1','-1','-1','-1'),
(39,4,81,'-1','-1','-1','-1'),
(40,5,2,'1','1','1','1'),
(41,5,11,'1','1','1','1'),
(42,5,20,'1','1','1','1'),
(43,5,27,'1','1','1','1'),
(44,5,31,'-1','-1','-1','-1'),
(45,5,32,'-1','-1','-1','-1'),
(46,5,33,'1','1','1','1'),
(47,5,43,'-1','-1','-1','-1'),
(48,5,44,'-1','-1','-1','-1'),
(49,5,45,'1','1','1','1'),
(50,5,59,'1','1','1','1'),
(51,5,68,'1','1','1','1'),
(52,5,69,'-1','-1','-1','-1'),
(53,5,72,'1','1','1','1'),
(54,5,75,'1','1','1','1'),
(55,5,80,'-1','-1','-1','-1'),
(56,5,81,'-1','-1','-1','-1');
/*!40000 ALTER TABLE `aros_acos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-19 21:44:07
