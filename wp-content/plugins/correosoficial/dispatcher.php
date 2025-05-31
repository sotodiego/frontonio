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
// Si incluimos el dispacher no cargar autoload
if (!isset($_GET['autoload'])) {
	include_once '../../../wp-load.php';
}

require_once 'controllers/admin/AdminHomeSendMailController.php';
require_once 'controllers/admin/AdminCorreosOficialCustomerDataProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialSendersProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialUserConfigurationProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialProductsProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialZonesCarriersProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialCustomsProcessingProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialCronProcessController.php';
require_once 'controllers/admin/AdminCorreosOficialUtilitiesProcessController.php';

require_once 'controllers/admin/AdminCorreosOficialActiveCustomersController.php';

require_once 'controllers/admin/AdminCorreosSOAPRequestController.php';
require_once 'controllers/admin/AdminCorreosRestRequestController.php';
require_once 'controllers/admin/AdminCEXRestRequestController.php';

require_once 'controllers/front/CorreosOficialCheckoutModuleFrontController.php';
require_once 'controllers/front/CorreosOficialAdminOrderModuleFrontController.php';

require_once 'vendor/ecommerce_common_lib/CorreosOficialSmarty.php';

$controller = sanitize_text_field(isset($_GET['controller']) ? $_GET['controller'] : '');
$controllerAction = sanitize_text_field(isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$controllerOperation = sanitize_text_field(isset($_REQUEST['operation']) ? $_REQUEST['operation'] : '');

switch ($controller) {
	case 'AdminHomeSendMail':
		$traza = '<strong> Llamando al controlador</strong>: AdminHomeSendMail';
		return new AdminHomeSendMailController();
		break;
	case 'AdminCorreosOficialCustomerDataProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialCustomerDataProcess';
		$controller = new AdminCorreosOficialCustomerDataProcessController();
		break;
	case 'AdminCorreosOficialSendersProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialSendersProcess';
		$controller = new AdminCorreosOficialSendersProcessController();
		break;
	case 'AdminCorreosOficialUserConfigurationProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialUserConfigurationProcess';
		$controller = new AdminCorreosOficialUserConfigurationProcessController();
		break;
	case 'AdminCorreosOficialProductsProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialProductsProcess';
		$controller = new AdminCorreosOficialProductsProcessController();
		break;
	case 'AdminCorreosOficialCustomsProcessingProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialCustomsProcessingProcess';
		$controller = new AdminCorreosOficialCustomsProcessingProcessController();
		break;
	case 'AdminCorreosOficialSettings':
		include_once 'vendor/smarty/Smarty.class.php';
		$smarty = CorreosOficialSmarty::loadSmartyInstance();
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialSettings';
		$controller = new AdminCorreosOficialSettingsController($smarty);
		break;
	case 'AdminCorreosOficialUtilitiesProcess':
		include_once 'vendor/smarty/Smarty.class.php';
		$smarty = CorreosOficialSmarty::loadSmartyInstance();
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialUtilitiesProcess';
		$controller = new AdminCorreosOficialUtilitiesProcessController($smarty);
		break;
	case 'AdminCorreosSOAPRequest':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosSOAPRequest';
		$controller = new AdminCorreosSOAPRequestController();
		break;
	case 'AdminCEXRestRequest':
		$traza = '<strong> Llamando al controlador</strong>: AdminCEXRestRequest';
		$controller = new AdminCEXRestRequestController();
		break;
	case 'AdminCorreosOficialActiveCustomers':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialActiveCustomers';
		$controller = new AdminCorreosOficialActiveCustomersController();
		break;
	case 'CorreosOficialCheckoutModuleFrontController':
		$traza = '<strong> Llamando al controlador</strong>: Checkout';
		$controller = new CorreosOficialCheckoutModuleFrontController($controllerAction);
		break;
	case 'AdminCorreosOficialZonesCarriersProcess':
		$traza = '<strong> Llamando al controlador</strong>: AdminCorreosOficialZonesCarriersProcess';
		$controller = new AdminCorreosOficialZonesCarriersProcessController();
		break;
	case 'CorreosOficialAdminOrderModuleFrontController':
		$traza = '<strong> Llamando al controlador</strong>: adminOrder';
		$controller = new CorreosOficialAdminOrderModuleFrontController();
		break;
	case 'AdminCorreosOficialDownloadLabelsController':
		$sanitizedName = sanitize_text_field(isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '');
		$filename = pathinfo($sanitizedName, PATHINFO_FILENAME);
		$filename = preg_replace('/\W+/', '', $filename) . '.pdf';
		wp_die( esc_html($filename) );
		break;
	default:
		die( 'WC DISPATCHER: Controlador no válido' );
}
