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
require_once dirname(__FILE__) . '/../../../classes/CorreosOficialOrders.php';

require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialSmarty.php';

class CorreosOficialPrepareRequestsDocAduanera extends CorreosOficialDAO
{

    private $optionButton;
    private $shipping_number;
    private $customer_country;
    private $customer_name;

    public function __construct($optionButton, $shipping_number, $customer_country, $customer_name)
    {

        parent::__construct();
        $this->optionButton = $optionButton;
        $this->shipping_number = $shipping_number;
        $this->customer_country = $customer_country;
        $this->customer_name = $customer_name;
    }

    public function prepareRequest()
    {
        $smarty = CorreosOficialSmarty::loadSmartyInstance();
        $doc_aduanera_data = self::getShippingDataForRequest();

        $smarty->assign("doc_aduanera_data", $doc_aduanera_data);

        switch ($this->optionButton) {
            case 'ImprimirCN23Button':
                $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/documentacion_aduaneraCN23.tpl');
                break;
            case 'ImprimirDUAButton':
            case 'ImprimirDDPButton':
                $request = $smarty->fetch(dirname(__FILE__) . '/../../ecommerce_common_lib/services/preregistro/documentacion_aduanera.tpl');
                break;
        }

        return $request;
    }

    /**
     * Consigue los detalles de un pedido a la hora para porder hacer llamada al Servicio de WS.
     */
    public function getShippingDataForRequest()
    {
        $doc_aduanera_data = array();

        switch ($this->optionButton) {
            case 'ImprimirCN23Button':
                $doc_aduanera_data['codigoEnvio'] = $this->shipping_number;
                break;
            case 'ImprimirDUAButton':
                $doc_aduanera_data['optionButton'] = 'DCAF';
                break;
            case 'ImprimirDDPButton':
                $doc_aduanera_data['optionButton'] = 'DDP';
                break;
        }
        $doc_aduanera_data['customer_country'] = $this->customer_country;
        $doc_aduanera_data['customer_name'] = $this->customer_name;
        $array_datos_correos_key = $this->getValue('CorreosKey', 'Correos', 'company', 'correos_oficial_codes');
        $doc_aduanera_data['cod_etiquetador'] = $array_datos_correos_key[0]['CorreosKey'];

        return $doc_aduanera_data;
    }

    // Función para recuperar un único campo de la DB
    private function getValue($value, $key, $field, $table)
    {
        $query = "SELECT " . $value . " FROM " . CorreosOficialUtils::getPrefix() . "$table WHERE $field='$key'";
        return $this->getRecordsWithQuery($query, true);
    }
}
