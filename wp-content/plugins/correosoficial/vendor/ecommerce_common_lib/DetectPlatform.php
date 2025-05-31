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
 * @uses    Provée métodos para detectar plataformas
 * @version: 1
 *
 */
class DetectPlatform
{
    /** @return bool Return true if is Prestashop */
    public static function isPrestashop()
    {
        if (defined('_PS_VERSION_')) {
            return true;
        }
    }

    /** @return bool Return true if is Wordpress */
    public static function isWordPress()
    {
        if (function_exists('add_action') && !defined('_PS_VERSION_')) {
            return true;
        }
    }
}
