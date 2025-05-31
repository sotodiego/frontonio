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
 * CorreosOficialSmarty Clase de uso general
 * Métodos para Smarty que se utilizan en todas las plataformas.
 */

 require_once "DetectPlatform.php";

class CorreosOficialSmarty {

    public static function loadSmartyInstance (){

        $path = '';

        if(DetectPlatform::isPrestashop()) { 
            $path = _PS_MODULE_DIR_;
        } else if(DetectPlatform::isWordPress()) {
            $path = WP_PLUGIN_DIR . '/';
        }

        $smarty = new Smarty();
        $smarty->setCompileDir($path . 'correosoficial/views/templates_c');
        return $smarty;
    }
}