-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dic 17, 2019 alle 09:04
-- Versione del server: 5.6.33-log
-- PHP Version: 5.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `my_seatheat` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `my_seatheat`;

-- --------------------------------------------------------

--
-- Struttura della tabella `administrators`
--

CREATE TABLE IF NOT EXISTS `administrators` (
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`),
  KEY `FK_CUSTOMER` (`customerEmail`),
  KEY `seatId` (`seatId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `surname` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `birthDate` date NOT NULL,
  `birthplace` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `currentAddress` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  `billingAddress` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `telephone` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  `allowMails` tinyint(1) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `eventCategories`
--

CREATE TABLE IF NOT EXISTS `eventCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `place` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `dateTime` datetime NOT NULL,
  `description` mediumtext CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `site` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  `promoterEmail` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PROMOTER` (`promoterEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `eventsToCategories`
--

CREATE TABLE IF NOT EXISTS `eventsToCategories` (
  `categoryId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  PRIMARY KEY (`categoryId`,`eventId`),
  KEY `FK_EVENT` (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` mediumtext CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `eventId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_NOTIFICATION_EVENT` (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `promoters`
--

CREATE TABLE IF NOT EXISTS `promoters` (
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `organizationName` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `VATid` char(11) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `website` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `ID_PROMOTER` (`VATid`),
  UNIQUE KEY `organizationName` (`organizationName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `purchases`
--

CREATE TABLE IF NOT EXISTS `purchases` (
  `eventId` int(11) NOT NULL,
  `seatId` int(11) NOT NULL,
  `customerEmail` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `amount` int(11) NOT NULL,
  `dateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventId`,`seatId`,`customerEmail`,`dateTime`),
  KEY `FK_CUSTOMER` (`customerEmail`),
  KEY `seatId` (`seatId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `seatCategories`
--

CREATE TABLE IF NOT EXISTS `seatCategories` (
  `eventId` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `price` decimal(13,2) NOT NULL,
  `seats` int(11) NOT NULL,
  PRIMARY KEY (`id`, `eventId`),
  KEY `FK_EVENT` (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `profilePhoto` longtext CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `type` enum('c','p','a') CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `usersNotifications`
--

CREATE TABLE IF NOT EXISTS `usersNotifications` (
  `notificationId` int(11) NOT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `dateTime` datetime NOT NULL,
  `visualized` tinyint(1) NOT NULL,
  PRIMARY KEY (`notificationId`,`email`,`dateTime`),
  KEY `FK_USER` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `administrators`
--
ALTER TABLE `administrators`
  ADD CONSTRAINT `administrators_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`seatId`) REFERENCES `seatCategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_ibfk_3` FOREIGN KEY (`customerEmail`) REFERENCES `customers` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`promoterEmail`) REFERENCES `promoters` (`email`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Limiti per la tabella `eventsToCategories`
--
ALTER TABLE `eventsToCategories`
  ADD CONSTRAINT `eventsToCategories_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `eventCategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventsToCategories_ibfk_2` FOREIGN KEY (`eventId`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `promoters`
--
ALTER TABLE `promoters`
  ADD CONSTRAINT `promoters_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`customerEmail`) REFERENCES `customers` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`eventId`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`seatId`) REFERENCES `seatCategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `seatCategories`
--
ALTER TABLE `seatCategories`
  ADD CONSTRAINT `seatCategories_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `usersNotifications`
--
ALTER TABLE `usersNotifications`
  ADD CONSTRAINT `usersNotifications_ibfk_1` FOREIGN KEY (`notificationId`) REFERENCES `notifications` (`id`),
  ADD CONSTRAINT `usersNotifications_ibfk_2` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
