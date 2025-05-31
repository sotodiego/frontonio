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
 * Clase Necesita Aduana
 *
 * Controla si el envío necesita aduana, ya sea interiores o exteriores.
 */
class NeedCustoms
{

    public static function isCustomsRequired($cp_source, $cp_dest, $country_source, $country_dest)
    {
        $sc_tenerife = 38;
        $lp_gran_canaria = 35;
        $ceuta = 51;
        $melilla = 52;

        if(is_string($cp_source)) {
            $cp_source2 = substr($cp_source, 0, 2);
        }
        $cp_dest2 = substr($cp_dest, 0, 2);

        /* Si el país de origen es distinto al de destino */
        if ($country_source != $country_dest) {
            return true;
        }

        /* Si es el mismo código de origen que destino, o un envío entre Islas Canarias */
        if ($cp_source2 == $cp_dest2 || ($cp_source2 == $sc_tenerife && $cp_dest2 == $lp_gran_canaria) || ($cp_source2 == $lp_gran_canaria && $cp_dest2 == $sc_tenerife)
        ) {
            return false;
        }

        $excluded = array($sc_tenerife, $lp_gran_canaria, $ceuta, $melilla);

        if (in_array($cp_source2, $excluded) || in_array($cp_dest2, $excluded)) {

            /* Si los códigos postales son distintos */
            if ($cp_source != $cp_dest) {
                return true;
            }
        }
        return false;
    }

    public static function isInternational($country_source, $country_dest)
    {
        // Mismo origen y destino -> Envío Nacional
        if ($country_source == $country_dest) {
            return false;
            // Si pais de origen ES, PT, AD y destino ES, AD, PT -> Envío Nacional
        } else if ($country_source == 'ES') {
            if ($country_dest == 'AD' || $country_dest == 'PT') {
                return false;
            } else {
                return true;
            }
        } else if ($country_source == 'AD') {
            if ($country_dest == 'ES' || $country_dest == 'PT') {
                return false;
            } else {
                return true;
            }
        } else if ($country_source == 'PT') {
            if ($country_dest == 'AD' || $country_dest == 'ES') {
                return false;
            } else {
                return true;
            }
            // Ninguno de los casos anteriores -> Envío Internacional
        } else {
            return true;
        }
    }

}
