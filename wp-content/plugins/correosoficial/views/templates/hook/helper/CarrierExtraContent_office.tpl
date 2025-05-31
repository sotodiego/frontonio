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
<div class="extra-container">
    <div class="row customs-advice-doc">
        {if $aviso_aduanas_interiores eq 'on' && $require_customs_doc eq true} {$string_translated} {/if}
    </div>
    <div class="row checkout-paq-advice">
        <div class="col-sm-12">
            <div class="mb-2 checkout-paq-advice">
                <span>{l s='Enter the Postcode to search for Office' mod='correosoficial'}</span>
            </div>
        </div>
    </div>

    <div class="row search-paq-section">
        <div class="section-SearchOfficeByCPInput">
            <input type="text" name="SearchOfficeByCPInput_{$params.id_carrier|intval}"
                id="SearchOfficeByCPInput_{$params.id_carrier|intval}"
                class="search-office-by-cp-input form-control frontColorStyle" />
        </div>
        <div class="section-SearchOfficeByCp">
            <button class="btn btn-outline SearchOfficeByCp co_primary_button"
                id="SearchOfficeByCpButton_{$params.id_carrier|intval}" type="button">
                {l s='Search' mod='correosoficial'}
            </button>
        </div>
        <div class="section-frontOptionSelector">
            <select class="officeSelector frontOptionSelector" name="OfficeSelect_{$params.id_carrier|intval}"
                id="OfficeSelect_{$params.id_carrier|intval}">
                <option value="none">{l s='Office found' mod='correosoficial'}</option>
            </select>
            <input type="hidden" name="office_postcode_{$params.id_carrier|intval}"
                id="office_postcode_{$params.id_carrier|intval}" value="{$params.postcode}" />
            <input type="hidden" name="office_reference_{$params.id_carrier|intval}"
                id="office_reference_{$params.id_carrier|intval}" value="{$params.office_reference|default:''}" />
            <input type="hidden" name="office_selector_{$params.id_carrier|intval}"
                id="office_selector_{$params.id_carrier|intval}" value="{$params.id_carrier|intval}" />
        </div>
    </div>
    <div class="row mb-3 schedule-and-map" id="scheduleAndMap_{$params.id_carrier|intval}">
        <div class="col-sm-12 office-schedule-and-map">
            <div class="col-sm-6">
                <div class="locationSection mb-1">
                    <span class="">
                        <h3>{l s='Office' mod='correosoficial'}</h3>
                    </span>
                </div>
                <div id="terminalInfo{$params.id_carrier|intval}" class="office-terminal-info">
                    <p class="nombre mb-2">
                        {if isset($officeOrCityPaqParams[0].nombre)}{$officeOrCityPaqParams[0].nombre}{/if}</p>
                </div>
                <div class="map-info mb-3 pl-0">
                    <div class="locationSection mb-1 timetable-section">
                        <span class="timetable-section">
                            <h3>{l s='Timetable' mod='correosoficial'}</h3>
                        </span>
                    </div>
                    <div class="scheduleInfo col-sm-11 pl-0 schedule-section"
                        id="scheduleInfo_{$params.id_carrier|intval}">
                        {l s='Monday to Friday' mod='correosoficial'}:
                        <p class="timeScheduleLV">
                            {if isset($officeOrCityPaqParams[0].horarioLV)}{$officeOrCityPaqParams[0].horarioLV}{/if}
                        </p>
                        {l s='Saturday' mod='correosoficial'}:
                        <p class="timeScheduleS">
                            {if isset($officeOrCityPaqParams[0].horarioLS)}{$officeOrCityPaqParams[0].horarioLS}{/if}
                        </p>
                        {l s='Holidays' mod='correosoficial'}:
                        <p class="timeScheduleF">
                            {if isset($officeOrCityPaqParams[0].horarioLF)}{$officeOrCityPaqParams[0].horarioLF}{/if}
                        </p>
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
                    <div class="office-address-info" id="addressInfo{$params.id_carrier|intval}">
                        {l s='Address' mod='correosoficial'}:
                        <p class="address">
                            {if isset($officeOrCityPaqParams[0].direccion)}{$officeOrCityPaqParams[0].direccion}{/if}
                        </p>
                        {l s='City' mod='correosoficial'}:
                        <p class="city">
                            {if isset($officeOrCityPaqParams[0].descLocalidad)}{$officeOrCityPaqParams[0].descLocalidad}{/if}
                        </p>
                        {l s='Zip Code' mod='correosoficial'}:
                        <p class="cp">{if isset($officeOrCityPaqParams[0].cp)}{$officeOrCityPaqParams[0].cp}{/if}</p>
                        {l s='Phone' mod='correosoficial'}:
                        <p class="phone">
                            {if isset($officeOrCityPaqParams[0].telefono)}{$officeOrCityPaqParams[0].telefono}{/if}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 mb-3 map-section {if !$show_maps }co_hidden_map{/if}">
                <div id="GoogleMapCorreos_{$params.id_carrier|intval}" class="map"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var openingInfo = "{l s='Open' mod='correosoficial'}";
    var closedInfo = "{l s='Closed' mod='correosoficial'}";
    var officeNotFound = "{l s='Can not connect with the Office service' mod='correosoficial'}";
    var officePostCodeNotFound = "{l s='Can not find Office for postal code' mod='correosoficial'} ";
    var pickupPointSameProvince = "{l s='You must choose a pickup point in the same province as the shipping address' mod='correosoficial'} ";
    var ajaxError = "{l s='Han error has ocurred calling the Office locator service' mod='correosoficial'}";
    var searchForOffice = "{l s='Please search and select an office before completing the order' mod='correosoficial'}";
    var defined_google_api_key = '{$defined_google_api_key}';
    var show_maps = '{$show_maps}';
</script>
