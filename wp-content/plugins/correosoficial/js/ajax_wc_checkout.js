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
// Página cargada
jQuery(document).ready(function (e) {
    const actionCityPaq = 'SearchCityPaqByPostalCode';
    const actionOffice = 'SearchOfficeByPostalCode';
    let googleMap = new Array();
    let carriersData = new Array();

    // ACCIONES SEGÚN CONTEXTO
    switch (varsAjax.whereAmI) {
        case 'cart':
            // Cargamos directamente
            initLoad();
            // Cargamos al cambiar de método
            jQuery(document.body).on('updated_shipping_method', function () {
                initLoad();
            });
            break;
        case 'checkout':
            // Esperamos carga ajax del checkout por parte de WC
            jQuery(document.body).on('updated_checkout', function () {
                initLoad();

                // Para algunos temas que no llegan a tiempo a cargar el código postal
                if (jQuery('.search-citypaq-by-cp-input').val() == '' || jQuery('.search-office-by-cp-input').val() == '') {
                    setTimeout(function () {
                        initLoad();
                    }, 3000);
                }
            });
            break;
    }

    jQuery(document.body).on('updated_wc_div', function () {
        initLoad();
    });

    // CARGA INICIAL DE SELECTORES
    function initLoad() {
        // EVENTO CAMBIO DE TRANSPORTISTA
        let carrierSelected = jQuery('input[type="radio"][name^="shipping_method["][name$="]"]:checked');
        let radioButtonsCarriers = jQuery('input[type="radio"][name^="shipping_method["][name$="]"]');
        radioButtonsCarriers.on('change', function () {
            cleanCheckoutMetadata();
            carrierSelected = jQuery(this).val();
            let separator = carrierSelected.split(':');
            let carrierData = carriersData[parseInt(separator[1])];
            if (carrierData != undefined) {
                if (carrierData.action == actionCityPaq) {
                    insertCityPaq(carrierData.selected_location, carrierSelected);
                }
                if (carrierData.action == actionOffice) {
                    insertOffice(carrierData.selected_location, carrierSelected);
                }
            }
        });

        // OBTENEMOS SELECTORES CARGADOS
        let citypaqCarriers = jQuery('[id^="citypaq_selector_"]');
        let officeCarriers = jQuery('[id^="office_selector_"]');

        // CITYPAQS
        citypaqCarriers.each(function (index, element) {
            // Obtenemos el value del elemento
            let carrierId = jQuery(element).val();

            // Elementos del DOM
            let currentReference = jQuery('#citypaq_reference_' + carrierId);
            let currentPostcode = jQuery('#citypaq_postcode_' + carrierId);
            let inputSearch = jQuery('#SearchCityPaqByCPInput_' + carrierId);
            let buttonSearch = jQuery('#SearchCityPaqByCpButton_' + carrierId);
            let selectCityPaqs = jQuery('#CityPaqSelect_' + carrierId);
            let scheduleAndMap = jQuery('#scheduleAndMap_' + carrierId);

            // Obtenemos metadata
            let checkoutMetadata = getCheckoutMetadata();

            // Si tenemos metadata, la cargamos
            if (checkoutMetadata.CarrierID == carrierId) {
                currentReference.val(checkoutMetadata.SelectedReference);

                // Si no tenemos currentReference, la buscamos en carriersData
            } else if (!currentReference.val()) {
                let carrierData = carriersData[carrierId];
                if (carrierData != undefined) {
                    currentReference.val(carrierData.selected_location.reference);
                }
            }

            // Ocultamos horario y mapa
            scheduleAndMap.hide();

            // Añadimos postcode al input de búsqueda
            inputSearch.val(currentPostcode.val());
            inputSearch.keydown(function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    buttonSearch.click();
                }
            });

            // Si el valor del inputsearch es igual o mayor a 5, buscamos oficinas
            if (inputSearch.val().length >= 5) {
                setLocations(actionCityPaq, currentPostcode.val(), carrierId, selectCityPaqs, currentReference.val());
            }

            // Añadimos evento click al botón de buscar citipaq
            buttonSearch.on('click', async function () {
                // Obtenemos el valor del input
                let searchPostcode = inputSearch.val();

                let searchPostalCodeSub;
                let shippingAddressPostalCodeSub;
                if (searchPostcode) {
                    searchPostalCodeSub = searchPostcode.substring(0, 2);
                }

                if (currentPostcode.val()) {
                    let shippingAddressPostalCode = currentPostcode.val();
                    shippingAddressPostalCodeSub = shippingAddressPostalCode.substring(0, 2);
                }

                if (searchPostalCodeSub && shippingAddressPostalCodeSub) {
                    if (searchPostalCodeSub != shippingAddressPostalCodeSub) {
                        alert(pickupPointSameProvince);
                        return false;
                    }
                }

                // set de locations
                try {
                    let locations = await setLocations(actionCityPaq, searchPostcode, carrierId, selectCityPaqs, null);
                    let selected_location = locations[0];
                    insertCityPaq(selected_location, carrierId);
                    currentReference.val(selected_location.reference); // set current reference
                    currentPostcode.val(searchPostcode); // set current postcode
                } catch (error) {
                    alert(cityPaqPostCodeNotFound);
                    inputSearch.val(currentPostcode.val()); // reset search input
                }
            });

            // Map
            if (defined_google_api_key == 1) {
                // verificar si google está cargado
                if (typeof google !== 'undefined') {
                    let newGoogleMaps = new google.maps.Map(document.getElementById('GoogleMapCorreos_' + carrierId), {
                        center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                        zoom: 13,
                    });

                    googleMap[carrierId] = {
                        map: newGoogleMaps,
                        markers: [],
                    };
                }
            }
        });

        // OFICINAS
        officeCarriers.each(function (index, element) {
            // Obtenemos el value del elemento
            let carrierId = jQuery(element).val();

            // Elementos del DOM
            let currentReference = jQuery('#office_reference_' + carrierId);
            let currentPostcode = jQuery('#office_postcode_' + carrierId);
            let inputSearch = jQuery('#SearchOfficeByCPInput_' + carrierId);
            let buttonSearch = jQuery('#SearchOfficeByCpButton_' + carrierId);
            let selectOffices = jQuery('#OfficeSelect_' + carrierId);
            let scheduleAndMap = jQuery('#scheduleAndMap_' + carrierId);

            // Obtenemos metadata
            let checkoutMetadata = getCheckoutMetadata();

            // Si tenemos metadata, la cargamos
            if (checkoutMetadata.CarrierID == carrierId) {
                currentReference.val(checkoutMetadata.SelectedReference);

                // Si no tenemos currentReference, la buscamos en carriersData
            } else if (!currentReference.val()) {
                let carrierData = carriersData[carrierId];
                if (carrierData != undefined) {
                    currentReference.val(carrierData.selected_location.reference);
                }
            }

            // Ocultamos horario y mapa
            scheduleAndMap.hide();

            // Añadimos postcode al input de búsqueda
            inputSearch.val(currentPostcode.val());
            inputSearch.keydown(function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    buttonSearch.click();
                }
            });

            // Si el valor del inputsearch es igual o mayor a 5, buscamos oficinas
            if (inputSearch.val().length >= 5) {
                setLocations(actionOffice, currentPostcode.val(), carrierId, selectOffices, currentReference.val());
            }

            // Añadimos evento click al botón de buscar oficinas
            buttonSearch.on('click', async function () {
                // Obtenemos el valor del input
                let searchPostcode = inputSearch.val();

                let searchPostalCodeSub;
                let shippingAddressPostalCodeSub;
                if (searchPostcode) {
                    searchPostalCodeSub = searchPostcode.substring(0, 2);
                }

                if (currentPostcode.val()) {
                    let shippingAddressPostalCode = currentPostcode.val();
                    shippingAddressPostalCodeSub = shippingAddressPostalCode.substring(0, 2);
                }

                if (searchPostalCodeSub && shippingAddressPostalCodeSub) {
                    if (searchPostalCodeSub != shippingAddressPostalCodeSub) {
                        alert(pickupPointSameProvince);
                        return false;
                    }
                }

                // set de locations
                try {
                    let locations = await setLocations(actionOffice, searchPostcode, carrierId, selectOffices, null);
                    let selected_location = locations[0];
                    insertOffice(selected_location, carrierId);
                    currentReference.val(selected_location.reference); // set current reference
                    currentPostcode.val(searchPostcode); // set current postcode
                } catch (error) {
                    alert(officePostCodeNotFound);
                    inputSearch.val(currentPostcode.val()); // reset search input
                }
            });

            // Map
            if (defined_google_api_key == 1) {
                // verificar si google está cargado
                if (typeof google !== 'undefined') {
                    let newGoogleMaps = new google.maps.Map(document.getElementById('GoogleMapCorreos_' + carrierId), {
                        center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                        zoom: 13,
                    });

                    googleMap[carrierId] = {
                        map: newGoogleMaps,
                        markers: [],
                    };
                }
            }
        });
    }

    // OBTIENE Y CARGA LOCATIONS
    function setLocations(action, postcode, id_carrier, select, currentReference) {
        return new Promise(function (resolve, reject) {
            let results = new Array();
            let selectedOutput = null;

            jQuery.ajax({
                url: varsAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialCheckoutModuleFrontController',
                        action: action,
                        id_carrier: id_carrier,
                        postcode: postcode,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.json_retorno.soapenvBody == undefined) {
                        reject(false);
                    } else {
                        // Parseamos para CityPaq
                        if (action == actionCityPaq) {
                            let homepaqs = parsed_data.json_retorno.soapenvBody.homePaqRespuesta1.listaHomePaq.homePaq;
                            selectedOutput = jQuery('#citypaq_reference_' + id_carrier);
                            if (homepaqs != undefined) {
                                // reindexamos solo 1 resultado
                                if (homepaqs.cod_homepaq != undefined) {
                                    let homepaq = homepaqs;
                                    homepaqs = [];
                                    homepaqs.push(homepaq);
                                }
                                homepaqs.forEach(function (valor, indice, array) {
                                    let address_info = array[indice].des_via + ' ' + array[indice].direccion + ' ' + array[indice].numero;
                                    let data = {
                                        reference: array[indice].cod_homepaq,
                                        address: address_info,
                                        city: array[indice].desc_localidad,
                                        cp: array[indice].cod_postal,
                                        terminal: array[indice].alias,
                                        schedule: array[indice].ind_horario,
                                        lat: parseFloat(array[indice].latitudETRS89),
                                        lng: parseFloat(array[indice].longitudETRS89),
                                        raw: array[indice],
                                    };
                                    results.push(data);
                                });
                            }

                            // Si tenemos resultados
                            if (results.length > 0) {
                                fillSelect(id_carrier, action, select, results, currentReference, selectedOutput);
                                resolve(results);
                            } else {
                                // reject(false);

                                // Limpiamos
                                cleanCheckoutMetadata();

                                // Informamos
                                alert(cityPaqPostCodeNotFound);
                            }
                        }
                        // Parseamos para Oficinas
                        if (action == actionOffice) {
                            let offices = parsed_data.json_retorno.soapenvBody.localizadorRespuesta.arrayOficina.item;
                            selectedOutput = jQuery('#office_reference_' + id_carrier);
                            if (offices != undefined) {
                                // reindexamos solo 1 resultado
                                if (offices.unidad != undefined) {
                                    let office = offices;
                                    offices = [];
                                    offices.push(office);
                                }
                                offices.forEach(function (valor, indice, array) {
                                    let address_info = array[indice].direccion;
                                    let data = {
                                        reference: array[indice].unidad,
                                        address: address_info,
                                        city: array[indice].descLocalidad,
                                        cp: array[indice].cp,
                                        phone: array[indice].telefono,
                                        terminal: array[indice].nombre,
                                        schedule: {
                                            horarioLV: array[indice].horarioLV,
                                            horarioS: array[indice].horarioS,
                                            horarioF: array[indice].horarioF,
                                        },
                                        lat: parseFloat(array[indice].latitudETRS89),
                                        lng: parseFloat(array[indice].longitudETRS89),
                                        raw: array[indice],
                                    };
                                    results.push(data);
                                });
                            }

                            // Si tenemos resultados
                            if (results.length > 0) {
                                fillSelect(id_carrier, action, select, results, currentReference, selectedOutput);
                                resolve(results);
                            } else {
                                // reject(false);

                                // Limpiamos
                                cleanCheckoutMetadata();

                                // Informamos
                                alert(officePostCodeNotFound);
                            }
                        }
                    }
                },
                error: function (e) {
                    reject(false);
                },
            });
        });
    }

    // CARGA EL SELECT DEL CARRIER PASADO
    function fillSelect(id_carrier, action, select, locations, currentReference, selectedOutput = null) {
        let currentLocation;

        // Desactivamos evento change en el select
        select.off('change');

        // Eliminamos todos los options
        select.find('option').remove();

        // Si tenemos currentReference, buscamos el location
        if (currentReference) {
            currentLocation = locations.find((location) => location.reference == currentReference);
        }

        // Si no tenemos currentReference, seleccionamos el primero
        if (!currentReference || currentLocation == undefined) {
            currentReference = locations[0].reference;
            currentLocation = locations.find((location) => location.reference == currentReference);
            if (selectedOutput != null) {
                selectedOutput.val(currentReference);
            }
        }

        // Carrier data
        updateCarrierData(id_carrier, action, currentLocation);

        // insertamos según action
        if (action == actionCityPaq) {
            insertCityPaq(currentLocation, id_carrier);
        }
        if (action == actionOffice) {
            insertOffice(currentLocation, id_carrier);
        }

        locations.forEach(function (location) {
            if (currentReference == location.reference) {
                select.append('<option value=' + location.reference + ' selected>' + location.terminal + '</option>');
            } else {
                select.append('<option value=' + location.reference + '>' + location.terminal + '</option>');
            }
        });

        // Mostramos horario, direccion y mapa
        setScheduleAndMap(id_carrier, locations, currentReference, action);

        // Tras Cargar los options, activamos el evento change
        select.on('change', function () {
            // obtenemos el valor de la option seleccionada y asignamos al hidden del carrier
            if (selectedOutput != null) {
                selectedOutput.val(jQuery(this).val());

                // Configuramos horarios
                setScheduleAndMap(id_carrier, locations, jQuery(this).val(), action);

                let selected_location = locations.find((location) => location.reference == jQuery(this).val());

                // Guardamos en BD
                if (action == actionCityPaq) {
                    insertCityPaq(selected_location, id_carrier);
                }

                if (action == actionOffice) {
                    insertOffice(selected_location, id_carrier);
                }
            }
        });
    }

    // CONTROL MARCADORES EN MAPA
    function setGoogleMapsMarkers(carrierId, myLatLng, title) {
        if (defined_google_api_key == 1) {
            let carrierMap = googleMap[carrierId];
            if (carrierMap != undefined) {
                let map = googleMap[carrierId].map;
                let markers = googleMap[carrierId].markers;

                // Verificar si hay marcadores existentes para este transportista y eliminamos
                if (markers.length > 0) {
                    markers.forEach(function (marker) {
                        marker.setMap(null);
                    });
                }
                let marker = new google.maps.Marker({
                    position: myLatLng,
                    title: title,
                });

                marker.setMap(map);
                markers.push(marker);

                map.setCenter(myLatLng);
                map.setZoom(14);
            }
        }
    }

    // ACIONES HORARIOS Y MAPA
    function setScheduleAndMap(id_carrier, locations, reference, action) {
        let scheduleAndMap = jQuery('#scheduleAndMap_' + id_carrier);

        // buscamos en el array de locations el que tenga el reference seleccionado
        let locationSelected = locations.find((location) => location.reference == reference);

        // Mostramos horario, direccion y mapa
        scheduleAndMap.show();

        // Actualizamos horario, dirección y mapa del carrier para CityPaq
        if (action == actionCityPaq) {
            scheduleAndMap.find('.citypaq-address-info p.address').text(locationSelected.address);
            scheduleAndMap.find('.citypaq-address-info p.city').text(locationSelected.city);
            scheduleAndMap.find('.citypaq-address-info p.cp').text(locationSelected.cp);
            scheduleAndMap.find('.citypaq-terminal-info p').text(locationSelected.terminal);
            scheduleAndMap.find('.scheduleInfo p').text(locationSelected.schedule === '1' ? openingInfo : opening24hInfo);
        }

        // Actualizamos horario, dirección y mapa del carrier para Oficinas
        if (action == actionOffice) {
            scheduleAndMap.find('.office-address-info p.address').text(locationSelected.address);
            scheduleAndMap.find('.office-address-info p.city').text(locationSelected.city);
            scheduleAndMap.find('.office-address-info p.cp').text(locationSelected.cp);
            scheduleAndMap.find('.office-address-info p.phone').text(locationSelected.phone);
            scheduleAndMap.find('.office-terminal-info p').text(locationSelected.terminal);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleLV').text(locationSelected.schedule.horarioLV);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleS').text(locationSelected.schedule.horarioS);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleF').text(locationSelected.schedule.horarioF);
        }
        // map
        setGoogleMapsMarkers(id_carrier, { lat: locationSelected.lat, lng: locationSelected.lng }, locationSelected.terminal);
    }

    function updateCarrierData(id_carrier, action, selected_location) {
        carriersData[id_carrier] = {
            action: action,
            selected_location: selected_location,
        };
    }

    function insertCityPaq(selected_location, id_carrier) {
        // Carrier data
        updateCarrierData(id_carrier, actionCityPaq, selected_location);

        // Metadata
        setMetadataCheckout(parseInt(id_carrier), 'CityPaq', selected_location.reference, JSON.stringify(selected_location.raw));
    }

    function insertOffice(selected_location, id_carrier) {
        // Carrier data
        updateCarrierData(id_carrier, actionOffice, selected_location);

        // Metadata
        setMetadataCheckout(parseInt(id_carrier), 'Oficina', selected_location.reference, JSON.stringify(selected_location.raw));
    }

    function setMetadataCheckout(CarrierID, ReferenceType, SelectedReference, SelectedReferenceData) {
        let metadataArray = [
            { name: 'CarrierID', value: parseInt(CarrierID) },
            { name: 'ReferenceType', value: 'Oficina' },
            { name: 'SelectedReference', value: SelectedReference },
            { name: 'SelectedReferenceData', value: SelectedReferenceData },
        ];

        // Actualizamos cookie
        let checkoutMetadata = getCheckoutMetadata();
        metadataArray.forEach(function (metadata) {
            let inputName = metadata.name;
            let newValue = metadata.value;
            checkoutMetadata[inputName] = newValue;
        });
        checkoutMetadata = JSON.stringify(checkoutMetadata);
        checkoutMetadata = btoa(checkoutMetadata);
        document.cookie = 'correosoficial_checkout=' + checkoutMetadata + '; path=/';

        // Actualizamos formulario
        let checkoutForm = jQuery('form[name="checkout"]');

        metadataArray.forEach(function (metadata) {
            let inputName = metadata.name;
            let newValue = metadata.value;
            let inputExist = jQuery('[name="' + inputName + '"]');

            if (inputExist.length > 0) {
                inputExist.val(newValue);
            } else {
                // El input no existe, crear uno nuevo y agregarlo al formulario
                let newInput = jQuery('<input>').attr('type', 'hidden').attr('name', inputName).val(newValue);
                checkoutForm.append(newInput);
            }
        });
    }

    function cleanCheckoutMetadata() {
        let metadataArray = ['CarrierID', 'ReferenceType', 'SelectedReference', 'SelectedReferenceData'];

        // Limpiamos inputs formulario
        metadataArray.forEach(function (metadata) {
            let inputExist = jQuery('[name="' + metadata + '"]');
            if (inputExist.length > 0) {
                inputExist.remove();
            }
        });

        // Limpiamos cookie
        let checkoutMetadata = getCheckoutMetadata();
        metadataArray.forEach(function (metadata) {
            checkoutMetadata[metadata] = '';
        });

        checkoutMetadata = JSON.stringify(checkoutMetadata);
        checkoutMetadata = btoa(checkoutMetadata);
        document.cookie = 'correosoficial_checkout=' + checkoutMetadata + '; path=/';
    }

    // Obtenemos metadata del checkout
    function getCheckoutMetadata() {
        return false;
        let checkoutMetadata = getCookie('correosoficial_checkout');
        checkoutMetadata = atob(checkoutMetadata);
        checkoutMetadata = JSON.parse(checkoutMetadata);
        return checkoutMetadata;
    }

    // Función para obtener el valor de una cookie específica
    function getCookie(name) {
        const nameEQ = name + '=';
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(nameEQ) === 0) {
                return decodeURIComponent(cookie.substring(nameEQ.length));
            }
        }
        return null;
    }
});
