-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 16, 2012 at 06:54 AM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nhbs_w`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_audit_trail`
--

CREATE TABLE IF NOT EXISTS `tbl_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_value` text COLLATE utf8_unicode_ci,
  `new_value` text COLLATE utf8_unicode_ci,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stamp` datetime NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tbl_audit_trail`
--

INSERT INTO `tbl_audit_trail` (`id`, `old_value`, `new_value`, `action`, `model`, `field`, `stamp`, `user_id`, `model_id`) VALUES
(1, '1', '2', 'CHANGE', 'Product', 'version', '2012-08-16 03:29:34', '1', '199718'),
(2, '2', '1', 'CHANGE', 'Product', 'version', '2012-08-16 03:30:02', '1', '199718'),
(3, 'Ecology and Conservation of Estuarine Ecosystems:Lake St. Lucia as a Model/Perissinotto HB', 'Ecology and Conservation of Estuarine Ecosystems:Lake St. Lucia as a Model/Perissinotto PB', 'CHANGE', 'Product', 'ns_purchase_description', '2012-08-16 03:30:02', '1', '199718'),
(4, '1', '2', 'CHANGE', 'Product', 'version', '2012-08-16 03:30:27', '1', '199718'),
(5, '2012-08-08', '2012-08-17', 'CHANGE', 'Product', 'status_last_confirmed', '2012-08-16 03:45:52', '1', '199718');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
