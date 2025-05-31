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

require_once dirname(__FILE__) . '/functions.php';

/**
 * Clase de uso general
 * Métodos que se utilizan en todas las plataformas.
 */
class CorreosOficialUtils
{

    /**
     * Función Genérica para traducir cadena
     * @param string $string_from_db La cadena que viene ya de la base de datos
     * @param int $id_language el id del idioma (de la plataforma)
     * @param string la nueva cadena a añadir.
     * @return json json co los idiomas
     */
    public static function translateStringsToDB($string_from_db, $id_language, $string)
    {

        // No permitimos grabar si no selecciona lenguage.
        if ($id_language == 0) {
            return false;
        }

        $dest_array = array();

        // Añadir lo que recupero de la bbdd...
        $dest_array = (json_decode($string_from_db, true));

        // Añadimos a idioma por id
        $dest_array[$id_language] = CorreosOficialUtils::replaceBadCharaters($string);

        return json_encode($dest_array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Función Genérica para traducir cadena
     * @param string $string_from_db La cadena que viene ya de la base de datos
     * @param int $id_language el id del idioma (de la plataforma)
     * @param string la nueva cadena a añadir.
     */
    public static function translateStringsFromDB($string_from_db, $id_language)
    {
        $dest_array = array();

        // Añadir lo que recupero de la bbdd...
        if (isset($string_from_db)) {
            $dest_array = (json_decode(CorreosOficialUtils::restoreBadCharacters($string_from_db), true, 512, JSON_UNESCAPED_UNICODE));
        }

        // Si no tenemos configurado el Mensaje para advertir al comprador sobre los tramites aduaneros
        if (!$dest_array) {
            return false;
        }

        if ($id_language != null && array_key_exists($id_language, $dest_array)) {
            return $dest_array[$id_language];
        }
        return false;
    }

    public static function replaceBadCharaters($string)
    {
        return str_replace("'", "__APOS__", $string);
    }

    public static function restoreBadCharacters($string)
    {
        return str_replace("__APOS__", "'", $string);
    }

    /**
     * Devolvemos los idiomas de Prestashop que tenga activos.
     * @param $contex Object Objeto de contexto de Pretashop, null si Wordpress
     * @return Array con los idiomas activos de Prestashop.
     */
    public static function getActiveLanguages($context = null)
    {
        if (DetectPlatform::isPrestashop()) {
            return Language::getLanguages(true, $context->shop->id);
        } elseif (DetectPlatform::isWordpress()) {
            require_once dirname(__FILE__) . '/Commons/BridgeWCLanguage.php';
            return BridgeWCLanguage::getLanguagesFromWC();
        }
    }

    /**
     * Rellenamos selector de idiomas con los idiomas de Prestashop que tenga activos.
     * y deja seleccionado el último idioma gestionado.
     * @param $actives_languages Array Idioma activos de Pretashop
     * @param $selected_language_id int Último idioma seleccionado
     * @param $contex Object Objeto de contexto de Pretashop
     * @return Array con los idiomas activos de Prestashop.
     */
    public static function fillLanguagesSelector($active_languages, $context, $selected_language_id = '')
    {
        if (DetectPlatform::isPrestashop()) {
            $smarty = $context->smarty;
        } elseif (DetectPlatform::isWordpress()) {
            $smarty = $context;
        }

        foreach ($active_languages as $language) {
            $array_languages[$language['id_lang']] = $language['iso_code'];
        }

        if (!empty($array_languages)) {
            $smarty->assign('array_languages', $array_languages);
            if ($selected_language_id != '') {
                $smarty->assign('selected_language_id', $selected_language_id);
            } else {
                $smarty->assign('selected_language_id', '');
            }
        } else {
            $smarty->assign('selected_language', '');
            $smarty->assign('selected_language_id', '');
        }
    }

    /*
     * Conseguimos solo las mayúsculas.
     */
    public static function getOnlyUpperCases($string)
    {
        return preg_replace('~[^A-Z]~', '', $string);
    }

    /**
     * Función que valida un xml
     * @todo refactorizar.
     */
    public static function isValidXml($xml)
    {
        try {
            if (empty($xml)) {
                return false;
            }
            libxml_use_internal_errors(true);
            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->loadXML($xml);
            $errors = libxml_get_errors();

            return empty($errors);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene la tabla según plataforma.
     */
    public static function getPrefix()
    {
        if (DetectPlatform::isWordPress()) {
            global $wpdb;
            return $wpdb->prefix;
        } elseif (DetectPlatform::isPrestashop()) {
            return _DB_PREFIX_;
        }
    }

    /**
     * Activa trazas. Ver includes/config.php.
     * @param string $texto texto descriptivo
     * @param mixed  $var variable a depurar
     * @param bool  $die Si $die!=null se para la ejecución y/o se muestra un mensaje
     */
    public static function varDump($text, $var, $die = null)
    {
        global $co_debugCorreosOficial;
        if ($co_debugCorreosOficial) {
            var_dump($text, $var);
        }

        if ($die != null) {
            die('Error 00000: Ejecución parada: ' . $die);
        }
    }

    /**
     * Reemplazo de caracteres unicode
     */
    public static function replaceUnicodeCharacters($str)
    {
        $str = str_replace('u00c1', 'Á', $str);
        $str = str_replace('u00e1', 'á', $str);
        $str = str_replace('u00c9', 'É', $str);
        $str = str_replace('u00e9', 'é', $str);
        $str = str_replace('u00cd', 'Í', $str);
        $str = str_replace('u00ed', 'í', $str);
        $str = str_replace('u00d3', 'Ó', $str);
        $str = str_replace('u00f3', 'ó', $str);
        $str = str_replace('u00da', 'Ú', $str);
        $str = str_replace('u00fa', 'ú', $str);
        $str = str_replace('u00d1', 'Ñ', $str);
        $str = str_replace('u00f1', 'ñ', $str);
        $str = str_replace('u00bf', '¿', $str);
        return $str;
    }

    /**
     * Reemplazo de caracteres unicode
     */
    public static function replaceCharacterWithEntities($str)
    {
        $src = array("á", "é", "í", "ó", "ú",
            "Á", "É", "Í", "Ó", "Ú",
            "ñ", "Ñ", "ü", "Ü");
        $dest = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;",
            "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;",
            "&ntilde;", "&Ntilde", "&uuml;", "&Uuml;");
        $str = str_replace($src, $dest, $str);
        return $str;
    }

    /**
     * Cambia el estado de pedido en según la plataforma
     * @param int $id_order nº de pedido de Prestashop
     * @param int $id_state estado del pedido
     * @param int $id_employee id del Administrador (como administrador de sistema).
     */
    public static function changeOrderStatus($idOrder, $order_status, $id_employee = 1)
    {
        if (DetectPlatform::isPrestashop()) {
            $order = new Order($idOrder);
            $order_data = $order->getFields();
            if (!empty($order_status) && strcmp($order_data['current_state'], $order_status) != 0) {
                $order->setCurrentState($order_status, $id_employee);
                $order->save();
            }
        } elseif (DetectPlatform::isWordpress()) {
            $order = new WC_Order($idOrder);

            if ($order->set_status($order_status)) {
                $order->save();
            }
        }
    }

    // Borra archivos temporales de la carpeta pdftmp
    public static function deleteFiles()
    {
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/labels*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/CEX_*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/E_*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/CN23*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/DCAF*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/DDP*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/pdftmp/manifest_*.*') as $filename) {
            unlink($filename);
        }
        return array('Resultado' => 'Etiquetas del directorio pdftmp eliminados correctamente');
    }

    /**
     * Método para depuración en cliente
     * Imprime datos por pantalla formateados y escribe un fichero de log a nivel de donde se llame la función
     *
     * @param string $string Identificador para poder reconocer que traza es.
     * @param string $var Variable a depurar.
     * @param bool $print Si true imprime la salida por el navegador (por defecto). Si false no imprime salida.
     * @return void
     */
    public static function debug($string, $var, $print = true)
    {
        $debug = "[" . date("Y-m-d H:i:s") . "] - ";
        $debug .= print_r(strtoupper($string), true) . "\r\n" . print_r($var, true) . "\r\n";

        $separator = "\r\n--------------------------------------------------------------------------------------------------------\r\n";

        if ($print) {
            echo "<pre>" . nl2br($debug) . "</pre>";
            echo "<BR>----------------------------------------------------------------------------------------------------------------<BR>";
        }

        file_put_contents(dirname(__FILE__) . "/debug.log.txt", $debug . $separator, FILE_APPEND);
    }

    /**
     * Método para saneamiento de datos
     * Sanitiza los datos según el tipo con métodos de WordPress
     *
     * @param mixed $data El dato sin sanear
     * @return $data      El dato saneado
     */
    public static function sanitize($data)
    {
        // Si es una cadena, simplemente sanitiza y regresa
        if (is_string($data)) {
            return sanitize_text_field($data);
        }

        // Si es un array, recorre cada elemento
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = self::sanitize($v); // Con self podemos tratar arrays de cualquier profundidad.
            } else {
                $data[$k] = sanitize_text_field($v);
            }
        }
        return $data;
    }

    /**
     * Verifica si un archivo existe y es escribible.
     *
     * @param string $fileName El nombre del archivo a verificar.
     * @return bool Devuelve true si el archivo existe en el directorio, de lo contrario devuelve false.
     */
    public static function dirExists($fileName)
    {
        return is_dir(dirname($fileName));
    }

    /**
     * Transforma el array para que sea igual al que devuelve el método getShippingNumbersByExp de UtilitiesDao
     */
    public static function transformArrayForPickups($array) {
        $transformed_array = array();
        foreach ($array as $item) {
            $transformed_array[] = array(
                'shipping_number' => (string)$item[0]
            );
        }
        return $transformed_array;
    }

    /**
     * Limpia los teléfonos que empiecen por 34, 351 y combinaciones
     */
    public static function cleanTelephoneNumber($number) {

        $number = str_replace(' ', '', $number);

        if       ( substr($number, 0, 5) == '0034 ' ) {         $result = substr($number, 5);
        } elseif ( substr($number, 0, 5) == '0034-' ) {         $result = substr($number, 5);
        } elseif ( substr($number, 0, 4) == '0034' ) {          $result = substr($number, 4);
        } elseif ( substr($number, 0, 4) == '034 ' ) {          $result = substr($number, 4);
        } elseif ( substr($number, 0, 4) == '034-' ) {          $result = substr($number, 4);    
        } elseif ( substr($number, 0, 4) == '+34 ' ) {          $result = substr($number, 4);
        } elseif ( substr($number, 0, 4) == '+34-' ) {          $result = substr($number, 4);
        } elseif ( substr($number, 0, 3) == '+34' ) {           $result = substr($number, 3);
        } elseif ( substr($number, 0, 3) == '34 ' ) {           $result = substr($number, 3);
        } elseif ( substr($number, 0, 3) == '34-' ) {           $result = substr($number, 3);
        } elseif ( substr($number, 0, 2) == '34' ) {            $result = substr($number, 2);
        } elseif ( substr($number, 0, 4) == '+351' ) {            $result = substr($number, 4);
        } elseif ( substr($number, 0, 5) == '+351-' ) {            $result = substr($number, 5);
        } elseif ( substr($number, 0, 4) == '+351 ' ) {            $result = substr($number, 4);
        } else {
            $result = $number;
        }       
        
        $result = str_replace('-', '', $result);

        return $result;
    }

    /**
     * Limpiamos todos los números del prefijo +34 y +351
     * Si el producto no está dentro de los de la península no tiene 9 dígitos ni empieza por 6 , 7 y 9
     * Si el producto está dentro de la península, el destino es ES o AD, no tiene 9 dígitos ni empieza por 6 o 7
     * Si el producto está dentro de la península, pero es un envio a PT, no tiene 9 dígitos ni empieza por 9
     * se devuelve vacío para que no se copie en NumeroSMS
     */
    public static function getMobilePhone($number, $iso, $product_code = null) {

        $products = ['S0132', 'S0133', 'S0178', 'S0176', 'S0235', 'S0236', 'S0179'];

        $result = self::cleanTelephoneNumber($number);
        
        if ($product_code !== null && 
            (strlen($result) !== 9 || 
            !(substr($result, 0, 1) == '6' || 
            substr($result, 0, 1) == '7' || 
            substr($result, 0, 1) == '9') || 
            !in_array($product_code, $products))) {
            return '';
        }
        
        // Comprobación adicional según el destino
        if ($iso === 'ES' || $iso === 'AD') {
            if (strlen($result) === 9 && 
            (substr($result, 0, 1) == '6' || 
            substr($result, 0, 1) == '7')) {
                return $result;  // Devuelve el número para <NumeroSMS>
            }
        } elseif ($iso === 'PT') {
            if (strlen($result) === 9 && 
            substr($result, 0, 1) == '9') {
                return $result; 
            }
        }

        return '';
    }

    /**
     * Devuelve el formato requerido para el Código Postal del Remitente
     * Si el remitente es de Portugal, solo se deveuelven los 4 primeros dígitos
     */
    public static function getSenderPostalCode($country, $postalCode) {

        if ($country == 'PT') {
            $result = substr($postalCode, 0, 4);
        } else {
            $result = $postalCode;
        }    
    
        return $result;

    }

    /**
     * Comprueba si el dni esta guardado como string
     * @param mixed $customerDni
     * @return string
     */
    public static function nifIsAnString($customerDni) {
        if(is_string($customerDni)) {
            return $customerDni;
        } else {
            return '';
        }
    }
    /**
     * Escribe en el fichero correosoficial/sql/install_error.log
     * @param string $line - Línea a escribir en el fichero
     * @return void
     */
    public static function writeInstallErrorLog($line) {
        $now = DateTime::createFromFormat('U.u', microtime(true));
        $date_time = $now->format("d-m-Y H:i:s:u");
        
        // Combina la fecha y el mensaje
        $logMessage = $date_time . ": " . $line."\r\n";
        // Escribe en el archivo
        file_put_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/sql/install_error.log", $logMessage."\r\n", FILE_APPEND);
    }

    /**
     * Comprueba que la extensión SOAP esté cargada
     * @param string $error Mensaje de error a mostrar
     */
    public static function checkSoapInstalled($error){
        if (!extension_loaded('soap')) {

            if (DetectPlatform::isPrestashop()) {
                // Agregar un mensaje de error visible en el administrador de PrestaShop
                Context::getContext()->controller->errors[] = $error;
            } elseif (DetectPlatform::isWordpress()) {
                add_action('admin_notices', function() use ($error) {
                    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error) . '</p></div>';
                });
            }
            return false;
        }
        return true;
    }

    public static function sislogModuleIsActive() {

        if (DetectPlatform::isWordPress()) {
            if (!function_exists('is_plugin_active')) {
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
    
            if (function_exists('is_plugin_active') && is_plugin_active('correosecomsga/correosecomsga.php')) {
                return true;
            }
    
            return false;
        }

        if (DetectPlatform::isPrestashop()) {
            if (Module::isEnabled('correosecomsga')) {
                return true;
            }
    
            return false;
        }
    }

}
