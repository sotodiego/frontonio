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

class BridgeWCLanguage
{

    /**
     * Compara iso_code con array de idiomas instalados en Woocommerce y devuelve el id_language configurado
     */
    public static function getIdLanguageByIsoCode($iso_code)
    {
        $iso_code = substr($iso_code, 0, 2);
        $array_lang = CorreosOficialUtils::getActiveLanguages();
        foreach ($array_lang as $lang) {
            if ($lang['iso_code'] == $iso_code) {
                return $lang['id_lang'];
            }
        }
    }

    /**
     * Devuelve un array con los id e ISO code de Wordpress
     */
    public static function getLanguagesFromWC()
    {
        $available_languages = get_available_languages();

        foreach ($available_languages as $key => $value) {
            $id_part1 = ord(substr($value, 0, 1));
            $id_part2 = ord(substr($value, 1, 2));

            $lang['id_lang'] = $id_part1 . $id_part2;
            $lang['iso_code'] = substr($value, 0, 2);
            $array_lang[$key] = $lang;
        }

        return $array_lang;
    }
}
