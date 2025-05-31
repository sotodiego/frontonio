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
require_once dirname(__FILE__).'/../../ecommerce_common_lib/Correos/CorreosDaoObject.php';
require_once dirname(__FILE__).'/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../ecommerce_common_lib/CorreosOficialUtils.php';
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;
require_once dirname(__FILE__).'/../../ecommerce_common_lib/DetectPlatform.php';

class CorreosOficialCustomerDataDao extends CorreosOficialDAO
{

    public function __construct()
    {
        parent::__construct();
    }
    public function updateFieldsSetRecord($fields)
    {
        foreach ($fields as $field => $value) {
            $this->createSettingRecord($field, $value, 'correos_oficial_configuration');
        }
    }

    public function addCustomerCode($CorreosOficialCustomerCode, $Company, $fields)
    {
        if (DetectPlatform::isPrestashop()) {
            // Se escapan comillas de la contraseña
            $fields['CorreosPassword'] = str_replace('\\', '\\\\', $fields['CorreosPassword']);
        }

        if ($fields['CorreosPassword']!='n/a') {
            $fields['CorreosPassword']= CorreosOficialCrypto::encrypt($fields['CorreosPassword']);
        }
      
        if ($fields['CEXPassword']!='n/a') {
            $fields['CEXPassword'] = CorreosOficialCrypto::encrypt($fields['CEXPassword']);
        }
        
        return $this->addCustomerCodeDao($CorreosOficialCustomerCode, $Company, $fields);
    }
   
    public function deleteCustomerCode($CorreosOficialCustomerCode)
    {
        $this->deleteCustomerCodeDao($CorreosOficialCustomerCode);
        $this->deleteCustomerDao($CorreosOficialCustomerCode);
    }

    public function getDataTableCustomerList()
    {
        $records = array();
        
        // Se evita que de error de datatable al cargar la liste de clientes activos en Ajustes
        if (!extension_loaded('soap')) {
            die(json_encode($records));
        }

        // Obtenemos todos los registros de la tabla
        $records=$this->getRecords('correos_oficial_codes');

        // Recorremos todos los registros, y añadimos una columna status para indicar si la conexión es correcta o no
        foreach ($records as $record) {

            // Si es Correos, hacemos una petición SOAP para comprobar que la conexión es correcta
            if ($record->company=='Correos') {
                $checkCorreosConnection = json_decode((new CorreosSoap())->altaClienteCorreosOpCall($record->id));
                $record->status = $checkCorreosConnection->validacion ? true : false;
            }
            
            if ($record->company=='CEX') {
                $checkCexConnection = json_decode((new CexRest())->altaClienteCEXCall($record->id));
                $record->status = $checkCexConnection->validacion ? true : false;
            }

        }



        die(json_encode($records));
    }
   
    public function getUserPassword($id)
    {
        $record=$this->getUserAndPasswordDao('id', $id, 'correos_oficial_codes');

        if (isset($record)) {
            if ($record->company=='Correos') {
                $user_password['login']=$record->CorreosUser;
                $user_password['password']=$record->CorreosPassword;
            } elseif ($record->company=='CEX') {
                $user_password['login']=$record->CEXUser;
                $user_password['password']=$record->CEXPassword;
            } else {
                throw new LogicException(__FILE__. " No se ha indicado la compañía al recuperar las credenciales.");
            }
            return $user_password;
        }
    }

    public function getIdByCompany($company)
    {
        return $this->getIdByCompanyDao($company);
    }
    public function getCustomerCodeByCompany($company){
        return $this->readRecord('correos_oficial_codes', "WHERE company='$company'");
    }

    public function getAllCode($id)
    {
        return $this->readRecord('correos_oficial_codes', "WHERE id= " . (int) $id);
    }

    public function getIdCodeFromSenderByDao($id_sender, $company = 'correos')
    {
        return $this->getIdCodeFromSender($id_sender, $company);
    }

    public function getCodeFromShipping($shipping, $company = 'correos', $origin = 'order')
    {

        if (DetectPlatform::isWordPress()) {
            global $wpdb;
            $sql =
            "SELECT c." . strtolower($company) . "_code
                FROM " . $wpdb->prefix . "correos_oficial_".$origin."s a 
                LEFT JOIN " . $wpdb->prefix . "correos_oficial_saved_".$origin."s b ON b.id_order = a.id_order
                LEFT JOIN " . $wpdb->prefix . "correos_oficial_senders c ON c.id = a.id_sender
                WHERE b.shipping_number = $shipping OR b.exp_number = $shipping";

            $result = $wpdb->get_var($sql);
    
        } elseif (DetectPlatform::isPrestashop()) {
            $sql =
            "SELECT c." . strtolower($company) . "_code
                FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_".$origin."s a 
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_".$origin."s b ON b.id_order = a.id_order
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders c ON c.id = a.id_sender
                WHERE b.shipping_number = $shipping OR b.exp_number = $shipping";
                
            $result = Db::getInstance()->getValue($sql);
        }

        return $result;
    }
}
