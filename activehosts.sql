-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 09, 2019 at 04:52 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newsletter`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlogs`
--

DROP TABLE IF EXISTS `adminlogs`;
CREATE TABLE IF NOT EXISTS `adminlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `activetime` int(11) DEFAULT NULL,
  `country` varchar(10) NOT NULL,
  `ipaddress` varchar(20) NOT NULL,
  `location` varchar(50) NOT NULL,
  `activeadmin` varchar(30) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adminlogs`
--

INSERT INTO `adminlogs` (`id`, `date`, `activetime`, `country`, `ipaddress`, `location`, `activeadmin`) VALUES
(1, '2019-05-05 11:19:34', 60, 'PL', '82.146.252.3', '53.1271,18.0200', 'Andrii');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `login`, `password`, `email`) VALUES
(1, 'Vova', '$2y$10$WFMXHeK73O6bthiJsWx8oemAKJTIp/LCSmizA0fOHMaXDdHX46JE.', 'vova@mail.com'),
(2, 'Andrii', '$2y$10$kWgUfJeMI6uNoipG0moqVONAbHJcvufOdNwNxoHexN9jBY.TwMfia', 'andrii.sh@wp.pl');

-- --------------------------------------------------------

--
-- Table structure for table `hosts`
--

DROP TABLE IF EXISTS `hosts`;
CREATE TABLE IF NOT EXISTS `hosts` (
  `id` int(11) NOT NULL,
  `host` text NOT NULL,
  `attempts` text NOT NULL,
  `time` int(11) DEFAULT NULL,
  `date of inactive` int(11) DEFAULT NULL,
  `responsible` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `responsible`
--

DROP TABLE IF EXISTS `responsible`;
CREATE TABLE IF NOT EXISTS `responsible` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `responsible person` text NOT NULL,
  `phone` text NOT NULL,
  `e-mail` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `responsible`
--

INSERT INTO `responsible` (`id`, `responsible person`, `phone`, `e-mail`) VALUES
(1, 'Pawel Piotrowicz', '+48566118747', 'p.piotrowicz@um.torun.pl'),
(2, 'Kamil Cierpialkowski', '+48566118840', 'bip@um.torun.pl');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
