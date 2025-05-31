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
 * Clase CorreosOficialDao
 */
class CorreosOficialDAO
{

    /** Objecto pdo con el que trabajaremos */
    private $connection;

    /** Tabla de la base de datos sobre la que actuar */
    private $table;
    private $table2;

    private $instance;

    /**
     * @param $pconnection Es un objeto pdo
     */
    public function __construct()
    {
        $this->connection = $this->getDBConnection();
    }

    public function getDBConnection()
    {
        global $wpdb;
  
        $this->instance = $wpdb;

    }

    /* **********************************************************************************************************
     *                              AJUSTES - Configuración
     ********************************************************************************************************* */

    /**
     * Función para insertar en configuración  (ajustes)
     * @param $fields: array de campos
     * @param $value: valor a insertar
     */
    public function createSettingRecord($field, $value, $table, $type = 'text')
    {

        if ($field == $this->getRecordSettingRecordByPrimareKey($field, $table, $type)) {
            $this->instance->update("{$this->instance->prefix}$table",
                array('name' => $field, 'value' => $value),
                array('name' => $field),
                array('%s', '%s'),
                '%s'
            );
        } else {
            $this->instance->insert("{$this->instance->prefix}$table",
                array("name" => $field, 'value' => $value, 'type' => $type),
                array('%s', '%s', '%s'));
        }
    }

    /**
     * Función para conseguir registros la configuración
     * @param $key: clave
     * @param $where: condición where de la sentencia SQL
     */

    public function readSettings($name, $as_array = false)
    {   

        $this->table = 'correos_oficial_configuration';

        if ($as_array) {
            $output = 'ARRAY_A';
        } else {
            $output = 'OBJECT';
        }

        if (isset($name) && !empty($name)) {

            $record = $this->instance->get_row(
                $this->instance->prepare(
                    "SELECT name, value FROM {$this->instance->prefix}$this->table WHERE name=%s",
                    $name
                ),
                $output
            );
        }

        if ($record === false) {
            return null;
        }

        return $record;

    }

    /**
     * Función para conseguir registros de los ajustes
     * @param $table: tabla en la que buscar
     */
    public function getRecordSettingRecordByPrimareKey($key, $table)
    {

        if (isset($key) && !empty($key)) {

            $record = $this->instance->get_row(
                $this->instance->prepare("SELECT * FROM {$this->instance->prefix}$table WHERE name=%s", $key)
            );

            if ($record) {
                return $record->name;
            } else {
                return null;
            }
        } else {
            throw new LogicException(__FILE__ . " Debe indicarse un $key (valor primario) o un where en la llamada.");
        }
    }

    /* **********************************************************************************************************
     *                              AJUSTES - Clientes
     ********************************************************************************************************* */

    /**
     * Añade un cliente a la tabla de clientes
     * @param $customer_code Códigoo de cliente
     * @param $company: CEX o Correos
     * @param $fields array con los campos
     */
    public function addCustomerCodeDao($customer_code, $company, $fields)
    {

        $table = "correos_oficial_codes";
        $table2 = "correos_oficial_codes_actives";

        if (isset($fields['idCEX'])) {
            $id = $fields['idCEX'];
        } elseif (isset($fields['idCorreos'])) {
            $id = $fields['idCorreos'];
        }
        if (empty($id)) {
            $id = 9999999999;
        }

        if ($id == $this->getRecordById($id, $table)) {
            $query = "
            UPDATE {$this->instance->prefix}$table
            SET
               CorreosContract = '" . $fields['CorreosContract'] . "',
               CorreosCustomer = '" . $fields['CorreosCustomer'] . "',
               CorreosKey = '" . $fields['CorreosKey'] . "',
               CorreosUser = '" . $fields['CorreosUser'] . "',
               CorreosPassword = '" . $fields['CorreosPassword'] . "',
               CorreosOv2Code = '" . $fields['CorreosOv2Code'] . "',

               CEXCustomer = '" . $fields['CEXCustomer'] . "',
               CEXUser = '" . $fields['CEXUser'] . "',
               CEXPassword = '" . $fields['CEXPassword'] . "',

               customer_code = '" . $customer_code . "'
            WHERE id='" . $id . "'";

            $this->executeQuery($query);
            echo $id;
            die();
        } else {

            $result = $this->readRecord("correos_oficial_codes", "WHERE customer_code = '$customer_code'");

            if ($result) {
                return false;
            } else {
                $query = "INSERT INTO {$this->instance->prefix}$table
                (id, customer_code,  company,
                CorreosContract, CorreosCustomer, CorreosKey, CorreosUser, CorreosPassword, CorreosOv2Code,
                CEXCustomer, CEXUser, CEXPassword)
                VALUES (
                   NULL,
                   '$customer_code',
                   '$company',
       
                   '" . $fields['CorreosContract'] . "',
                   '" . $fields['CorreosCustomer'] . "',
                   '" . $fields['CorreosKey'] . "',
                   '" . $fields['CorreosUser'] . "',
                   '" . $fields['CorreosPassword'] . "',
                   '" . $fields['CorreosOv2Code'] . "',
                   '" . $fields['CEXCustomer'] . "',
                   '" . $fields['CEXUser'] . "',
                   '" . $fields['CEXPassword'] . "')";

                $this->executeQuery($query);
                $lastId = $this->getLastId($table);

                $this->instance->update(
                    "{$this->instance->prefix}$table2",
                    array('active' => 1),
                    array('company' => $company),
                    null,
                    '%s'
                );

                echo $lastId[0]->id;
                die();
            }
        }
    }

    /**
     * Elimina un cliente por id
     * @param $id id del cliente
     */
    public function deleteCustomerCodeDao($id)
    {
        $table = "{$this->instance->prefix}correos_oficial_codes";
        $table2 = "{$this->instance->prefix}correos_oficial_codes_actives";

        $customer = $this->readRecord('correos_oficial_codes', "WHERE id='$id'");
        $company = $customer[0]->company;

        $this->instance->delete("$table", array('id' => $id));
        $this->instance->update($table2,
            array('active' => 0),
            array('company' => $company),
            null,
            '%s'
        );

        die();
    }

    /**
     * Elimina un cliente por código de cliente.
     * @param $customer_code Código de cliente.
     */
    public function deleteCustomerDao($customer_code)
    {
        $this->table = "correos_oficial_customers";

        $this->instance->delete("{$this->instance->prefix}$this->table", array('customer_code' => $customer_code));
    }

    /* **********************************************************************************************************
     *                                  AJUSTES - Productos
     ********************************************************************************************************* */
    /**
     * Función para actualizar productos
     * @param $table: tabla en la que buscar
     */
    public function updateProductsDao($id)
    {
        $table = "{$this->instance->prefix}correos_oficial_products";

        $this->instance->update($table,
            array('active' => 1),
            array('id' => $id),
            null,
            '%d'
        );
    }

    /**
     * Función para actualizar productos
     * @param $table: tabla en la que buscar
     */
    public function resetProductsDao()
    {
        $table = "{$this->instance->prefix}correos_oficial_products";
        
        $this->instance->query("UPDATE $table SET active = 0");
    }
    

    /**
     * Función para eliminar carrier_products
     * @param $table: tabla en la que buscar
     */
    public function deleteCarriersProductsDao()
    {
        $this->table = 'correos_oficial_carriers_products';
        $query = "DELETE FROM {$this->instance->prefix}$this->table}";
        $this->executeQuery($query);
    }

    /**
     * Función para eliminar carrier_products by id
     * @param $table: tabla en la que buscar
     */
    public function deleteCarrierProductsById($id_carrier, $id_zone)
    {
        $this->table = "{$this->instance->prefix}correos_oficial_carriers_products";
        $this->instance->delete($this->table, array('id_carrier' => $id_carrier, 'id_zone' => $id_zone));
    }

    /**
     * Función para conseguir productos activos
     * @param $where: condición where de la sentencia SQL
     * @param $table: tabla en la que buscar
     * OK
     */
    public function getActiveProductsDao($where)
    {

        $record = $this->instance->get_results(
                "SELECT
                cop.id,
                cop.name,
                cop.active,
                cop.delay,
                cop.company,
                cop.url,
                cop.codigoProducto,
                cop.id_carrier,
                cop.product_type,
                cop.max_packages
                FROM {$this->instance->prefix}correos_oficial_products as cop LEFT JOIN {$this->instance->prefix}correos_oficial_codes_actives
                as coc ON cop.company=coc.company $where"
        );

        if ($record === false) {
            return null;
        }
        return $record;
    }

    /**
     * Consigue el transportista activo de la zona
     * @param id_carrier
     * @param id_zone
     */
    public function getActiveProductCarrier($id_carrier, $id_zone)
    {

        $record = $this->instance->get_results(
            $this->instance->prepare("SELECT id_product FROM {$this->instance->prefix}correos_oficial_carriers_products
                WHERE id_carrier = %s AND id_zone = %d", $id_carrier, $id_zone), ARRAY_A
        );

        return $record;

    }

    /**
     * Actualiza el producto en la zona
     * @param id_zone
     * @param id_zone
     * @param id_carrier
     */
    public function updateCarrierProduct($id_product, $id_zone, $id_carrier)
    {
        $table = "{$this->instance->prefix}correos_oficial_carriers_products";

        $records = $this->instance->get_results(
            $this->instance->prepare(
                "SELECT id_product FROM $table WHERE id_carrier = %d", $id_carrier
            )
        );

        if (empty($records)) {
            $this->instance->insert($table,
                array('id_carrier' => $id_carrier, 'id_product' => $id_product, 'id_zone' => $id_zone),
                array('%d', '%d', '%d')
            );
        } else {
            $this->instance->update($table,
                array('id_product' => $id_product),
                array('id_zone' => $id_zone, 'id_carrier' => $id_carrier),
                '%d',
                array('%d', '%d')
            );
        }

    }

    /* **********************************************************************************************************
     *                                  Funciones Genéricas
     ********************************************************************************************************* */

    /**
     * @param $where: condición where de la sentencia SQL
     * @param $table: tabla en la que buscar
     * @param $fields: campos a recuperar
     * @example $this->dao->readRecord($table, "WHERE exp_number='$expedition'", 'exp_number');
     */
    public function readRecord($table, $where = '', $fields = null, $as_array = false)
    {
        if ($as_array) {
            $output = 'ARRAY_A';
        } else {
            $output = 'OBJECT';
        }

        if (isset($where) && !empty($where)) {

            if ($fields == null) {
                $record = $this->instance->get_results(
                    "SELECT * FROM {$this->instance->prefix}$table $where"
                    ,
                    $output
                );
            } else {

                $record = $this->instance->get_results(
                    "SELECT $fields FROM {$this->instance->prefix}$table $where"
                    ,
                    $output);
            }
        } else {
            $record = $this->instance->get_results(
                "SELECT * FROM {$this->instance->prefix}$table $where"
                ,
                $output);
        }

        if ($record === false) {
            return null;
        }

        return $record;
    }

    /**
     * CRUD
     */
    public function insertRecord($table, $data)
    {
        $values = '';
        $columns = '';

        foreach ($data as $key => $value) {
            $columns .= "" . $key . ",";
            $values .= "'" . $value . "',";
        }

        $columns = rtrim($columns, ",");
        $values = rtrim($values, ",");

        $query = "INSERT INTO {$this->instance->prefix}$table ($columns) VALUES ($values)";
        $this->executeQuery($query);
    }

    public function updateRecord($table, $data = null, $where = '')
    {
        $fields = '';

        foreach ($data as $key => $value) {
            $fields .= "$key='$value',";
        }
        $fields = substr($fields, 0, -1);

        $query = "UPDATE {$this->instance->prefix}$table SET $fields $where";

        $this->executeQuery($query);
    }

    public function deleteRecord($id, $table)
    {
        
        $this->instance->delete("{$this->instance->prefix}$table", array('id' => $id));
    }

    /**
     * Función Genérica para conseguir un objeto de una tabla
     * @param $colum: columna
     * @param $key: clave de la tabla
     * @param $table: tabla en la que buscar
     */
    public function getRecordById($key, $table)
    {
        if (isset($key) && !empty($key)) {
            
            $record = $this->instance->get_row(
                $this->instance->prepare("SELECT * FROM {$this->instance->prefix}$table WHERE id = %d", $key)
            );

            if ($record) {
                return $record->id;
            } else {
                return null;
            }
        } else {
            throw new LogicException(__FILE__ . " Debe indicarse un $key (valor primario) o un where en la llamada.");
        }
    }

    /**
     * Función Genérica para conseguir un objeto de una tabla
     * Si $as_array==true se retorna como array_asociativo,
     * sino como objecto de la clase CorreosDaoObject
     * @param string $table tabla en la que buscar
     * @param bool as_array si true se devuelve un array. Si false devuelve un objeto
     * @return array/Objeto $records depende de as_array
     */
    public function getRecords($table, $as_array = false)
    {
        if ($as_array) {
            $output = 'ARRAY_A';
        } else {
            $output = 'OBJECT';
        }

        $records = $this->instance->get_results("SELECT * FROM {$this->instance->prefix}$table", $output);

        if ($records === false) {
            return null;
        }
        return $records;
    }

    /**
     * Función Genérica para conseguir varios objeto de una tabla
     * Si $as_array==true se retorna como array_asociativo,
     * sino como objecto de la clase CorreosDaoObject
     * @param string $query consulta a la base de datos
     * @param bool as_array si true se devuelve un array. Si false devuelve un objeto
     * @return array/Objeto $records depende de as_array
     */
    public function getRecordsWithQuery($query, $as_array = false)
    {
        if ($as_array) {
            $output = 'ARRAY_A';
        } else {
            $output = 'OBJECT';
        }

        $records = $this->instance->get_results($query, $output);

        if (isset($records)) {
            return $records;
        } else {
            return null;
        }
    }

    /**
     * Función Genérica para conseguir un objeto de una tabla y devolver el último id
     * @param $table: tabla en la que buscar
     */
    public function getLastId($table)
    {
        $record = $this->instance->get_results(
            "SELECT id FROM {$this->instance->prefix}$table ORDER BY id DESC LIMIT 1"
        );

        if ($record) {
            return $record;
        } else {
            return null;
        }

    }

    /* **********************************************************************************************************
     *                                  Funciones de Usuario y compañia
     ********************************************************************************************************* */

    /**
     * Función Genérica para usuario/contraseña de la tabla de usuarios.
     * @param $colum: columna
     * @param $key: clave de la tabla
     * @param $table: tabla en la que buscar
     */
    public function getIdByCompanyDao($company)
    {
        $companyCode = 'correos_code';
        if ($company === 'CEX') {
            $companyCode = 'cex_code';
        }
        $record = $this->instance->get_row(
            $this->instance->prepare(
                "SELECT
                    %i
                FROM
                    {$this->instance->prefix}correos_oficial_senders
                WHERE
                    `sender_default` = 1"
                , $companyCode
            ), ARRAY_A
        );

        $exists = $this->instance->get_var(
            "SELECT
                id
            FROM 
                {$this->instance->prefix}correos_oficial_codes
            WHERE
                id = " . (int) $record[$companyCode]
        );

        if (empty($record) || empty($exists)) {
            $record = $this->instance->get_row(
                $this->instance->prepare(
                    "SELECT
                        id as " . $companyCode . "
                    FROM
                        {$this->instance->prefix}correos_oficial_codes
                    WHERE
                        `company` = %s"
                    , $company
                ), ARRAY_A
            );
        }

        if (isset($record)) {
            return $record[$companyCode];
        } else {
            return null;
        }
    }

    public function getIdCodeFromSender($id_sender, $company = 'correos')
    {
        $prepare = $this->instance->prepare(
            "SELECT " . strtolower($company) . "_code FROM {$this->instance->prefix}correos_oficial_senders WHERE `id` = %d", $id_sender
        );
        $record = $this->instance->get_var($prepare);
        if (isset($record)) {
            return $record;
        } else {
            return null;
        }
    }

    /**
     * Función Genérica para usuario/contraseña de la tabla de usuarios.
     * @param $colum: columna
     * @param $key: clave de la tabla
     * @param $table: tabla en la que buscar
     */
    public function getUserAndPasswordDao($column, $key, $table)
    {
        if (isset($key) && !empty($key)) {

            $record = $this->instance->get_row(
                $this->instance->prepare("SELECT CorreosUser, CorreosPassword, CEXUser, CEXPassword, company
                        FROM {$this->instance->prefix}$table WHERE $column=%s", $key)
            );
        }

        if (isset($record)) {
            return $record;
        } else {
            return null;
        }

    }

    public function getIdCodeFromOrder($id_order, $company = 'correos') {
        $prepare = $this->instance->prepare(
            "SELECT
                b." . $company . "_code
            FROM
                {$this->instance->prefix}correos_oficial_orders a
            LEFT JOIN
                {$this->instance->prefix}correos_oficial_senders b ON b.id = a.id_sender
            WHERE
                a.id_order = %d"
            , (int) $id_order
        );

        return $this->instance->get_var($prepare);
    }

    public function getCodeFromShippingDao($shipping, $company)
    {
        $prepare = $this->instance->prepare(
            "SELECT
                c." . strtolower($company) . "_code
            FROM
                {$this->instance->prefix}correos_oficial_orders a
            LEFT JOIN
                {$this->instance->prefix}correos_oficial_saved_orders b ON b.id_order = a.id_order
            LEFT JOIN
                {$this->instance->prefix}correos_oficial_senders c ON c.id = a.id_sender
            WHERE
                b.shipping_number = %s OR b.exp_number = %s"
            , $shipping, $shipping
        );

        return $this->instance->get_var($prepare);
    }

    /* **********************************************************************************************************
     *                                  Ejecuta la consulta
     ********************************************************************************************************* */
    /**
     * Ejecuta la consulta
     * @param $query Consulta SQL a ser ejecutada.
     */
    public function executeQuery($query)
    {
        try {
            $this->instance->query($query);

            if ($this->instance->last_error) {
                echo 'ERROR 230010: WordPress database error - ' . $this->instance->last_error;
            }

        } catch (Exception $e) {
            throw new \LogicException($e->getMessage() . " " . $e->getCode() . " " . $e->getPrevious());
        }
    }

    public static function getConfigValue($data){
    
        $dao=new CorreosOficialDao();
        $record=$dao->readSettings($data);

        if(isset($record->value)){
            return $record->value;
        } else return null;
    }

    public static function getDefaultSender($company) {
        global $wpdb;
    
        $result = $wpdb->get_row($wpdb->prepare("SELECT codes.*, senders.* FROM {$wpdb->prefix}correos_oficial_codes AS codes
        LEFT JOIN {$wpdb->prefix}correos_oficial_senders AS senders ON codes.id = senders.cex_code OR codes.id = senders.correos_code
        WHERE senders.sender_default = 1 AND codes.company = %s", $company), ARRAY_A);
    
        return $result;
    }    
    
    public static function getSGAOrdersWithTrackingNumber ($searchBytrackingNumber = false, $trackingNumber = null) {
        global $wpdb;

        $doSearch = '';

        if( $searchBytrackingNumber) {
            $doSearch = " AND pm.meta_value = '$trackingNumber'";
        } else {
            $doSearch = " AND pm.meta_key = 'correosecom_sga_tracking_number'";
        }

        $query = "SELECT p.ID as id_order
        FROM  {$wpdb->prefix}posts AS p
        INNER JOIN  {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
        WHERE p.post_type = 'shop_order'" . $doSearch;

        return $wpdb->get_col( $query );
    }

    public static function getCarrierByShippingMethod ($instanceId) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT cop.company FROM {$wpdb->prefix}correos_oficial_carriers_products AS cocp
            LEFT JOIN {$wpdb->prefix}correos_oficial_products AS cop ON cop.id = cocp.id_product
            WHERE cocp.id_carrier = %d",
            $instanceId
        );

        return $wpdb->get_col( $query );
    }

    public function getIdCodeFromSGAOrder ($company = "Correos") {
        $prepare = $this->instance->prepare(
            "SELECT id FROM {$this->instance->prefix}correos_oficial_codes WHERE company = %s", $company);

        return $this->instance->get_var($prepare);
    }

    public function getCodeFromSGAOrder ($company = "Correos") {
        $prepare = $this->instance->prepare(
            "SELECT customer_code FROM {$this->instance->prefix}correos_oficial_codes WHERE company = %s", $company);

        return $this->instance->get_var($prepare);
    }
}
