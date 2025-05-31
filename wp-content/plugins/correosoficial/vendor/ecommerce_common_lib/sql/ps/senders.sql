-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-07-2021 a las 09:14:17
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
-- Estructura de tabla para la tabla `ps_correos_oficial_senders`
--

DROP TABLE IF EXISTS `ps_correos_oficial_senders`;
CREATE TABLE IF NOT EXISTS `ps_correos_oficial_senders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sender_address` varchar(90) NOT NULL,
  `sender_cp` varchar(11) NOT NULL,
  `sender_nif_cif` varchar(10) NOT NULL,
  `sender_city` varchar(20) NOT NULL,
  `sender_contact` varchar(30) NOT NULL,
  `sender_phone` varchar(14) NOT NULL,
  `sender_from_time` time NOT NULL,
  `sender_to_time` time NOT NULL,
  `sender_iso_code_pais` varchar(4) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `sender_default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Senders_unicos_completos` (`sender_name`,`sender_address`,`sender_cp`,`sender_city`,`sender_contact`,`sender_phone`,`sender_from_time`,`sender_to_time`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
