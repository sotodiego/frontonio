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
 * Consigue los clientes activos Correos o CEX
 * @author A649437
 */
require_once dirname(__FILE__).'/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../ecommerce_common_lib/Commons/Normalization.php';

class CorreosOficialActiveCustomersDao extends CorreosOficialDao
{

    private $table='correos_oficial_codes_actives';
 
    public function __construct()
    {
        parent::__construct();

        $action = '';
        $company = '';
        $active = '';
        
        if (DetectPlatform::isWordPress()) {
            if (isset($_REQUEST['action'])) {
                $action = $_REQUEST['action'];
            }
            if (isset($_REQUEST['company'])) {
                $company = $_REQUEST['company'];
            }
            if (isset($_REQUEST['active'])) {
                $active = $_REQUEST['active'];
            }
        } elseif (DetectPlatform::isPrestashop()) {
            $action=Tools::getValue('action');
            $company=Normalization::normalizeData('company');
            $active=Normalization::normalizeData('active');
        }

        if (isset($action) && $action == 'updateActiveCustomers') {
            $this->updateActiveCustomers($company, $active);
        } elseif (isset($action) && $action == 'getActivesCustomers') {
            $this->getActivesCustomers(true);
        }
    }

    /**
     * @param ajax si true devuelve la salida para AJAX, si false retorna salida
     * @return string 'both' siempre, se comprueba mediante remitente si tiene Correos, CEX o ambos
     */
    public function getActivesCustomers($ajax=false)
    {
        if ($ajax) die ('both'); else return 'both';
    }

    public function getCustomer($company)
    {
        $customer=$this->readRecord($this->table, "WHERE company='$company'");
        return $customer[0];
    }

    public function updateActiveCustomers($company, $active)
    {
        $active=(int) $active;
        $query="UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET active='$active' WHERE company='$company'";
        $this->executeQuery($query);
        die('Actualizado cliente activo');
    }
}
