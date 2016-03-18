-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2015 at 06:10 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `culinarydirectors`
--

-- --------------------------------------------------------

--
-- Table structure for table `Account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(32) NOT NULL,
  `password` text NOT NULL,
  `type` TINYINT(11) UNSIGNED NOT NULL,
  `accountProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Museum`
--

CREATE TABLE IF NOT EXISTS `museum` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `accountId` int(11) UNSIGNED NOT NULL,
  `museumName` varchar(64) NOT NULL,
  `address` varchar(128) NOT NULL,
  `museumProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Gallery`
--

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `museumId` int(11) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `galleryProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Exhibit`
--

CREATE TABLE IF NOT EXISTS `exhibit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `galleryId` int(11) UNSIGNED NOT NULL,
  `museumId` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `exhibitProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Content`

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `galleryId` int(11) UNSIGNED NOT NULL,
  `exhibitId` int(11) UNSIGNED NOT NULL,
  `museumId` int(11) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `pathToContent` varchar(64) NOT NULL,
  `contentProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(128) NOT NULL,
  `userProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- Table structure for table `login history`
--
CREATE TABLE IF NOT EXISTS `loginHistory` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(11) UNSIGNED NOT NULL,
  `timestampp` timestamp NOT NULL,
  `ip` varchar(64) NOT NULL,
  `loginProfileJSON` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
