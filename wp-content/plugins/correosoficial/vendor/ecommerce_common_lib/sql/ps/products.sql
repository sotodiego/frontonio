-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 28-01-2022 a las 13:05:04
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
-- Estructura de tabla para la tabla `ps_correos_oficial_products`
--

DROP TABLE IF EXISTS `ps_correos_oficial_products`;
CREATE TABLE IF NOT EXISTS `ps_correos_oficial_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `active` int(11) NOT NULL,
  `delay` varchar(200) NOT NULL,
  `company` varchar(10) NOT NULL,
  `url` varchar(250) NOT NULL,
  `codigoProducto` varchar(10) NOT NULL,
  `id_carrier` int(10) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `max_packages` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `ps_correos_oficial_products`
--

INSERT INTO `ps_correos_oficial_products` (`id`, `name`, `active`, `delay`, `company`, `url`, `codigoProducto`, `id_carrier`, `product_type`, `max_packages`) VALUES
(1, 'Paq 10', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '61', 0, NULL, 99),
(2, 'Paq 14', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '62', 0, NULL, 99),
(3, 'Paq 24', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '63', 0, NULL, 99),
(4, 'Paq Empresa 14', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '92', 336, NULL, 99),
(5, 'ePaq 24', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '93', 0, NULL, 99),
(6, 'Islas Express', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '26', 0, NULL, 99),
(7, 'Islas Documentación', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '46', 325, NULL, 99),
(8, 'Islas Marítimo', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '79', 0, NULL, 99),
(9, 'Internacional Express', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '91', 302, 'international', 1),
(10, 'Internacional Estandard', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '90', 303, 'international', 1),
(11, 'Entrega Plus', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '54', 0, NULL, 99),
(12, 'Campaña Cex', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '27', 0, NULL, 99),
(13, 'Portugal Óptica', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '73', 0, NULL, 99),
(14, 'Paquetería Óptica', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '76', 0, NULL, 99),
(15, 'Paq Premium Domicilio', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0235', 0, 'homedelivery', 10),
(16, 'Paq Premium Oficina Elegida', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0236', 0, 'office', 10),
(17, 'Paq Premium City Paq', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0176', 0, 'citypaq', 10),
(18, 'Paq Estándar Domicilio', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0132', 0, 'homedelivery', 10),
(19, 'Paq Estándar Oficina Elegida', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0133', 0, 'office', 10),
(20, 'Paq Estándar City Paq', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0178', 0, 'citypaq', 10),
(21, 'Paq Ligero', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0179', 0, 'homedelivery', 10),
(22, 'Paq Premium Internacional', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0411', 0, 'international', 1),
(23, 'Paq Estándar Internacional', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0410', 0, 'international', 1),
(24, 'Paq Light Internacional', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0360', 0, 'international', 1),
(25, 'Paq 24 Oficina Elegida', 0, 'Envíos con Correos OFICIAL', 'CEX', 'https://s.correosexpress.com/c?n=@', '44', 0, 'office', 99);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
