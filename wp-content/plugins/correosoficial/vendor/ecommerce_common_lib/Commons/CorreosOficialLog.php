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
 * Clase Log
 *
 * Clase para la gestión de logs
 *
 */
class CorreosOficialLog
{
     /**
      * @params void
      * @return d-m-Y H:i:s:u Devuelve fecha con microsegundos en formato dia/mes/año hora:min:segundos:microsegundos
      */
    public static function logDate()
    {
        $now = DateTime::createFromFormat('U.u', microtime(true));
        return $now->format("d-m-Y H:i:s:u");
    }

    /**
     * Obtiene tamaño del fichero de cron_error_log
     * @params void
     * @return int tamaño del fichero de Log de Errores en Kb
     */
    public static function getSizeErrorLog($file)
    {
        $size = filesize($file);
        return intval($size/1000);
    }

    /**
     * Rota el fichero de cron_error_log
     * @params void
     * @return void
     */
    public static function rotateErrorLog($file)
    {
       $backupErrorLog = str_replace(".txt", "", $file)."-lastbackup.txt";

       if (!copy($file, $backupErrorLog)) {
            error_log("CorreosEcommerce: No se pudo copiar el fichero de error para rotar: ".$file);
       }

       if (!unlink($file)) {
                error_log("CorreosEcommerce: No se pudo eliminar fichero de error para rotar: ".$file);
        }
    }

}
