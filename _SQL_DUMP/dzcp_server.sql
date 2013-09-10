-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. Sep 2013 um 13:59
-- Server Version: 5.1.50-community
-- PHP-Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dzcp_server`
--
CREATE DATABASE IF NOT EXISTS `dzcp_server` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `dzcp_server`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_addons`
--

CREATE TABLE IF NOT EXISTS `dzcp_server_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `autor` varchar(200) NOT NULL DEFAULT 'Hammermaps.de',
  `dir` varchar(200) NOT NULL DEFAULT 'HM-XXXX',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `dzcp_server_addons`
--

INSERT INTO `dzcp_server_addons` (`id`, `name`, `autor`, `dir`) VALUES
(1, 'DZCP - Live!', 'Hammermaps.de', 'HM-DZCP-Live'),
(2, 'ProFTPD Administrator', 'Hammermaps.de', 'HM-ProFTPD');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_addons_updates`
--

CREATE TABLE IF NOT EXISTS `dzcp_server_addons_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` int(11) NOT NULL DEFAULT '0',
  `version` varchar(7) NOT NULL DEFAULT '1.0',
  `build` varchar(50) NOT NULL DEFAULT '0001',
  `changelog` text COMMENT 'string::encode()',
  `title` text COMMENT 'string::encode()',
  `rev` varchar(20) NOT NULL DEFAULT '#f808nk',
  `time` int(11) NOT NULL DEFAULT '0',
  `update_type` int(1) NOT NULL DEFAULT '0',
  `file` varchar(200) NOT NULL DEFAULT 'update123.zip',
  `extract_to` text,
  `for_version` varchar(7) NOT NULL DEFAULT '1.0',
  `for_build` varchar(50) NOT NULL DEFAULT '0001',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `dzcp_server_addons_updates`
--

INSERT INTO `dzcp_server_addons_updates` (`id`, `addon_id`, `version`, `build`, `changelog`, `title`, `rev`, `time`, `update_type`, `file`, `extract_to`, `for_version`, `for_build`) VALUES
(2, 1, '1.0.0.1', '0002', 'Test Update f&uuml;r DZCP - Live!', 'for DZCP - Live!', '#f32mr', 0, 0, 'update123.zip', NULL, '1.0', '0001');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_clients`
--

CREATE TABLE IF NOT EXISTS `dzcp_server_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_news`
--

CREATE TABLE IF NOT EXISTS `dzcp_server_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dzcp_server_updates`
--

CREATE TABLE IF NOT EXISTS `dzcp_server_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition` varchar(100) NOT NULL DEFAULT 'Extended Edition',
  `version` varchar(7) NOT NULL DEFAULT '1.0',
  `build` varchar(50) NOT NULL DEFAULT 'repo:dev:git:0176',
  `dbv` int(11) NOT NULL DEFAULT '1600',
  `changelog` text COMMENT 'string::encode()',
  `title` text COMMENT 'string::encode()',
  `rev` varchar(20) NOT NULL DEFAULT '#f808nk',
  `time` int(11) NOT NULL DEFAULT '0',
  `update_type` int(1) NOT NULL DEFAULT '0',
  `file` varchar(200) NOT NULL DEFAULT 'update123.zip',
  `extract_to` text,
  `for_edition` varchar(100) NOT NULL DEFAULT 'Extended Edition',
  `for_version` varchar(7) NOT NULL DEFAULT '1.0',
  `for_build` varchar(50) NOT NULL DEFAULT 'repo:dev:git:0176',
  `for_dbv` int(11) NOT NULL DEFAULT '1600',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `dzcp_server_updates`
--

INSERT INTO `dzcp_server_updates` (`id`, `edition`, `version`, `build`, `dbv`, `changelog`, `title`, `rev`, `time`, `update_type`, `file`, `extract_to`, `for_edition`, `for_version`, `for_build`, `for_dbv`) VALUES
(1, 'Extended Edition', '1.1', 'repo:dev:git:0180', 1600, 'Test Update auf Version 1.1 Beschreibung <p>Test <p><b>TEST</b>', ' Test Update auf Version 1.1 von 1.0', '#f808nk', 0, 0, 'update_test_1-0_1-1.zip', NULL, 'Extended Edition', '1.0', 'repo:dev:git:0176', 1600),
(2, 'Extended Edition', '1.0', 'repo:dev:git:0180', 1600, 'Test Update auf Version 1.0 Beschreibung <p>Test <p><b>TEST</b>', ' Test Update auf Version 1.0 von 1.1', '#f808nk', 0, 0, 'update_test_1-1_1-0.zip', NULL, 'Extended Edition', '1.1', 'repo:dev:git:0176', 1600);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
