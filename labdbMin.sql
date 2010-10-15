-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 02, 2009 at 11:21 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `labdbMin`
--

-- --------------------------------------------------------

--
-- Table structure for table `boxes`
--

CREATE TABLE `boxes` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
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
  `connID` int(11) NOT NULL auto_increment,
  `record` int(11) NOT NULL,
  `belongsTo` int(11) NOT NULL,
  `fragName` varchar(255) collate utf8_unicode_ci NOT NULL,
  `start` int(11) NOT NULL COMMENT 'start position on plasmid in bp',
  `end` int(11) NOT NULL COMMENT 'end position on plasimd in bp',
  `direction` int(11) NOT NULL COMMENT 'direction on plasmid. fw is 1, rev is 0',
  PRIMARY KEY  (`connID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `connections`
--


-- --------------------------------------------------------

--
-- Table structure for table `fragments`
--

CREATE TABLE `fragments` (
  `id` int(11) NOT NULL auto_increment,
  `reaction` varchar(50) NOT NULL,
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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fragments`
--


-- --------------------------------------------------------

--
-- Table structure for table `fragmentsinplasmids`
--

CREATE TABLE `fragmentsinplasmids` (
  `id` int(11) NOT NULL auto_increment,
  `plasmidID` int(11) NOT NULL,
  `fragmentID` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fragmentsinplasmids`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `belongsToGroup` int(11) NOT NULL,
  `defaultPermissions` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` VALUES(69, 34, 34, 2);
INSERT INTO `groups` VALUES(70, 34, 35, 0);
INSERT INTO `groups` VALUES(71, 33, 35, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `location` int(11) NOT NULL,
  `orderNumber` varchar(255) collate utf8_unicode_ci NOT NULL,
  `quantity` varchar(250) collate utf8_unicode_ci NOT NULL,
  `unitMeas` varchar(255) collate utf8_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `orderDate` date NOT NULL,
  `received` date NOT NULL,
  `supplier` varchar(255) collate utf8_unicode_ci NOT NULL,
  `manufacturer` varchar(255) collate utf8_unicode_ci NOT NULL,
  `casNumber` varchar(255) collate utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `funding` varchar(250) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
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
  `statusName` varchar(250) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`statusNr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `itemstatus`
--


-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `locations`
--


-- --------------------------------------------------------

--
-- Table structure for table `oligos`
--

CREATE TABLE `oligos` (
  `id` int(11) NOT NULL auto_increment,
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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oligos`
--


-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `trackID` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL,
  `permission` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` VALUES(1, 34, 2);
INSERT INTO `permissions` VALUES(1, 33, 1);

-- --------------------------------------------------------

--
-- Table structure for table `plasmids`
--

CREATE TABLE `plasmids` (
  `id` int(6) NOT NULL auto_increment COMMENT 'unique identifier',
  `name` varchar(50) NOT NULL COMMENT 'clone name',
  `description` mediumtext NOT NULL,
  `generation` mediumtext NOT NULL,
  `sequence` mediumtext NOT NULL,
  `created` date NOT NULL COMMENT 'created on',
  `changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'last changed',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `plasmids`
--

INSERT INTO `plasmids` VALUES(1, 'test', '', '', '', '0000-00-00', '2009-09-02 23:20:26');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(25) collate utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `proteins`
--

CREATE TABLE `proteins` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `source` mediumtext NOT NULL,
  `glycerolStockID` int(11) NOT NULL,
  `purity` text NOT NULL,
  `concentration` text NOT NULL,
  `amount` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `proteins`
--


-- --------------------------------------------------------

--
-- Table structure for table `sampletypes`
--

CREATE TABLE `sampletypes` (
  `id` int(11) NOT NULL auto_increment,
  `st_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `plural` varchar(50) collate utf8_unicode_ci NOT NULL,
  `form` varchar(50) collate utf8_unicode_ci NOT NULL,
  `table` varchar(50) collate utf8_unicode_ci NOT NULL,
  `list` varchar(250) collate utf8_unicode_ci NOT NULL,
  `isSample` tinyint(1) NOT NULL default '1',
  `labPermission` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `sequencing`
--

CREATE TABLE `sequencing` (
  `id` int(11) NOT NULL auto_increment,
  `vialID` int(11) NOT NULL,
  `analysis` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sequencing`
--


-- --------------------------------------------------------

--
-- Table structure for table `sequencing-datafiles`
--

CREATE TABLE `sequencing-datafiles` (
  `id` int(11) NOT NULL auto_increment,
  `sequenceID` int(11) NOT NULL,
  `datafile` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sequencing-datafiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL auto_increment,
  `variable` varchar(256) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `strains`
--

CREATE TABLE `strains` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `organism` varchar(255) NOT NULL,
  `strain` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `strains`
--


-- --------------------------------------------------------

--
-- Stand-in structure for view `trackBoxes`
--
CREATE TABLE `trackBoxes` (
`id` int(6)
,`name` varchar(50)
,`description` mediumtext
,`location` varchar(50)
,`tID` int(11)
,`boxName` varchar(50)
);
-- --------------------------------------------------------

--
-- Table structure for table `tracker`
--

CREATE TABLE `tracker` (
  `trackID` int(11) NOT NULL auto_increment,
  `tableName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `sampleType` int(11) NOT NULL,
  `sampleID` int(11) NOT NULL,
  `created` date NOT NULL,
  `changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `deleted` date NOT NULL,
  `project` int(11) NOT NULL,
  `subProject` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `groupid` int(11) NOT NULL default '0',
  `permOwner` smallint(6) NOT NULL,
  `permGroup` smallint(6) NOT NULL,
  `permOthers` smallint(6) NOT NULL,
  PRIMARY KEY  (`trackID`),
  UNIQUE KEY `sampleType` (`sampleType`,`sampleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tracker`
--

INSERT INTO `tracker` VALUES(1, '', 1, 1, '2009-09-02', '2009-09-02 23:20:26', '0000-00-00', 0, 0, 34, 0, 2, 0, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tracklocations`
--
CREATE TABLE `tracklocations` (
`tID` int(11)
,`locationName` varchar(255)
);
-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL auto_increment,
  `userid` varchar(100) collate utf8_unicode_ci NOT NULL,
  `password` char(32) collate utf8_unicode_ci NOT NULL,
  `fullname` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `notes` text collate utf8_unicode_ci,
  `groupType` tinyint(4) NOT NULL COMMENT '0 is user, 1 is labgroup, 2 is projectgroup',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES(33, 'admins', '', 'database admins', 'schalch@cshl.edu', NULL, 3);
INSERT INTO `user` VALUES(34, 'thomas', 'a118a3080f56a62df891eb8d3a734130', 'Thomas Schalch', 'schalch@cshl.edu', 'test', 0);
INSERT INTO `user` VALUES(35, 'leemorlab', '', 'Leemor lab', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vials`
--

CREATE TABLE `vials` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `concentration` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `creator` varchar(255) NOT NULL,
  `sID` int(11) NOT NULL COMMENT 'trackerID of vial content',
  `boxID` int(11) NOT NULL,
  `position` varchar(25) NOT NULL,
  `exists` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vials`
--


-- --------------------------------------------------------

--
-- Structure for view `trackBoxes`
--
DROP TABLE IF EXISTS `trackBoxes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `labdbMin`.`trackBoxes` AS select `labdbMin`.`boxes`.`id` AS `id`,`labdbMin`.`boxes`.`name` AS `name`,`labdbMin`.`boxes`.`description` AS `description`,`labdbMin`.`boxes`.`location` AS `location`,`labdbMin`.`tracker`.`trackID` AS `tID`,`labdbMin`.`boxes`.`name` AS `boxName` from (`labdbMin`.`boxes` join `labdbMin`.`tracker` on(((`labdbMin`.`boxes`.`id` = `labdbMin`.`tracker`.`sampleID`) and (`labdbMin`.`tracker`.`sampleType` = 8))));

-- --------------------------------------------------------

--
-- Structure for view `tracklocations`
--
DROP TABLE IF EXISTS `tracklocations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `labdbMin`.`tracklocations` AS select `labdbMin`.`tracker`.`trackID` AS `tID`,`labdbMin`.`locations`.`name` AS `locationName` from (`labdbMin`.`locations` join `labdbMin`.`tracker` on(((`labdbMin`.`locations`.`id` = `labdbMin`.`tracker`.`sampleID`) and (`labdbMin`.`tracker`.`sampleType` = 11))));
