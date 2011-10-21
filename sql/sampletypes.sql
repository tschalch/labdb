-- phpMyAdmin SQL Dump
-- version 2.9.0-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 21, 2011 at 11:32 AM
-- Server version: 5.0.77
-- PHP Version: 5.1.6
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- 
-- Dumping data for table `sampletypes`
-- 

INSERT INTO `sampletypes` (`id`, `st_name`, `plural`, `form`, `table`, `list`, `isSample`, `labPermission`) VALUES 
(1, 'Plasmid', 'Plasmids', 'frmPlasmids', 'plasmids', 'listPlasmids', 1, 0),
(2, 'Oligo', 'Oligos', 'frmOligo', 'oligos', 'listOligo', 1, 0),
(3, 'Protein', 'Proteins', 'frmProteins', 'proteins', '', 1, 0),
(4, 'Glycerolstock', 'Glycerolstocks', 'frmStrain', 'strains', 'listStrains', 1, 0),
(6, 'Vial', 'Vials', 'frmVial', 'vials', 'listVials', 0, 0),
(7, 'Fragment', 'Fragments', 'frmGene', 'fragments', 'listGene', 1, 0),
(8, 'Box', 'Boxes', 'frmBoxes', 'boxes', 'listBoxes', 0, 0),
(9, 'Project', 'Projects', 'frmProject', 'projects', 'listProjects', 0, 0),
(10, 'item', 'Items', 'frmItem', 'inventory', 'listItems', 0, 2),
(11, 'location', 'locations', 'frmLocations', 'locations', 'listLocations', 0, 2),
(12, 'logbook entry', 'logbook entries', 'frmLog', 'logbook', 'listLogbook', 0, 2);
