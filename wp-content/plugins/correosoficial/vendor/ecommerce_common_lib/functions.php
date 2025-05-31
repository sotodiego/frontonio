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
require_once "DetectPlatform.php";

//Para debug completo del var_dump
ini_set('xdebug.var_display_max_depth', -1 );
ini_set('xdebug.var_display_max_children', -1 );
ini_set('xdebug.var_display_max_data', -1 );

/**
 * Devuelve la ruta real del fichero pasado como parámetro
 * @param string $file Fichero, eje: c:\ruta\fichero.php
 * @return string $ruta Ruta completa del fichero, ej: c:\ruta\ 
 */
function get_real_path($file) {
	return dirname(realpath($file));
}

