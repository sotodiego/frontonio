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

class CorreosOficialSendersDao extends CorreosOficialDAO {

   private $table='correos_oficial_senders';

   public function __construct() {
      parent::__construct();
   }

   public static function getValue($value, $key, $field, $table){
        $dao = new CorreosOficialDAO();
        $record=$dao->readRecord($table, " WHERE $field='$key'", $value, true);

        if (isset($record[0])) {
            return $record[0];
        } else {
            return false;
        }
    }

   //Inserta un registro completo
   public function insertFieldsSetRecord($fields){
      $this->insertSendersRecord($fields);
   }

   //Actualiza un registro completo
   public function updateFieldsSetRecord($fields){
      $this->updateSendersRecord($fields);
   }

   //Actualiza default_sender
   public function updateFieldSetRecord($field){
      $this->saveSenderDefaultRecord($field);
   }

   //Borra un registro completo
   public function deleteFieldsSetRecord($field){
      $this->deleteSenderRecord($field);
   }

   /* **********************************************************************************************************
     *                              AJUSTES - Remitentes
     ********************************************************************************************************* */

    /**
     * Función para insertar remitente (senders)
     * @param $data: id a eliminar
     * @param $table: tabla en la que buscar
     */
    public function insertSendersRecord($data)
    {
        $this->insertRecord($this->table, $data);

        $lastId=$this->getLastId($this->table);

        // Si solo hay un remitente es el remitente por defecto
        if (count($this->readSenders()) == 1) {
            $this->saveSenderDefaultRecord($lastId[0]->id);
        }
        //echo $lastId[0]->id;
        //die();
    }

    /**
     * Función para actualizar destinatario por defecto remitentes (senders)
     * @param $data: id a eliminar
     * @param $table: tabla en la que buscar
     * tabla 'correos_oficial_senders'
     */
    public function saveSenderDefaultRecord($data)
    {
        $query = "UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET sender_default='0'";
        $query2 = "UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET sender_default='1' WHERE id=". $data;

        $this->executeQuery($query);
        $this->executeQuery($query2);
    }

    /**
     * Función para actualizar registros de remitentes (senders)
     * @param $data: id a eliminar
     * @param $table: tabla en la que buscar
     */
    public function updateSendersRecord($data)
    {

        $query = "UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET 
         sender_name = '$data[sender_name]',
         sender_address = '$data[sender_address]',
         sender_cp = '$data[sender_cp]',
         sender_nif_cif = '$data[sender_nif_cif]',
         sender_city = '$data[sender_city]',
         sender_contact = '$data[sender_contact]',
         sender_phone = '$data[sender_phone]',
         sender_from_time = '$data[sender_from_time]',
         sender_to_time = '$data[sender_to_time]',
         sender_iso_code_pais = '$data[sender_iso_code_pais]',
         sender_email = '$data[sender_email]',
         correos_code = '$data[correos_code]',
         cex_code = '$data[cex_code]'
         where id=$data[id]";
        $this->executeQuery($query);
    }

    /**
     * Función para eliminar registros de remitentes (senders)
     * @param $data: id a eliminar
     * @param $table: tabla en la que buscar
     */
    public function deleteSenderRecord($data)
    {
       $this->deleteRecord($data, $this->table);
    }

    /**
     * Función para conseguir registros de remitentes (senders)
     * @param $where: condición where de la sentencia SQL
     */
    public function readSenders($where='')
    {
        $records=$this->readRecord($this->table, $where);

        if ($records === false) {
            return null;
        }
        return $records;
   }
  
    public static function getDefaultSender($id_sender = false){
        if ($id_sender) {
            return self::getValue('*' , (int) $id_sender, 'id', 'correos_oficial_senders');
        }
        return self::getValue('*', 1, 'sender_default', 'correos_oficial_senders');
    }

    public static function getSenders($company = false){
        $class = new self();

        if (!$company) {
            $dao = new CorreosOficialDAO();
            return $dao->getRecords('correos_oficial_senders', true);
        }

        $where = 'WHERE ' . strtolower($company) . '_code <> 0 ORDER BY sender_default desc';
 
        return $class->readRecord('correos_oficial_senders', $where, null, true);
    }

    public static function getSendersWithCodes(){
    
        $class = new self();
        $senders = $class->getSenders(false);

        foreach($senders as $key => $value){

            if($senders[$key]['correos_code'] != 0){
                $resultCorreos = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['correos_code'], null, true);
                $senders[$key]['correos_code'] = $resultCorreos[0]['CorreosCustomer'];
            }

            if($senders[$key]['cex_code'] != 0){
                $resultCEX = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['cex_code'], null, true);
                    $resultCEX = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['cex_code'], null, true);
                $senders[$key]['cex_code'] = $resultCEX[0]['CEXCustomer'];
            }

        }

        return $senders;
    }

    public static function getCodeBySenderAndCompany($sender_id, $company){
        if($company == 'correos' || $company == 'cex'){
            $sender = self::getValue('*', $sender_id, 'id', 'correos_oficial_senders');
            $code_id = $sender[$company.'_code'];
            $code = self::getValue('*', $code_id, 'id', 'correos_oficial_codes');
            return $code;
        }else{
            return null;
        }
    }
  
    public static function getSenderById($sender_id){
            return self::getValue('*', $sender_id, 'id', 'correos_oficial_senders');
    }
  
    public static function getDefaultTime(){
          return self::getValue('sender_from_time, sender_to_time', 1, 'sender_default', 'correos_oficial_senders');
    }
}
