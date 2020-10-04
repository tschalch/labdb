-- MySQL dump 10.13  Distrib 5.5.62, for Linux (x86_64)
--
-- Host: localhost    Database: dev_labdb
-- ------------------------------------------------------
-- Server version	5.5.62

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
-- Table structure for table `boxes`
--

DROP TABLE IF EXISTS `boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boxes` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boxes`
--

LOCK TABLES `boxes` WRITE;
/*!40000 ALTER TABLE `boxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `boxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connections`
--

DROP TABLE IF EXISTS `connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connections` (
  `connID` int(11) NOT NULL AUTO_INCREMENT,
  `record` int(11) NOT NULL,
  `belongsTo` int(11) NOT NULL,
  `fragName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` int(11) NOT NULL COMMENT 'start position on plasmid in bp',
  `end` int(11) NOT NULL COMMENT 'end position on plasimd in bp',
  `direction` int(11) NOT NULL COMMENT 'direction on plasmid. fw is 1, rev is 0',
  PRIMARY KEY (`connID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connections`
--

LOCK TABLES `connections` WRITE;
/*!40000 ALTER TABLE `connections` DISABLE KEYS */;
/*!40000 ALTER TABLE `connections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crystals`
--

DROP TABLE IF EXISTS `crystals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crystals` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crystals`
--

LOCK TABLES `crystals` WRITE;
/*!40000 ALTER TABLE `crystals` DISABLE KEYS */;
/*!40000 ALTER TABLE `crystals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crystalsdev`
--

DROP TABLE IF EXISTS `crystalsdev`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crystalsdev` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crystalsdev`
--

LOCK TABLES `crystalsdev` WRITE;
/*!40000 ALTER TABLE `crystalsdev` DISABLE KEYS */;
/*!40000 ALTER TABLE `crystalsdev` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(7) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `resource` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_resource` (`resource`),
  KEY `FK_user` (`user`),
  CONSTRAINT `FK_user` FOREIGN KEY (`user`) REFERENCES `user` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fragments`
--

DROP TABLE IF EXISTS `fragments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fragments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reaction` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `organism` text NOT NULL,
  `description` mediumtext NOT NULL,
  `DNASequence` mediumtext NOT NULL,
  `proteinSequence` mediumtext NOT NULL,
  `link` mediumtext NOT NULL,
  `type` text NOT NULL,
  `PCRoligo1` int(11) NOT NULL,
  `PCRoligo2` int(11) NOT NULL,
  `PCRtemplate` int(11) NOT NULL,
  `PCRremarks` mediumtext NOT NULL,
  `resistance` text NOT NULL,
  `origin` text NOT NULL,
  `attachment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fragments`
--

LOCK TABLES `fragments` WRITE;
/*!40000 ALTER TABLE `fragments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fragments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `belongsToGroup` int(11) NOT NULL,
  `defaultPermissions` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,1,1,2),(2,1,2,1);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `www` text COLLATE utf8_unicode_ci,
  `hazards` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `files` text COLLATE utf8_unicode_ci,
  `location` int(11) NOT NULL,
  `orderNumber` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `unitMeas` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `orderDate` date NOT NULL,
  `received` date NOT NULL,
  `supplier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manufacturer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `casNumber` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `funding` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL COMMENT 'used for log, 0=regular item, 1=instrument, 2=column',
  `billed` date DEFAULT '0000-00-00',
  `poNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemstatus`
--

DROP TABLE IF EXISTS `itemstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemstatus` (
  `statusNr` int(11) NOT NULL,
  `statusName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`statusNr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemstatus`
--

LOCK TABLES `itemstatus` WRITE;
/*!40000 ALTER TABLE `itemstatus` DISABLE KEYS */;
INSERT INTO `itemstatus` VALUES (0,'under consideration'),(1,'to be ordered'),(2,'order placed'),(3,'in stock'),(4,'finished');
/*!40000 ALTER TABLE `itemstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logbook`
--

DROP TABLE IF EXISTS `logbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instrumentID` int(11) NOT NULL,
  `columnID` int(11) NOT NULL,
  `sample` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `buffer` text COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `bypresbef` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bypresaf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `colpresbef` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `colpresaf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `storage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logbook`
--

LOCK TABLES `logbook` WRITE;
/*!40000 ALTER TABLE `logbook` DISABLE KEYS */;
/*!40000 ALTER TABLE `logbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newpermissions`
--

DROP TABLE IF EXISTS `newpermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newpermissions` (
  `trackID` int(11) NOT NULL DEFAULT '0',
  `1` int(1) NOT NULL DEFAULT '0',
  `2` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newpermissions`
--

LOCK TABLES `newpermissions` WRITE;
/*!40000 ALTER TABLE `newpermissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `newpermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oligos`
--

DROP TABLE IF EXISTS `oligos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oligos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` mediumtext NOT NULL,
  `sequence` mediumtext NOT NULL,
  `targetmatch` text NOT NULL,
  `tm` float NOT NULL,
  `PCRconc` float NOT NULL,
  `Saltconc` float NOT NULL,
  `supplier` text NOT NULL,
  `bpPrice` float NOT NULL,
  `scale` text NOT NULL,
  `modifications` mediumtext NOT NULL,
  `purity` text NOT NULL,
  `concentration` text NOT NULL,
  `orderDate` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oligos`
--

LOCK TABLES `oligos` WRITE;
/*!40000 ALTER TABLE `oligos` DISABLE KEYS */;
/*!40000 ALTER TABLE `oligos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `trackID` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL,
  `permission` smallint(6) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracknUser` (`trackID`,`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plasmids`
--

DROP TABLE IF EXISTS `plasmids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plasmids` (
  `id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier',
  `name` varchar(50) NOT NULL COMMENT 'clone name',
  `description` mediumtext NOT NULL,
  `resistance` varchar(64) DEFAULT NULL,
  `generation` mediumtext NOT NULL,
  `sequence` mediumtext NOT NULL,
  `enzymes` text NOT NULL COMMENT 'comma separated list of restriction enzyme names (emboss restrict syntax)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plasmids`
--

LOCK TABLES `plasmids` WRITE;
/*!40000 ALTER TABLE `plasmids` DISABLE KEYS */;
/*!40000 ALTER TABLE `plasmids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sampletypes`
--

DROP TABLE IF EXISTS `sampletypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sampletypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `st_code` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `st_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `plural` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `form` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `table` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `list` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `isSample` tinyint(1) NOT NULL DEFAULT '1',
  `labPermission` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sampletypes`
--

LOCK TABLES `sampletypes` WRITE;
/*!40000 ALTER TABLE `sampletypes` DISABLE KEYS */;
INSERT INTO `sampletypes` VALUES 
  (1,'P','Plasmid','Plasmids','frmPlasmids','plasmids','listPlasmids',1,0),
  (2,'O','Oligo','Oligos','frmOligo','oligos','listOligo',1,0),
  (3,'B','Protein','Proteins','frmProteins','proteins','',1,0),
  (4,'S','Strain','Strains','frmStrain','strains','listStrains',1,0),
  (6,NULL,'Vial','Vials','frmVial','vials','listVials',0,0),
  (7,'B','Fragment','Fragments','frmGene','fragments','listGene',1,0),
  (8,NULL,'Box','Boxes','frmBoxes','boxes','listBoxes',0,0),
  (9,NULL,'Project','Projects','frmProject','projects','listProjects',0,0),
  (10,'TSL','item','Items','frmItem','inventory','listItems',0,2),
  (11,NULL,'location','locations','frmLocations','locations','listLocations',0,2),
  (12,NULL,'logbook entry','logbook entries','frmLog','logbook','listLogbook',0,2), 
  (13,NULL, 'Resource', 'Resources', 'frmResource', 'resources', 'listResources', '0', '2');
/*!40000 ALTER TABLE `sampletypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strains`
--

DROP TABLE IF EXISTS `strains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `strains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `organism` varchar(255) NOT NULL,
  `strain` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strains`
--

LOCK TABLES `strains` WRITE;
/*!40000 ALTER TABLE `strains` DISABLE KEYS */;
/*!40000 ALTER TABLE `strains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `trackboxes`
--

DROP TABLE IF EXISTS `trackboxes`;
/*!50001 DROP VIEW IF EXISTS `trackboxes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `trackboxes` (
  `id` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `location` tinyint NOT NULL,
  `tID` tinyint NOT NULL,
  `boxName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tracker`
--

DROP TABLE IF EXISTS `tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracker` (
  `trackID` int(11) NOT NULL AUTO_INCREMENT,
  `sampleType` int(11) NOT NULL,
  `sampleID` int(11) NOT NULL,
  `created` date NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` date NOT NULL,
  `project` int(11),
  `subProject` int(11),
  `owner` int(11) NOT NULL,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `permOwner` smallint(6) NOT NULL,
  `permGroup` smallint(6) NOT NULL,
  `permOthers` smallint(6) NOT NULL,
  PRIMARY KEY (`trackID`),
  UNIQUE KEY `sampleType` (`sampleType`,`sampleID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracker`
--

LOCK TABLES `tracker` WRITE;
/*!40000 ALTER TABLE `tracker` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `tracklocations`
--

DROP TABLE IF EXISTS `tracklocations`;
/*!50001 DROP VIEW IF EXISTS `tracklocations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tracklocations` (
  `tID` tinyint NOT NULL,
  `locationName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `groupType` tinyint(4) NOT NULL COMMENT '0 is user, 1 is labgroup, 2 is projectgroup, 3 for administrator groups',
  `color` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','','','default user with administrator privileges',3,'red'),(2,'default_group','l1232f297a57a5a743894a0e4a801fc3','','','default group',2,'');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vials`
--

DROP TABLE IF EXISTS `vials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `concentration` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `creator` varchar(255) NOT NULL,
  `sID` int(11) NOT NULL COMMENT 'trackerID of vial content',
  `boxID` int(11) NOT NULL,
  `position` varchar(25) NOT NULL,
  `exists` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vials`
--

LOCK TABLES `vials` WRITE;
/*!40000 ALTER TABLE `vials` DISABLE KEYS */;
/*!40000 ALTER TABLE `vials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `trackboxes`
--

/*!50001 DROP TABLE IF EXISTS `trackboxes`*/;
/*!50001 DROP VIEW IF EXISTS `trackboxes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `trackboxes` AS select `boxes`.`id` AS `id`,`boxes`.`name` AS `name`,`boxes`.`description` AS `description`,`boxes`.`location` AS `location`,`tracker`.`trackID` AS `tID`,`boxes`.`name` AS `boxName` from (`boxes` join `tracker` on(((`boxes`.`id` = `tracker`.`sampleID`) and (`tracker`.`sampleType` = 8)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tracklocations`
--

/*!50001 DROP TABLE IF EXISTS `tracklocations`*/;
/*!50001 DROP VIEW IF EXISTS `tracklocations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tracklocations` AS select `tracker`.`trackID` AS `tID`,`locations`.`name` AS `locationName` from (`locations` join `tracker` on(((`locations`.`id` = `tracker`.`sampleID`) and (`tracker`.`sampleType` = 11)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-02 13:22:23
