-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: tatoeba2
-- ------------------------------------------------------
-- Server version 5.5.16

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
-- Table structure for table `acos`
--

DROP TABLE IF EXISTS `acos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acos`
--

LOCK TABLES `acos` WRITE;
/*!40000 ALTER TABLE `acos` DISABLE KEYS */;
INSERT INTO `acos` VALUES (1,NULL,NULL,NULL,'controllers',1,162),
(2,1,NULL,NULL,'Favorites',2,7),
(3,2,NULL,NULL,'add_favorite',3,4),
(4,2,NULL,NULL,'remove_favorite',5,6),
(5,1,NULL,NULL,'Imports',8,13),
(6,5,NULL,NULL,'import_single_sentences',9,10),
(7,5,NULL,NULL,'import_sentences_with_translation',11,12),
(8,1,NULL,NULL,'Links',14,19),
(9,8,NULL,NULL,'add',15,16),
(10,8,NULL,NULL,'delete',17,18),
(11,1,NULL,NULL,'PrivateMessages',20,37),
(12,11,NULL,NULL,'index',21,22),
(13,11,NULL,NULL,'folder',23,24),
(14,11,NULL,NULL,'send',25,26),
(15,11,NULL,NULL,'show',27,28),
(16,11,NULL,NULL,'delete',29,30),
(17,11,NULL,NULL,'restore',31,32),
(18,11,NULL,NULL,'mark',33,34),
(19,11,NULL,NULL,'write',35,36),
(20,1,NULL,NULL,'SentenceAnnotations',38,51),
(21,20,NULL,NULL,'index',39,40),
(22,20,NULL,NULL,'show',41,42),
(23,20,NULL,NULL,'save',43,44),
(24,20,NULL,NULL,'delete',45,46),
(25,20,NULL,NULL,'search',47,48),
(26,20,NULL,NULL,'replace',49,50),
(27,1,NULL,NULL,'SentenceComments',52,63),
(28,27,NULL,NULL,'save',53,54),
(29,27,NULL,NULL,'edit',55,56),
(30,27,NULL,NULL,'delete_comment',57,58),
(31,27,NULL,NULL,'hide_message',59,60),
(32,27,NULL,NULL,'unhide_message',61,62),
(33,1,NULL,NULL,'Sentences',64,87),
(34,33,NULL,NULL,'add',65,66),
(35,33,NULL,NULL,'delete',67,68),
(36,33,NULL,NULL,'add_an_other_sentence',69,70),
(37,33,NULL,NULL,'edit_sentence',71,72),
(38,33,NULL,NULL,'adopt',73,74),
(39,33,NULL,NULL,'let_go',75,76),
(40,33,NULL,NULL,'save_translation',77,78),
(41,33,NULL,NULL,'change_language',79,80),
(42,33,NULL,NULL,'import',81,82),
(43,33,NULL,NULL,'edit_correctness',83,84),
(44,33,NULL,NULL,'edit_audio',85,86),
(45,1,NULL,NULL,'SentencesLists',88,105),
(46,45,NULL,NULL,'edit',89,90),
(47,45,NULL,NULL,'add',91,92),
(48,45,NULL,NULL,'save_name',93,94),
(49,45,NULL,NULL,'delete',95,96),
(50,45,NULL,NULL,'add_sentence_to_list',97,98),
(51,45,NULL,NULL,'remove_sentence_from_list',99,100),
(52,45,NULL,NULL,'add_new_sentence_to_list',101,102),
(53,45,NULL,NULL,'set_as_public',103,104),
(54,1,NULL,NULL,'Tags',106,115),
(55,54,NULL,NULL,'add_tag_post',107,108),
(56,54,NULL,NULL,'add_tag',109,110),
(57,54,NULL,NULL,'remove_tag_from_sentence',111,112),
(58,54,NULL,NULL,'remove_tag_of_sentence_from_tags_show',113,114),
(59,1,NULL,NULL,'User',116,133),
(60,59,NULL,NULL,'save_image',117,118),
(61,59,NULL,NULL,'save_description',119,120),
(62,59,NULL,NULL,'save_basic',121,122),
(63,59,NULL,NULL,'save_settings',123,124),
(64,59,NULL,NULL,'save_password',125,126),
(65,59,NULL,NULL,'edit_profile',127,128),
(66,59,NULL,NULL,'settings',129,130),
(67,59,NULL,NULL,'language',131,132),
(68,1,NULL,NULL,'Users',134,141),
(69,68,NULL,NULL,'index',135,136),
(70,68,NULL,NULL,'edit',137,138),
(71,68,NULL,NULL,'delete',139,140),
(72,1,NULL,NULL,'UsersLanguages',142,147),
(73,72,NULL,NULL,'save',143,144),
(74,72,NULL,NULL,'delete',145,146),
(75,1,NULL,NULL,'Wall',148,161),
(76,75,NULL,NULL,'save',149,150),
(77,75,NULL,NULL,'save_inside',151,152),
(78,75,NULL,NULL,'edit',153,154),
(79,75,NULL,NULL,'delete_message',155,156),
(80,75,NULL,NULL,'hide_message',157,158),
(81,75,NULL,NULL,'unhide_message',159,160);
/*!40000 ALTER TABLE `acos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-19 21:44:12
