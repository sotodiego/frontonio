-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-06-2022 a las 15:03:46
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wordpress5.8.2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_correos_oficial_returns`
--

CREATE TABLE `wp_correos_oficial_returns` (
  `id_order` int(11) NOT NULL,
  `id_sender` int(11) NOT NULL,
  `reference` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `shipping_number` varchar(255) NOT NULL,
  `carrier_type` varchar(20) NOT NULL,
  `date_add` datetime NOT NULL,
  `office` varchar(255) DEFAULT NULL,
  `id_product` int(11) NOT NULL,
  `id_carrier` int(11) NOT NULL,
  `bultos` int(11) NOT NULL,
  `AT_code` varchar(30) DEFAULT '',
  `last_status` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `pickup` tinyint(1) DEFAULT NULL,
  `pickup_number` varchar(50) DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_from_hour` time DEFAULT NULL,
  `pickup_to_hour` time DEFAULT NULL,
  `package_size` int(2) DEFAULT NULL,
  `print_label` varchar(1) DEFAULT 'N',
  `pickup_status` varchar(50) DEFAULT NULL,
  `require_customs_doc` int(1) DEFAULT 0,  
  PRIMARY KEY (`shipping_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
