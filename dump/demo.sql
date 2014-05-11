-- MySQL dump 10.14  Distrib 5.5.36-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: cms
-- ------------------------------------------------------
-- Server version	5.5.36-MariaDB-log

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
-- Table structure for table `layout`
--

DROP TABLE IF EXISTS `layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `structure` longtext COLLATE utf8_unicode_ci NOT NULL,
  `template` longtext COLLATE utf8_unicode_ci NOT NULL,
  `default_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `removed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier_version` (`identifier`,`version_id`),
  KEY `IDX_3A3A6BE24BBC2705` (`version_id`),
  CONSTRAINT `FK_3A3A6BE24BBC2705` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `layout`
--

LOCK TABLES `layout` WRITE;
/*!40000 ALTER TABLE `layout` DISABLE KEYS */;
INSERT INTO `layout` VALUES (1,1,'One column','[\r\n  [[{\"head\":12}]],\r\n  [[{\"header\":\"12\"}]],\r\n  [[{\"column\":\"12\"}]],\r\n  [[{\"footer\":\"12\"}]]\r\n]','<!DOCTYPE html>\r\n<html lang=\"en\">\r\n  <head>\r\n    <meta charset=\"utf-8\">\r\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\r\n    {{ head | raw }}\r\n\r\n    <!-- Bootstrap -->\r\n    <link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css\">\r\n\r\n    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\r\n    <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->\r\n    <!--[if lt IE 9]>\r\n      <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>\r\n      <script src=\"https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js\"></script>\r\n    <![endif]-->\r\n  </head>\r\n  <body>\r\n    <div class=\"container\">\r\n      <div class=\"jumbotron\">\r\n        {{ header | raw }}\r\n      </div>\r\n    </div>\r\n    <div class=\"container\">\r\n      {{ column | raw }}\r\n    </div>\r\n    <div id=\"footer\">\r\n      <div class=\"container\">\r\n        {{ footer | raw }}\r\n      </div>\r\n    </div>\r\n\r\n    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->\r\n    <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>\r\n    <script src=\"//code.jquery.com/ui/1.10.4/jquery-ui.js\"></script>\r\n    <!-- Include all compiled plugins (below), or include individual files as needed -->\r\n    <script src=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js\"></script>\r\n  </body>\r\n</html>','{}',0);
/*!40000 ALTER TABLE `layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `layout_identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `removed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier_version` (`identifier`,`version_id`),
  KEY `IDX_140AB6204BBC2705` (`version_id`),
  CONSTRAINT `FK_140AB6204BBC2705` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` VALUES (1,1,'home','One column','{\"head\":[{\"identifier\":\"headtitle\",\"vars\":{\"title\":\"My home page\"}}],\"header\":[{\"identifier\":\"title\",\"vars\":{\"level\":\"1\",\"content\":\"Demo of VCM\"}},{\"identifier\":\"paragraph\",\"vars\":{\"content\":\"This is a demo of VCM.\"}}],\"column\":[{\"identifier\":\"title\",\"vars\":{\"level\":\"2\",\"content\":\"Welcome in my website!\"}},{\"identifier\":\"paragraph\",\"vars\":{\"content\":\"This website use VCM a small and easy to use versioned content management system based on symfony 2 project. See more on <a href=\\\"https://github.com/madef/MadefCms\\\">my github </a>!\"}}],\"footer\":[{\"identifier\":\"poweredby\",\"vars\":[]}]}',0);
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `current` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `published_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BF1CD3C3772E836A` (`identifier`),
  UNIQUE KEY `UNIQ_BF1CD3C3D1B862B8` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `version`
--

LOCK TABLES `version` WRITE;
/*!40000 ALTER TABLE `version` DISABLE KEYS */;
INSERT INTO `version` VALUES (1,'1 - initial version','072b2e1073a1f625',1,'2014-05-11 14:33:19','2014-05-11 16:09:57');
/*!40000 ALTER TABLE `version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `form` longtext COLLATE utf8_unicode_ci NOT NULL,
  `template` longtext COLLATE utf8_unicode_ci NOT NULL,
  `default_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `removed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier_version` (`identifier`,`version_id`),
  KEY `IDX_85F91ED04BBC2705` (`version_id`),
  CONSTRAINT `FK_85F91ED04BBC2705` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget`
--

LOCK TABLES `widget` WRITE;
/*!40000 ALTER TABLE `widget` DISABLE KEYS */;
INSERT INTO `widget` VALUES (1,1,'headtitle','[{\"title\": \"text\"}]','<title>{{ content }}</title>','{\"title\": \"Hello world!\"}',0),(2,1,'title','[{\"level\":\"text\"}, {\"content\":\"text\"}]','<h{{ level}}>{{ content }}</h{{ level}}>','{\"level\":1, \"content\": \"Hello world!\"}',0),(3,1,'paragraph','[{\"content\": \"textarea\"}]','<p>{{ content | raw }}</p>','{\"content\": \"Hello world!\"}',0),(4,1,'poweredby','[]','<p>Powered by VCM</p>','{}',0);
/*!40000 ALTER TABLE `widget` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-11 16:14:26
