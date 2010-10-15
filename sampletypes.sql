-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 02, 2009 at 10:56 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `labdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `sampletypes`
--

DROP TABLE IF EXISTS `sampletypes`;
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
