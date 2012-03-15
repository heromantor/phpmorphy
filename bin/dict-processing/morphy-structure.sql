-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: morphy
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4

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
-- Table structure for table `accents`
--

DROP TABLE IF EXISTS `accents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `form_no` int(11) NOT NULL,
  `accent_pos` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`form_no`),
  KEY `accents_FI_1` (`dict_id`),
  CONSTRAINT `accents_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ancodes`
--

DROP TABLE IF EXISTS `ancodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ancodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `pos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ancodes_FI_1` (`dict_id`) USING BTREE,
  KEY `ancodes_FI_2` (`pos_id`) USING BTREE,
  CONSTRAINT `ancodes_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ancodes_FK_2` FOREIGN KEY (`pos_id`) REFERENCES `poses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=722 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ancodes2grammems`
--

DROP TABLE IF EXISTS `ancodes2grammems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ancodes2grammems` (
  `ancode_id` int(11) NOT NULL,
  `grammem_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forward` (`ancode_id`,`grammem_id`) USING BTREE,
  UNIQUE KEY `reverse` (`grammem_id`,`ancode_id`) USING BTREE,
  CONSTRAINT `ancodes2grammems_FK_1` FOREIGN KEY (`ancode_id`) REFERENCES `ancodes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ancodes2grammems_FK_2` FOREIGN KEY (`grammem_id`) REFERENCES `grammems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2889 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dicts`
--

DROP TABLE IF EXISTS `dicts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dicts` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `locale` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flexias`
--

DROP TABLE IF EXISTS `flexias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flexias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `flexia_model_id` int(11) NOT NULL,
  `form_no` smallint(6) NOT NULL,
  `suffix` varchar(32) NOT NULL,
  `prefix` varchar(16) NOT NULL,
  `ancode_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flexiaModel_formNo` (`flexia_model_id`,`form_no`) USING BTREE,
  KEY `flexias_FI_1` (`dict_id`) USING BTREE,
  KEY `flexias_FI_2` (`ancode_id`) USING BTREE,
  CONSTRAINT `flexias_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flexias_FK_2` FOREIGN KEY (`ancode_id`) REFERENCES `ancodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=127502 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grammems`
--

DROP TABLE IF EXISTS `grammems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grammems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `grammem` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `grammems_FI_1` (`dict_id`) USING BTREE,
  CONSTRAINT `grammems_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lemmas`
--

DROP TABLE IF EXISTS `lemmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lemmas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `base_str` varchar(64) NOT NULL,
  `flexia_id` int(11) NOT NULL,
  `accent_id` int(11) DEFAULT NULL,
  `prefix_id` int(11) DEFAULT NULL,
  `common_ancode_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `base_str` (`base_str`) USING BTREE,
  KEY `lemmas_FI_1` (`dict_id`) USING BTREE,
  KEY `lemmas_FI_2` (`flexia_id`) USING BTREE,
  KEY `lemmas_FI_3` (`accent_id`) USING BTREE,
  KEY `lemmas_FI_4` (`prefix_id`) USING BTREE,
  KEY `lemmas_FI_5` (`common_ancode_id`) USING BTREE,
  CONSTRAINT `lemmas_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lemmas_FK_2` FOREIGN KEY (`flexia_id`) REFERENCES `flexias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lemmas_FK_3` FOREIGN KEY (`accent_id`) REFERENCES `accents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lemmas_FK_4` FOREIGN KEY (`prefix_id`) REFERENCES `prefixes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lemmas_FK_5` FOREIGN KEY (`common_ancode_id`) REFERENCES `ancodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=174786 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poses`
--

DROP TABLE IF EXISTS `poses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `is_predict` tinyint(4) NOT NULL,
  `pos` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poses_FI_1` (`dict_id`) USING BTREE,
  CONSTRAINT `poses_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefixes`
--

DROP TABLE IF EXISTS `prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prefixes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_id` smallint(6) NOT NULL,
  `prefix_no` smallint(6) NOT NULL,
  `prefix` varchar(16) NOT NULL,
  PRIMARY KEY (`id`,`prefix_no`),
  KEY `prefixes_FI_1` (`dict_id`) USING BTREE,
  CONSTRAINT `prefixes_FK_1` FOREIGN KEY (`dict_id`) REFERENCES `dicts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `a` int(11) NOT NULL AUTO_INCREMENT,
  `b` int(11) NOT NULL,
  PRIMARY KEY (`a`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `words` (
  `caption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `caption_length` tinyint(4) unsigned NOT NULL,
  `id` tinyint(4) unsigned NOT NULL,
  KEY `Index 1` (`caption`,`caption_length`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-07  3:57:35
