-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: dd19204.kasserver.com
-- Erstellungszeit: 04. Juni 2010 um 09:58
-- Server Version: 5.0.51
-- PHP-Version: 5.2.12-nmm1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `d00dc2ec`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_marketlog`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_marketlog` (
  `marketId` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `action` set('unknown','create','edit','suspend','void','settle','flag','buypos','sellpos','comment') character set utf8 NOT NULL default 'unknown',
  `details` text character set utf8 NOT NULL,
  KEY `timestamp` (`timestamp`),
  KEY `userId` (`userId`),
  KEY `action` (`action`),
  KEY `marketId` (`marketId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_marketoptions`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_marketoptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `marketId` int(10) unsigned NOT NULL,
  `label` varchar(512) NOT NULL,
  `shares` double unsigned NOT NULL,
  `value` double unsigned NOT NULL COMMENT 'shares of this option devided by the sum of all shares over all options to one market',
  `description` text NOT NULL,
  `open` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `marketId` (`marketId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1089 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_marketpositions`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_marketpositions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `marketId` int(10) unsigned NOT NULL,
  `optionId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL default '1',
  `state` set('Open','Sold','Won','Lost','Voided') NOT NULL default 'Open',
  `buyprice` double unsigned NOT NULL COMMENT 'amount of cash used to buy the position',
  `currprice` double unsigned default NULL COMMENT 'the current price ',
  `sellprice` double unsigned default NULL COMMENT 'amount of cash returned when selling the position. Is NULL when positions has not been sold or settled',
  `bought` datetime NOT NULL COMMENT 'GMT buy time',
  `sold` datetime default NULL COMMENT 'GMT sell or settle time',
  `buyvalue` double unsigned NOT NULL COMMENT 'percentage of market volume at buy time',
  `currvalue` double unsigned default NULL,
  `sellvalue` double unsigned default NULL COMMENT 'percentage of market volume at sell or settle time ',
  `shares` double unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `marketId` (`marketId`),
  KEY `userId` (`userId`),
  KEY `state` (`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_markets`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_markets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `hubdubId` int(10) unsigned NOT NULL default '0',
  `title` varchar(512) NOT NULL,
  `creator` varchar(254) NOT NULL,
  `state` set('Created','Open','Voided','Settled','Suspended','Undefined') NOT NULL default 'Undefined',
  `type` set('0','1','4') NOT NULL default '0',
  `description` text NOT NULL,
  `imageurl` text NOT NULL,
  `maincat` varchar(254) NOT NULL,
  `subcat` varchar(254) NOT NULL,
  `settlementdetails` text NOT NULL,
  `suspension` datetime NOT NULL COMMENT 'GMT datetime of suspension',
  `publication` datetime NOT NULL COMMENT 'GMT datetime of publication',
  `settlement` datetime default NULL COMMENT 'GMT datetime of settlement',
  PRIMARY KEY  (`id`),
  KEY `maincat` (`maincat`,`subcat`),
  KEY `publication` (`publication`),
  KEY `state` (`state`),
  KEY `suspension` (`suspension`),
  KEY `creator` (`creator`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=450 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_markettags`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_markettags` (
  `marketId` int(10) unsigned NOT NULL,
  `tagname` varchar(254) NOT NULL,
  KEY `marketId` (`marketId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `predictionmarkets_useraccounts`
--

CREATE TABLE IF NOT EXISTS `predictionmarkets_useraccounts` (
  `userId` int(11) NOT NULL,
  `userName` varchar(254) NOT NULL,
  `cash` double NOT NULL,
  `allowMarketCreate` tinyint(1) NOT NULL default '0',
  `allowMarketDuplicate` tinyint(1) NOT NULL default '0',
  `allowMarketEdit` tinyint(1) NOT NULL default '0',
  `allowMarketSuspend` tinyint(1) NOT NULL default '0',
  `allowMarketSettle` tinyint(1) NOT NULL default '0',
  `allowMarketVoid` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`userId`),
  UNIQUE KEY `userName` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
