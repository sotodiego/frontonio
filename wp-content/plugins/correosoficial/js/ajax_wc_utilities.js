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
 * Version 3.0
 */
jQuery(document).ready(function () {
    //--------------------------------------------------------------------------------------//
    //                                                                                      //
    //                              GESTIÓN MASIVA DE PEDIDOS                               //
    //                                                                                      //
    //--------------------------------------------------------------------------------------//

    /**
     * Generacion de envio y recogida.
     * Generación de etiqueta desde panel "Gestion Masiva Pedidos" e Impresion.
     */
    jQuery('#generateOrdersButton').on('click', function () {
        jQuery('#processingOrdersButtonMsg').removeClass('hidden-block');
        jQuery('#generateOrdersButtonMsg').addClass('hidden-block');

        let msgErrors_package_size = '';
        let msgErrors_packages = '';

        if (jQuery('#inputCheckSavePickup').is(':checked')) {
            var selectedGrabarRecogida = 'S';
        } else {
            var selectedGrabarRecogida = 'N';
        }

        if (jQuery('#inputCheckPrintLabel').is(':checked')) {
            var selectedImprimirEtiqueta = 'S';
        } else {
            var selectedImprimirEtiqueta = 'N';
        }

        let selectedTamanioPaquete = jQuery('#input_tamanio_paquete').val();

        let selectedData = tableRegOrders.rows({ selected: true }).data().toArray();

        selectedData.forEach(function (valor, indice, array) {
            array[indice].mod_product = jQuery('#select_option_' + array[indice].id_order).val();
            array[indice].bultos = jQuery('#input_text_' + array[indice].id_order).val();
            array[indice].AT_code = jQuery('#AT_code' + array[indice].id_order).val();
            array[indice].sender_default = jQuery('#sender_option_' + array[indice].id_order).val();
            array[indice].sender_iso_code = jQuery('#sender_option_' + array[indice].id_order + ' option:selected').data('iso');
            array[indice].senders = null; // Limpiamos array de senders

            if (selectedGrabarRecogida == 'S') {
                if (selectedTamanioPaquete == 0 && array[indice].carrier_type == 'Correos') {
                    msgErrors_package_size = msgErrors_package_size + array[indice].id_order + ' Seleccione un tamaño de paquete para la recogida <br />';
                }
            }

            // Si se ha seleccionado un carrier del select -> comprobamos máximo de bultos
            if (array[indice].mod_product != null) {
                htmlObject = jQuery('#select_option_' + array[indice].id_order);
                let selected_carrier = htmlObject.find('option:selected');
                let max_packages_carrier_selected = selected_carrier.data('max-packages');

                // Cambiamos el producto por el seleccionado
                let mod_company = selected_carrier.data('company');
                array[indice].company = mod_company;

                array[indice].id_product = jQuery('#select_option_' + array[indice].id_order).val();
                if (Number.parseInt(array[indice].bultos) > Number.parseInt(max_packages_carrier_selected)) {
                    msgErrors_packages = msgErrors_packages + array[indice].id_order + ' ' + parcelMaxForthisProduct + ' ' + max_packages_carrier_selected + '<br />';
                }
            } else {
                if (array[indice].id_product != null) {
                    if (Number.parseInt(array[indice].bultos) > Number.parseInt(array[indice].max_packages)) {
                        msgErrors_packages = msgErrors_packages + array[indice].id_order + ' ' + parcelMaxForthisProduct + ' ' + array[indice].max_packages + '<br />';
                    }
                } else {
                    array[indice].id_product = array[indice].id_product_custom;
                    if (Number.parseInt(array[indice].bulto) > Number.parseInt(array[indice].max_packages_custom)) {
                        msgErrors_packages = msgErrors_packages + array[indice].id_order + ' ' + parcelMaxForthisProduct + ' ' + array[indice].max_packages_custom + '<br />';
                    }
                }
            }
        });

        if (msgErrors_package_size != '') {
            showModalInfoWindow(msgErrors_package_size);
        } else if (msgErrors_packages != '') {
            showModalInfoWindow(msgErrors_packages);
        } else {
            let PickupDateRegister = jQuery('#PickupDateRegister').val();
            let PickupFromRegister = jQuery('#PickupFromRegister').val();
            let PickupToRegister = jQuery('#PickupToRegister').val();

            if (selectedData.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: varsAjax.ajaxUrl,
                    data: {
                        _nonce: varsAjax.nonce,
                        action: 'correosOficialDispacher',
                        dispatcher: {
                            controller: 'AdminCorreosOficialUtilitiesProcess',
                            action: 'registerOrders',
                            selectedData: selectedData,
                            selectedGrabarRecogida: selectedGrabarRecogida,
                            selectedImprimirEtiqueta: selectedImprimirEtiqueta,
                            selectedTamanioPaquete: selectedTamanioPaquete,
                            PickupDateRegister: PickupDateRegister,
                            PickupFromRegister: PickupFromRegister,
                            PickupToRegister: PickupToRegister,
                        },
                    },
                    success: function (data) {
                        let parsed_data = JSON.parse(data);

                        jQuery('#reg_orders_errors_container').hide();
                        jQuery('#input_tipo_etiqueta_container_gestion').hide();
                        jQuery('#print_label_reg_container').hide();

                        if (parsed_data.status_code == '404' || parsed_data.status_code == '401') {
                            error = timeoutError(parsed_data);
                            table_errors_reg_orders.clear().draw();
                            table_errors_reg_orders.rows.add(error);
                            table_errors_reg_orders.columns.adjust().draw();
                            jQuery('#reg_orders_errors_container').show();
                        } else {
                            if (parsed_data['errors'].length != 0) {
                                let error = transformErrorData(parsed_data['errors']);
                                table_errors_reg_orders.clear().draw();
                                table_errors_reg_orders.rows.add(error);
                                table_errors_reg_orders.columns.adjust().draw();
                                jQuery('#reg_orders_errors_container').show();
                                if (parsed_data['done_orders'].length != 0) {
                                    jQuery('#input_tipo_etiqueta_container_gestion').show();
                                    jQuery('#print_label_reg_container').show();
                                } else {
                                    jQuery('#input_tipo_etiqueta_container_gestion').hide();
                                    jQuery('#print_label_reg_container').hide();
                                }
                            }
                        }

                        if (parsed_data['done_orders'].length != 0) {
                            jQuery('#input_tipo_etiqueta_container_gestion').show();
                            jQuery('#print_label_reg_container').show();

                            //ImprimirEtiquetasButton
                            jQuery('#printLabelsGenerated').on('click', function () {
                                let selectedDataReimpresion = parsed_data['done_orders'];
                                let selectedTipoEtiquetaReimpresion = jQuery('#input_tipo_etiqueta_gestion').val();
                                let selectedFormatEtiquetaReimpresion = jQuery('#input_format_etiqueta_gestion').val();
                                let selectedPosicionEtiquetaReimpresion = jQuery('#input_pos_etiqueta_gestion').val();

                                // Compatibilidad de etiquetas
                                if (!checkCEXLabelFormat(selectedDataReimpresion, selectedFormatEtiquetaReimpresion)) {
                                    return;
                                } else {
                                    jQuery('#ProcessingprintLabelsGeneratedButton').removeClass('hidden-block');
                                    jQuery('.label-message').addClass('hidden-block');
                                }

                                jQuery.ajax({
                                    type: 'post',
                                    url: varsAjax.ajaxUrl,
                                    data: {
                                        action: 'correosOficialDispacher',
                                        _nonce: varsAjax.nonce,
                                        dispatcher: {
                                            controller: 'AdminCorreosOficialUtilitiesProcess',
                                            action: 'printLabelsGenerated',
                                            selectedDataReimpresion: selectedDataReimpresion,
                                            selectedTipoEtiquetaReimpresion: selectedTipoEtiquetaReimpresion,
                                            selectedFormatEtiquetaReimpresion: selectedFormatEtiquetaReimpresion,
                                            selectedPosicionEtiquetaReimpresion: selectedPosicionEtiquetaReimpresion,
                                        },
                                    },
                                    success: function (data) {
                                        let printLabelData = JSON.parse(data);
                                        if (printLabelData.status_code == '404') {
                                            error = timeoutError(printLabelData);
                                            table_errors_reg_orders.clear().draw();
                                            table_errors_reg_orders.rows.add(error);
                                            table_errors_reg_orders.columns.adjust().draw();
                                            jQuery('#reg_orders_errors_container').show();
                                        } else {
                                            printGeneratedLabels(printLabelData.filePath, co_path_to_module);
                                        }

                                        jQuery('#ProcessingprintLabelsGeneratedButton').addClass('hidden-block');
                                        jQuery('.label-message').removeClass('hidden-block');
                                    },
                                });
                            });
                        }

                        jQuery('#processingOrdersButtonMsg').addClass('hidden-block');
                        jQuery('#generateOrdersButtonMsg').removeClass('hidden-block');

                        // Para refrescar la tabla hay que volver a llamar a ajax
                        // con la misma co_fecha seleccionada en los inputs de búsqueda
                        let data_search = {
                            FromDateOrdersReg: jQuery('#inputFromDateOrdersReg').val(),
                            ToDateOrdersReg: jQuery('#inputToDateOrdersReg').val(),
                        };

                        if (new Date(data_search.ToDateOrdersReg).getTime() < new Date(data_search.FromDateOrdersReg).getTime()) {
                            showModalInfoWindow(dateFromIsMinor);
                        } else {
                            jQuery('#GestionDataTable').DataTable().ajax.reload();
                            let el = jQuery('#table-select-all').get(0);
                            if (el && el.checked && 'indeterminate' in el) {
                                el.indeterminate = true;
                            }
                        }
                    },
                });
            } else {
                jQuery('#processingOrdersButtonMsg').addClass('hidden-block');
                jQuery('#generateOrdersButtonMsg').removeClass('hidden-block');
                showModalInfoWindow(mustSelectOneRecord);
            }
        }
    });

    //--------------------------------------------------------------------------------------//
    //                                                                                      //
    //                               REIMPRESION DE ETIQUETAS                               //
    //                                                                                      //
    //--------------------------------------------------------------------------------------//

    /**
     * Reimpresion de etiquetas desde el panel "Reimpresion de etiquetas"
     */
    jQuery('#ReimprimirEtiquetasButton').on('click', function (e) {
        let selectedDataReimpresion = tableEtiquetas.rows({ selected: true }).data().toArray();

        let selectedTipoEtiquetaReimpresion = jQuery('#input_tipo_etiqueta_reimpresion').val();
        let selectedFormatEtiquetaReimpresion = jQuery('#input_format_etiqueta_reimpresion').val();
        let selectedPosicionEtiquetaReimpresion = jQuery('#input_pos_etiqueta_reimpresion').val();

        // Compatibilidad de etiquetas
        if (!checkCEXLabelFormat(selectedDataReimpresion, selectedFormatEtiquetaReimpresion)) {
            return;
        }

        if (selectedDataReimpresion.length > 0) {
            jQuery('#ProcessingReimprimirEtiquetasButton').removeClass('hidden-block');
            jQuery('.label-message').addClass('hidden-block');

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'AdminCorreosOficialUtilitiesProcess',
                        action: 'printLabelsGenerated',
                        selectedDataReimpresion: selectedDataReimpresion,
                        selectedTipoEtiquetaReimpresion: selectedTipoEtiquetaReimpresion,
                        selectedFormatEtiquetaReimpresion: selectedFormatEtiquetaReimpresion,
                        selectedPosicionEtiquetaReimpresion: selectedPosicionEtiquetaReimpresion,
                    },
                },
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.status_code == '404') {
                        error = timeoutError(parsed_data);
                        table_errors_print_labels.clear().draw();
                        table_errors_print_labels.rows.add(error);
                        table_errors_print_labels.columns.adjust().draw();
                        jQuery('#print_label_errors_container').removeClass('hidden-block');
                    } else {
                        printGeneratedLabels(parsed_data.filePath, co_path_to_module);
                    }
                    jQuery('#ProcessingReimprimirEtiquetasButton').addClass('hidden-block');
                    jQuery('.label-message').removeClass('hidden-block');
                },
            });
        } else {
            showModalInfoWindow(mustSelectOneRecord);
        }
    });

    //--------------------------------------------------------------------------------------//
    //                                                                                      //
    //                              GENERACION RESUMEN PEDIDOS                              //
    //                                                                                      //
    //--------------------------------------------------------------------------------------//

    jQuery('#ImprimirResumenButton').on('click', function () {
        //tableResumen.button('.buttons-print').trigger();

        var selectedData = tableResumen.rows({ selected: true }).data().toArray();

        if (selectedData.length > 0) {
            jQuery('#ProcessingImprimirResumenButton').removeClass('hidden-block');
            jQuery('.label-message').addClass('hidden-block');

            jQuery.ajax({
                type: 'post',
                url: url_prefix_back + '?controller=AdminCorreosOficialUtilitiesProcess&action=generatePDFManifest',
                data: {
                    selectedData: selectedData,
                },
                success: function (data) {
                    parsed_data = JSON.parse(data);
                    printGeneratedLabels(parsed_data, co_path_to_module);
                    jQuery('#ResumenDataTable').DataTable().ajax.reload();
                    jQuery('#ProcessingImprimirResumenButton').addClass('hidden-block');
                    jQuery('.label-message').removeClass('hidden-block');
                },
            });
        } else {
            showModalInfoWindow(mustSelectOneRecord);
        }
    });

    //--------------------------------------------------------------------------------------//
    //                                                                                      //
    //                                      RECOGIDAS                                       //
    //                                                                                      //
    //--------------------------------------------------------------------------------------//

    // Seteamos co_fecha min y máxima para la recogida
    document.getElementById('PickupDate').value = co_ano + '-' + co_mes + '-' + co_dia;
    jQuery('#PickupDate').attr('min', co_ano + '-' + co_mes + '-' + co_dia);

    jQuery('#datatable_errors_pickups_container').hide();

    // Ordena Recogidas con los elementos seleccionados del datatable
    jQuery('#generatePickupsButton').on('click', function () {
        jQuery('#processingPickupsButtonMsg').removeClass('hidden-block');
        jQuery('#generatePickupsButtonMsg').addClass('hidden-block');

        jQuery('#success_pickup_msg').addClass('hidden-block');

        let msgErrors_pickup_package_size = '';

        if (jQuery('#inputPrintLabelPickups').is(':checked')) {
            var PrintLabelPickups = 'S';
        } else {
            var PrintLabelPickups = 'N';
        }

        let TamLabelPickups = jQuery('#inputTamLabelPickups').val();

        let selectedDataPickups = tablePickups.rows({ selected: true }).data().toArray();

        //Actualizo valor de los inputs tamaño paquete e imprimir etiqueta en selectedDataPickups
        selectedDataPickups.forEach(function (valor, indice, array) {
            array[indice].package_size = jQuery('#select_option_tam_recogidas_' + array[indice].id_order).val();
            array[indice].print_label = jQuery('#select_option_imp_recogidas_' + array[indice].id_order).val();

            if (array[indice].company == 'Correos') {
                if (TamLabelPickups == 0) {
                    if (array[indice].package_size == 0) {
                        msgErrors_pickup_package_size = msgErrors_pickup_package_size + order_string_translate + ' ' + array[indice].id_order + ': ' + size_pickup_string_translate + ' <br />';
                    }
                }
            }
        });

        if (msgErrors_pickup_package_size != '') {
            jQuery('#processingPickupsButtonMsg').addClass('hidden-block');
            jQuery('#generatePickupsButtonMsg').removeClass('hidden-block');
            showModalInfoWindow(msgErrors_pickup_package_size);
        } else {
            let PickupDate = jQuery('#PickupDate').val();
            let PickupFrom = jQuery('#PickupFrom').val();
            let PickupTo = jQuery('#PickupTo').val();

            if (selectedDataPickups.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: varsAjax.ajaxUrl,
                    data: {
                        action: 'correosOficialDispacher',
                        _nonce: varsAjax.nonce,
                        dispatcher: {
                            controller: 'AdminCorreosOficialUtilitiesProcess',
                            action: 'generatePickups',
                            selectedDataPickups: selectedDataPickups,
                            PrintLabelPickups: PrintLabelPickups,
                            TamLabelPickups: TamLabelPickups,
                            PickupDate: PickupDate,
                            PickupFrom: PickupFrom,
                            PickupTo: PickupTo,
                        },
                    },
                    success: function (data) {
                        var parsed_data = JSON.parse(data);

                        jQuery('#processingPickupsButtonMsg').addClass('hidden-block');
                        jQuery('#generatePickupsButtonMsg').removeClass('hidden-block');

                        //successDialog('Recogidas seleccionadas: ', 'Se han generado correctamente');

                        if (parsed_data['errors'].length != 0) {
                            table_errors_recogidas.clear().draw();
                            table_errors_recogidas.rows.add(parsed_data['errors']);
                            table_errors_recogidas.columns.adjust().draw();
                            jQuery('#datatable_errors_pickups_container').show();
                        } else {
                            if (parsed_data['done_pickups'].length != 0) {
                                if (parsed_data['errors'].length != 0) {
                                    jQuery('#datatable_errors_pickups_container').show();
                                } else {
                                    jQuery('#datatable_errors_pickups_container').hide();
                                }

                                jQuery('#success_pickup_msg').removeClass('hidden-block');

                                let data_search = {
                                    FromDatePickups: jQuery('#inputFromDatePickups').val(),
                                    ToDatePickups: jQuery('#inputToDatePickups').val(),
                                };

                                if (new Date(data_search.ToDatePickups).getTime() < new Date(data_search.FromDatePickups).getTime()) {
                                    showModalInfoWindow(dateFromIsMinor);
                                } else {
                                    jQuery.ajax({
                                        type: 'post',
                                        url: varsAjax.ajaxUrl,
                                        data: {
                                            action: 'correosOficialDispacher',
                                            _nonce: varsAjax.nonce,
                                            dispatcher: {
                                                controller: 'AdminCorreosOficialUtilitiesProcess',
                                                action: 'searchPickups',
                                                FromDatePickups: data_search['FromDatePickups'],
                                                ToDatePickups: data_search['ToDatePickups'],
                                            },
                                        },
                                        success: function (data) {
                                            jQuery('#card4').show();
                                            jQuery('#PickupDataTable').DataTable().ajax.reload();
                                            let el = jQuery('#table-select-all-pickups').get(0);
                                            if (el && el.checked && 'indeterminate' in el) {
                                                el.indeterminate = true;
                                            }
                                        },
                                        error: function (e) {
                                            alert('ERROR 17010: Error al imprimir etiquetas de las recogidas');
                                        },
                                    });
                                }
                                jQuery('#processingPickupsButtonMsg').addClass('hidden-block');
                                jQuery('#generatePickupsButtonMsg').removeClass('hidden-block');
                            }
                        }
                    },
                });
            } else {
                jQuery('#processingPickupsButtonMsg').addClass('hidden-block');
                jQuery('#generatePickupsButtonMsg').removeClass('hidden-block');
                showModalInfoWindow(mustSelectOneRecord);
            }
        }
    });

    //--------------------------------------------------------------------------------------//
    //                                                                                      //
    //                          GENERACION DOCUMENTACION ADUANERA                           //
    //                                                                                      //
    //--------------------------------------------------------------------------------------//

    jQuery('#ImprimirCN23Button').on('click', function (event) {
        handleButtonClickUtilities('CN23');
    });

    jQuery('#ImprimirDUAButton').on('click', function (event) {
        handleButtonClickUtilities('DUA');
    });

    jQuery('#ImprimirDDPButton').on('click', function (event) {
        handleButtonClickUtilities('DDP');
    });

    // Imprimimos los registros seleccionados del datatable de Generación documentación aduanera
});

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                        COMUN                                         //
//                                                                                      //
//--------------------------------------------------------------------------------------//

/**
 * function para imprimir etiquetas
 * @param {string} data nombre del archivo PDF
 * @param {string} co_path_to_module ruta http del archivo PDF
 */

function printGeneratedLabels(data, co_path_to_module) {
    let secureUrl = co_path_to_module;

    // Comprobar si la tienda WordPress utiliza SSL
    if (isHttps()) {
        secureUrl = secureUrl.replace('http://', 'https://');
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
            let fileHref = secureUrl + '/pdftmp/' + filename;

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

function isHttps() {
    return document.location.protocol == 'https:';
}

function timeoutError(data) {
    error = [];
    data.id_order = '';
    data.reference = data.status_code;
    data.error = data.mensajeRetorno;
    error.push(data);

    return error;
}

function transformErrorData(data) {
    const hasAnObject = data.some((item) => typeof item === 'object' && !Array.isArray(item));

    if (!hasAnObject) {
        return data;
    }

    let result = [];

    data.forEach((item) => {
        let transformedItem = {
            id_order: item.id_order,
            reference: item.reference,
            error: '',
        };

        if (typeof item.error === 'object' && !Array.isArray(item.error)) {
            transformedItem.error = Object.values(item.error)[0];
        } else {
            transformedItem.error = item.error;
        }

        result.push(transformedItem);
    });

    return result;
}

function handleButtonClickUtilities(type) {
    let button = jQuery(`#Imprimir${type}Button`);

    let selectedDataDocAduanera = tableDocAduanera.rows({ selected: true }).data().toArray();

    if (selectedDataDocAduanera.length > 0) {
        button.find('.spin').removeClass('hidden-block');
        button.find('.label-message').addClass('hidden-block');

        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCorreosOficialUtilitiesProcess',
                    action: 'getCustomsDoc',
                    selectedDataDocAduanera: selectedDataDocAduanera,
                    optionButton: `Imprimir${type}Button`,
                },
            },
            success: function (data) {
                let parsed_data = JSON.parse(data);
                let files = parsed_data['files'];
                if (parsed_data.status_code == '404') {
                    error = timeoutError(parsed_data);
                    table_errors_aduanera.clear().draw();
                    table_errors_aduanera.rows.add(error);
                    table_errors_aduanera.columns.adjust().draw();
                    jQuery('#datatable_results_aduanera_container').show();
                } else {
                    files.forEach((f) => {
                        printGeneratedLabels(f.filename, co_path_to_module);
                    });
                }

                if (parsed_data['errors'].length != 0) {
                    table_errors_aduanera.clear().draw();
                    table_errors_aduanera.rows.add(parsed_data['errors']);
                    table_errors_aduanera.columns.adjust().draw();
                    jQuery('#datatable_results_aduanera_container').show();
                }

                button.find('.spin').addClass('hidden-block');
                button.find('.label-message').removeClass('hidden-block');
            },
        });
    } else {
        showModalInfoWindow(mustSelectOneRecord);
    }
}
