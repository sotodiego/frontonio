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
 * Reglas de métodos de envíos y zonas según PRODUCTO
 */
class ShippingMethodZoneRules extends CorreosOficialCarrier
{

    private $national_types;
    private $exclude_CEX_90;

    private $exclude_S0360;
    private $exclude_PT;

    private $other_regions;
    private $exclude_ES;
    private $exclude_ES_PT_AD;
    
    public function __construct()
    {
        parent::__construct();

        $this->national_types = array('44', '61', '62', '63', '92', '93', '26', '46', '79', '54', '27', '76', 'S0235', 'S0236', 'S0176', 'S0132', 'S0133', 'S0178', 'S0179', null);

        $this->exclude_CEX_90 = array(
            'LU', 'AT', 'BE', 'BG', 'CH', 'CZ', 'DE', 'DK', 'EE', 'FI', 'FR', 'GB',
            'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LV', 'NL', 'NO', 'PL', 'RO', 'RS', 'SE', 'SI', 'SK', 'TR'
        );

        $this->exclude_S0360 = array('LU', 'SA', 'DO', 'AE', 'AT', 'AU', 'AW', 'BB', 'BE', 'BR', 'CA', 'CH', 'CN', 'CY',
            'CZ', 'DE', 'DK', 'EE', 'EG', 'FI', 'FR', 'GB', 'GE', 'GI', 'GR', 'HK', 'HR', 'HU', 'ID', 'IE', 'IL',
            'IS', 'IT', 'JE', 'JP', 'KR', 'LB', 'LT', 'LV', 'MT', 'MX', 'MY', 'NL', 'NO', 'NZ', 'PL', 'PT', 'RO',
            'RS', 'RU', 'SE', 'SG', 'SI', 'SK', 'SZ', 'TH', 'TR', 'ZA', 'US', 'KZ', 'ZW');
        $this->exclude_PT = array('90', '91', 'S0133', 'S0176', 'S0178', 'S0236', '46', '76');

        $this->exclude_ES = array('73', '90', '91', 'S0410', 'S0411', 'S0360', 'S0004', 'S0031');
        $this->exclude_ES_PT_AD = array('26', '27', '46', '54', '61', '62', '63', '73', '76', '79', '92', '93', 'S0132', 'S0133', 'S0176', 'S0178', 'S0179', 'S0235', 'S0236');

        $this->other_regions = array('90', 'S0410', 'S0411', 'S0004', 'S0031');
    }

    /**
     * (Regla 1) Si la zona contiene ISOS “ES o AD” eliminamos de la lista los siguientes productos
     *            internacionales  “91, 90, S0410, S0411, S0360”
     */
    public function isInternational($iso, $product_type)
    {
		$iso = strtoupper($iso);
        if (($iso != 'ES' && $iso != 'AD') && $product_type == 'international') {
            return true;
        }
        return false;
    }

    /**
     * (Regla 2) Si la zona contiene algún ISO diferente a
     * “LU, AT, BE, BG, CH, CZ, DE, DK, EE, FI, FR, GB, GR, HR,
     * HU, IE, IT, LT, LV, NL, NO, PL, RO, RS, SE, SI, SK, TR”
     * eliminamos de la lista el Internacional Estándar de CEX (90)
     */
    public function excludeCEX90($iso, $product_code)
    {
		$iso = strtoupper($iso);
        if (in_array($iso, $this->exclude_CEX_90) && $product_code == '90') {
            return true;
        }
        return false;
    }

    /**
     * (Regla 3) Si la zona contiene algún ISO diferente a “LU, SA, DO, AE, AT, AU, AW, BB, BE, BR, CA, CH,
     * CY, CZ, DE, DK, EE, EG, FI, FR, GB, GE, GI, GR, HK, HR, HU, ID, IE, IL, IS, IT, JE, JP, KR, LB, LT, LV,
     * MT, MX, MY, NL, NO, NZ, PL, PT, RO, RS, RU, SE, SG, SI, SK, SZ, TH, TR, ZA, US, KZ” eliminamos de la lista el
     * Paq Light Internacional (S0360)
     */
    public function excludeS360($iso, $product_code)
    {
		$iso = strtoupper($iso);
        if (!in_array($iso, $this->exclude_S0360) && $product_code == 'S0360') {
            return true;
        }
        return false;
    }

    /**
     * (Regla 4) Si la zona contiene el ISO “PT” eliminamos los internacionales de CEX (91 y 90) y los productos Citypaq y Oficina (S0236, S0176, S0133 y S0178)
     */
    public function excludeNationalProducts($iso, $product_type)
    {
		$iso = strtoupper($iso);
        if (($iso == 'PT') && in_array($product_type, $this->exclude_PT)) {
            return true;
        }
        return false;
    }

    public function isNational($iso, $product_type)
    {
		$iso = strtoupper($iso);
        if (($iso == 'ES' || $iso == 'PT' || $iso == 'AD') && in_array($product_type, $this->national_types)) {
            return true;
        }
        return false;
    }

    public function filterProducts($zone_array)
    {
        $products = array(
            '26', '27', '44', '46', '54', '61', '62', '63', '73', '76', '79', '90', '91', '92', '93',
            'S0132', 'S0133', 'S0176', 'S0178', 'S0179', 'S0235', 'S0236', 'S0360', 'S0410', 'S0411', 'S0004', 'S0031'
        );

        // Ubicaciones no cubiertas por tus otras zonas
        if ($zone_array['id'] == 0) {
            return $this->other_regions;
        } else {
            foreach ($zone_array['zone_locations'] as $region) {
                $iso = self::getRegionType($region);

                if ($iso == null) {
                    continue;
                }
                if ($iso == 'ES') {
                    $products = array_diff($products, $this->exclude_ES);
                }

                if ($iso == 'PT') {
                    $products = array_diff($products, $this->exclude_PT);
                }

                if (!in_array($iso, array('ES', 'PT', 'AD'))) {
                    $products = array_diff($products, $this->exclude_ES_PT_AD);
                }

                if (!in_array($iso, $this->exclude_CEX_90)) {
                    $products = array_diff($products, array('90'));
                }

                if (!in_array($iso, $this->exclude_S0360)) {
                    $products = array_diff($products, array('S0360'));
                }
            }

            if (isset($products) && $zone_array['zone_locations']) {
                return $products;
            }
        }
    }

    /**
     * Obtiene el código ISO país según la tabla <prefix>_woocommerce_shipping_zone_locations
     * @param data location_code de la tabla
     * @return Isocode
     */
    public static function getRegionType($data)
    {
        if ($data->type == 'state') {
            return substr($data->code, 0, 2);
        } elseif ($data->type == 'postcode') {
            if (strstr($data->code, '...')) {
                $tokens = explode('...', $data->code);
                $from = $tokens[0];
                $to = $tokens[1];

                $sql = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_postcodes WHERE postcode BETWEEN '{$from}' AND '{$to}'";
            } else {
                $sql = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_postcodes WHERE postcode='{$data->code}' LIMIT 1";
            }
            $result = self::getCarrierRecords($sql, true);

            if (isset($result[0])) {
                return $result[0]['type'];
            }
        } elseif ($data->type == 'country' || $data->type == 'continent') {
            return $data->code;
        }
    }

    public function filterCarriers($all_carriers, $countries)
    {

        foreach ($all_carriers as $carrier) {

            foreach ($countries as $country) {

                $add_carrier = true;
                $exclude = false;

                // Excluir CEX90
                if (self::excludeCEX90($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                // PAQ LIGHT INTERNACIONAL
                if (self::excludeS360($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                // Portugal
                if (self::excludeNationalProducts($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                //Internacionales
                if (self::isInternational($country['iso_code'], $carrier->product_type) && !$exclude) {
                    break;
                }

                // Nacionales
                if (self::isNational($country['iso_code'], $carrier['codigoProducto']) && !$exclude) {
                    break;
                } else {
                    $add_carrier = false;
                }
            }

            if ($add_carrier) {
                $carriers[] = $carrier;
            }
        }

        return $carriers;
    }

    public function getIsoS0360() {
        return $this->exclude_S0360;
    }
}
