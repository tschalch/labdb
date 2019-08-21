-- MySQL dump 10.13  Distrib 5.5.60, for Linux (x86_64)
--
-- Host: localhost    Database: lab_labdb
-- ------------------------------------------------------
-- Server version	5.5.60

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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1876 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=3169 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fragmentsinplasmids`
--

DROP TABLE IF EXISTS `fragmentsinplasmids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fragmentsinplasmids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plasmidID` int(11) NOT NULL,
  `fragmentID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=3000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=2084 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=40460 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1640 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proteins`
--

DROP TABLE IF EXISTS `proteins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proteins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `source` mediumtext NOT NULL,
  `glycerolStockID` int(11) NOT NULL,
  `purity` text NOT NULL,
  `concentration` text NOT NULL,
  `amount` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sequencing`
--

DROP TABLE IF EXISTS `sequencing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequencing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vialID` int(11) NOT NULL,
  `analysis` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sequencing-datafiles`
--

DROP TABLE IF EXISTS `sequencing-datafiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequencing-datafiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sequenceID` int(11) NOT NULL,
  `datafile` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=836 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tagID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`tagID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_map`
--

DROP TABLE IF EXISTS `tag_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_map` (
  `trackID` int(11) NOT NULL DEFAULT '0',
  `tagID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`trackID`,`tagID`),
  KEY `tag_fk` (`tagID`),
  KEY `track_fk` (`trackID`),
  CONSTRAINT `tag_fk` FOREIGN KEY (`tagID`) REFERENCES `tag` (`tagID`),
  CONSTRAINT `track_fk` FOREIGN KEY (`trackID`) REFERENCES `tracker` (`trackID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `trackBoxes`
--

DROP TABLE IF EXISTS `trackBoxes`;
/*!50001 DROP VIEW IF EXISTS `trackBoxes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `trackBoxes` (
  `id` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `location` tinyint NOT NULL,
  `tID` tinyint NOT NULL,
  `boxName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

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
  `project` int(11) NOT NULL,
  `subProject` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `permOwner` smallint(6) NOT NULL,
  `permGroup` smallint(6) NOT NULL,
  `permOthers` smallint(6) NOT NULL,
  PRIMARY KEY (`trackID`),
  UNIQUE KEY `sampleType` (`sampleType`,`sampleID`)
) ENGINE=InnoDB AUTO_INCREMENT=12534 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trackID` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  PRIMARY KEY (`ID`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `user` VALUES(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', '', 'default user with administrator privileges', 0);
INSERT INTO `user` VALUES(2, 'defaultGroup', '', 'Default Group', '', NULL, 1)

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
) ENGINE=InnoDB AUTO_INCREMENT=3089 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `trackBoxes`
--

/*!50001 DROP TABLE IF EXISTS `trackBoxes`*/;
/*!50001 DROP VIEW IF EXISTS `trackBoxes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `trackBoxes` AS select 1 AS `id`,1 AS `name`,1 AS `description`,1 AS `location`,1 AS `tID`,1 AS `boxName` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

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

-- Dump completed on 2019-08-21  1:22:26
