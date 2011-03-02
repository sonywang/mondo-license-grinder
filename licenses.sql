-- phpMyAdmin SQL Dump
-- version 3.3.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 01, 2010 at 04:39 PM
-- Server version: 5.1.50
-- PHP Version: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `licenses_pub`
--

-- --------------------------------------------------------

--
-- Table structure for table `consortium`
--

DROP TABLE IF EXISTS `consortium`;
CREATE TABLE IF NOT EXISTS `consortium` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
CREATE TABLE IF NOT EXISTS `meta` (
  `fieldname` varchar(255) NOT NULL,
  `nicename` varchar(255) NOT NULL,
  `type` enum('binary','textfield','text') NOT NULL,
  `viewable_public` tinyint(1) NOT NULL,
  `viewable_staff` tinyint(1) NOT NULL,
  `default_value` int(11) NOT NULL,
  `boilerplate_long` int(11) NOT NULL,
  `boilerplate_short` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

DROP TABLE IF EXISTS `record`;
CREATE TABLE IF NOT EXISTS `record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `vendor` int(10) unsigned NOT NULL,
  `consortium` int(10) unsigned NOT NULL,
  `e_reserves` tinyint(1) NOT NULL,
  `course_pack` tinyint(1) NOT NULL,
  `durable_url` tinyint(1) NOT NULL,
  `alumni_access` tinyint(1) NOT NULL,
  `ill_print` tinyint(1) NOT NULL,
  `ill_electronic` tinyint(1) NOT NULL,
  `ill_ariel` tinyint(1) NOT NULL,
  `walk_in` tinyint(1) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sherpa_romeo` varchar(255) NOT NULL,
  `perpetual_access` tinyint(1) NOT NULL DEFAULT '0',
  `perpetual_access_note` text NOT NULL,
  `notes` text NOT NULL,
  `notes_public` text NOT NULL,
  `date_signed_approved` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `t` (`title`(255),`vendor`,`consortium`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
CREATE TABLE IF NOT EXISTS `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
