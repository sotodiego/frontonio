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

/**
 * Clase Normalizar Datos
 *
 * Controla que los datos necesarios entren en la BBDD sin espacios y en mayúsculas
 * Filtra y sanea los datos de entrada.
 */
class Normalization
{

    private static $regex = "/^[a-zA-Z0-9\.\-_=ªº°’€`´ÁÉÍÓÚáéíóúàèìòùÀÈÌÒÙÑñÇçüÜÂâÃãÊêÔôÕõ():,*\/¿?“·$%&[\]{}\^\+\;\<\>\|\~\#!¡@ åÄßÖÆØŒÐÞŐŰŁĆĐ]+$/";
    private static $regexpasswd = "/^[a-zA-Z0-9\!\"\\@\$%'#\(\)\*\+,\-\.\/\:;\=\>\?@\[\]\^_`\{\|\}~]+$/";

    /**
     * @param mixed $input campo a introducir en la BD
     * @param string $type tipo de campo (alfanumérico, email, etc)
     *
     * Ejemplo de uso:
     * @example $variable=NormalizeData::normalize($variable);
     * @example $variable=NormalizeData::normalize($email, 'email');
     */
    public static function normalizeData($input, $type = 'alphanumeric')
    {

        $input = trim($input); // TODO: Da warning para tipos array
        $input = self::getData($input, $type);

        // Tratamiento de dato tipo array
        if (is_array($input)) {

            $output = array();
            $n = 0;

            // Tratamiento array dentro de array
            foreach ($input as $key => $value) {

                if (is_array($value) || is_array($key)) {
                    $output[$n] = array();
                    foreach ($value as $key => $value2) {

                        if (is_array($value2)) {
                            $value2 = trim($value2[0]);
                        } else {
                            $value2 = trim($value2);
                        }

                        $output[$n][$key] = self::sanitize($value2, $type);
                    }
                    $n++;
                } else {
                    // Tratamiento datos atómicos
                    $type_data = self::isEmail($value, $type);
                    $output[$key] = self::sanitize($value, $type_data, $key);
                }
            }
        } else {
            $output = self::sanitize($input, $type);
        }

        return $output;
    }

    /**
     * @param  $input campo a introducir en la BD
     * @return devuelve $input convertida a mayúsculas excepto las excepciones
     */
    public static function toUpperCase($input)
    {
        $exceptions = array('on', 'true', 'false', 'Correos', 'CEX');

        /* Para campos tipo checkbox o radio button */
        if (!in_array($input, $exceptions)) {
            $input = strtoupper($input);
        }

        return $input;
    }

    /**
     * Se sanean los datos de entrada según el tipo
     * @param string $input
     * @return string $input saneado
     * @return string $key campos a excluir
     */
    public static function sanitize($input, $type, $key = null)
    {
        if ($type != 'password') {
            $input = trim($input, "'");
            $input = str_replace("\\", "", $input);
            $input = str_replace("'", "", $input);
        }

        if (is_integer($input)) {
            $input = filter_var($input, FILTER_VALIDATE_INT);
        } elseif ($type == 'email') {
            $input = filter_var($input, FILTER_VALIDATE_EMAIL);
        } elseif ($type == 'user' || $type == 'password') {
            $input = self::replaceDoubleQuote($input);
            $input = self::replaceBar($input);
            $input = filter_var($input, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => self::$regexpasswd)));
            $input = self::restoreBar($input);
        } elseif ($type == 'cookie_cart' || $type == 'no_uppercase') {
            $input = filter_var($input, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => self::$regex)));
        } elseif ($type == 'nospaces') {
            $input = preg_replace('/\s+/', '', $input);
        } else {
            $exclude_fields = array('customer_cp', 'customer_firstname', 'customer_lastname', 'customer_company', 'customer_contact',
                                     'customer_address', 'customer_city', 'customer_phone', 'customer_phone', 'customer_dni');
            if (!in_array($key, $exclude_fields)) {
                $input = filter_var($input, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => self::$regex)));
            } 
            
            $input = self::replaceQuote($input);
            $input = self::toUpperCase($input);
            $input = self::restoreQuote($input);
        }
        return $input;
    }

    /**
     * Adecuación del nombre del fichero
     * @param string $targetPath
     * @return string $targetPath nombre del fichero
     */
    public static function filterFiles($targetPath)
    {
        $info = new SplFileInfo($targetPath);
        $full_name = $info->getBaseName();
        $ext = $info->getExtension();
        $allowed_ext = array('png', 'jpg', 'jpeg');

        if (!in_array($ext, $allowed_ext)) {
            return "ERROR: 12010";
        }

        $name = basename($full_name, "." . $ext);
        $targetPath = filter_var($name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => self::$regex)));
        $targetPath = $targetPath . "." . $ext;

        return $targetPath;
    }

    /**
     * Retorna el dato según la entrada y la plataforma
     * @param string $input entrada
     * @param string $type  tipo de dato
     */
    private static function getData($input, $type)
    {
        if ($type != 'value') {
            if (DetectPlatform::isWordPress() && $type != 'cookie_cart') {
                if ($type == 'cookie') {
                    $input = $_COOKIE[$input];
                } else {
                    $input = isset($_REQUEST[$input]) ? $_REQUEST[$input] : '';
                }
            } elseif (DetectPlatform::isPrestashop()) {
                $input = Tools::getValue($input);
            }
        }
        return $input;
    }

    /**
     * Configura el tipo según sea email
     * @param string $value valor
     * @param string $type  tipo de dato
     */
    private static function isEmail($value, $type)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $type_data = 'email';
        } else {
            $type_data = $type;
        }
        return $type_data;
    }

    public static function replaceQuote($input)
    {
        return str_replace(array('’', "`", "´"), "__QUOTE__", $input);
    }

    public static function restoreQuote($input)
    {
        return str_replace('__QUOTE__', "’", $input);
    }

    public static function replaceBar($input)
    {
        return str_replace("\\", "__BAR__", $input);
    }

    public static function restoreBar($input)
    {
        return str_replace("__BAR__", "\\", $input);
    }

    public static function replaceDoubleQuote($input)
    {
        return str_replace('\"', '"', $input);
    }

}
