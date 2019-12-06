SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `my_seatheat` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_cs;
USE `my_seatheat`;

CREATE TABLE IF NOT EXISTS `administrators` (
  `email` varchar(30) COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `carts` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`),
  KEY `FK_CUSTOMER` (`customerEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `customers` (
  `email` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `username` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `name` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `surname` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `birthDate` date NOT NULL,
  `birthplace` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `currentAddress` varchar(30) COLLATE latin1_general_cs DEFAULT NULL,
  `billingAddress` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `telephone` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `eventCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `place` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `dateTime` datetime NOT NULL,
  `description` mediumtext COLLATE latin1_general_cs NOT NULL,
  `site` varchar(30) COLLATE latin1_general_cs DEFAULT NULL,
  `promoterEmail` varchar(30) COLLATE latin1_general_cs DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PROMOTER` (`promoterEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `eventsToCategories` (
  `categoryId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  PRIMARY KEY (`categoryId`,`eventId`),
  KEY `FK_EVENT` (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` mediumtext COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `promoters` (
  `email` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `organizationName` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `VATid` char(11) NOT NULL,
  `website` varchar(30) COLLATE latin1_general_cs DEFAULT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `ID_PROMOTER` (`VATid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `purchases` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`),
  KEY `FK_CUSTOMER` (`customerEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `seatCategories` (
  `eventId` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `price` decimal(13,2) NOT NULL,
  `seats` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `password` varchar(255) COLLATE latin1_general_cs NOT NULL,
  `profilePhoto` mediumblob NOT NULL,
  `type` enum('c','p','a') COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

CREATE TABLE IF NOT EXISTS `usersNotifications` (
  `notificationId` int(11) NOT NULL,
  `email` varchar(30) COLLATE latin1_general_cs NOT NULL,
  `dateTime` datetime NOT NULL,
  `visualized` tinyint(1) NOT NULL,
  PRIMARY KEY (`notificationId`,`email`,`dateTime`),
  KEY `FK_USER` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;
