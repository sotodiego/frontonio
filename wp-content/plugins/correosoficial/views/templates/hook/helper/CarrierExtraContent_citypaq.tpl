{**
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
 *}
<div class="extra-container ">
    <div class="row customs-advice-doc">
        {if $aviso_aduanas_interiores eq 'on' && $require_customs_doc eq true} {$string_translated} {/if}
    </div>
    <div class="row checkout-paq-advice">
        <div class="col-sm-12">
            <div class="input-group mb-2">
                <span>{l s='Enter the Postcode to search for CityPaq' mod='correosoficial'}</span>
            </div>
        </div>
    </div>

    <div class="row search-paq-section">
        <div class="section-SearchCityPaqByCPInput">
            <input type="text" name="SearchCityPaqByCPInput_{$params.id_carrier|intval}"
                id="SearchCityPaqByCPInput_{$params.id_carrier|intval}"
                class="search-citypaq-by-cp-input form-control frontColorStyle" />
        </div>
        <div class="section-SearchCityPaqByCp">
            <button class="btn btn-outline SearchCityPaqByCp co_primary_button"
                id="SearchCityPaqByCpButton_{$params.id_carrier|intval}" type="button">
                {l s='Search' mod='correosoficial'}
            </button>
        </div>
        <div class="section-frontOptionSelector">
            <select class="citypaqSelector frontOptionSelector" name="CityPaqSelect_{$params.id_carrier|intval}"
                id="CityPaqSelect_{$params.id_carrier|intval}">
                <option value="none">{l s='Office found' mod='correosoficial'}</option>
            </select>
            <input type="hidden" name="citypaq_postcode_{$params.id_carrier|intval}"
                id="citypaq_postcode_{$params.id_carrier|intval}" value="{$params.postcode}" />
                <input type="hidden" name="citypaq_reference_{$params.id_carrier|intval}" 
                id="citypaq_reference_{$params.id_carrier|intval}" value="{$params.citypaq_reference|default:''}" />         
            <input type="hidden" name="citypaq_selector_{$params.id_carrier|intval}"
                id="citypaq_selector_{$params.id_carrier|intval}" value="{$params.id_carrier|intval}" />
        </div>
    </div>
    <div class="row mb-3 schedule-and-map" id="scheduleAndMap_{$params.id_carrier|intval}">
        <div class="colm-12 city-paq-schedule-and-map">
            <div class="col-sm-6">
                    <div class="locationSection mb-1">
                        <span class="">
                            <h3>{l s='Terminal' mod='correosoficial'}</h3>
                        </span>
                    </div>
                    <div id="terminalInfo{$params.id_carrier|intval}" class="citypaq-terminal-info">
                        <p class="nombre mb-2">
                            {if isset($officeOrCityPaqParams[0].alias)}{$officeOrCityPaqParams[0].alias}{/if}</p>
                    </div>
                <div class="map-info mb-1 pl-0">
                    <div class="locationSection mb-1">
                        <span class="timetable-section">
                            <h3>{l s='Timetable' mod='correosoficial'}</h3>
                        </span>
                    </div>
                    <div class="timetable-info col-sm-11 pl-0 scheduleInfo">
                        <p>-</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-1 pl-0">
                    <div class="mb-1 locationSection">
                        <span class="">
                            <h3>{l s='Location' mod='correosoficial'}</h3>
                        </span>
                    </div>
                    <div class="citypaq-address-info" id="addressInfo{$params.id_carrier|intval}">
                        {l s='Address' mod='correosoficial'}
                        <p class="address">
                            {if isset($officeOrCityPaqParams[0].direccion) }{$officeOrCityPaqParams[0].direccion}{/if}
                        </p>
                        {l s='City' mod='correosoficial'}
                        <p class="city">
                            {if isset($officeOrCityPaqParams[0].desc_localidad)}{$officeOrCityPaqParams[0].desc_localidad}{/if}
                        </p>
                        {l s='Zip Code' mod='correosoficial'}
                        <p class="cp">
                            {if isset($officeOrCityPaqParams[0].cod_postal)}{$officeOrCityPaqParams[0].cod_postal}{/if}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-3 map-section {if !$show_maps }co_hidden_map{/if}">
                <div id="GoogleMapCorreos_{$params.id_carrier}" class="map"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var openingInfo = "{l s='Opening hours' mod='correosoficial'}";
    var opening24hInfo = "{l s='Open 24 hours' mod='correosoficial'}";
    var cityPaqNotFound = "{l s='Can not connect with the CityPaq service' mod='correosoficial'}";
    var cityPaqPostCodeNotFound = "{l s='Can not find CityPaqs for postal code' mod='correosoficial'} ";
    var pickupPointSameProvince = "{l s='You must choose a pickup point in the same province as the shipping address' mod='correosoficial'} ";
    var ajaxError = "{l s='Han error has ocurred calling the CityPaq locator service' mod='correosoficial'}";
    var searchForCityPaq =
    "{l s='Please search and select a terminal before completing the order' mod='correosoficial'}";
    var defined_google_api_key = '{$defined_google_api_key}';
    var show_maps = '{$show_maps}';
</script>
