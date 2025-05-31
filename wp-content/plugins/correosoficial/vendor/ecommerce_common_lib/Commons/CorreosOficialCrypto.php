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
 * Proporciona métodos para la encriptación de claves
 */

namespace CorreosOficialCommonLib\Commons;
define ('MODULE_DIR', dirname(__FILE__));

class CorreosOficialCrypto
{
    /**
     * Función de encriptado de uso genérico
     * @param string data: cadena que queremos encriptar
     */
    const METHOD = 'aes-128-cbc';
    const SECRET_HASH_FILE  = MODULE_DIR.'/openssl/openssl_shiv/secret.hash.php';
    const SECRET_IV_FILE  = MODULE_DIR.'/openssl/openssl_shiv/secret.iv.php';

    public static function encrypt($data)
    {
        if (!file_exists(self::SECRET_HASH_FILE)) {
            $secret_hash= openssl_random_pseudo_bytes(32);
            $iv= openssl_random_pseudo_bytes(16);

            file_put_contents(self::SECRET_HASH_FILE, '<?php "');
            file_put_contents(self::SECRET_HASH_FILE, base64_encode($secret_hash), FILE_APPEND);
            file_put_contents(self::SECRET_HASH_FILE, '" ?>', FILE_APPEND);

            file_put_contents(self::SECRET_IV_FILE, '<?php "');
            file_put_contents(self::SECRET_IV_FILE, base64_encode($iv), FILE_APPEND);
            file_put_contents(self::SECRET_IV_FILE, '" ?>', FILE_APPEND);
        } else {
            $open_ssl_params=self::removePHPTags();
            $secret_hash=$open_ssl_params['secret_hash'];
            $iv=$open_ssl_params['iv'];
        }
        return openssl_encrypt($data, self::METHOD, $secret_hash, 0, $iv);
    }

    /**
     * Función de desencriptado de uso genérico
     * @param string data: cadena que queremos desencriptar
     */
    public static function decrypt($data)
    {
        $open_ssl_params=self::removePHPTags();
        $secret_hash=$open_ssl_params['secret_hash'];
        $iv=$open_ssl_params['iv'];
        return openssl_decrypt($data, self::METHOD, $secret_hash, 0, $iv);
    }

    /**
      * Devuelve un array decoficado de forma forma recursiva.
      * @param array in: array que contiene cadenas utf-8 y queremos decodificarlo.
      */
    public static function removePHPTags()
    {
        // Evitamos Warning
        if (!file_exists(self::SECRET_HASH_FILE)) {
            return;
        }

        $php_tags=array('<?php "', '" ?>');
        $secret_hash=base64_decode(str_replace($php_tags, '', file_get_contents(self::SECRET_HASH_FILE, FILE_USE_INCLUDE_PATH)));
        $iv=base64_decode(str_replace($php_tags, '', file_get_contents(self::SECRET_IV_FILE, FILE_USE_INCLUDE_PATH)));
       
        $ret = array();
        $ret['secret_hash']=$secret_hash;
        $ret['iv']=$iv;
        return $ret;
    }
}
