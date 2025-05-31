-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-08-2021 a las 11:53:33
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
-- Base de datos: `wordpress`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_correos_oficial_configuration`
--

DROP TABLE IF EXISTS `wp_correos_oficial_configuration`;
CREATE TABLE IF NOT EXISTS `wp_correos_oficial_configuration` (
  `name` varchar(50) NOT NULL,
  `value` text,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `wp_correos_oficial_configuration`
--

INSERT INTO `wp_correos_oficial_configuration` (`name`, `value`, `type`) VALUES
('ActivateAllCarriers', '', 'checkbox'),
('ActivateAutomaticTracking', '', 'checkbox'),
('ActivateOrderStatusChange', '', 'checkbox'),
('ActivateOrderStatusChangeAfterSave', '', 'checkbox'),
('ActivateTrackingLink', '', 'checkbox'),
('ActivateWeightByDefault', 'on', 'checkbox'),
('AgreeToAlterReferences', '', 'checkbox'),
('BankAccNumberAndIBAN', '', 'text'),
('CashOnDeliveryMethod', '', 'select'),
('ChangeLogoOnLabel', '', 'checkbox'),
('CronInterval', '4', 'number'),
('CronLastExecutionTime', '1970-01-01 00:00:00', 'datetime'),
('CustomerAlternativeText', '', 'checkbox'),
('CustomsDesriptionAndTariff', '', 'text'),
('DefaultCustomsDescription', '', 'text'),
('DefaultLabel', '2', 'select'),
('DefaultPackages', '1', 'number'),
('DescriptionRadio', '', 'checkbox'),
('FormSwitchLanguage', '', 'select'),
('GoogleMapsApi', '', 'text'),
('LabelAlternativeText', '', 'checkbox'),
('LabelObservations', '', 'checkbox'),
('MessageToWarnBuyer', '', 'checkbox'),
('RemoveSenderFromLabel', '', 'checkbox'),
('ShippCustomsReference', '', 'text'),
('ShowLabelData', '', 'checkbox'),
('SSLAlternative', '', 'checkbox'),
('Tariff', '', 'select'),
('TariffDescription', '', 'text'),
('TariffRadio', 'on', 'checkbox'),
('TranslatableInput', '{\"\":\"\",\"0\":\"This shipment is subject to customs clearance. The price of the shipment may be increased.\",\"1\":\"Este envío conlleva tramite Aduanero. El precio del envío puede  verse incrementado.\"}', 'text'),
('UploadLogoLabels', '', 'checkbox'),
('WeightByDefault', '1', 'number');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
