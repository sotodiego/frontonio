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
class WSValidationResponse {
    /**
	 * Valida la respuesta del webservice de CEX
     * @param array $retorno del webservice para ser procesado.
	 * @return array: $retorno array con el mensaje de error y estado de validación a true o false. 
	 */
    public static function validateRestRequest($retorno) {
        
        $validacion = false;

        switch($retorno['status']){
            case "404":
            case "0":
                $message=array(
                    'error_code'  =>  '404',
                    'type'        =>  'error'
                    );
                $validacion=false;
                break;
            case "401":
                $message=array(
                    'error_code'  =>  '401',
                    'type'        =>  'error'
                    );
                $validacion=false;
                break;       
            case "200":
                $message=array(
                    'error_code'  =>  '200',
                    'type'        =>  'success'
                    );
                $validacion=true;
                break;
            default:
                $message=array(
                    'error_code'  =>  '999',
                    'type'        =>  'error'
                    );
                $validacion=false;
                break;

        }  
        $retorno  = array(
            'message'   => $message,
            'validacion'    => $validacion 
        );  
       
      return json_encode($retorno);
    }
}
?>