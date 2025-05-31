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
class CorreosOficialDAO extends PDO
{

    /** Objecto pdo con el que trabajaremos */
    private $connection;

    /** Tabla de la base de datos sobre la que actuar */
    private $table;
    private $table2;
    public static $context;
    private static $instance;

    /**
     * @param $pconnection Es un objeto pdo
     */
    public function __construct()
    {
        $this->connection = $this->getDBConnection();
        self::$context = Context::getContext();
    }

    public function getDBConnection()
    {

        if (empty(self::$instance)) {
            if (DetectPlatform::isWordPress()) {
                $server = DB_HOST === '[::1]' ? DB_HOST : str_replace(":3306", '', DB_HOST);
                self::$instance = new PDO(
                    'mysql:host=' . $server . ';dbname=' . DB_NAME . ';charset=utf8',
                    DB_USER,
                    DB_PASSWORD,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT => false
                    )
                );
            } elseif (DetectPlatform::isPrestashop()) {
                $server = _DB_SERVER_ === '[::1]' ? _DB_SERVER_ : str_replace(":3306", '', _DB_SERVER_);
                self::$instance = new PDO(
                    'mysql:host=' . $server . ';dbname=' . _DB_NAME_ . ';charset=utf8',
                    _DB_USER_,
                    _DB_PASSWD_,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT => false
                    )
                );
            }
        }
        return self::$instance;
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
        $this->table = $table;

        if ($field == $this->getRecordSettingRecordByPrimareKey($field, $this->table, $type)) {
            $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $table . " SET name='$field', value='$value'
            WHERE name='$field' AND id_shop=".self::$context->shop->id;
        } else {
            $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $table . " (name, value, type, id_shop) VALUES ('$field', '$value', '$type', ".self::$context->shop->id.")";
        }
        $this->executeQuery($query);
    }

    /**
     * Función para conseguir registros la configuración
     * @param $key: clave
     * @param $where: condición where de la sentencia SQL
     */

    public function readSettings($name, $as_array = false)
    {
        $this->table = 'correos_oficial_configuration';
        if (isset($name) && !empty($name)) {
            $query = "SELECT name, value FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE name=:name AND id_shop = ".self::$context->shop->id;
            $pdo = $this->connection->prepare($query);

            $pdo->execute(array(':name' => $name));
        }

        if ($as_array) {
            $record = $pdo->fetch(\PDO::FETCH_ASSOC);
        } else {
            $record = $pdo->fetchObject('CorreosDaoObject');
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
            $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE name=? AND id_shop = ".self::$context->shop->id;
            $pdo = $this->connection->prepare($query);

            $pdo->execute(array($key));
            $record = $pdo->fetchObject('CorreosDaoObject');
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
        $this->table = "correos_oficial_codes";
        $this->table2 = "correos_oficial_codes_actives";

        if (isset($fields['idCEX'])) {
            $id = $fields['idCEX'];
        } elseif (isset($fields['idCorreos'])) {
            $id = $fields['idCorreos'];
        }
        if (empty($id)) {
            $id = 9999999999;
        }

        if ($id == $this->getRecordPrimaryKey('id', $id, $this->table)) {
            $query = "
            UPDATE " . CorreosOficialUtils::getPrefix() . $this->table . "
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
               id_shop = '" . $fields['id_shop'] . "',

               customer_code = '" . $customer_code . "'
            WHERE id='" . $id . "'";

            $this->executeQuery($query);
            echo $id;
            die();
        } else {
            $result = $this->readRecord("correos_oficial_codes", "WHERE customer_code = '$customer_code' AND id_shop = '".$fields['id_shop']."'");

            if ($result) {
                return false;
            } else {
                $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $this->table . "
                (id, customer_code,  company,
                CorreosContract, CorreosCustomer, CorreosKey, CorreosUser, CorreosPassword, CorreosOv2Code,
                CEXCustomer, CEXUser, CEXPassword, id_shop)
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
                   '" . $fields['CEXPassword'] . "',
                   '" . $fields['id_shop'] . "'
                   )";

                $this->executeQuery($query);
                $lastId = $this->getLastId($this->table);

                // comprobamos que existe el id_shop y si no lo insertamos
                $codes_actives_by_shop = $this->readRecord($this->table2, "WHERE company='$company' AND id_shop=" . $fields['id_shop']);

                if ($codes_actives_by_shop == null) {
                    $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $this->table2 . " (company, active, id_shop) VALUES ('$company', 1," . $fields['id_shop'] . ")";
                } else {
                    $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->table2 . " SET active='1' WHERE company='$company' AND id_shop=" . $fields['id_shop'];
                }
                $this->executeQuery($query);

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
        $this->table = "correos_oficial_codes";
        $this->table2 = "correos_oficial_codes_actives";

        $customer = $this->readRecord('correos_oficial_codes', "WHERE id='$id'");
        $company = $customer[0]->company;

        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE id='$id'";
        $this->executeQuery($query);

        // Comprobamos que no haya más de un cliente de la misma company activo
        $codes_by_company = $this->readRecord($this->table, " WHERE company='$company' AND id_shop=".self::$context->shop->id);

        if ($codes_by_company == null){
            $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->table2 . " SET active='0' WHERE company='$company' AND id_shop=".self::$context->shop->id;
        }

        $this->executeQuery($query);
        die();
    }

    /**
     * Elimina un cliente por código de cliente.
     * @param $customer_code Código de cliente.
     */
    public function deleteCustomerDao($customer_code)
    {
        $this->table = "correos_oficial_customers";

        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE customer_code='$customer_code'";
        $this->executeQuery($query);
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
        $this->table = 'correos_oficial_products_shop';
        $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $this->table . " (id_product, active, id_shop)
        VALUES ($id, 1, ".self::$context->shop->id.")
        ON DUPLICATE KEY UPDATE active = VALUES(active);";
        //$query = "UPDATE " . Utils::getPrefix() . $this->table . " SET active='1' WHERE id=$id";
        $pdo = $this->connection->prepare($query);
        $pdo->execute();
    }


    /**
     * Función para actualizar productos
     * @param $table: tabla en la que buscar
     */
    public function resetProductsDao()
    {
        $this->table = 'correos_oficial_products_shop';
        $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->table . " SET active=0 WHERE id_shop =".self::$context->shop->id;
        $pdo = $this->connection->prepare($query);
        $pdo->execute();
    }

    /**
     * Función para eliminar carrier_products
     * @param $table: tabla en la que buscar
     */
    public function deleteCarriersProductsDao()
    {
        $this->table = 'correos_oficial_carriers_products';
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->table." WHERE id_shop =".self::$context->shop->id;
        $pdo = $this->connection->prepare($query);
        $pdo->execute();
    }

    /**
     * Función para eliminar carrier_products by id
     * @param $table: tabla en la que buscar
     */
    public function deleteCarrierProductsById($id_carrier, $id_zone)
    {
        $this->table = 'correos_oficial_carriers_products';
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE id_carrier='$id_carrier' AND id_zone='$id_zone' AND id_shop =".self::$context->shop->id;
        $pdo = $this->connection->prepare($query);
        $pdo->execute();
    }

    /**
     * Función para conseguir productos activos
     * @param $where: condición where de la sentencia SQL
     * @param $table: tabla en la que buscar
     * OK
     */
    public function getActiveProductsDao($where)
    {
        $this->table = 'correos_oficial_products';
        $query = "SELECT DISTINCT 
            cop.id,
            cop.name,
            cops.active,
            cop.delay,
            cop.company,
            cop.url,
            cop.codigoProducto,
            cop.id_carrier,
            cop.product_type,
            cop.max_packages
             FROM " . CorreosOficialUtils::getPrefix() . $this->table . " as cop 
             LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes_actives as coc ON cop.company=coc.company
             LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products_shop as cops ON cops.id_product=cop.id 
             $where";
        $pdo = $this->connection->prepare($query);

        $pdo->execute();
        $record = $pdo->fetchAll(PDO::FETCH_CLASS, 'CorreosDaoObject');

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
        $sql = "SELECT id_product FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products
                WHERE id_carrier = " . $id_carrier . " AND id_zone = " . $id_zone . " AND id_shop =".self::$context->shop->id;

        $pdo = $this->connection->prepare($sql);
        $pdo->execute();
        return $pdo->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el producto en la zona
     * @param id_zone
     * @param id_zone
     * @param id_carrier
     */
    public function updateCarrierProduct($id_product, $id_zone, $id_carrier)
    {
        $this->table = 'correos_oficial_carriers_products';
        $query = "SELECT id_product FROM " . CorreosOficialUtils::getPrefix() . $this->table . "
                  WHERE id_carrier = " . $id_carrier." AND id_zone = " . $id_zone." AND id_shop =".self::$context->shop->id;
        $pdo = $this->connection->prepare($query);
        $pdo->execute();
        $result = $pdo->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $this->table . " (id_carrier, id_product, id_zone, id_shop)
                      VALUES ('$id_carrier', '$id_product', '$id_zone', '".self::$context->shop->id."')";
            $pdo = $this->connection->prepare($query);
            $pdo->execute();
        } else {
            $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->table . " SET id_product=" . $id_product . "
                      WHERE id_zone=$id_zone AND id_carrier=$id_carrier AND id_shop =".self::$context->shop->id;
            $pdo = $this->connection->prepare($query);
            $pdo->execute();
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
        if (isset($where) && !empty($where)) {

            if ($fields == null) {
                $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . $table . " $where";
            } else {
                $query = "SELECT $fields FROM " . CorreosOficialUtils::getPrefix() . $table . " $where";
            }
            $pdo = $this->connection->prepare($query);
            $pdo->execute();
        } else {
            $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . $table;
            $pdo = $this->connection->prepare($query);

            $pdo->execute();
        }

        if ($as_array) {
            $record = $pdo->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $record = $pdo->fetchAll(PDO::FETCH_CLASS, 'CorreosDaoObject');
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

        $query = "INSERT INTO " . CorreosOficialUtils::getPrefix() . $table . " ($columns) VALUES ($values)";
        $this->executeQuery($query);
    }

    public function updateRecord($table, $data = null, $where = '')
    {
        $fields = '';

        foreach ($data as $key => $value) {
            $fields .= "$key='$value',";
        }
        $fields = substr($fields, 0, -1);

        $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $table . " SET $fields $where";

        $this->executeQuery($query);
    }

    public function deleteRecord($id, $table)
    {
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE id = $id";
        $this->executeQuery($query);
    }

    /**
     * Función Genérica para conseguir un objeto de una tabla
     * @param $colum: columna
     * @param $key: clave de la tabla
     * @param $table: tabla en la que buscar
     */
    public function getRecordPrimaryKey($column, $key, $table)
    {
        if (isset($key) && !empty($key)) {
            $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE $column=?";
            $pdo = $this->connection->prepare($query);
            $pdo->execute(array($key));
            $record = $pdo->fetchObject('CorreosDaoObject');
            if ($record) {
                return $record->$column;
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
        $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . $table;

        $pdo = $this->connection->prepare($query);
        $pdo->execute();

        if ($as_array) {
            $records = $pdo->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $records = $pdo->fetchAll(\PDO::FETCH_CLASS, 'CorreosDaoObject');
        }

        if ($records === false) {
            return null;
        }
        return $records;
    }

    /**
     * Función Genérica para conseguir varios objeto de una tabla
     * Si $as_array==true se retorna como array_asociativo,
     * sino como objecto de la clase CorreosDaoObject
     * @param bool as_array si true se devuelve un array. Si false devuelve un objeto
     * @return array/Objeto $records depende de as_array
     */
    public function getRecordsWithQuery($query, $as_array = false)
    {
        $pdo = $this->connection->prepare($query);
        $pdo->execute();

        if ($as_array) {
            $records = $pdo->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $records = $pdo->fetchAll(\PDO::FETCH_CLASS, 'CorreosDaoObject');
        }

        if (isset($records)) {
            return $records;
        } else {
            return null;
        }
    }

    /**
     * @param string $query consulta SQL.
     * @return $objeto
     */
    public function getRecordWithQuery($query, $as_array = false)
    {
        $pdo = $this->connection->prepare($query);
        $pdo->execute();

        if ($as_array) {
            $records = $pdo->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $records = $pdo->fetchAll(\PDO::FETCH_CLASS, 'CorreosDaoObject');
        }

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
        $query = "SELECT id FROM " . CorreosOficialUtils::getPrefix() . $table . " ORDER BY id DESC LIMIT 1";

        $pdo = $this->connection->prepare($query);
        $pdo->execute();

        $records = $pdo->fetchAll(PDO::FETCH_CLASS, 'CorreosDaoObject');
        if ($records) {
            return $records;
        } else {
            return null;
        }
    }

    /* **********************************************************************************************************
     *                                  Funciones de Usuario y compañia
     ********************************************************************************************************* */

     public function getIdCodeFromSender($id_sender, $company = 'correos')
     {
        $this->table = 'correos_oficial_senders';

        $query = "SELECT " . strtolower($company) . "_code as id_code FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE id=" . $id_sender . " LIMIT 1";

        $result = (array) $this->getRecordWithQuery($query, true);

        if (count($result)) {
            return $result[0]['id_code'];
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
    public function getIdByCompanyDao($company)
    {
        $this->table = 'correos_oficial_codes';

        $query = "SELECT id FROM " . CorreosOficialUtils::getPrefix() . $this->table . " WHERE company='$company'";

        $pdo = $this->connection->prepare($query);
        $pdo->execute();
        $record = $pdo->fetchObject('CorreosDaoObject');
        if (isset($record)) {
            return $record->id;
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
            $query = "SELECT CorreosUser, CorreosPassword, CEXUser, CEXPassword, company
                    FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE $column=?";
            $pdo = $this->connection->prepare($query);
            $pdo->execute(array($key));
            $record = $pdo->fetchObject('CorreosDaoObject');
            if ($record) {
                return $record;
            } else {
                return null;
            }
        }
    }

    public function getIdCodeFromOrder($id_order, $company = 'correos') {
        $query =    "SELECT
                        b." . $company . "_code
                    FROM
                        " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders a
                    LEFT JOIN
                        " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders b ON b.id = a.id_sender
                    WHERE
                        a.id_order=?";

        $pdo = $this->connection->prepare($query);
        $pdo->execute(array((int) $id_order));

        $record = $pdo->fetchAll(PDO::FETCH_ASSOC);

        if ($record === false) {
            return null;
        }
        return $record[0][$company . "_code"];

        // $prepare = $this->instance->prepare(
        //     "SELECT
        //         b." . $company . "_code
        //     FROM
        //         {$this->instance->prefix}correos_oficial_orders a
        //     LEFT JOIN
        //         {$this->instance->prefix}correos_oficial_senders b ON b.id = a.id_sender
        //     WHERE
        //         a.id_order = %d"
        //     , (int) $id_order
        // );

        // return $this->instance->get_var($prepare);
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
            $pdo = $this->connection->prepare($query);

            $pdo->execute();
            if (!$pdo->errorCode()) {
                print_r($pdo->errorInfo());
                return;
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

    public static function getSGAOrdersWithTrackingNumber($searchBytrackingNumber = false, $trackingNumber = null) {

        if ($searchBytrackingNumber) {
            $sql = "SELECT id_order FROM " . _DB_PREFIX_ . "order_carrier WHERE tracking_number = '" . pSQL($trackingNumber) . "'";
        } else {
            $sql = "SELECT id_order FROM " . _DB_PREFIX_ . "order_carrier WHERE tracking_number <> ''";
        }
    
        $result = Db::getInstance()->executeS($sql);
        return !empty($result) ? array_column($result, 'id_order') : [];
    }
    

    public static function getCompanyByIdCarrier ($id_carrier) {
        return Db::getInstance()->getValue(
            'SELECT cop.company 
            FROM ' . _DB_PREFIX_ . 'correos_oficial_products cop
            LEFT JOIN ' . _DB_PREFIX_ . 'correos_oficial_carriers_products cocp 
            ON cop.id = cocp.id_product
            WHERE cocp.id_carrier = ' . (int) $id_carrier . '
            UNION 
            SELECT company 
            FROM ' . _DB_PREFIX_ . 'correos_oficial_products 
            WHERE id_carrier = ' . (int) $id_carrier);
    }

    public function getIdCodeFromSGAOrder($company = "Correos") {
        $sql = "SELECT id FROM " . _DB_PREFIX_ . "correos_oficial_codes WHERE company = '" . pSQL($company) . "'";
        return Db::getInstance()->getValue($sql);
    }

    public function getCodeFromSGAOrder($company = "Correos") {
        $sql = 'SELECT customer_code FROM ' . _DB_PREFIX_ . 'correos_oficial_codes WHERE company = "' . pSQL($company) . '"';
        return Db::getInstance()->getValue($sql);
    }
}
