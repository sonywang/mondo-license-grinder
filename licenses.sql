-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 26, 2012 at 04:21 PM
-- Server version: 5.5.17
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `licenses`
--

-- --------------------------------------------------------

--
-- Table structure for table `consortium`
--

CREATE TABLE IF NOT EXISTS `consortium` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `doc`
--

CREATE TABLE IF NOT EXISTS `doc` (
  `filename` varchar(255) NOT NULL,
  `alias` varchar(6) NOT NULL,
  `mime` varchar(255) NOT NULL,
  UNIQUE KEY `alias` (`alias`),
  UNIQUE KEY `alias_2` (`alias`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `html`
--

CREATE TABLE IF NOT EXISTS `html` (
  `tag` varchar(255) NOT NULL,
  `content` text NOT NULL,
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

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
  `handouts` tinyint(1) NOT NULL DEFAULT '2',
  `images` tinyint(1) NOT NULL DEFAULT '2',
  `research_private_study` tinyint(1) NOT NULL DEFAULT '1',
  `blackboard` tinyint(1) NOT NULL DEFAULT '1',
  `fulltext` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'fulltext available',
  `password` varchar(255) NOT NULL,
  `perpetual_access` tinyint(1) NOT NULL DEFAULT '0',
  `perpetual_access_note` text NOT NULL,
  `notes` text NOT NULL,
  `notes_public` text NOT NULL,
  `date_signed_approved` date NOT NULL,
  `sherpa_romeo` varchar(255) DEFAULT '',
  `doc_alias` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `t` (`title`(255),`vendor`,`consortium`),
  KEY `doc_alias` (`doc_alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=912 ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE IF NOT EXISTS `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=657 ;
