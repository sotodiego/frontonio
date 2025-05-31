-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-07-2021 a las 10:39:43
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prestashop`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ps_correos_oficial_codes`
--

DROP TABLE IF EXISTS `ps_correos_oficial_codes`;
CREATE TABLE IF NOT EXISTS `ps_correos_oficial_codes` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(30) NOT NULL,
  `company` varchar(7) DEFAULT NULL,
  `CorreosContract` varchar(50) NOT NULL,
  `CorreosCustomer` varchar(50) NOT NULL,
  `CorreosKey` varchar(50) NOT NULL,
  `CorreosUser` varchar(50) NOT NULL,
  `CorreosPassword` varchar(50) NOT NULL,
  `CorreosOv2Code` varchar(50) NOT NULL,
  `CEXCustomer` varchar(50) NOT NULL,
  `CEXUser` varchar(50) NOT NULL,
  `CEXPassword` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  UNIQUE KEY `company` (`company`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
