<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */

ini_set("default_socket_timeout", 20);

/**
 * Constantes de configuración del Módulo CorreosOficial
 */

require_once "DetectPlatform.php";

if (DetectPlatform::isPrestashop()) {
    define('PLATFORM', 'PS');
    define('MODULE', 'undefined' );
    define('VERSION', _PS_VERSION_);

    // Analitica
    define('ANALYTICS_CLIENT_ID', '88e1a19c67b744d7bc778c37006148ad');
    define('ANALYTICS_CLIENT_SECRET', '2D414ad47C0F4C93a9323660833690cD');

} elseif (DetectPlatform::isWordpress()) {
    define('PLATFORM', 'WP');
    define('MODULE', 'WC' );
    define('VERSION', get_option('woocommerce_version'));

    // Analitica
    define('ANALYTICS_CLIENT_ID', '73029a838e4340a8a7bd7534de8781ed');
    define('ANALYTICS_CLIENT_SECRET', '6AAA484Be66B4aCfbB26C07aF5E61095');

} else {
    define('PLATFORM', 'undefined');
    define('VERSION', 'undefined');
    define('PLATFORM_AND_VERSION', 'undefined');
}

/** Variables de configuración */
global $co_debugCorreosOficial;
global $co_signup_customers_from;
global $co_signup_customers_cc;

// Activa o desactiva la depuración de los webservices.
$co_debugCorreosOficial = false;

/**
 * Alta de nuevo cliente - AJUSTES - Inicio
 */
// Dirección email por defecto del modulo
$co_signup_customers_from = 'moduloecommercecorreosoficial@' . $_SERVER['SERVER_NAME'];
// Destinatarios Alta de nuevo cliente
$co_signup_customers_cc = array('alvaro.vergara@correos.com',
    'rosario.encinas@correos.com',
    'david.lorencio@correos.com');
// Recogidas Correos
define('SERVICIO_RECOGIDAS_CORREOS', 'https://serviciorecogidas.correos.es/serviciorecogidas');
define('SERVICIO_RECOGIDAS_CORREOS_PRE', 'https://serviciorecogidaspre.correos.es:20189/serviciorecogidas');

// Localizador de Correos
define('CORREOS_BASE_LOCATION', 'https://localizador.correos.es/canonico/eventos_envio_servicio_auth');

// CEX
define('CEX_BASE_LOCATION', 'https://www.cexpr.es/wspsc/apiRestSeguimientoEnviosk8s/json/seguimientoEnvio');
define('CEX_BASE_LOCATION_LISTA', 'https://www.cexpr.es/wspsc/apiRestListaEnvios/json/listaEnvios');
define('CEX_BASE_LABELS', 'https://www.cexpr.es/wspsc/apiRestEtiquetaTransporte/json/etiquetaTransporte');
define('CEX_GRABAR_ENVIO', 'https://www.cexpr.es/wspsc/apiRestGrabacionEnviok8s/json/grabacionEnvio');
define('CEX_GRABAR_RECOGIDA', 'https://www.cexpr.es/wsps/apiRestGrabacionRecogidaEnviok8s/json/grabarRecogida');
define('CEX_ANULAR_RECOGIDA', 'https://www.cexpr.es/wsps/apiRestGrabacionRecogidaEnviok8s/json/anularRecogida');
define('CEX_CONSULTAR_RECOGIDA', 'https://www.cexpr.es/wspsc/apiRestSeguimientoRecogidak8s/json/seguimientoRecogida');

//Pre
define('CEX_BASE_LOCATION_LISTA_PRE', 'https://www.test.cexpr.es/wspsc/apiRestListaEnvios/json/listaEnvios');
define('CEX_GRABAR_ENVIO_PRE', 'https://www.test.cexpr.es/wspsc/apiRestGrabacionEnviok8s/json/grabacionEnvio');
define('CEX_ANULAR_RECOGIDA_PRE', 'https://www.test.cexpr.es/wsps/apiRestGrabacionRecogidaEnviok8s/json/anularRecogida');
define('CEX_GRABAR_RECOGIDA_PRE', 'https://www.test.cexpr.es/wsps/apiRestGrabacionRecogidaEnviok8s/json/grabarRecogida');
define('CEX_CONSULTAR_RECOGIDA_PRE', 'https://www.test.cexpr.es/wsps/apiRestSeguimientoRecogidak8s/json/seguimientoRecogida');

// Servicios Oficina/CityPaq
define('LOCALIZADOR_OFICINAS', 'http://localizadoroficinas.correos.es/localizadoroficinas');
define('LOCALILZADOR_OFICINAS_PRE', 'http://localizadoroficinaspre.correos.es/localizadoroficinas');

// Tipos Etiquetas
define('LABEL_TYPE_ADHESIVE', 0);
define('LABEL_TYPE_HALF', 1);
define('LABEL_TYPE_THERMAL', 2);

// Formatos Etiquetas
define('LABEL_FORMAT_STANDAR', 0);
define('LABEL_FORMAT_3A4', 1);
define('LABEL_FORMAT_4A4', 2);

// Formatos CEX
define('CEX_LABEL_THERMAL_ADHESIVE', 1);
define('CEX_LABEL_3A4', 3);

define('CO_TIMEOUT_MSG', 'El tiempo de espera se ha agotado');

class Config
{
    private static $correos_url_preregistro;
    private static $correos_url_analitica;

    private static $environment = 'PRO';

    public static function getEnvironment()
    {
        return self::$environment;
    }

    public static function getCorreosURL()
    {
        if (self::$environment == 'PRO') {
            self::$correos_url_preregistro = 'https://preregistroenvios.correos.es/preregistroenvios';
        } elseif (self::$environment == 'PRE') {
            self::$correos_url_preregistro = 'https://preregistroenviospre.correos.es/preregistroenvios';
        } else {
            die('Error 00010: Entorno no válido');
        }
        return self::$correos_url_preregistro;
    }

    public static function getAnaliticaHost()
    {
        if (self::$environment == 'PRO') {
            self::$correos_url_analitica = 'api1.correos.es';
        } elseif (self::$environment == 'PRE') {
            self::$correos_url_analitica = 'api1.correospre.es';
        } else {
            die('Error 00010: Entorno no válido');
        }
        return self::$correos_url_analitica;
    }

}
