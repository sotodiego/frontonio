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
 * @uses provide methods to request Rest calls to Correos WebServices
 * @version: 1
 *
 */

require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';
require_once dirname(__FILE__) . '/../CorreosOficialUtils.php';
require_once dirname(__FILE__) . '/../DetectPlatform.php';
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;

class CorreosRest
{

    /**
     * Constructor. Realiza las operaciones necesarias según la plataforma.
     * @return void
     *
     */
    public function __construct()
    {

    }

    /* **********************************************************************************************************
     *                                            HISTÓRICO DE PEDIDO
     ********************************************************************************************************* */
    public function getOrderStatus($correos_code, $all, $id_sender = false)
    {

        try {
            $customer = new CorreosOficialCustomerDataDao();

            if ($id_sender) {
                $id = $customer->getIdCodeFromSenderByDao($id_sender);
            } else {
                $id = $customer->getIdByCompany('Correos');
            }
    
            $correos_user_password = $customer->getUserPassword($id);

            $correos_user_password['password'] = CorreosOficialCrypto::decrypt($correos_user_password['password']);

            if ($all) {
                $url = $this->location($correos_code) . '&indUltEvento=N';
            } else {
                $url = $this->location($correos_code) . '&indUltEvento=S';
            }

            $username = $correos_user_password['login'];
            $password = $correos_user_password['password'];

            $headers = array('http' => array(
                'method' => 'GET',
                'header' => "Accept-language: en\r\n" . "Authorization: Basic " . base64_encode("$username:$password")
            )
            );

            $context = stream_context_create($headers);
    
            $result = CorreosRest::curlExec($url, $username, $password);

            if (!$result) {
                throw new LogicException("ERROR 15550: El servicio de seguimiento no está disponible temporalmente\r\n");
            }

            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function cronTrackingCorreos($orders)
    {

        $correos_code = '';

        foreach ($orders as $order) {
            $correos_code .= $order['shipping_number'] . ",";
        }

        $correos_code[strlen($correos_code) - 1] = ' ';
        $url = $this->location(trim($correos_code));

        $numship_list = array();
        foreach ($orders as $order) {
            $numship = $order['shipping_number'];
            if (!empty($numship)) {
                array_push($numship_list, $numship);
            }
        }

        $rest_request['nEnvios'] = $numship_list;

        return array('peticion' => json_encode($rest_request),
            'url' => $url);
    }

    public function cronTrackingCorreosRequestCall($orders)
    {
        $response = [];
        $customer = new CorreosOficialCustomerDataDao();

        foreach ($orders as $order) {
            $id = $customer->getIdCodeFromOrder($order['id_order']);
    
            $correos_user_password = $customer->getUserPassword($id);
            $correos_user_password['password'] = CorreosOficialCrypto::decrypt($correos_user_password['password']);
            
            $correos_code = $order['shipping_number'];

            $url = $this->location(trim($correos_code));
            $username = $correos_user_password['login'];
            $password = $correos_user_password['password'];

            $result = CorreosRest::curlExec($url, $username, $password);

            if (!$result) {
                throw new LogicException("ERROR 15540: El servicio de localizador no está disponible temporalmente.
                     El Cron puede no reflejar los estados de sus envíos hasta que se ejecute de nuevo correctamente
                     según su configuración en Ajustes->Configuración de usuario\r\n");
            }
            $response[] = json_decode($result);
        }

        return json_encode($response);
    }

    public function cronTrackingSGARequestCall ( $orders ) {
        $response = [];
        $customer = new CorreosOficialCustomerDataDao();

        foreach ($orders as $order) {

            $id = $customer->getIdCodeFromSGAOrder();

            $correos_user_password = $customer->getUserPassword($id);
            $correos_user_password['password'] = CorreosOficialCrypto::decrypt($correos_user_password['password']);

            if(DetectPlatform::isPrestashop()) {
                $correos_code = $order->getWsShippingNumber();
            } else if (DetectPlatform::isWordpress()) {
                $correos_code = $this->getWcTrackingNumber($order);
            }

            $url = $this->location(trim($correos_code));
            $username = $correos_user_password['login'];
            $password = $correos_user_password['password'];

            $result = CorreosRest::curlExec($url, $username, $password);
            
            if (!$result) {
                throw new LogicException("ERROR 15540: El servicio de localizador no está disponible temporalmente.
                     El Cron puede no reflejar los estados de sus envíos hasta que se ejecute de nuevo correctamente
                     según su configuración en Ajustes->Configuración de usuario\r\n");
            }
            $response[] = json_decode($result);
        }

        return json_encode($response);
    }

    private function getWcTrackingNumber($order) {
        if (!wc_get_container()->get(CustomOrdersTableController::class)->custom_orders_table_usage_is_enabled()) {
            $correos_code = get_post_meta($order->get_id(), 'correosecom_sga_tracking_number', true);
        } else {
            $correos_code = $order->get_meta('correosecom_sga_tracking_number', true);
        }

        return $correos_code;
    }

    /**
     *  Devuelve la URL para el servicio de seguimietno de correos
     * @link https://localizador.correos.es/canonico/eventos_envio_servicio_auth/PY43B40720207850128042X?codIdioma=ES&indUltEvento=N';
     * @return string Url para el servicio de seguimiento de correos
     */
    private function location($correos_code)
    {
        return sprintf(
            '%s/%s?codIdioma=ES',
            CORREOS_BASE_LOCATION,
            $correos_code
        );
    }

    public static function curlExec($url, $username, $password) {
		/* 
		  Se elimina la posible doble barra que pueda venir en la contraseña (después de la función CorreosOficialCrypto::decrypt
		*/
		$password = str_replace('\\\\', '\\', $password);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-language: en\r\n'
        ));
        
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get status code
        $info = curl_getinfo($ch);
        $codigo_error = curl_errno($ch);
        curl_close($ch);
        return $result;
    }
}
