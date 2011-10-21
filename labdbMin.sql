-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 21, 2011 at 02:12 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `labdbtest`
--

-- --------------------------------------------------------

--
-- Table structure for table `boxes`
--

CREATE TABLE `boxes` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `boxes`
--


-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `connID` int(11) NOT NULL AUTO_INCREMENT,
  `record` int(11) NOT NULL,
  `belongsTo` int(11) NOT NULL,
  `fragName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` int(11) NOT NULL COMMENT 'start position on plasmid in bp',
  `end` int(11) NOT NULL COMMENT 'end position on plasimd in bp',
  `direction` int(11) NOT NULL COMMENT 'direction on plasmid. fw is 1, rev is 0',
  PRIMARY KEY (`connID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `connections`
--


-- --------------------------------------------------------

--
-- Table structure for table `crystals`
--

CREATE TABLE `crystals` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `crystals`
--


-- --------------------------------------------------------

--
-- Table structure for table `fragments`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fragments`
--


-- --------------------------------------------------------

--
-- Table structure for table `fragmentsinplasmids`
--

CREATE TABLE `fragmentsinplasmids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plasmidID` int(11) NOT NULL,
  `fragmentID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fragmentsinplasmids`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `belongsToGroup` int(11) NOT NULL,
  `defaultPermissions` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `inventory`
--


-- --------------------------------------------------------

--
-- Table structure for table `itemstatus`
--

CREATE TABLE `itemstatus` (
  `statusNr` int(11) NOT NULL,
  `statusName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`statusNr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `itemstatus`
--


-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `locations`
--


-- --------------------------------------------------------

--
-- Table structure for table `logbook`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logbook`
--


-- --------------------------------------------------------

--
-- Table structure for table `oligos`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oligos`
--


-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `trackID` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL,
  `permission` smallint(6) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracknUser` (`trackID`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `permissions`
--


-- --------------------------------------------------------

--
-- Table structure for table `plasmids`
--

CREATE TABLE `plasmids` (
  `id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier',
  `name` varchar(50) NOT NULL COMMENT 'clone name',
  `description` mediumtext NOT NULL,
  `generation` mediumtext NOT NULL,
  `sequence` mediumtext NOT NULL,
  `created` date NOT NULL COMMENT 'created on',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'last changed',
  `enzymes` text NOT NULL COMMENT 'comma separated list of restriction enzyme names (emboss restrict syntax)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `plasmids`
--


-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `proteins`
--

CREATE TABLE `proteins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `source` mediumtext NOT NULL,
  `glycerolStockID` int(11) NOT NULL,
  `purity` text NOT NULL,
  `concentration` text NOT NULL,
  `amount` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `proteins`
--


-- --------------------------------------------------------

--
-- Table structure for table `sampletypes`
--

CREATE TABLE `sampletypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `st_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `plural` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `form` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `table` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `list` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `isSample` tinyint(1) NOT NULL DEFAULT '1',
  `labPermission` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `sampletypes`
--

INSERT INTO `sampletypes` VALUES(1, 'Plasmid', 'Plasmids', 'frmPlasmids', 'plasmids', 'listPlasmids', 1, 0);
INSERT INTO `sampletypes` VALUES(2, 'Oligo', 'Oligos', 'frmOligo', 'oligos', 'listOligo', 1, 0);
INSERT INTO `sampletypes` VALUES(3, 'Protein', 'Proteins', 'frmProteins', 'proteins', '', 1, 0);
INSERT INTO `sampletypes` VALUES(4, 'Glycerolstock', 'Glycerolstocks', 'frmStrain', 'strains', 'listStrains', 1, 0);
INSERT INTO `sampletypes` VALUES(6, 'Vial', 'Vials', 'frmVial', 'vials', 'listVials', 0, 0);
INSERT INTO `sampletypes` VALUES(7, 'Fragment', 'Fragments', 'frmGene', 'fragments', 'listGene', 1, 0);
INSERT INTO `sampletypes` VALUES(8, 'Box', 'Boxes', 'frmBoxes', 'boxes', 'listBoxes', 0, 0);
INSERT INTO `sampletypes` VALUES(9, 'Project', 'Projects', 'frmProject', 'projects', 'listProjects', 0, 0);
INSERT INTO `sampletypes` VALUES(10, 'item', 'Items', 'frmItem', 'inventory', 'listItems', 0, 2);
INSERT INTO `sampletypes` VALUES(11, 'location', 'locations', 'frmLocations', 'locations', 'listLocations', 0, 2);
INSERT INTO `sampletypes` VALUES(12, 'logbook entry', 'logbook entries', 'frmLog', 'logbook', 'listLogbook', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sequencing`
--

CREATE TABLE `sequencing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vialID` int(11) NOT NULL,
  `analysis` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sequencing`
--


-- --------------------------------------------------------

--
-- Table structure for table `sequencing-datafiles`
--

CREATE TABLE `sequencing-datafiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sequenceID` int(11) NOT NULL,
  `datafile` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sequencing-datafiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `strains`
--

CREATE TABLE `strains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `organism` varchar(255) NOT NULL,
  `strain` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `strains`
--


-- --------------------------------------------------------

--
-- Table structure for table `tracker`
--

CREATE TABLE `tracker` (
  `trackID` int(11) NOT NULL AUTO_INCREMENT,
  `tableName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tracker`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', '', 'default user with administrator privileges', 0);
INSERT INTO `user` VALUES(2, 'defaultGroup', '', 'Default Group', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vials`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vials`
--

