-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Gen 09, 2020 alle 00:55
-- Versione del server: 5.6.33-log
-- PHP Version: 5.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `my_seatheat`
--
CREATE DATABASE IF NOT EXISTS `my_seatheat` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `my_seatheat`;

-- --------------------------------------------------------

--
-- Struttura della tabella `administrators`
--

CREATE TABLE IF NOT EXISTS `administrators` (
  `email` varchar(30) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`),
  KEY `FK_CUSTOMER` (`customerEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `email` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `birthDate` date NOT NULL,
  `birthplace` varchar(30) NOT NULL,
  `currentAddress` varchar(30) DEFAULT NULL,
  `billingAddress` varchar(30) NOT NULL,
  `telephone` varchar(11) DEFAULT NULL,
  `allowMails` tinyint(1) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `eventCategories`
--

CREATE TABLE IF NOT EXISTS `eventCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `place` varchar(30) NOT NULL,
  `dateTime` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  `site` varchar(30) DEFAULT NULL,
  `promoterEmail` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PROMOTER` (`promoterEmail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `eventsToCategories`
--

CREATE TABLE IF NOT EXISTS `eventsToCategories` (
  `categoryId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  PRIMARY KEY (`categoryId`,`eventId`),
  KEY `FK_EVENT` (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` mediumtext NOT NULL,
  `eventId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_NOTIFICATION_EVENT` (`eventId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `promoters`
--

CREATE TABLE IF NOT EXISTS `promoters` (
  `email` varchar(30) NOT NULL,
  `organizationName` varchar(30) NOT NULL,
  `VATid` char(11) NOT NULL,
  `website` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `ID_PROMOTER` (`VATid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `purchases`
--

CREATE TABLE IF NOT EXISTS `purchases` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  `dateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`,`dateTime`),
  KEY `FK_CUSTOMER` (`customerEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `seatCategories`
--

CREATE TABLE IF NOT EXISTS `seatCategories` (
  `eventId` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `price` decimal(13,2) NOT NULL,
  `seats` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profilePhoto` longtext NOT NULL,
  `type` enum('c','p','a') NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `usersNotifications`
--

CREATE TABLE IF NOT EXISTS `usersNotifications` (
  `notificationId` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `dateTime` datetime NOT NULL,
  `visualized` tinyint(1) NOT NULL,
  PRIMARY KEY (`notificationId`,`email`,`dateTime`),
  KEY `FK_USER` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
