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
 * Clase BridgeCEX
 *
 * Esta clase es un puente para las particularidades de CEX
 * Adapta datos para el webservices de CEX
 *
 */
class BridgeCEXAdapter
{

    /**
     * Según el código destino de CEX se retorna la siguiente tabla:
     * AD100 - 71003
     * AD200 - 71002
     * AD300 - 71008
     * AD400 - 71007
     * AD500 - 71001
     * AD600 - 71013
     * AD700 - 71014
     *
     * @param string  $cp_input  Código postal para ANDORRA
     * @return string $cp_output Código postal válido para el webservice de CEX o si no el mismo código de entrada.
    */
    public static function getWSAndorraPostalCode($cp_input)
    {
        $cp_output='';
        switch ($cp_input) {
            case 'AD100':
                $cp_output='71003';
                break;
            case 'AD200':
                $cp_output='71002';
                break;
            case 'AD300':
                $cp_output='71008';
                break;
            case 'AD400':
                $cp_output='71007';
                break;
            case 'AD500':
                $cp_output='71001';
                break;
            case 'AD600':
                $cp_output='71013';
                break;
            case 'AD700':
                $cp_output='71014';
                break;
            default:
                $cp_output=$cp_input;
        }
        
        return $cp_output;
    }
}
