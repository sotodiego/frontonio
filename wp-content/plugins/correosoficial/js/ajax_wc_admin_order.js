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
jQuery(document).ready(function () {

    const mainContentLoaded = document.getElementById('correos_oficial_main_container');
    
    if (mainContentLoaded) {
        let pickupDone = false;
        //                            Buscador de Oficina - CityPaq                             //
        if (typeof google !== 'undefined') {
            /* MAPAS OFICINA Y CITYPAQ */
            var mapOfficeObj = new google.maps.Map(document.getElementById('mapOffice'), {
                center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                zoom: 13,
            });

            var mapCityPaqObj = new google.maps.Map(document.getElementById('mapCityPaq'), {
                center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                zoom: 13,
            });
        }

        /* FUNCIONALIDAD OFICINA */
        jQuery('#changeOffice').on('click', function (e) {
            jQuery('.change-container-office').toggle();
            jQuery('#mapOffice').hide();
        });

        jQuery('#searchOfficeButton').on('click', function (event) {
            jQuery('#office-list').find('option').remove();

            let postcode = jQuery('#input_cp_office').val();
            // Datos de las oficinas del webservice de localizador de oficinas
            let offices = '';

            jQuery.ajax({
                type: 'POST',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialCheckoutModuleFrontController',
                        action: 'SearchOfficeByPostalCode',
                        token: static_token,
                        postcode: postcode,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    let dir_office, loc_office, cp_office, cod_office;
                    offices = parsed_data.json_retorno.soapenvBody.localizadorRespuesta.arrayOficina.item;

                    if (offices != undefined) {
                        if (offices.length > 1) {
                            offices.forEach(function (valor, indice, array) {
                                if (indice == 0) {
                                    dir_office = offices[0].direccion;
                                    loc_office = offices[0].descLocalidad;
                                    cp_office = offices[0].cp;
                                    cod_office = offices[0].unidad;

                                    // Informamos los campos ocultos con la primera oficina cuando hacemos click con el botón Buscar (3)
                                    jQuery('#reference_code').val(cod_office);
                                    jQuery('#request_data').val(JSON.stringify(offices[0]));

                                    document.getElementById('dir-office').innerHTML = dir_office;
                                    document.getElementById('loc-office').innerHTML = loc_office;
                                    document.getElementById('cp-office').innerHTML = cp_office;
                                    document.getElementById('cod_office').value = cod_office;

                                    document.getElementById('office_address').value = dir_office;
                                    document.getElementById('office_city').value = loc_office;
                                    document.getElementById('office_cp').value = cp_office;

                                    const myLatLng = {
                                        lat: parseFloat(offices[0].latitudETRS89),
                                        lng: parseFloat(offices[0].longitudETRS89),
                                    };
                                    if (typeof google !== 'undefined') {
                                        let marker = new google.maps.Marker({
                                            position: myLatLng,
                                            title: offices[0].nombre,
                                            //icon: image
                                        });
                                        marker.setMap(mapOfficeObj);
                                        mapOfficeObj.setCenter(myLatLng);
                                        mapOfficeObj.setZoom(14);
                                    }
                                }

                                jQuery('#inputSelectOffices').append('<option value=' + indice + '>' + array[indice].nombre + '</option>');
                            });

                            // Acciones cuando cambia el selector de Oficinas
                            jQuery('#inputSelectOffices').on('change', function (e) {
                                // Se consigue el raw de la oficina escogida al cambiar el selector
                                let raw = JSON.stringify(offices[jQuery(this).val()]);

                                dir_office = offices[jQuery(this).val()].direccion;
                                loc_office = offices[jQuery(this).val()].descLocalidad;
                                cp_office = offices[jQuery(this).val()].cp;
                                cod_office = offices[jQuery(this).val()].unidad;

                                document.getElementById('dir-office').innerHTML = dir_office;
                                document.getElementById('loc-office').innerHTML = loc_office;
                                document.getElementById('cp-office').innerHTML = cp_office;
                                document.getElementById('cod_office').value = cod_office;

                                // Informamos los campos ocultos cambiar el selector de Oficinas (1)
                                jQuery('#reference_code').val(cod_office);
                                jQuery('#request_data').val(raw);

                                const myLatLng = {
                                    lat: parseFloat(offices[jQuery(this).val()].latitudETRS89),
                                    lng: parseFloat(offices[jQuery(this).val()].longitudETRS89),
                                };
                                if (typeof google !== 'undefined') {
                                    let marker = new google.maps.Marker({
                                        position: myLatLng,
                                        title: offices[jQuery(this).val()].nombre,
                                        //icon: image
                                    });
                                    marker.setMap(mapOfficeObj);
                                    mapOfficeObj.setCenter(myLatLng);
                                    mapOfficeObj.setZoom(14);
                                }
                            });

                            jQuery('#inputSelectOffices').show();
                            jQuery('#office-list').show();
                            jQuery('#no_offices_zip_message').addClass('hidden-block');
                        } else {
                            dir_office = offices.direccion;
                            loc_office = offices.descLocalidad;
                            cp_office = offices.cp;
                            cod_office = offices.unidad;

                            // Informamos los campos ocultos botón Buscar y solo hay una oficina (4)
                            jQuery('#reference_code').val(cod_office);
                            jQuery('#request_data').val(JSON.stringify(offices));

                            document.getElementById('dir-office').innerHTML = dir_office;
                            document.getElementById('loc-office').innerHTML = loc_office;
                            document.getElementById('cp-office').innerHTML = cp_office;
                            document.getElementById('cod_office').value = cod_office;

                            document.getElementById('office_address').value = dir_office;
                            document.getElementById('office_city').value = loc_office;
                            document.getElementById('office_cp').value = cp_office;

                            jQuery('#inputSelectOffices').append('<option value=0>' + offices.nombre + '</option>');

                            const myLatLng = {
                                lat: parseFloat(offices.latitudETRS89),
                                lng: parseFloat(offices.longitudETRS89),
                            };
                            if (typeof google !== 'undefined') {
                                let marker = new google.maps.Marker({
                                    position: myLatLng,
                                    title: offices.nombre,
                                    //icon: image
                                });
                                marker.setMap(mapOfficeObj);
                                mapOfficeObj.setCenter(myLatLng);
                                mapOfficeObj.setZoom(14);
                            }
                            jQuery('#inputSelectOffices').show();
                            jQuery('#office-list').show();
                        }

                        jQuery('.map-info-office').show();
                        jQuery('#mapOffice').show();
                        jQuery('#no_offices_zip_message').addClass('hidden-block');

                        jQuery('#selectOfficeButton').on('click', function (e) {
                            let offices_array;
                            jQuery('.change-container-office').hide();
                            document.getElementById('office_address').value = dir_office;
                            document.getElementById('office_city').value = loc_office;
                            document.getElementById('office_cp').value = cp_office;
                            document.getElementById('cod_office').value = cod_office;

                            // Informamos los campos ocultos cuando hacemos click con el botón Seleccionar Oficina (2)
                            jQuery('#reference_code').val(cod_office);

                            officeSelectorContent = jQuery('#inputSelectOffices');

                            // Comprobamos si el selector tiene uno o mas options.
                            if(officeSelectorContent.find('option').length > 1) {
                                offices_array = Object.values(offices);
                            } else {
                                offices_array = [offices]; 
                            }

                            if (offices_array.length == 1) { // Si ha devuelto solo una oficina
                                jQuery('#request_data').val(JSON.stringify(offices));
                            } else { // Convertimos a array para poder calcular los elementos
                                jQuery('#request_data').val(JSON.stringify(offices[jQuery('#inputSelectOffices').val()]));
                            }
                        });
                    } else {
                        jQuery('.map-info-office').hide();
                        jQuery('#mapOffice').hide();
                        jQuery('#inputSelectOffices').hide();
                        jQuery('#office-list').hide();
                        document.getElementById('office_address').value = '';
                        document.getElementById('office_city').value = '';
                        document.getElementById('office_cp').value = '';
                        document.getElementById('cod_office').value = '';
                        jQuery('#no_offices_zip_message').removeClass('hidden-block');
                    }
                },
            });
            event.preventDefault();
        });

        /**
     * Cambio de País en bloque Destinatario
     */
    jQuery('#customer_country').on('change', function(e){
        let destination = jQuery(this).val();
        
        // $cp_source, $cp_dest, $country_source, $country_dest
        let rand = 'rand=' + new Date().getTime();
        let ajaxtrue = '&ajax=true';

        let data = {
            action: 'RequireCustom',
            ajax: true,
            token: static_token,
            action: 'RequireCustom',
            cp_source: jQuery('#order_form input[name="sender_cp"]').val(),
            cp_dest: jQuery('#order_form input[name="customer_cp"]').val(), 
            country_source: jQuery('#order_form input[name="sender_country"]').val(),
            country_dest: jQuery('#order_form select[name="customer_country"]').val(),
        };

        jQuery.ajax({
            url: AdminOrderURL + rand + ajaxtrue,
            type: 'POST',
            data: data,
            cache: false,
            processData: true,
            success: function (data) {
                let parsed_data = JSON.parse(data);
                if (parsed_data['require_custom']  == true) {
                     /** @todo Pendiente lógica Requiere aduanas */
                    jQuery('#customs_correos_container_shipping').removeClass('hidden-block');
                    // Marcamos el pedido como "Requiere aduanas"
                    jQuery('#order_form input[name="require_customs_doc"]').val(1);
                } else {
                    jQuery('#customs_correos_container_shipping').addClass('hidden-block');
                }
            }
        });
    });

        /* FUNCIONALIDAD CITYPAQ */
        jQuery('#changeCityPaq').on('click', function (e) {
            jQuery('.change-container-citypaq').toggle();
            jQuery('#mapCityPaq').hide();
        });

        jQuery('#searchCityPaqButton').on('click', function (event) {
            jQuery('#citypaq-list').find('option').remove();

            let postcode = jQuery('#input_cp_citypaq').val();
            // Datos de las oficinas del webservice de localizador de oficinas
            let citypaqs = '';

            jQuery.ajax({
                type: 'POST',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialCheckoutModuleFrontController',
                        action: 'SearchCityPaqByPostalCode',
                        token: static_token,
                        postcode: postcode,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    let dir_citypaq, loc_citypaq, cp_citypaq, cod_homepaq;
                    citypaqs = parsed_data.json_retorno.soapenvBody.homePaqRespuesta1.listaHomePaq.homePaq;

                    if (citypaqs != undefined) {
                        if (citypaqs.length > 1) {
                            citypaqs.forEach(function (valor, indice, array) {
                                jQuery('#inputSelectCityPaqs').append('<option value=' + indice + '>' + array[indice].alias + '</option>');

                                if (indice == 0) {
                                    dir_citypaq = citypaqs[0].des_via + ' ' + citypaqs[0].direccion + ' ' + citypaqs[0].numero;
                                    loc_citypaq = citypaqs[0].desc_localidad;
                                    cp_citypaq = citypaqs[0].cod_postal;
                                    cod_homepaq = citypaqs[0].cod_homepaq;

                                    // Informamos los campos ocultos con el primer CityPaq cuando hacemos click con el botón Buscar (3)
                                    jQuery('#reference_code').val(cod_homepaq);
                                    jQuery('#request_data').val(JSON.stringify(citypaqs[0]));

                                    document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                                    document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                                    document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                                    document.getElementById('cod_homepaq').value = cod_homepaq;

                                    document.getElementById('citypaq_address').value = dir_citypaq;
                                    document.getElementById('citypaq_city').value = loc_citypaq;
                                    document.getElementById('citypaq_cp').value = cp_citypaq;
                                    document.getElementById('cod_homepaq').value = cod_homepaq;

                                    const myLatLng = {
                                        lat: parseFloat(citypaqs[0].latitudETRS89),
                                        lng: parseFloat(citypaqs[0].longitudETRS89),
                                    };
                                    if (typeof google !== 'undefined') {
                                        var marker = new google.maps.Marker({
                                            position: myLatLng,
                                            title: citypaqs[0].alias,
                                            //icon: image
                                        });
                                        marker.setMap(mapCityPaqObj);
                                        mapCityPaqObj.setCenter(myLatLng);
                                        mapCityPaqObj.setZoom(14);
                                    }
                                }
                            });

                            // Acciones cuando cambia el selector de CityPaq
                            jQuery('#inputSelectCityPaqs').on('change', function (e) {
                                // Se consigue el raw de la oficina escogida al cambiar el selector
                                let raw = JSON.stringify(citypaqs[jQuery(this).val()]);

                                dir_citypaq = citypaqs[jQuery(this).val()].des_via + ' ' + citypaqs[jQuery(this).val()].direccion + ' ' + citypaqs[jQuery(this).val()].numero;
                                loc_citypaq = citypaqs[jQuery(this).val()].desc_localidad;
                                cp_citypaq = citypaqs[jQuery(this).val()].cod_postal;
                                cod_homepaq = citypaqs[jQuery(this).val()].cod_homepaq;

                                // Informamos los campos ocultos cambiar el selector de CityPaqs (1)
                                jQuery('#reference_code').val(cod_homepaq);
                                jQuery('#request_data').val(raw);

                                document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                                document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                                document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                                document.getElementById('cod_homepaq').value = cod_homepaq;

                                const myLatLng = {
                                    lat: parseFloat(citypaqs[jQuery(this).val()].latitudETRS89),
                                    lng: parseFloat(citypaqs[jQuery(this).val()].longitudETRS89),
                                };
                                if (typeof google !== 'undefined') {
                                    var marker = new google.maps.Marker({
                                        position: myLatLng,
                                        title: citypaqs[jQuery(this).val()].alias,
                                        //icon: image
                                    });
                                    marker.setMap(mapCityPaqObj);
                                    mapCityPaqObj.setCenter(myLatLng);
                                    mapCityPaqObj.setZoom(14);
                                }
                            });

                            jQuery('#inputSelectCityPaqs').show();
                            jQuery('#citypaq-list').show();
                            jQuery('#no_citypaqs_zip_message').addClass('hidden-block');
                        } else {
                            dir_citypaq = citypaqs.des_via + ' ' + citypaqs.direccion + ' ' + citypaqs.numero;
                            loc_citypaq = citypaqs.desc_localidad;
                            cp_citypaq = citypaqs.cod_postal;
                            cod_homepaq = citypaqs.cod_homepaq;

                            // Informamos los campos ocultos botón Buscar y solo hay una CityPaq (4)
                            jQuery('#reference_code').val(cod_homepaq);
                            jQuery('#request_data').val(JSON.stringify(citypaqs));

                            document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                            document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                            document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                            document.getElementById('cod_homepaq').value = cod_homepaq;

                            document.getElementById('citypaq_address').value = dir_citypaq;
                            document.getElementById('citypaq_city').value = loc_citypaq;
                            document.getElementById('citypaq_cp').value = cp_citypaq;

                            jQuery('#inputSelectCityPaqs').append('<option value=0>' + citypaqs.alias + '</option>');

                            const myLatLng = {
                                lat: parseFloat(citypaqs.latitudETRS89),
                                lng: parseFloat(citypaqs.longitudETRS89),
                            };
                            if (typeof google !== 'undefined') {
                                let marker = new google.maps.Marker({
                                    position: myLatLng,
                                    title: citypaqs.alias,
                                    //icon: image
                                });
                                marker.setMap(mapCityPaqObj);
                                mapCityPaqObj.setCenter(myLatLng);
                                mapCityPaqObj.setZoom(14);
                            }
                            jQuery('#inputSelectCityPaqs').show();
                            jQuery('#citypaq-list').show();
                            jQuery('#no_citypaqs_zip_message').addClass('hidden-block');
                        }

                        jQuery('.map-info-citypaq').show();
                        jQuery('#mapCityPaq').show();
                        jQuery('#no_citypaqs_zip_message').addClass('hidden-block');

                        jQuery('#selectCityPaqButton').on('click', function (e) {
                            let citypaqs_array;

                            jQuery('.change-container-citypaq').hide();
                            document.getElementById('citypaq_address').value = dir_citypaq;
                            document.getElementById('citypaq_city').value = loc_citypaq;
                            document.getElementById('citypaq_cp').value = cp_citypaq;
                            document.getElementById('cod_homepaq').value = cod_homepaq;

                            // Informamos los campos ocultos cuando hacemos click con el botón Seleccionar CityPaq (2)
                            jQuery('#reference_code').val(cod_homepaq);
                            
                            cityPaqSelectorContent = jQuery('#inputSelectCityPaqs');

                            // Comprobamos si el selector tiene uno o mas options.
                            if(cityPaqSelectorContent.find('option').length > 1) {
                                citypaqs_array = Object.values(citypaqs);
                            } else {
                                citypaqs_array = [citypaqs]; 
                            }
                            
                            if (citypaqs_array.length == 1) { // Si ha devuelto solo un CityPaq
                                jQuery('#request_data').val(JSON.stringify(citypaqs));
                            } else { // Si ha devuelvo varios CityPaqs
                                jQuery('#request_data').val(JSON.stringify(citypaqs[jQuery('#inputSelectCityPaqs').val()]));
                            }
                        });
                    } else {
                        jQuery('.map-info-citypaq').hide();
                        jQuery('#mapCityPaq').hide();
                        jQuery('#inputSelectCityPaqs').hide();
                        jQuery('#citypaq-list').hide();
                        document.getElementById('citypaq_address').value = '';
                        document.getElementById('citypaq_city').value = '';
                        document.getElementById('citypaq_cp').value = '';
                        document.getElementById('cod_homepaq').value = '';
                        jQuery('#no_citypaqs_zip_message').removeClass('hidden-block');
                    }
                    event.preventDefault();
                },
            });
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                           PREREGISTRO DE ENVÍO EN PEDIDOS                            //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        /* Añadimos una nueva regla que compruebe que las dimensiones son 10x15x1 como mínimo,
        es decir, que sean mayores que 0, uno mayor que 10 y otro mayor de 15 */
        jQuery.validator.addMethod(
            'dimensionesValidadas',
            function (value, element) {
                // comprobamos que el carrier seleccionado se paq ligera o city paq, si no no validamos estos campos
                var carriers_default_dimensions = ['S0179', 'S0176', 'S0178'];
                if (!carriers_default_dimensions.includes(jQuery('#input_select_carrier').find('option:selected').val())) {
                    return true;
                }

                var container = element.closest('.container-bulto').id;
                var values = jQuery('#' + container)
                    .find('.validate-dimensions')
                    .map(function () {
                        return parseInt(jQuery(this).val());
                    })
                    .get();

                var mayorQue0 = values.every((num) => num > 0);
                var mayorQue10 = false;
                var mayorQue15 = false;

                for (var i = values.length - 1; i > -1; i--) {
                    if (values[i] >= 15 && mayorQue15 === false) {
                        mayorQue15 = true;
                        values.splice(i, 1);
                    }
                    if (values[i] >= 10 && mayorQue10 === false) {
                        mayorQue10 = true;
                        values.splice(i, 1);
                    }
                }

                return mayorQue0 && mayorQue10 && mayorQue15;
            },
            jQuery.validator.format(valuesDimensionDefault)
        );

        // Para añdir la regla de validación dinámicamente hacemos uso de esta class "validate-dimensions"
        jQuery.validator.addClassRules('validate-dimensions', { dimensionesValidadas: true });

        // Preregistro de envío
        jQuery('#order_form').validate({
            onkeyup: function (element) {
                jQuery(element).valid();
            },

            rules: {
                // DESTINATARIO
                customer_firstname: {
                    required: function (element) {
                        return jQuery('#order_form #customer_company').val() == '';
                    },
                    maxlength: 40,
                },
                customer_lastname: {
                    required: false,
                    maxlength: 40,
                },
                customer_company: {
                    required: function (element) {
                        return !(jQuery('#order_form #customer_firstname').val() != '');
                    },
                    maxlength: 40,
                },
                customer_contact: {
                    required: false,
                    maxlength: 40,
                },
                customer_address: {
                    required: true,
                    maxlength: 300,
                },
                customer_city: {
                    required: true,
                    maxlength: 40,
                },
                customer_cp: {
                    required: false,
                    maxlength: 8,
                },
                /*customer_phone: {
                    required: false,
                    number: true,
                },*/
                customer_email: {
                    required: false,
                    email: true,
                    maxlength: 50,
                },
                customer_dni: {
                    required: false,
                    maxlength: 15,
                    validate_nif_cif_nie: false,
                },
                order_reference: {
                    required: false,
                    maxlength: 20,
                },
                desc_reference_1: {
                    required: false,
                    maxlength: 100,
                },
                desc_reference_2: {
                    required: false,
                    maxlength: 100,
                },
                code_at: {
                    required: false,
                    maxlength: 30,
                },
                // VALORES AÑADIDOS
                cash_on_delivery_value: {
                    required: false,
                    number: true,
                    maxlength: 6,
                },
                insurance_value: {
                    required: false,
                    number: true,
                    maxlength: 100,
                },
                bank_acc_number: {
                    required: false,
                    maxlength: 34,
                    validate_acc_iban: false,
                },
                packageWeight_1: {
                    required: true,
                    number: true,
                },
                packageWeight_2: {
                    required: true,
                    number: true,
                },
                packageWeight_3: {
                    required: true,
                    number: true,
                },
                packageWeight_4: {
                    required: true,
                    number: true,
                },
                packageWeight_5: {
                    required: true,
                    number: true,
                },
                packageWeight_6: {
                    required: true,
                    number: true,
                },
                packageWeight_7: {
                    required: true,
                    number: true,
                },
                packageWeight_8: {
                    required: true,
                    number: true,
                },
                packageWeight_9: {
                    required: true,
                    number: true,
                },
                packageWeight_10: {
                    required: true,
                    number: true,
                },
                PickupDateRegister: {
                    required: function (element) {
                        const checkDateRegister = jQuery('#inputCheckSavePickup');
                        return checkDateRegister.checked;
                    },
                    date: true,
                },
            },
            messages: {
                // DESTINATARIO
                customer_firstname: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_lastname: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_company: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_contact: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_address: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 300 ' + characters,
                },
                customer_city: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_cp: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 8 ' + characters,
                },
                /* customer_phone: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                    maxlength: maxLengthMessage + ' 9 ' + characters,
                },*/
                customer_email: {
                    required: requiredCustomMessage,
                    email: invalidEmail,
                    maxlength: maxLengthMessage + ' 50 ' + characters,
                },
                customer_dni: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 15 ' + characters,
                    validate_nif_cif_nie: wrongDniCif,
                },
                order_reference: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 20 ' + characters,
                },
                desc_reference_1: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                desc_reference_2: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                code_at: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 30 ' + characters,
                },
                // VALORES AÑADIDOS
                cash_on_delivery_value: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                    maxlength: maxLengthMessage + ' 6 ' + characters,
                },
                insurance_value: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                bank_acc_number: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 34 ' + characters,
                    validate_acc_iban: wrongACCAndIBAN,
                },
                packageWeight_1: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_2: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_3: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_4: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_5: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_6: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_7: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_8: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_9: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_10: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
            },
            // Añadimos los grupos para que solo aparezca un mensaje por bloque de inputs
            groups: {
                valuesDimensionDefault1: 'packageLarge_1 packageWidth_1 packageHeight_1',
                valuesDimensionDefault2: 'packageLarge_2 packageWidth_2 packageHeight_2',
                valuesDimensionDefault3: 'packageLarge_3 packageWidth_3 packageHeight_3',
                valuesDimensionDefault4: 'packageLarge_4 packageWidth_4 packageHeight_4',
                valuesDimensionDefault5: 'packageLarge_5 packageWidth_5 packageHeight_5',
                valuesDimensionDefault6: 'packageLarge_6 packageWidth_6 packageHeight_6',
                valuesDimensionDefault7: 'packageLarge_7 packageWidth_7 packageHeight_7',
                valuesDimensionDefault8: 'packageLarge_8 packageWidth_8 packageHeight_8',
                valuesDimensionDefault9: 'packageLarge_9 packageWidth_9 packageHeight_9',
                valuesDimensionDefault10: 'packageLarge_10 packageWidth_10 packageHeight_10',
            },

        submitHandler: function () {
            jQuery('#processingOrderButtonMsg').removeClass('hidden-block');
            jQuery('#generateOrderButtonMsg').addClass('hidden-block');
            jQuery('#generateOrderButton').prop('disabled', true);

                let order_id = jQuery('#id_order_hidden').val();
                let order_form = getFormData('order_form');
                let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
                let company = selected_carrier.data('company');
                let delivery_mode = selected_carrier.data('carrier_type');
                let id_carrier = selected_carrier.data('id_carrier');
                let id_product = selected_carrier.data('id_product');
                let max_packages = selected_carrier.data('max_packages');
                let packages = jQuery('#correos-num-parcels').val();
                let id_sender = jQuery('#senderSelect').val();
                let added_values_cash_on_delivery = jQuery('#contrareembolsoCheckbox').is(':checked');
                let added_values_insurance = jQuery('#seguroCheckbox').is(':checked');
                let added_values_partial_delivery = jQuery('#partial_delivery').is(':checked');
                let added_values_delivery_saturday = jQuery('#delivery_saturday').is(':checked');
                let added_values_cash_on_delivery_iban = jQuery('#bank_acc_number').val();
                let added_values_cash_on_delivery_value = jQuery('#cash_on_delivery_value').val();
                let added_values_insurance_value = jQuery('#insurance_value').val();
                let at_code = jQuery('#code_at').val();
                let request_data = jQuery('#request_data').val();

                if(request_data) {
                    request_data = JSON.parse(request_data);
                }
                /* Recogemos los datos de todos los bultos */
                var info_bultos = {};
                jQuery('.container-bulto-info').each(function () {
                    var reference = jQuery(this).find('input[name^="packageRef"').val();
                    var weight = jQuery(this).find('input[name^="packageWeight"').val();
                    var large = jQuery(this).find('input[name^="packageLarge"').val();
                    var width = jQuery(this).find('input[name^="packageWidth"').val();
                    var height = jQuery(this).find('input[name^="packageHeight"').val();
                    var observations = jQuery(this).find('textarea[name^="deliveryRemarks"').val();

                    info_bultos[jQuery(this).attr('id').split('_')[1]] = { reference: reference, weight: weight, large: large, width: width, height: height, observations, observations };
                });
                info_bultos = JSON.stringify(info_bultos);

                let pickupCheck = jQuery('#inputCheckSavePickup');
                let printLablPickupCheck = jQuery('#inputCheckPrintLabel');

                let needPickup = 'N';
                let PickupDateRegister = '';
                let PickupFromRegister = '';
                let PickupToRegister = '';
                let needPrintLablPickup = 'N';
                let select_input_tamanio_paquete = '';

            if (jQuery(pickupCheck).is(':checked')) {
                needPickup = 'S';
                PickupDateRegister = jQuery('#PickupDateRegister').val();
                PickupFromRegister = jQuery('#PickupFromRegister').val();
                PickupToRegister = jQuery('#PickupToRegister').val();
                select_input_tamanio_paquete = jQuery('#input_tamanio_paquete').val();
                if (company == 'Correos' && select_input_tamanio_paquete == 0) {
                    jQuery('#error_register strong').html('Error:  Debe seleccionar el tamaño del paquete');
                    jQuery('#error_register').removeClass('hidden-block');
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#generateOrderButton').prop('disabled', false);
                    return;
                }
                let pickupDateComplete = new Date(PickupDateRegister);
                pickupDateComplete.setHours(23);
                pickupDateComplete.setMinutes(59);
                pickupDateComplete.setSeconds(59);
                if (pickupDateComplete < new Date() || (PickupFromRegister == '00:00:00' && PickupToRegister == '00:00:00')) {
                    jQuery('#error_register strong').html('Error:  Debe seleccionar fecha y rango de horas válidos en la recogida');
                    jQuery('#error_register').removeClass('hidden-block');
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#generateOrderButton').prop('disabled', false);
                    return;
                }
            }

                if (jQuery(printLablPickupCheck).is(':checked')) {
                    needPrintLablPickup = 'S';
                }

                if (packages <= max_packages) {
                    let modifiedOrderForm = {};

                    for (const key in order_form) {
                        if (order_form.hasOwnProperty(key)) {
                            const value = order_form[key];
                            let matches = RegExp(/^customs_desc\[(\d+)\]\[(\d+)\]$/).exec(key);
                            if (matches) {
                                let descNumber1 = matches[1];
                                let descNumber2 = matches[2];
                                if (!modifiedOrderForm.customs_desc) {
                                    modifiedOrderForm.customs_desc = {};
                                }
                                if (!modifiedOrderForm.customs_desc[descNumber1]) {
                                    modifiedOrderForm.customs_desc[descNumber1] = {};
                                }
                                modifiedOrderForm.customs_desc[descNumber1][descNumber2] = value;
                            } else {
                                modifiedOrderForm[key] = value;
                            }
                        }
                    }

                    if (modifiedOrderForm.customs_desc) {
                        modifiedOrderForm = {
                            ...modifiedOrderForm,
                            ...modifiedOrderForm.customs_desc,
                        };
                        delete modifiedOrderForm.customs_desc;
                    }

                    order_form = modifiedOrderForm;
                    order_form['AT_code'] = at_code;

                    jQuery.ajax({
                        type: 'post',
                        url: varsAjax.ajaxUrl,
                        data: {
                            action: 'correosOficialDispacher',
                            _nonce: varsAjax.nonce,
                            dispatcher: {
                                controller: 'CorreosOficialAdminOrderModuleFrontController',
                                action: 'generateOrder',
                                order_id: order_id,
                                id_carrier: id_carrier,
                                id_product: id_product,
                                id_sender: id_sender,
                                company: company,
                                delivery_mode: delivery_mode,
                                order_form: order_form,
                                needPickup: needPickup,
                                pickupDateRegister: PickupDateRegister,
                                pickupFromRegister: PickupFromRegister,
                                pickupToRegister: PickupToRegister,
                                needPrintLablPickup: needPrintLablPickup,
                                packetSize: select_input_tamanio_paquete,
                                added_values_cash_on_delivery: added_values_cash_on_delivery,
                                added_values_insurance: added_values_insurance,
                                added_values_partial_delivery: added_values_partial_delivery,
                                added_values_delivery_saturday: added_values_delivery_saturday,
                                added_values_cash_on_delivery_iban: added_values_cash_on_delivery_iban,
                                added_values_cash_on_delivery_value: added_values_cash_on_delivery_value,
                                added_values_insurance_value: added_values_insurance_value,
                                info_bultos: info_bultos,
                                request_data: request_data
                            },
                        },
                        cache: false,
                        processData: true,
                        success: function (data) {
                            if (isValidJson(data)) {
                                let parsed_data = JSON.parse(data);
                                let bultos = parsed_data.num_bultos_reg;
                                let pickupStatus = false;
                                loadOpacity();

                                if (parsed_data.codigoRetorno == '0') {
                                    disableForm('#container_sender');
                                    disableForm('#container_customer');
                                    disableForm('#container_shipping');
                                    disableForm('#added_values');

                                    jQuery('#order_exp_number_hidden').val(parsed_data.exp_number);

                                    // Verificar si la variable company
                                    if (company === 'Correos') {
                                        jQuery('#correos_provider').val('Correos');
                                    } else if (company === 'CEX') {
                                        jQuery('#correos_provider').val('CEX');
                                    }

                                    /* Se informa el nº de seguimiento en el metabox */
                                    tracking_shipping_number = parsed_data.bultos_reg[0].shipping_number;
                                    jQuery('#correos_tracking_number').val(tracking_shipping_number);

                                    /* Informamos link de seguimiento */
                                    let co_tracking_link = 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=' + tracking_shipping_number;
                                    jQuery('#correos_tracking_link').val(co_tracking_link);

                                    // Fecha de seguimiento
                                    jQuery('#correos_tracking_date').val(coGetToday());

                                    jQuery('#order-done-info').removeClass('hidden-block');
                                    jQuery('#input_format_etiqueta_container_reimpresion').removeClass('hidden-block');
                                    jQuery('.cancel-container').removeClass('hidden-block');
                                    jQuery('#cancelOrderButton').removeClass('hidden-block');
                                    jQuery('.send-container').addClass('hidden-block');

                                    if (company == 'Correos') {
                                        jQuery('#correos-options-pickup-container').removeClass('hidden-block');
                                        jQuery('#general-pickup-container').removeClass('hidden-block');
                                    } else {
                                        jQuery('#correos-options-pickup-container').addClass('hidden-block');
                                        //jQuery('#input_grabar_recogida_container').closest('.card-body').addClass('d-none');
                                    }

                                    //jQuery('#pickupBlock').addClass('d-none');

                                    jQuery('#error_register').addClass('hidden-block');
                                    jQuery('#success_register').addClass('hidden-block');

                                    managePrintLabel(bultos);

                                    setDatatableHistory();

                                    jQuery('#general-pickup-container').removeClass('hidden-block');

                                pickupStatus = true;
                            } else {
                                jQuery('#generateOrderButton').prop('disabled', false);
                                jQuery('#success_register').addClass('hidden-block');
                                jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                                jQuery('#error_register').removeClass('hidden-block');
                                jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                            }
                            if (needPickup == 'S' && parsed_data.codigoRetorno == '0' && pickupStatus) {
                                pickupDone = true;
                            };
                            
                            if (!pickupDone && parsed_data.codigoRetorno == '0') {
                                if (company === 'Correos') {
                                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                    jQuery('#data-pickup-container').addClass('hidden-block');
                                    jQuery('#input_grabar_recogida_container').addClass('hidden-block');
                                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                    jQuery('#save-pickup-container').removeClass('hidden-block');
                                } else if (company === 'CEX') {
                                    jQuery('#masive_pickup_container').addClass('hidden-block');
                                    jQuery('#inputCheckSavePickup').addClass('hidden-block');
                                    jQuery('#save-pickup-container').addClass('hidden-block');
                                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                    jQuery('#data-pickup-container').addClass('hidden-block');
                                    jQuery('#input_grabar_recogida_container').addClass('hidden-block');
                                }
                            } else if (parsed_data.codigoRetorno == '0') {
                                location.reload();
                            }

                                changeOrderStatusFromSelector(parsed_data.changeStatus);

                            if (needPickup == 'S' && parsed_data.codigoRetorno == 1111) {
                                setTimeout(function () {
                                    location.reload();
                                }, 5000);
                            }
                        } else {
                            console.error('[DEBUG MODE ON] Received data is not valid JSON:', data);
                            jQuery('#success_register').addClass('hidden-block');
                            jQuery('#error_register strong').html('[DEBUG MODE ON] Received data is not valid JSON');
                            jQuery('#error_register').removeClass('hidden-block');
                            jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                            jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                            jQuery('#generateOrderButton').prop('disabled', false);
                        }
                    },
                });
            } else if (id_carrier == 0) {
                jQuery('#error_register strong').html('Error:  Seleccione transportista antes de generar el envío');
                jQuery('#error_register').removeClass('hidden-block');
                jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                jQuery('#input_select_carrier').addClass('error');
                jQuery('#generateOrderButton').prop('disabled', false);
            } else {
                jQuery('#success_register').hide();
                jQuery('#error_register strong').html('Error bultos: El transportista seleccionado no permite envíos de varios bultos');
                jQuery('#error_register').removeClass('hidden-block');
                jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                jQuery('#generateOrderButton').prop('disabled', false);
            }
        },
    });

        function changeOrderStatusFromSelector(status) {
            if (status) {
                let selector = jQuery('#order_status');
                let valueToSelect = status;
                let selectionElement = selector.siblings('.select2-container').find('.select2-selection__rendered');

                selector.find('option').removeAttr('selected');
                selector.val(valueToSelect);
                selectionElement.text(selector.find('option:selected').text());
            }
        }

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                         CANCELACION DE PREREGISTRO DE ENVIO                          //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#cancelOrderButton').on('click', function (event) {
            let oficinaOrCityPaq = false;
            let selectedValue = jQuery('#input_select_carrier').val();

            if (selectedValue === 'S0176' || selectedValue === 'S0178' || selectedValue === '44' || selectedValue === 'S0236' || selectedValue === 'S0133') {
                oficinaOrCityPaq = true;
            }

            jQuery('#processingCancelOrderButtonMsg').removeClass('hidden-block');
            jQuery('#cancelOrderButtonMsg').addClass('hidden-block');

            // Eliminar tracking_number al cancelar un envío en Prestashop
            removeTrackingNumberInfo();

            let pickup_number = jQuery('#pickup_code_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');

            if (company == 'CEX' || company == 'Correos' && (pickup_number == '' || !pickup_number)) {

                if (oficinaOrCityPaq) { // WIP Infomar los campos 
                    showModalForOfficeAndCityPaq().then(() => {
                        cancelOrder();
                        
                    });
                } else {
                    cancelOrder();
                }
                // fin WIP
            } else {
                jQuery('#success_register').addClass('hidden-block');
                jQuery('#error_register strong').html('El envío tiene una recogida grabada. Para cancelar el envío, es necesario cancelar la recogida');
                jQuery('#error_register').removeClass('hidden-block');
                jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
                jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
            }

            event.preventDefault();
        });

        function cancelOrder() {
            let order_id = jQuery('#id_order_hidden').val();
            let lang = jQuery('#customer_country').val();
            let expedition_number = jQuery('#order_exp_number_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let id_carrier = selected_carrier.data('id_carrier');
            let company = selected_carrier.data('company');
            var id_sender = jQuery('#senderSelect').val();

        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'CorreosOficialAdminOrderModuleFrontController',
                    action: 'cancelOrder',
                    order_id: order_id,
                    id_carrier: id_carrier,
                    company: company,
                    lang: lang,
                    expedition_number: expedition_number,
                    id_sender: id_sender,
                },
            },
            cache: false,
            processData: true,
            success: function (data) {
                let parsed_data = JSON.parse(data);
                loadOpacity();
                if (parsed_data.codigoRetorno == '0') {
                    jQuery('#generateOrderButton').prop('disabled', false);
                    jQuery('#myModal').modal('hide');
                    enableForm('#container_customer');
                    enableForm('#container_shipping');
                    enableForm('#added_values');

                        jQuery('#senderSelect').attr('disabled', false);
                        jQuery('#client_code').attr('disabled', true);

                        jQuery('#order-done-info').addClass('hidden-block');

                        jQuery('.cancel-container').addClass('hidden-block');
                        jQuery('.send-container').removeClass('hidden-block');

                        jQuery('#save-pickup-container').addClass('hidden-block');
                        jQuery('#data-pickup-container').hide();

                        jQuery('#success_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register').removeClass('hidden-block');
                        jQuery('#error_register').addClass('hidden-block');
                        //jQuery('#pickupBlock').removeClass('d-none');

                        jQuery('#input_grabar_recogida_container').removeClass('hidden-block');                        
                        jQuery('#inputCheckSavePickup').removeClass('hidden-block');

                        if(company == 'CEX') {
                            jQuery('#inputCheckSavePickup').prop('checked', true);
                            jQuery('#masive_pickup_container').removeClass('hidden-block');
                        }

                        /* Limpieza de metabox: proveedor, tracking_number, tracking_link, tracking_date */
                        jQuery('#correos_tracking_number').val('');
                        jQuery('#correos_tracking_link').val('');
                        jQuery('#correos_tracking_date').val('');
                        jQuery('#correos_provider').val('');

                        cleanStatusDatatable();
                        changeOrderStatusFromSelector(parsed_data.changeStatus);
                    } else if (parsed_data.status_code == 401) {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    } else {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    }
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
                    jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
                },
            });
        }

        jQuery('body').on('click', '#myModalCancelButton', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();
            jQuery('#myModal').modal('hide');
            jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
            jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                   GENERAR RECOGIDA                                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#generate_pickup').on('click', function (event) {
            jQuery('#processingPickupButtonMsg').removeClass('hidden-block');
            jQuery('#pickupButtonMsg').addClass('hidden-block');
            jQuery('#generate_pickup').attr('disabled', true);

            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            let id_carrier = selected_carrier.data('id_carrier');

            let print_label = 0;

            if (!jQuery('#package_type').val() && company == 'Correos') {
                jQuery('#package_type').addClass('error');
                return;
            }

            if (jQuery('#print_label').is(':checked')) {
                print_label = 1;
            }

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'generatePickup',
                        mode_pickup: 'pickup',
                        order_id: jQuery('#id_order_hidden').val(),
                        bultos: jQuery('#correos-num-parcels').val(),
                        expedition_number: jQuery('#order_exp_number_hidden').val(),
                        order_reference: jQuery('#order_reference').val(),
                        pickup_date: jQuery('#pickup_date').val(),
                        sender_from_time: jQuery('#sender_from_time').val(),
                        sender_to_time: jQuery('#sender_to_time').val(),
                        sender_address: jQuery('#sender_address').val(),
                        sender_city: jQuery('#sender_city').val(),
                        sender_cp: jQuery('#sender_cp').val(),
                        sender_name: jQuery('#sender_name').val(),
                        sender_contact: jQuery('#sender_contact').val(),
                        sender_phone: jQuery('#sender_phone').val(),
                        sender_email: jQuery('#sender_email').val(),
                        sender_nif_cif: jQuery('#sender_nif_cif').val(),
                        sender_country: jQuery('#sender_country').val(),
                        producto: selected_carrier.val(),
                        package_type: jQuery('#package_type').val(),
                        print_label: print_label,
                        company: company,
                        id_carrier: id_carrier,
                        id_sender: jQuery('#senderSelect').val(),
                    },
                },
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    loadOpacity();
                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#pickup_code_hidden').val(parsed_data.codSolicitud);
                        location.reload();
                        return;
                    } else {
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#success_register').addClass('hidden-block');
                    }
                    jQuery('#processingPickupButtonMsg').addClass('hidden-block');
                    jQuery('#pickupButtonMsg').removeClass('hidden-block');
                    jQuery('#generate_pickup').attr('disabled', false);
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                  CANCELAR RECOGIDA                                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#cancel_pickup').on('click', function (event) {
            jQuery('#processingCancelPickupButtonMsg').removeClass('hidden-block');
            jQuery('#pickupCancelButtonMsg').addClass('hidden-block');

            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            let id_carrier = selected_carrier.data('id_carrier');
            let id_sender = jQuery('#senderSelect').val();

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'cancelPickup',
                        mode_pickup: 'pickup',
                        order_id: jQuery('#id_order_hidden').val(),
                        codSolicitud: jQuery('#pickup_code_hidden').val(),
                        company: company,
                        id_carrier: id_carrier,
                        id_sender: id_sender,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    loadOpacity();
                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#success_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register').removeClass('hidden-block');
                        jQuery('#error_register').addClass('hidden-block');

                        jQuery('#pickup_code_hidden').val('');

                        jQuery('#save-pickup-container').removeClass('hidden-block');
                        jQuery('#data-pickup-container').hide();
                    } else {
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#success_register').addClass('hidden-block');
                    }
                    jQuery('#processingCancelPickupButtonMsg').addClass('hidden-block');
                    jQuery('#pickupCancelButtonMsg').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                       IMPRIMIR ETIQUETA DE ENVÍO PREREGISTRADO                       //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ReimprimirEtiquetasButton').on('click', function (event) {
            let exp_number = jQuery('#order_exp_number_hidden').val();
            let id_order = jQuery('#id_order_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            let selectedTipoEtiquetaReimpresion = jQuery('#input_tipo_etiqueta_reimpresion').val();
            let selectedFormatEtiquetaReimpresion = jQuery('#input_format_etiqueta_reimpresion').val();
            let selectedPosicionEtiquetaReimpresion = jQuery('#input_pos_etiqueta_reimpresion').val();
            jQuery('#processingPrintLabelButtonMsg').removeClass('hidden-block');
            jQuery('#PrintLabelMessageButton').addClass('hidden-block');

            if(company == 'Correos' && selectedFormatEtiquetaReimpresion == '1' ) {
                jQuery('#processingPrintLabelButtonMsg').addClass('hidden-block');
                jQuery('#PrintLabelMessageButton').removeClass('hidden-block');
                showWrongLabelFormat();
                return;
            }

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'printLabel',
                        exp_number: exp_number,
                        selectedTipoEtiquetaReimpresion: selectedTipoEtiquetaReimpresion,
                        selectedFormatEtiquetaReimpresion: selectedFormatEtiquetaReimpresion,
                        selectedPosicionEtiquetaReimpresion: selectedPosicionEtiquetaReimpresion,
                        id_order: id_order,
                        company: company,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    if (parsed_data.status_code == '404') {
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                    } else {
                        printGeneratedLabels(parsed_data.filePath, varsAjax.path_to_module);
                    }
                    jQuery('#processingPrintLabelButtonMsg').addClass('hidden-block');
                    jQuery('#PrintLabelMessageButton').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                           IMPRIMIR DOCS ADUANA PREREGISTRO                           //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ImprimirCN23Button').on('click', function (event) {
            handleButtonClick('CN23');
        });
        
        jQuery('#ImprimirDUAButton').on('click', function (event) {
            handleButtonClick('DUA');
        });
        
        jQuery('#ImprimirDDPButton').on('click', function (event) {
            handleButtonClick('DDP');
        });
        
        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                 DEVOLUCION DE ENVIO                                  //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#generateReturnButton').on('click', function (event) {
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');

            if (company == 'Correos') {
                if (jQuery('#packageWeightReturn_1').val() == '' || jQuery('#packageAmountReturn_1').val() == '') {
                    jQuery('#packageWeightReturn_1').addClass('error');
                    jQuery('#packageAmountReturn_1').addClass('error');
                } else {
                    generateReturn();
                    jQuery('#ImprimirCN23Button2').removeClass('hidden-block');
                }
            } else {
                if (!jQuery('#packageWeightReturn_1').val()) {
                    jQuery('#packageWeightReturn_1').addClass('error');
                    jQuery('#packageAmountReturn_1').addClass('error');
                } else {
                    generateReturn();
                    jQuery('#ImprimirCN23Button2').addClass('hidden-block');
                }
            }
        });

        function generateReturn(event) {
            jQuery('#processingReturnButtonMsg').removeClass('hidden-block');
            jQuery('#generateReturnButtonMsg').addClass('hidden-block');

            let order_id = jQuery('#id_order_hidden').val();
            let order_form = getFormData('order_form');
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');
            let expedition_number = '';
            let parsed_data = '';
            let id_sender = jQuery('#senderSelect').val();
            let needPickup = company == 'CEX' ? 'S' : 'N';

        let modifiedOrderForm = {};
        jQuery('#generateReturnButton').prop('disabled', true);

            for (const key in order_form) {
                if (order_form.hasOwnProperty(key)) {
                    const value = order_form[key];
                    let matches = RegExp(/^customs_desc\[(\d+)\]\[(\d+)\]$/).exec(key);
                    if (matches) {
                        let descNumber1 = matches[1];
                        let descNumber2 = matches[2];
                        if (!modifiedOrderForm.customs_desc) {
                            modifiedOrderForm.customs_desc = {};
                        }
                        if (!modifiedOrderForm.customs_desc[descNumber1]) {
                            modifiedOrderForm.customs_desc[descNumber1] = {};
                        }
                        modifiedOrderForm.customs_desc[descNumber1][descNumber2] = value;
                    } else {
                        modifiedOrderForm[key] = value;
                    }
                }
            }

            if (modifiedOrderForm.customs_desc) {
                modifiedOrderForm = {
                    ...modifiedOrderForm,
                    ...modifiedOrderForm.customs_desc,
                };
                delete modifiedOrderForm.customs_desc;
            }

            order_form = modifiedOrderForm;
            order_form['needPickup'] = needPickup;
            order_form['pickup_date'] = jQuery('#return_pickup_date').val();
            order_form['sender_from_time'] = jQuery('#return_sender_from_time').val();
            order_form['sender_to_time'] = jQuery('#return_sender_to_time').val();

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'generateReturn',
                        order_id: order_id,
                        company: company,
                        order_form: order_form,
                        id_sender: id_sender,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    loadOpacity();
                    let mensajeRetorno = '';
                    if (parsed_data['errores'].length != 0) {
                        mensajeRetorno = '';
                        parsed_data['errores'].forEach(function (item) {
                            if (item.codigoRetorno == null) {
                                mensajeRetorno = 'ERROR 18002: ' + item.mensajeRetorno + '<br>';
                            } else {
                                mensajeRetorno = mensajeRetorno + 'Bulto ' + item.num_bulto + ': ' + item.mensajeRetorno + '<br>';
                            }
                        });
                        jQuery('#error_register_return strong').html(mensajeRetorno);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    } else {
                        jQuery('#error_register_return').addClass('hidden-block');
                        jQuery('#generate-return-container').addClass('hidden-block');
                        jQuery('#general-return-pickup-container').removeClass('hidden-block');
                        jQuery('#cancel-return-container').removeClass('hidden-block');
                        jQuery('.container-bultos-return').addClass('hidden-block');
                        jQuery('#return-status').text('Prerregistrado');
                        jQuery('#generateReturnButton').addClass('hidden-block');
                        jQuery('#cancelReturnButton').removeClass('hidden-block');
                        jQuery('#save-return-pickup-container').removeClass('hidden-block');
                        changeOrderStatusFromSelector(parsed_data['aciertos'][0].changeStatus);
                    }

                    if (parsed_data['aciertos'].length != 0) {
                        let return_codes = '';
                        parsed_data['aciertos'].forEach(function (item) {
                            return_codes = return_codes + '<span class="return-done-info-text">' + 'Bulto ' + item.num_bulto + ': ' + item.shipping_number + '<span><br>';
                            expedition_number = item.exp_number;
                        });
                        jQuery('.shipping-numbers-container-return').html(return_codes);
                        jQuery('#return-done-info').removeClass('hidden-block');
                        jQuery('#success_register_return').addClass('hidden-block');
                        jQuery('#return_exp_number_hidden').val(expedition_number);
                        jQuery('#pickup_return_code_hidden').val(parsed_data.codSolicitud);

                    if (company == 'CEX') {
                        location.reload();
                    }
                } else {
                    jQuery('#success_register_return').addClass('hidden-block');
                    jQuery('#generate-return-container').removeClass('hidden-block');
                    jQuery('#cancel-return-container').addClass('hidden-block');
                    jQuery('.container-bultos-return').removeClass('hidden-block');
                    jQuery('#error_register_return').removeClass('hidden-block');
                    jQuery('#cancelReturnButton').addClass('hidden-block');
                    jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                    jQuery('#generateReturnButtonMsg').prop('disabled', false);
                }

                jQuery('#processingReturnButtonMsg').addClass('hidden-block');
                jQuery('#generateReturnButtonMsg').removeClass('hidden-block');
                jQuery('#generateReturnButton').prop('disabled', false);
            },
            error: function (e) {
                parsed_data = JSON.parse(data);
                jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                jQuery('#generateReturnButton').prop('disabled', false);
            },
        });
    }

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                        IMPRIMIR ETIQUETAS   //   DEVOLUCIONES                        //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ReimprimirEtiquetasDevolucionButton').on('click', function (event) {
            let order_id = jQuery('#id_order_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');
            let selectedTipoEtiquetaReimpresionReturn = jQuery('#input_tipo_etiqueta_reimpresion_return').val();
            let selectedPosicionEtiquetaReimpresionReturn = jQuery('#input_pos_etiqueta_reimpresion_return').val();

            jQuery('#ProcessingMsgEtiquetasDevolucionButton').addClass('hidden-block');
            jQuery('#ProcessingReimprimirEtiquetasDevolucionButton').removeClass('hidden-block');

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'printLabelReturn',
                        order_id: order_id,
                        selectedTipoEtiquetaReimpresionReturn: selectedTipoEtiquetaReimpresionReturn,
                        selectedPosicionEtiquetaReimpresionReturn: selectedPosicionEtiquetaReimpresionReturn,
                        company: company,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    if (parsed_data.status_code == '404') {
                        jQuery('#error_register_return').removeClass('hidden-block');
                        jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                    } else {
                        printGeneratedLabels(parsed_data.filePath, varsAjax.path_to_module);
                    }
                    jQuery('#ProcessingMsgEtiquetasDevolucionButton').removeClass('hidden-block');
                    jQuery('#ProcessingReimprimirEtiquetasDevolucionButton').addClass('hidden-block');
                },
            });

            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                          GENERAR DOC ADUANERA DEVOLUCIONES                           //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        // Documentación aduanera devoluciones
        jQuery('.PrintGestionAduaneraLabels2').on('click', function (event) {
            let exp_number = jQuery('#id_order_hidden').val();
            let sender_name = jQuery('#sender_name').val();
            let sender_country = jQuery('#sender_country').val();

            jQuery('#ProcessingImprimirCN23Button2').removeClass('hidden-block');
            jQuery('#ProcessingMsgImprimirCN23Button2').addClass('hidden-block');

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'getCustomsDoc',
                        type: 'return',
                        exp_number: exp_number,
                        sender_name: sender_name,
                        sender_country: sender_country,
                        optionButton: event.target.id,
                        token: static_token,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    parsed_data = JSON.parse(data);
                    loadOpacity();

                    let files = parsed_data['files'];
                    let errors = parsed_data['errors'];
                    files.forEach((f) => {
                        printGeneratedLabels(f, co_path_to_module);
                    });

                    if (errors.length > 0) {
                        let error_msg = '';
                        errors.forEach(function (item) {
                            error_msg = error_msg + item.error_msg + '<br>';
                        });
                        jQuery('#success_register_return').hide();
                        jQuery('#error_register_return strong').html(error_msg);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    }

                    jQuery('#ProcessingImprimirCN23Button2').addClass('hidden-block');
                    jQuery('#ProcessingMsgImprimirCN23Button2').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                  ENVIAR DOCUMENTACION POR CORREO  //  DEVOLUCIONES                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#SendDocumentationByEmail').on('click', function (event) {
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');

            jQuery('#ProcessingSendDocumentationByEmailButton').removeClass('hidden-block');
            jQuery('#ProcessingMsgSendDocumentationByEmailButton').addClass('hidden-block');

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'sendEmail',
                        order_id: jQuery('#id_order_hidden').val(),
                        pickup_date: jQuery('#pickup_date').val(),
                        sender_from_time: jQuery('#sender_from_time').val(),
                        sender_address: jQuery('#sender_address').val(),
                        sender_city: jQuery('#sender_city').val(),
                        company: company,
                        customer_email: jQuery('#customer_email').val(),
                        default_sender_email: jQuery('#sender_email').val(),
                        customer_cp: jQuery('#customer_cp').val(),
                        customer_country: jQuery('#customer_country').val(),
                        sender_cp: jQuery('#sender_cp').val(),
                        sender_country: jQuery('#sender_country').val(),
                        return_code_1: jQuery('#hidden_return_code_1').val(),
                        return_code_2: jQuery('#hidden_return_code_2').val(),
                        return_code_3: jQuery('#hidden_return_code_3').val(),
                        return_code_4: jQuery('#hidden_return_code_4').val(),
                        return_code_5: jQuery('#hidden_return_code_5').val(),
                        return_code_6: jQuery('#hidden_return_code_6').val(),
                        return_code_7: jQuery('#hidden_return_code_7').val(),
                        return_code_8: jQuery('#hidden_return_code_8').val(),
                        return_code_9: jQuery('#hidden_return_code_9').val(),
                        return_code_10: jQuery('#hidden_return_code_10').val(),
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    loadOpacity();
                    jQuery('#ProcessingSendDocumentationByEmailButton').addClass('hidden-block');
                    jQuery('#ProcessingMsgSendDocumentationByEmailButton').removeClass('hidden-block');

                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#success_register_return_email strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register_return_email').removeClass('hidden-block');
                        jQuery('#error_register_return_email').addClass('hidden-block');
                        return data;
                    } else {
                        jQuery('#error_register_return_email strong').html('Error 22020: ' + parsed_data.mensajeRetorno);
                        jQuery('#success_register_return_email').addClass('hidden-block');
                        jQuery('#error_register_return_email').removeClass('hidden-block');
                    }
                },
                error: function (e) {
                    parsed_data = JSON.parse(data);
                    jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                },
            });
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                         CANCELAR RECOGIDA  //  DEVOLUCIONES                          //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#cancelReturnButton').on('click', function (event) {
            jQuery('#processingCancelReturnButtonMsg').removeClass('hidden-block');
            jQuery('#cancelReturnButtonMsg').addClass('hidden-block');

            let pickup_number_return = jQuery('#pickup_return_code_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');
        
            if (company == 'CEX' || company == 'Correos' && (pickup_number_return == '' || !pickup_number_return)) {
                let order_id = jQuery('#id_order_hidden').val();
                let lang = jQuery('#customer_country').val();
                let require_customs_doc = jQuery('#require_customs_doc').val();
                var id_sender = jQuery('#senderSelect').val();

                if (company == 'Correos' && require_customs_doc == 1) {
                    jQuery('.customs-correos-container-return').removeClass('hidden-block');
                } else {
                    jQuery('.customs-correos-container-return').addClass('hidden-block');
                }
                jQuery.ajax({
                    type: 'post',
                    url: varsAjax.ajaxUrl,
                    data: {
                        action: 'correosOficialDispacher',
                        _nonce: varsAjax.nonce,
                        dispatcher: {
                            controller: 'CorreosOficialAdminOrderModuleFrontController',
                            action: 'cancelReturn',
                            order_id: order_id,
                            company: company,
                            lang: lang,
                            expedition_number: '',
                            id_sender: id_sender,
                            pickup_number_return: pickup_number_return,
                        },
                    },
                    cache: false,
                    processData: true,
                    success: function (data) {
                        parsed_data = JSON.parse(data);
                        loadOpacity();
                        if (parsed_data.codigoRetorno == '0') {
                            jQuery('#success_register_return strong').html(parsed_data.mensajeRetorno);
                            jQuery('#success_register_return').removeClass('hidden-block');
                            jQuery('#error_register_return').addClass('hidden-block');

                        jQuery('#generate-return-container').removeClass('hidden-block');
                        jQuery('#cancel-return-container').addClass('hidden-block');
                        jQuery('.container-bultos-return').removeClass('hidden-block');
                        jQuery('#return-done-info').addClass('hidden-block');
                        jQuery('#save-return-pickup-container').addClass('hidden-block');

                        if (!require_customs_doc) {
                            jQuery('#customs_correos_container_return').addClass('hidden-block');
                        }

                        if (company !== 'CEX') {
                            jQuery('#save-return-pickup-container').addClass('hidden-block');
                        } else if(company == 'CEX') {
                            jQuery('#save-return-pickup-container').removeClass('hidden-block');
                        }
                        
                    } else {
                        jQuery('#success_register_return').addClass('hidden-block');
                        jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno[0]);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    }
                    jQuery('#processingCancelReturnButtonMsg').addClass('hidden-block');
                    jQuery('#cancelReturnButtonMsg').removeClass('hidden-block');
                    jQuery('#generateReturnButton').removeClass('hidden-block');
                    jQuery('#cancelReturnButton').addClass('hidden-block');
                },
            });
        } else {
            jQuery('#success_register_return').addClass('hidden-block');
            jQuery('#error_register_return strong').html('La devolución tiene una recogida grabada. Para cancelar la devolución, es necesario cancelar la recogida');
            jQuery('#error_register_return').removeClass('hidden-block');
            jQuery('#processingCancelReturnButtonMsg').addClass('hidden-block');
            jQuery('#cancelReturnButtonMsg').removeClass('hidden-block');
        }

            event.preventDefault();
        });

        jQuery('#cancel_return_pickup').on('click', function (event) {
            jQuery('#processingCancelReturnPickupButtonMsg').removeClass('hidden-block');
            jQuery('#returnPickupCancelButtonMsg').addClass('hidden-block');

            let selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier_return.data('company');
            let id_carrier = 0;
            let id_sender = jQuery('#senderSelect').val();

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'CorreosOficialAdminOrderModuleFrontController',
                        action: 'cancelPickup',
                        mode_pickup: 'return',
                        order_id: jQuery('#id_order_hidden').val(),
                        codSolicitud: jQuery('#pickup_return_code_hidden').val(),
                        company: company,
                        id_carrier: id_carrier,
                        id_sender: id_sender,
                    },
                },
                cache: false,
                processData: true,
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    loadOpacity();
                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#success_register_return strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register_return').removeClass('hidden-block');
                        jQuery('#error_register_return').addClass('hidden-block');

                        jQuery('#pickup_return_code_hidden').val('');

                        jQuery('#save-return-pickup-container').removeClass('hidden-block');
                        jQuery('#data-return-pickup-container').addClass('hidden-block');
                    } else {
                        jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register_return').removeClass('hidden-block');
                        jQuery('#success_register_return').addClass('hidden-block');
                    }
                    jQuery('#processingCancelReturnPickupButtonMsg').addClass('hidden-block');
                    jQuery('#returnPickupCancelButtonMsg').removeClass('hidden-block');
                },
            });

            event.preventDefault();
        });
    }
});

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                      AUXILIARES                                      //
//                                                                                      //
//--------------------------------------------------------------------------------------//

/**
* function para imprimir etiquetas
* @param {string} data nombre del archivo PDF
* @param {string} co_path_to_module ruta http del archivo PDF
*/
function printGeneratedLabels(data, co_path_to_module) {
    /**
     * @TODO Instanciar ruta local ya que la de woocommerce no la esta detectando.
     */
    let alternativeRoute = woocommerceVars.pluginsUrl + '/correosoficial';

    if (isHttps()) {
        alternativeRoute = alternativeRoute.replace('http://', 'https://');
    }

    jQuery.ajax({
        type: 'post',
        url: varsAjax.ajaxUrl, // Ruta al archivo PHP
        'Content-Type': 'application/pdf',
        'Content-Disposition': 'attachment; filename="label.pdf"',
        data: {
            action: 'correosOficialDispacher',
            _nonce: varsAjax.nonce,
            dispatcher: {
                controller: 'AdminCorreosOficialDownloadLabelsController',
                filename: data + '&path=pdftmp',
            },
        },
        success: function (filename) {
            let fileHref = alternativeRoute + '/pdftmp/' + filename;

            let anchor = document.createElement('a');
            anchor.setAttribute('download', filename);
            anchor.setAttribute('href', fileHref);
            anchor.click();

            setTimeout(function () {
                jQuery.ajax({
                    type: 'post',
                    url: varsAjax.ajaxUrl,
                    data: {
                        action: 'correosOficialDispacher',
                        _nonce: varsAjax.nonce,
                        dispatcher: {
                            controller: 'AdminCorreosOficialUtilitiesProcess',
                            action: 'deleteFiles',
                        },
                    },
                });
            }, 6500);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error('Error al iniciar la descarga: ', textStatus, errorThrown);
        },
    });
}

function isHttps(){
    return (document.location.protocol == 'https:');
}


/**
 * Ajusta la opacidad de los elementos 'error_register' y 'success_register'
 * a 1 para mantener su visibilidad, ignorando estilos externos.
 */
function loadOpacity() {
    jQuery('#error_register').css('opacity', '1');
    jQuery('#success_register').css('opacity', '1');
}

/**
 * Detecta que el dato que le pasamos sea un Json válido
 */
function isValidJson(data) {
    try {
        JSON.parse(data);
        return true;
    } catch (e) {
        return false;
    }
}

function handleButtonClick(type) {
    let button = jQuery(`#Imprimir${type}Button`);

    button.find('.spin').removeClass('hidden-block');
    button.find('.label-message').addClass('hidden-block');

    jQuery.ajax({
        type: 'POST',
        url: varsAjax.ajaxUrl,
        data: {
            action: 'correosOficialDispacher',
            _nonce: varsAjax.nonce,
            dispatcher: {
                controller: 'CorreosOficialAdminOrderModuleFrontController',
                action: 'getCustomsDoc',
                type: 'order',
                exp_number: jQuery('#order_exp_number_hidden').val(),
                customer_country: jQuery('#customer_country').val(),
                customer_name: jQuery('#customer_firstname').val(),
                customer_lastname: jQuery('#customer_lastname').val(),
                optionButton: `Imprimir${type}Button`,
                token: static_token,
            },
        },
        cache: false,
        success: function (data) {
            let parsed_data = JSON.parse(data);
            let files = parsed_data['files'];
            let errors = parsed_data['errors'];

            if (parsed_data.status_code == '404') {
                jQuery('#error_register').removeClass('hidden-block');
                jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
            } else {
                files.forEach((f) => {
                    printGeneratedLabels(f, co_path_to_module);
                });

                if (errors.length > 0) {
                    let error_msg = '';
                    errors.forEach(function (item) {
                        error_msg = error_msg + item.error_msg + '<br>';
                    });
                    jQuery('#success_register').hide();
                    jQuery('#error_register strong').html(error_msg);
                    jQuery('#error_register').removeClass('hidden-block');
                }
            }
            button.find('.spin').addClass('hidden-block');
            button.find('.label-message').removeClass('hidden-block');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            jQuery('#error_register').removeClass('hidden-block');
            jQuery('#error_register strong').html('An error occurred while processing your request.');
            button.find('.spin').addClass('hidden-block');
            button.find('.label-message').removeClass('hidden-block');
        },
    });

    event.preventDefault();
}

function showModalForOfficeAndCityPaq() {
    promiseModal = new Promise((resolve, reject) => {
        revolvePromise = resolve;
        let confirmationTitle = atention;
        let description = messageForCancelOfficeAndCityPaq;
        jQuery('#myModalTitle').html(confirmationTitle);
        jQuery('#myModalDescription p').html(description);
        jQuery('#myModalActionButton').html(cancelOrderStr);
        jQuery('#myModalCancelButton').html(cancelStr);
        jQuery('#myModalCancelButton').removeAttr('disabled').show();
        jQuery('#myModalActionButton').removeAttr('disabled').show();
        jQuery('#myModalActionButton').on('click', function () {
        revolvePromise(true);
    });

    jQuery('#myModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    
    jQuery('#myModal').modal('show');
    });

    return promiseModal;
}


function showWrongLabelFormat() {
    promiseModal = new Promise((resolve, reject) => {
        revolvePromise = resolve;
        let confirmationTitle = atention;
        let description = messageWrongLabelFormat;
        jQuery('#myModalTitle').html(confirmationTitle);
        jQuery('#myModalDescription p').html(description);
        jQuery('#myModalCancelButton').html(cancelStr);
        jQuery('#myModalActionButton').hide();

        jQuery('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
        });

        jQuery('#myModal').modal('show');
    });

    return promiseModal;
}