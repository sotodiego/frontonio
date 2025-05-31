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

require_once dirname(__FILE__) . "/../Correos/CorreosRest.php";
require_once dirname(__FILE__) . "/../Cex/CexRest.php";
require_once dirname(__FILE__) . "/../config.inc.php";
require_once dirname(__FILE__) . "/../CorreosOficialUtils.php";

require_once dirname(__FILE__) . "/../Dao/CorreosOficialCronDao.php";
require_once dirname(__FILE__) . "/../Dao/CorreosOficialDAO.php";

require_once dirname(__FILE__) . '/../Commons/CorreosOficialLog.php';

require_once dirname(__FILE__) . "../../../../classes/CorreosOficialOrder.php";

require_once dirname(__FILE__) . "../../../../controllers/admin/AdminCorreosOficialCronProcessController.php";
require_once dirname(__FILE__) . "/../DetectPlatform.php";

/**
 * Cron para actualizar el estado de los pedidos
 * Al terminar el cron, se actualiza el campo CronLastExecutionTime de la tabla correos_oficial_configuration.
 * Este campo es luego comprobado en el despachador correos_oficial.php
 */
class CronCorreosOficial extends CorreosOficialCronDao
{

    private $logFile;
    private $logErrors;
    private $logLastRequest;
    private $cronErrorLog;

    private $change_status;

    private $limit;
    private $how_many;

    private $dao;

    /** Estado para varios tipos de pedidos en PRESTAHOP */
    const PS_STATUS = '16';
    const CHANGESTATUS = self::PS_STATUS;

    const RECORDSTATUS_CORREOS_CEX = 900;
    const CANCELEDSTATUS_CORREOS_CEX = 901;
    const RETURNEDSTATUS_CORREOS_CEX = 902;
    const DELIVEREDSTATUS_CORREOS_CEX = 903;
    const SENDINGSTATUS_CORREOS_CEX = 904;
    const ANNULLED_CORREOS_CEX = 905;

    const MAX_CRON_ERROR_LOG_SIZE = 500;

    /**
     * Cron para CorreosOficial
     */
    public function __construct()
    {
        parent::__construct();

        $this->logFile = dirname(__FILE__) . "/../../../log/log_cron_register.txt";
        $this->logErrors = dirname(__FILE__) . "/../../../log/log_cron_error_update.txt";
        $this->logLastRequest = dirname(__FILE__) . "/../../../log/log_cron_last_request.txt";
        $this->cronErrorLog = dirname(__FILE__) . "/../../../log/cron_error_log.txt";

        $this->connection = CorreosOficialDAO::getDBConnection();
        // Obtenemos un objetoDao
        $this->dao = new CorreosOficialDao($this->connection);
        $this->change_status = $this->dao->readSettings('ActivateOrderStatusChange');

        // Para el limit de la consulta sql.
        $this->limit = 0;
        $this->how_many = 10;
    }

    /**
     * Llamada principal.
     */
    public function cronInit()
    {
        global $co_debugCorreosOficial;
        $update_cron = true;
        $cex_counted_orders = 0;
        $correos_counted_orders = 0;

        // Iniciamos fichero de log para cron
        $hora_inicial = date('d-m-Y H:i:s');

        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, "CorreosOficial: LOG del CRON\r\nComenzamos ejecucion Cron -> $hora_inicial" . PHP_EOL, LOCK_EX);
        }
        if (CorreosOficialUtils::dirExists($this->logLastRequest)) {
            file_put_contents($this->logLastRequest, "CorreosOficial: ÚLTIMA PETICIÓN: " . $hora_inicial . PHP_EOL, LOCK_EX);
        }
        if (CorreosOficialUtils::dirExists($this->logErrors)) {
            file_put_contents($this->logErrors, "CorreosOficial: REGISTRO DE ERRORES: " . $hora_inicial . PHP_EOL, LOCK_EX);
        }

        if ($co_debugCorreosOficial) {
            print "Iniciando CRON CorreosOficial <br>";
        }

        $total_orders = 0;

        try {
            // Datos para BBDD
            $fecha = date("Y-m-d H:i:s");
            $this->dao->createSettingRecord(
                'CronLastExecutionTime',
                $fecha,
                'correos_oficial_configuration',
                'datetime'
            );

        } catch (Exception $e) {
            if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                file_put_contents($this->cronErrorLog, $e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        }

        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, "Modificado valor de control CronLastExecutionTime => " . $fecha . PHP_EOL, FILE_APPEND);
        }

        if (!CorreosOficialUtils::sislogModuleIsActive()) {
            $total_orders = $this->cronProcessing($update_cron, $co_debugCorreosOficial);
        } else {
            $idOrders = $this->dao->getSGAOrdersWithTrackingNumber();

            $CEX_orders = array();
            $CORREOS_orders = array();

            $shipmentDelivered = $this->dao->readSettings('ShipmentDelivered');
            $shipmentReturned = $this->dao->readSettings('ShipmentReturned');

            if (DetectPlatform::isWordPress()) {
                $clean_shipmentDelivered = str_replace('wc-', '', $shipmentDelivered->value);
                $clean_shipmentReturned = str_replace('wc-', '', $shipmentReturned->value);
            }

            foreach($idOrders as $id) {

                if (DetectPlatform::isPrestashop()) {
                    $order = new Order($id);
                } else if (DetectPlatform::isWordPress()) {
                    $order = wc_get_order($id);
                }

                if ($order) {

                    if(DetectPlatform::isPrestashop()) {

                        if($shipmentDelivered->value == $order->getCurrentState() || $shipmentReturned->value == $order->getCurrentState()) {
                            continue;
                        }

                        $id_carrier = $order->id_carrier;
                        $company = $this->dao->getCompanyByIdCarrier($id_carrier);

                        if ($company == 'Correos') {
                            $CORREOS_orders[] = $order;
                        } else if ($company == 'CEX') {
                            $CEX_orders[] = $order;
                        }
                    } else if (DetectPlatform::isWordPress()) {
                        if ($clean_shipmentDelivered == $order->get_status() || $clean_shipmentReturned == $order->get_status()) {
                            continue;
                        }

                        $shipping_methods = $order->get_shipping_methods();

                        if (!empty($shipping_methods)) {

                            $shipping_method = current( $shipping_methods );
                            $instance_id   = $shipping_method->get_instance_id();
                            $company = $this->dao->getCarrierByShippingMethod($instance_id);

                            if ($company[0] == 'Correos') {
                                $CORREOS_orders[] = $order;
                            } else if ($company[0] == 'CEX') {
                                $CEX_orders[] = $order;
                            }
                        }
                    }
                }
            }
            
            if ($CEX_orders){
                $cex_counted_orders += $this->getCEXWSReturn($CEX_orders);
            }

            if ($CORREOS_orders){
                $this->getCorreosWSReturn($CORREOS_orders);
            }

            $total_orders = count($idOrders);
        }
      
        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, "Finalizando cron, número de ordenes ejecutadas => " . $total_orders . PHP_EOL, FILE_APPEND);
        }

        if ($co_debugCorreosOficial) {
            print "Finalizando cron, número de ordenes ejecutadas => " . $total_orders . PHP_EOL;
        }



        // FINALIZAMOS
        $hora_final = date('d-m-Y H:i:s');
        $hora_final = new DateTime($hora_final);
        $hora_inicial = new DateTime($hora_inicial);
        $tiempo_ejecucion = $hora_inicial->diff($hora_final);

        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, "Tiempo transcurrido en ejecución => " . $tiempo_ejecucion->format('%h horas %i minutos %s segundos') . PHP_EOL, FILE_APPEND);
        }

        if ($co_debugCorreosOficial) {
            print "<br>Finalizada CRON CorreosOficial con exito<br>";
        }

    }

    public function cronProcessing ($update_cron, $co_debugCorreosOficial) {

        $cex_counted_orders = 0;
        $correos_counted_orders = 0;
        $total_orders = 0;

        while ($update_cron) {
            // Comprobamos ordenes tanto de cex como correos express
            $cex_orders = $this->getOrdersForTracking('CEX', $this->limit);
            $correos_orders = $this->getOrdersForTracking('Correos', $this->limit);

            $this->limit += 10;

            if (!count($cex_orders) && !count($correos_orders)) {
                $update_cron = false;

                if ($co_debugCorreosOficial) {
                    print "No hay ordenes para ejecutar. Se finaliza el CRON." . PHP_EOL;
                }
            } else {
                // Nº de ordenes ejecutadas en el WS
                if (count($cex_orders)){
                    $cex_counted_orders += $this->getCEXWSReturn($cex_orders);
                }
                if (count($correos_orders)){
                    $correos_counted_orders += $this->getCorreosWSReturn($correos_orders);
                }

                $total_orders = $cex_counted_orders + $correos_counted_orders;
            }
        }

        return $total_orders;
    }

    public function getCEXWSReturn($orders)
    {
        $cex = new CEXRest($orders);
        $retorno = '';

        if (CorreosOficialLog::getSizeErrorLog($this->cronErrorLog) >= self::MAX_CRON_ERROR_LOG_SIZE) {
            CorreosOficialLog::rotateErrorLog($this->cronErrorLog);
        }

        if(!CorreosOficialUtils::sislogModuleIsActive()) {
            try {
                $rest = $cex->cronTrackingCEX($orders);
                if (isset($rest) && !empty($rest)) {
                    $this->writeLogLastRequest($rest);
                }
            } catch (Exception $e) {
                if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                    file_put_contents($this->cronErrorLog, $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }
            try {
                if (isset($rest) && !empty($rest)) {
                    $retorno = $cex->trackingCEXRequestCall($rest);
                }
            } catch (Exception $e) {
                if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                    file_put_contents($this->cronErrorLog, $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }
    
            if (strstr($retorno[0], '<!DOCTYPE')) {
                if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                    file_put_contents($this->cronErrorLog, trim($retorno[0]). PHP_EOL, FILE_APPEND);
                }
                throw new LogicException('ERROR 15531: No es posible conectar con el servicio de CRON');
            }
        } else {
            $rest = $cex->CronSGATrackingCEX($orders);
            $retorno = $cex->trackingSGACEXRequestCall($rest);
        }
        
        if (isset($retorno) && !empty($retorno) && !is_array($retorno)) {
            $retorno = json_decode($retorno, true);
            CorreosOficialUtils::varDump("RETORNO1_CEX:", $retorno);

            $flag_generate_error_log = false;

            $i = 0;

            if (isset($retorno['listaEnvios'])) {
                foreach ($retorno['listaEnvios'] as $response) {
                    CorreosOficialUtils::varDump("RESPONSE_CEX", $response);

                    if (!CorreosOficialUtils::sislogModuleIsActive()) {
                        $this->processTrackingOrder($response);
                    } else {
                        $this->processSGATrackingOrder($response);
                    }
                    
                    $i++;

                    // Comprobamos código de estado de algún pedido.
                    switch ($response['codigoEstado']) {
                        // Sin recepción
                        case 1:
                        // Devuelto
                        case 17:
                        // Anulados
                        case 13:
                        case 14:
                        case 15:
                        case 16:
                        case 19:
                        case 31:
                            $flag_generate_error_log = false;
                            break;
                        default:
                            $flag_generate_error_log = true;
                    }
                }

                // Si hubo errores generamos fichero de error.
                if ($flag_generate_error_log) {
                    if (CorreosOficialUtils::dirExists($this->logErrors)) {
                        file_put_contents($this->logErrors, print_r($retorno, true) . PHP_EOL, FILE_APPEND);
                    }
                } elseif (!$flag_generate_error_log) { // Si hubo errores generamos fichero de error.
                    if (CorreosOficialUtils::dirExists($this->logErrors)) {
                        file_put_contents($this->logErrors, 'No ha habido errores' . PHP_EOL, FILE_APPEND);
                    }
                }
            }
            return $i;
        } elseif (isset($retorno) && !empty($retorno) && is_array($retorno)) {
            $i = 0;
            foreach ($retorno as $ret) {
                $count = $this->processDataApi($ret, $orders);
                $i++;
            }
            return $i;
        }
    }

    public function getCorreosWSReturn($orders)
    {
        $correos = new CorreosRest();
        $retorno = '';

        if (CorreosOficialLog::getSizeErrorLog($this->cronErrorLog) >= self::MAX_CRON_ERROR_LOG_SIZE) {
            CorreosOficialLog::rotateErrorLog($this->cronErrorLog);
        }

        if (!CorreosOficialUtils::sislogModuleIsActive()) {
            try {
                $rest = $correos->cronTrackingCorreos($orders);
                if (isset($rest) && !empty($rest)) {
                    $this->writeLogLastRequest($rest);
                }
            } catch (Exception $e) {
                if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                    file_put_contents($this->cronErrorLog, $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }
        }

        try {
            if (!CorreosOficialUtils::sislogModuleIsActive()) {
                $retorno = $correos->cronTrackingCorreosRequestCall($orders);
            } else {
                $retorno = $correos->cronTrackingSGARequestCall($orders);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                file_put_contents($this->cronErrorLog, CorreosOficialLog::logDate()." ".$e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        }

        $i = 0;

        if (strstr($retorno, '<!DOCTYPE')) {
            if (CorreosOficialUtils::dirExists($this->cronErrorLog)) {
                file_put_contents($this->cronErrorLog, trim($retorno). PHP_EOL, FILE_APPEND);
            }
            throw new LogicException('ERROR 15530: No es posible conectar con el servicio de CRON');
        }
        
        if (isset($retorno) && !empty($retorno)) {
            $retorno = json_decode($retorno, true);
            CorreosOficialUtils::varDump("RETORNO2_CORREOS", $retorno);
            $flag_generate_error_log = false;
    
            foreach ($retorno as $response) {
                // Continuamos si viene como vacío o no es un array
				if (!is_array($response)) {
					continue;
				}					
				
                if ($response[0]['error']['codError'] == 0) {
                    CorreosOficialUtils::varDump("RESPONSE_CORREOS: ", $response[0]['error']);

                    $events_counter = count($response[0]['eventos']);

                    if (!CorreosOficialUtils::sislogModuleIsActive()) {
                        // buscar la orden en BBDD que corresponda con la respuesta iterada d la consulta a BBDD
                        $wsStatus = $this->getWSStatusByShippingNumber($response[0]['codEnvio'], $orders);
                        $this->processTrackingOrder($response[0], $wsStatus, $retorno);
                    } else {
                        $this->processSGATrackingOrder($response[0]);
                    }
                    
                    $i++;

                    $order_state = $response[0]['eventos'][$events_counter - 1]['desTextoResumen'];

                    if ($order_state == 'Cancelado' || $order_state == 'Anulado') {
                        $flag_generate_error_log = true;
                    }

                }

                // Si hubo errores generamos fichero de error.
                if ($flag_generate_error_log) {
                    if (CorreosOficialUtils::dirExists($this->logErrors)) {
                        file_put_contents($this->logErrors, print_r($response[0], true) . PHP_EOL, FILE_APPEND);
                    }
                }
            }
        }
        return $i;
    }

    public function processTrackingOrder($response)
    {
        global $co_debugCorreosOficial;
        $status = '';

        // Para CEX
        if (isset($response['nEnvio'])) {
            $shipping_number = $response['nEnvio'];
            $ws_status_code = $this->getStatus($response['codigoEstado']);
            $order_state = $response['descripcionEstado'];
        } elseif (isset($response['codEnvio'])) { // Para Correos

            CorreosOficialUtils::varDump("EVENTOS: ", count($response['eventos']));
            $events_counter = count($response['eventos']);
            $shipping_number = $response['codEnvio'];
            $ws_status_code = $this->getStatus($response['eventos'][$events_counter - 1]['codEvento']);
            $order_state = $response['eventos'][$events_counter - 1]['desTextoResumen'];
        } else {
            throw new LogicException("La respuesta del servidor no es ni de Correos ni de Correos Express", 1);
        }

        $id_order = $this->getOrderIdByShippingNumber($shipping_number);

        $objectOrderStart = new CorreosOficialOrder($id_order);

        if (!$objectOrderStart->orderExist()) {
            return false;
        }

        $currentState = $objectOrderStart->getCurrentState();

        $this->getStatusFromCorreosOficialOrders($id_order);

        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, "Orden id => " . $id_order . " con estado actual => " . $currentState . PHP_EOL, FILE_APPEND);
        }

        if ($co_debugCorreosOficial) {
            print "Orden id => " . $id_order . " con estado actual => " . $currentState;
            print "   La respuesta del WS ha sido " . $ws_status_code . "<br>";
        }

        /**********************************************************************************************
         *                      ESTADOS DE CORREOS Y CORREOS EXPRESS                                  *
         **********************************************************************************************/

        if ($ws_status_code == 'Prerregistrado') { // PREREGISTRADO
            $config_status = $this->dao->readSettings('ShipmentPreregistered');
            CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
            if (CorreosOficialUtils::dirExists($this->logFile)) {
                file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
            }
            $this->changeShippingStatus($id_order, $order_state, 'Grabado');
        } elseif ($ws_status_code == 'Envío entregado') { // ENTREGADO
            $config_status = $this->dao->readSettings('ShipmentDelivered');
            if ($this->change_status) {
                CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
                if (CorreosOficialUtils::dirExists($this->logFile)) {
                    file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
                }
                $this->changeShippingStatus($id_order, $order_state, 'Entregado');
            }
        } elseif ($ws_status_code == 'En curso') { // EN CURSO
            $config_status = $this->dao->readSettings('ShipmentInProgress');
            if ($this->change_status) {
                CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
                if (CorreosOficialUtils::dirExists($this->logFile)) {
                    file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
                }
                if ($order_state == 'Reetiquetado') {
                    $status = $order_state;
                }
                
                $this->changeShippingStatus($id_order, $order_state, $status);
            }
        } elseif ($ws_status_code == 'Envío Anulado') { // CANCELADOS, ANULADOS
            $config_status = $this->dao->readSettings('ShipmentCanceled');
            if ($this->change_status) {
                CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
                if (CorreosOficialUtils::dirExists($this->logFile)) { 
                    file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
                }
                $this->changeShippingStatus($id_order, $order_state, 'Cancelado');
            }
        } elseif ($ws_status_code == 'Envío Devuelto') { // DEVUELTO
            $config_status = $this->dao->readSettings('ShipmentReturned');
            if ($this->change_status) {
                CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
                if (CorreosOficialUtils::dirExists($this->logFile)) {
                    file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
                }
                $this->changeShippingStatus($id_order, $order_state, 'Cancelado');
            }
        } else { // SIN CAMBIOS
            if (CorreosOficialUtils::dirExists($this->logFile)) {
                file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " no ha cambiado" . PHP_EOL, FILE_APPEND);
            }
        }

    }
    public function processSGATrackingOrder ($response) {
        if (isset($response['nEnvio'])) {
            $shipping_number = $response['nEnvio'];
            $ws_status_code = $this->getStatus($response['codigoEstado']);
            $order_state = $response['descripcionEstado'];
        } elseif (isset($response['codEnvio'])) { 
            CorreosOficialUtils::varDump("EVENTOS: ", count($response['eventos']));
            $events_counter = count($response['eventos']);
            $shipping_number = $response['codEnvio'];
            $ws_status_code = $this->getStatus($response['eventos'][$events_counter - 1]['codEvento']);
            $order_state = $response['eventos'][$events_counter - 1]['desTextoResumen'];
        }

        switch ($ws_status_code) {

            case 'Prerregistrado':
                $config_status = $this->dao->readSettings('ShipmentPreregistered');
                $this->processSGAOrderStatus ($shipping_number, $order_state, $config_status);
                break;
            case 'Envío entregado':
                $config_status = $this->dao->readSettings('ShipmentDelivered');
                $this->processSGAOrderStatus ($shipping_number, $order_state, $config_status);
                break;
            case 'En curso':
                $config_status = $this->dao->readSettings('ShipmentInProgress');
                $this->processSGAOrderStatus ($shipping_number, $order_state, $config_status);

                if ($order_state == 'Reetiquetado') {
                    $status = $order_state;
                }
                break;
            case 'Envío Anulado':
                $config_status = $this->dao->readSettings('ShipmentCanceled');
                $this->processSGAOrderStatus ($shipping_number, $order_state, $config_status);
                break;
            case 'Envío Devuelto':
                $config_status = $this->dao->readSettings('ShipmentReturned');
                $this->processSGAOrderStatus ($shipping_number, $order_state, $config_status);
                break;
            default:
                $id_order = $this->dao->getSGAOrdersWithTrackingNumber(true, $shipping_number);
                if (CorreosOficialUtils::dirExists($this->logFile)) {
                    file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order . " no ha cambiado" . PHP_EOL, FILE_APPEND);
                } 
        }
    }

    public function processSGAOrderStatus ($shipping_number, $order_state, $config_status) {

        $id_order = $this->dao->getSGAOrdersWithTrackingNumber(true, $shipping_number);

        if(DetectPlatform::isPrestashop()) {
            CorreosOficialUtils::changeOrderStatus($id_order[0], $config_status->value);
        } else if (DetectPlatform::isWordPress()) {
            CorreosOficialUtils::changeOrderStatus($id_order[0], $config_status->value);
        }

        if (CorreosOficialUtils::dirExists($this->logFile)) {
            file_put_contents($this->logFile, CorreosOficialLog::logDate()." estado de la orden => " . $id_order[0] . " ha cambiado a => " . $order_state . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Devuelve los pedidos que han sido preregistrados para hacer seguimiento.
     */
    public function getOrdersForTracking($company, $limit)
    {
        $records = array();

        $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders";
        $result = $this->getOrdersForTrackingDao($query);
        if (count($result) == 0) {
            return array();
        }

        $query = "SELECT 
                    coo.shipping_number as exp_number, 
                    coo.id_order as id_order,
                    coo.last_status,
                    Case
                        When coo.carrier_type = 'Correos'
                        Then " . CorreosOficialUtils::getPrefix() . "correos_codes.customer_code
                        When coo.carrier_type = 'CEX'
                        Then " . CorreosOficialUtils::getPrefix() . "cex_codes.customer_code
                        Else Null
                    End As customer_code
                FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders as coo JOIN 
                     " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders As coss On coss.id = coo.id_sender LEFT Join
                     " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes As " . CorreosOficialUtils::getPrefix() . "correos_codes On " . CorreosOficialUtils::getPrefix() . "correos_codes.id = coss.correos_code LEFT Join
                     " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes As " . CorreosOficialUtils::getPrefix() . "cex_codes On " . CorreosOficialUtils::getPrefix() . "cex_codes.id = coss.cex_code
                WHERE
                TIMESTAMPDIFF(MONTH, date_add, now()) < 4
                AND carrier_type = '$company'
                AND (last_status != 12 AND
                        last_status != 13 AND
                        last_status != 14 AND
                        last_status != 15 AND
                        last_status != 16 AND
                        last_status != 17 AND
                        last_status != 19 AND
                        last_status != 31 AND
                        last_status != 'Entregado' AND
                        last_status != 'Reetiquetado')
                    AND coo.shipping_number !=''
                    AND deleted_at IS NULL
                    ORDER BY coo.id_order ASC
                    LIMIT $limit, $this->how_many";        


        $records = $this->getOrdersForTrackingDao($query);

        return $this->getOrdersFromSavedOrders($records);
    }

    private function getOrdersFromSavedOrders($records)
    {
        $recordsWithShippingNumber = array();

        foreach ($records as $record) {
            $shipping_number = $this->getShippingNumbersByExpediton($record['exp_number']);
            $record['shipping_number'] = $shipping_number;
            $recordsWithShippingNumber[] = $record;
        }

        return $recordsWithShippingNumber;
    }

    private function getShippingNumbersByExpediton($expedition)
    {
        $shipping_number_array = array();

        $expeditions = $this->readRecord('correos_oficial_saved_orders', "WHERE exp_number='$expedition'", 'shipping_number');
        foreach ($expeditions as $expedition) {
            $shipping_number_array['shipping_number'] = $expedition->shipping_number;
        }
        return $shipping_number_array['shipping_number'];
    }

    /**
     * Cambia el estado de pedido en la tabla correos_oficial_orders
     * @param int $shipping_number nº de envío de Correos o CEX
     * @param int $tracking_response Respuesta del webserver
     * @param int $status Estado con el que quedará en la bbdd (Grabado, Enviado, Anulado, Devuelto, Entregado).
     */
    public function changeShippingStatus($shipping_number, $tracking_response, $status)
    {
        $table = 'correos_oficial_orders';
        $record['last_status'] = $tracking_response;
        $record['status'] = $status;
        $record['updated_at'] = date("Y-m-d H:i:s");
        $where = " WHERE id_order = '" . $shipping_number . "' AND status != '" . $status . "' ";

        return $this->changeShippingStatusDao($table, $record, $where);
    }

    /**
     * Obtiene el nº de pedido por el nº de envío.
     * @param string $shipping_number Nº de Envío
     * @return int id
     */
    public function getOrderIdByShippingNumber($shipping_number)
    {
        return $this->getOrderIdByShippingNumberDao($shipping_number);
    }
    /**
     * Obtiene el estado del pedido de la base de datos mediante el nº de envío.
     * @param string $shipping_number Nº de Envío
     * @return int estado del webserver
     */
    public function getWSStatusByShippingNumber($shipping_number, $orders)
    {
        foreach ($orders as $order) {
            if ($shipping_number == $order['shipping_number']) {
                return $order['last_status'];
            }
        }
    }

    /**
     * Devuelve los pedidos que han sido preregistrados para hacer seguimiento.
     */
    public function getStatusFromCorreosOficialOrders($id_order)
    {
        $query = "SELECT last_status
            FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders
            WHERE id_order=$id_order";
        return $this->getOrdersForTrackingDao($query);
    }

    /**
     * Escribe los últimas peticiones a los webservices.
     */
    private function writeLogLastRequest($last_request)
    {
        if (CorreosOficialUtils::dirExists($this->logLastRequest)) {
            file_put_contents($this->logLastRequest, print_r($last_request, true) . PHP_EOL, FILE_APPEND);
        }
    }

    private function processDataApi($response, $orders)
    {
        $ret = json_decode($response, true);
            CorreosOficialUtils::varDump("RETORNO1_CEX:", $ret);

            $flag_generate_error_log = false;

            if (isset($ret['listaEnvios'])) {
                foreach ($ret['listaEnvios'] as $item) {
                    CorreosOficialUtils::varDump("RESPONSE_CEX", $item);

                    if (!CorreosOficialUtils::sislogModuleIsActive()) {
                        $this->getWSStatusByShippingNumber($item['nEnvio'], $orders);
                        $this->processTrackingOrder($item);
                    } else {
                        $this->processSGATrackingOrder($item);
                    }

                    // Comprobamos código de estado de algún pedido.
                    switch ($item['codigoEstado']) {
                        // Sin recepción
                        case 1:
                        // Devuelto
                        case 17:
                        // Anulados
                        case 13:
                        case 14:
                        case 15:
                        case 16:
                        case 19:
                        case 31:
                            $flag_generate_error_log = false;
                            break;
                        default:
                            $flag_generate_error_log = true;
                    }
                }

                // Si hubo errores generamos fichero de error.
                if ($flag_generate_error_log) {
                    if (CorreosOficialUtils::dirExists($this->logErrors)) {
                        file_put_contents($this->logErrors, print_r($response, true) . PHP_EOL, FILE_APPEND);
                    }
                } elseif (!$flag_generate_error_log) { // Si hubo errores generamos fichero de error.
                    if (CorreosOficialUtils::dirExists($this->logErrors)) {
                        file_put_contents($this->logErrors, 'No ha habido errores' . PHP_EOL, FILE_APPEND);
                    }
                }
            }
            return true;
    }

}
