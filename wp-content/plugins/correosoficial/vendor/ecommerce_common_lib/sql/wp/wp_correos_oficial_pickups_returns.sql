-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 05-07-2022 a las 09:37:17
-- Versión del servidor: 5.7.31-log
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wordpress`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_correos_oficial_pickups_returns`
--

DROP TABLE IF EXISTS `wp_correos_oficial_pickups_returns`;
CREATE TABLE IF NOT EXISTS `wp_correos_oficial_pickups_returns` (
  `id_order` int(11) NOT NULL,
  `pickup_number` varchar(50) DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_from_hour` time DEFAULT NULL,
  `pickup_to_hour` time DEFAULT NULL,
  `package_size` int(2) DEFAULT NULL,
  `print_label` varchar(1) DEFAULT 'N',
  `pickup_status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
