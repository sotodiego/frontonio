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
    // Seteamos co_fecha actual y min y max para todos los buscadores
    jQuery('.search-utilities-input').val(co_ano + '-' + co_mes + '-' + co_dia);
    jQuery('.search-utilities-input').val(co_ano + '-' + co_mes + '-' + co_dia);
    jQuery('.search-utilities-input').attr('max', co_ano + '-' + co_mes + '-' + co_dia);
    jQuery('.search-utilities-input').attr('max', co_ano + '-' + co_mes + '-' + co_dia);

    jQuery.extend(true, jQuery.fn.dataTable.defaults, {
        language: {
            decimal: ',',
            thousands: '.',
            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
            infoPostFix: '',
            infoFiltered: '(filtrado de un total de _MAX_ registros)',
            loadingRecords: 'Cargando...',
            lengthMenu: 'Mostrar _MENU_ registros',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior',
            },
            processing: 'Procesando...',
            search: 'Buscar:',
            searchPlaceholder: 'Término de búsqueda',
            zeroRecords: 'No se encontraron resultados',
            emptyTable: 'Ningún dato disponible en esta tabla',
            aria: {
                sortAscending: ': Activar para ordenar la columna de manera ascendente',
                sortDescending: ': Activar para ordenar la columna de manera descendente',
            },
            //only works for built-in buttons, not for custom buttons
            buttons: {
                create: 'Nuevo',
                edit: 'Cambiar',
                remove: 'Borrar',
                copy: 'Copiar',
                csv: 'CSV',
                excel: 'Excel',
                pdf: 'PDF',
                print: 'Imprimir',
                colvis: 'Visibilidad columnas',
                collection: 'Colección',
                upload: 'Seleccione fichero....',
            },
            select: {
                rows: {
                    _: '%d filas seleccionadas',
                    0: 'Haga click en una fila para seleccionar',
                    1: 'una fila seleccionada',
                },
            },
        },
    });

    /////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// GESTIÓN MASIVA DE ENVÍOS /////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    // Seteamos co_fecha min y máxima para la recogida
    document.getElementById('PickupDateRegister').value = co_ano + '-' + co_mes + '-' + co_dia;
    jQuery('#PickupDateRegister').attr('min', co_ano + '-' + co_mes + '-' + co_dia);

    // Ocultamos contenedor de recogidas
    jQuery('#masive_pickup_container').hide();

    // Ocultamos impresión de etiquetas
    jQuery('#print_label_reg_container').hide();
    jQuery('#input_tipo_etiqueta_container_gestion').hide();
    jQuery('#input_pos_etiqueta_container_gestion').hide();

    // Ocultamos errores
    jQuery('#reg_orders_errors_container').hide();

    // Comprobamos el tipo etiqueta seleccionada
    labelsSelectActions(jQuery('#input_tipo_etiqueta_gestion').val(), 'gestion');

    // Escuchamos cambios de tipo
    jQuery('#input_tipo_etiqueta_gestion').on('change', function () {
        labelsSelectActions(this.value, 'gestion');
    });

    jQuery('#inputCheckSavePickup').on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#masive_pickup_container').show();
            jQuery('#inputCheckPrintLabel').prop('checked', false);
        } else {
            jQuery('#masive_pickup_container').hide();
            jQuery('#inputCheckPrintLabel').prop('checked', false);
        }
    });

    // Selecciona todas las filas
    jQuery('#table-select-all').on('click', function () {
        var rows = tableRegOrders.rows({ search: 'applied' }).nodes();
        jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
        if (jQuery(this).is(':checked')) {
            rows.rows('.selectable').select();
        } else {
            rows.rows().deselect();
        }
    });

    //Desmarca checkbox select all cuando eliminas la selección de algún select
    jQuery('#GestionDataTable tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = jQuery('#table-select-all').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
    });

    //Oculta campos
    jQuery('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
        jQuery(this).toggleClass('option-selected');
        var column = tableRegOrders.column(jQuery(this).attr('data-column'));
        column.visible(!column.visible());
    });

    jQuery('a.show-cols').on('click', function (e) {
        jQuery(this).toggleClass('option-selected');
        jQuery('.showButtonsContainer').toggleClass('hidden-block');
    });

    // OPCION PICKUP CHECKEADO SEGUN SI ES CORREOS/CEX

    let allcheckbox = false;

    jQuery('#table-select-all').on('change', function () {
        if (jQuery(this).is(':checked')) {
            allcheckbox = true;
            manageCheckbox(allcheckbox, false);
        } else {
            allcheckbox = false;
            hidePickup();
        }
    });

    jQuery('#GestionDataTable tbody').on('change', 'input[type="checkbox"]', function () {
        manageCheckbox(allcheckbox, false);
    });

    /* Controla que se cambia el transportista del selector en gestion masiva */
    jQuery(document).on('change', '.select_product', function () {
        let selectedOption = jQuery(this).find('option:selected');
        let dataCompany = selectedOption.data('company');
        manageCheckbox(false, dataCompany);
    });

    function showPickUp() {
        jQuery('#inputCheckSavePickup').prop('checked', true);
        jQuery('#masive_pickup_container').css('display', '');
    }

    function hidePickup() {
        jQuery('#inputCheckSavePickup').prop('checked', false);
        jQuery('#masive_pickup_container').css('display', 'none');
    }

    function manageCheckbox(allcheckbox, productChanged) {
        let companyIndex = 5; // Índice fijo para la columna "Transportista"
        let pickupFrom = jQuery('#PickupFromRegister').val();
        let pickupTo = jQuery('#PickupToRegister').val();
        let isValidTimeRange = pickupFrom > '00:00:00' && pickupTo > '00:00:00';

        let cexSelected = false;
        let correosSelected = false;

        jQuery('#GestionDataTable tbody input[type="checkbox"]').each(function () {
            let company = jQuery(this).closest('tr').find('td').eq(companyIndex).text().trim();

            if (jQuery(this).is(':checked') && company === 'CEX') {
                cexSelected = true;
            } else if (jQuery(this).is(':checked') && company === 'Correos') {
                correosSelected = true;
            }
        });

        if ((!allcheckbox && productChanged === 'CEX') || (cexSelected && !correosSelected)) {
            jQuery('#select_package').hide();
            jQuery('#print_label_on_pickup').hide();
        } else {
            jQuery('#select_package').show();
            jQuery('#print_label_on_pickup').show();
        }

        if (productChanged === 'CEX' || (cexSelected && isValidTimeRange)) {
            showPickUp();
        } else {
            hidePickup();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// GESTIÓN DE ETIQUETAS /////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    // Comprobamos el tipo seleccionado
    labelsSelectActions(jQuery('#input_tipo_etiqueta_reimpresion').val(), 'reimpresion');

    // Escuchamos cambios de tipo
    jQuery('#input_tipo_etiqueta_reimpresion').on('change', function () {
        labelsSelectActions(this.value, 'reimpresion');
    });

    // Selecciona todas las filas
    jQuery('#table-select-all-etiquetas').on('click', function () {
        var rows = tableEtiquetas.rows({ search: 'applied' }).nodes();
        jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
        if (jQuery(this).is(':checked')) {
            rows.rows().select();
        } else {
            rows.rows().deselect();
        }
    });

    //Desmarca checkbox select all cuando eliminas la selección de algún select
    jQuery('#EtiquetasDataTable tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = jQuery('#table-select-all-etiquetas').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
    });

    //Oculta campos
    jQuery('a.toggle-vis2').on('click', function (e) {
        e.preventDefault();
        jQuery(this).toggleClass('option-selected');
        var column = tableEtiquetas.column(jQuery(this).attr('data-column'));
        column.visible(!column.visible());
    });

    jQuery('a.show-cols2').on('click', function (e) {
        jQuery(this).toggleClass('option-selected');
        jQuery('.showButtonsContainer2').toggleClass('hidden-block');
    });

    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////// RECOGIDAS ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    // Selecciona todas las filas
    jQuery('#table-select-all-pickups').on('click', function () {
        var rows = tablePickups.rows({ search: 'applied' }).nodes();
        jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
        if (jQuery(this).is(':checked')) {
            rows.rows().select();
        } else {
            rows.rows().deselect();
        }
    });

    //Desmarca checkbox select all cuando eliminas la selección de algún select
    jQuery('#PickupDataTable tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = jQuery('#table-select-all-pickups').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
    });

    //Oculta campos
    jQuery('a.toggle-vis4').on('click', function (e) {
        e.preventDefault();
        jQuery(this).toggleClass('option-selected');
        var column = tablePickups.column(jQuery(this).attr('data-column'));
        column.visible(!column.visible());
    });

    jQuery('a.show-cols4').on('click', function (e) {
        jQuery(this).toggleClass('option-selected');
        jQuery('.showButtonsContainer4').toggleClass('hidden-block');
    });

    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////// GENERACIÓN DOCUMENTACIÓN ADUANERA ///////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    // Selecciona todas las filas
    jQuery('#table-select-all-doc-aduanera').on('click', function () {
        var rows = tableDocAduanera.rows({ search: 'applied' }).nodes();
        jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
        if (jQuery(this).is(':checked')) {
            rows.rows().select();
        } else {
            rows.rows().deselect();
        }
    });

    //Desmarca checkbox select all cuando eliminas la selección de algún select
    jQuery('#DocAduaneraDataTable tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = jQuery('#table-select-all-doc-aduanera').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
    });

    //Oculta campos
    jQuery('a.toggle-vis5').on('click', function (e) {
        e.preventDefault();
        jQuery(this).toggleClass('option-selected');
        var column = tableDocAduanera.column(jQuery(this).attr('data-column'));
        column.visible(!column.visible());
    });

    jQuery('a.show-cols5').on('click', function (e) {
        jQuery(this).toggleClass('option-selected');
        jQuery('.showButtonsContainer5').toggleClass('hidden-block');
    });

    /////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////        FUNCIONES                  ///////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    function labelsSelectActions(label_type, utility_name) {
        var label_pos_container = jQuery('#input_pos_etiqueta_container_' + utility_name);
        var label_format_container = jQuery('#input_format_etiqueta_container_' + utility_name);

        var label_pos_input = jQuery('#input_pos_etiqueta_' + utility_name);
        var label_format_input = jQuery('#input_format_etiqueta_' + utility_name);

        switch (label_type) {
            case '0': // Adhesiva
                label_pos_container.show();
                label_format_container.show();

                switch (label_format_input.val()) {
                    case '1': // 3/A4
                        loadLabelSelectPositions(label_pos_input, 3);
                        break;
                    default: // Estandar y 4/A4
                        loadLabelSelectPositions(label_pos_input, 4);
                        break;
                }

                label_format_input.on('change', function () {
                    switch (this.value) {
                        case '1': // 3/A4
                            loadLabelSelectPositions(label_pos_input, 3);
                            break;
                        default: // Estandar y 4/A4
                            loadLabelSelectPositions(label_pos_input, 4);
                            break;
                    }
                });

                break;

            case '1': // Medio Folio
                loadLabelSelectPositions(label_pos_input, 2);
                label_pos_container.show();

                break;

            case '2': // Térmica
                label_pos_container.hide();

                // Reset input formarto
                label_format_input.val(0);
                label_format_container.hide();

                break;

            default:
                break;
        }
    }

    // Funcion que nos permite rellenar dinámicamente el select de posiciones de etiquetas
    function loadLabelSelectPositions(element, positions) {
        let select_input = jQuery(element);

        select_input.empty();
        for (let i = 1; i <= positions; i++) {
            select_input.append('<option value="' + i + '">' + i + '</option>');
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////        COMUN                      ///////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    var tab = 'gestion-tab';

    jQuery('#GestionMasivaPedidosSearchButton').click();

    jQuery('#gestion-tab').on('click', function () {
        jQuery('#GestionMasivaPedidosSearchButton').click();
        tab = 'gestion-tab';
    });

    jQuery('#reimpresion-tab').on('click', function () {
        jQuery('#EtiquetasSearchButton').click();
        tab = 'reimpresion-tab';
    });

    jQuery('#generacion-tab').on('click', function () {
        jQuery('#SummarySearchButton').click();
        tab = 'generacion-tab';
    });

    jQuery('#pickups-tab').on('click', function () {
        jQuery('#PickupsSearchButton').click();
        tab = 'pickups-tab';
    });

    jQuery('#documentacion-tab').on('click', function () {
        jQuery('#DocAduaneraSearchButton').click();
        tab = 'documentacion-tab';
    });

    function setTomorrowsDate(inputField) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        var month = tomorrow.getMonth() + 1;
        var day = tomorrow.getDate();
        var year = tomorrow.getFullYear();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;

        var calendar_day = year + '-' + month + '-' + day;
        document.getElementById(inputField).setAttribute('min', calendar_day);
        document.getElementById(inputField).value = year + '-' + month + '-' + day;
    }

    function setTodayDate(inputField) {
        const today = new Date();

        var month = today.getMonth() + 1;
        var day = today.getDate();
        var year = today.getFullYear();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;

        var calendar_day = year + '-' + month + '-' + day;
        document.getElementById(inputField).setAttribute('min', calendar_day);
        document.getElementById(inputField).value = year + '-' + month + '-' + day;
    }

    function set30DaysAfterDate(inputField) {
        const today = new Date();
        const dayAfter30 = new Date(today);
        dayAfter30.setDate(dayAfter30.getDate() + 30);

        var month = dayAfter30.getMonth() + 1;
        var day = dayAfter30.getDate();
        var year = dayAfter30.getFullYear();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;

        var calendar_day = year + '-' + month + '-' + day;
        document.getElementById(inputField).setAttribute('max', calendar_day);
    }
});

// ---- Se instancian las tablas de Utilidades ----------------------------------

let tableRegOrders = '';
let tableEtiquetas = '';
let tableResumen = '';
let tablePickups = '';
let tableDocAduanera = '';

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                              GESTIÓN MASIVA DE PEDIDOS                               //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// ---- DATATABLE GESTIÓN DE ENVÍOS ------------------------------------------------------

jQuery('#GestionMasivaPedidosSearchButton').on('click', function () {
    loadGestionMasivaTable();
});

// ---- DATATABLE ERRORES GESTIÓN DE ENVÍOS ----------------------------------------------

let table_errors_reg_orders = jQuery('#datatableErrorsRegOrders').DataTable({
    paging: false,
    info: false,
    searching: false,
    orderable: true,
    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-4"i><"col-sm-6"p>>',
    buttons: ['csv', 'excel', 'pdf'],
    columns: [{ data: 'id_order' }, { data: 'reference' }, { data: 'error' }],
    order: [[0, 'desc']],
});

let inputs_tableRegOrders = {
    exportOptions: {
        format: {
            body: function (data, row, column, node) {
                let htmlObject = jQuery(data);
                if (column == 9) {
                    return htmlObject.val();
                } else if (column == 1) {
                    return htmlObject.data('value');
                } else {
                    return data;
                }
            },
        },
    },
};

/**
 *  Función que solo permite imprimir etiquetas 3/A4 a CEX
 */
function checkCEXLabelFormat(orders, format_selected) {
    // Si queremos imprimir formato 3/A4 solo se permite para CEX
    if (format_selected == '1') {
        let allHaveCEX = true;

        jQuery.each(orders, function (index, order) {
            if (order.company !== 'CEX') {
                allHaveCEX = false;
                return false;
            }
        });

        if (!allHaveCEX) {
            showModalErrorWindow('El Formato seleccionado solo está permitido para CEX');
            return false;
        }
    }

    return true;
}


//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                 GESTION DE ETIQUETAS                                 //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// ---- DATATABLE GESTIÓN DE ETIQUETAS ---------------------------------------------------


jQuery('#EtiquetasSearchButton').on('click', function() {
    loadEtiquetasTable();
});

// ---- DATATABLE ERRORES REIMPRESION ETIQUETAS ------------------------------

var table_errors_print_labels = jQuery('#datatablePrintLabels').DataTable({
    paging: false,
    info: false,
    searching: false,
    orderable: true,
    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-4"i><"col-sm-6"p>>',
    buttons: ['csv', 'excel', 'pdf'],
    columns: [{ data: 'id_order' }, { data: 'reference' }, { data: 'error' }],
    order: [[0, 'desc']],
});

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                              RESUMEN DE PEDIDOS                                      //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// 11-09-2024
/*var button_print = ''; 


button_print = new jQuery.fn.dataTable.Buttons(tableResumen, {
    buttons: ['print'],
})
    .container()
    .appendTo(jQuery('#button_print'));*/


// ---- DATATABLE RESUMEN PEDIDOS --------------------------------------------------------

jQuery('#SummarySearchButton').on('click', function () {
    loadSummaryTable();
});

// ---- DATATABLE ERRORES RESUMEN PEDIDOS ------------------------------

var table_errors_printlabel = jQuery('#datatableSummaryPrint').DataTable({
    paging: false,
    info: false,
    searching: false,
    orderable: true,
    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-4"i><"col-sm-6"p>>',
    buttons: ['csv', 'excel', 'pdf'],
    columns: [{ data: 'id_order' }, { data: 'reference' }, { data: 'error' }],
    order: [[0, 'desc']],
});

/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// GENERACIÓN RESUMEN PEDIDOS //////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
        
// Selecciona todas las filas
jQuery('#table-select-all-resumen').on('click', function () {
    var rows = tableResumen.rows({ search: 'applied' }).nodes();
    jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
    if (jQuery(this).is(':checked')) {
        rows.rows().select();
    } else {
        rows.rows().deselect();
    }
});

//Oculta campos
jQuery('a.toggle-vis3').on('click', function (e) {
    e.preventDefault();
    jQuery(this).toggleClass('option-selected');
    var column = tableResumen.column(jQuery(this).attr('data-column'));
    column.visible(!column.visible());
});

jQuery('a.show-cols3').on('click', function (e) {
    jQuery(this).toggleClass('option-selected');
    jQuery('.showButtonsContainer3').toggleClass('hidden-block');
});

// ---- Desmarca checkbox select all cuando eliminas la selección de algún select --------

jQuery('#ResumenDataTable tbody').on('change', 'input[type="checkbox"]', function () {
    if (!this.checked) {
        let el = jQuery('#table-select-all-resumen').get(0);
        if (el && el.checked && 'indeterminate' in el) {
            el.indeterminate = true;
        }
    }
});

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                      RECOGIDAS                                       //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// ---- DATATABLE RECOGIDAS --------------------------------------------------------------

jQuery('#PickupsSearchButton').on('click', function() {
    loadPickupTable();
});

// ---- DATATABLE ERRORES RECOGIDAS ------------------------------------------------------

var table_errors_recogidas = jQuery('#datatableResultsRecogidas').DataTable({
    paging: false,
    info: false,
    searching: false,
    orderable: true,
    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
    buttons: ['csv', 'excel', 'pdf'],
    columns: [{ data: 'id_order' }, { data: 'reference' }, { data: 'error' }],
    order: [[0, 'desc']],
});

// ---- INPUTS TABLE PICKUPS -------------------------------------------------------------

var inputs_tablePickups = {
    exportOptions: {
        format: {
            body: function (data, row, column, node) {
                if (column == 8) {
                    var htmlObject = jQuery(data);
                    switch (htmlObject.val()) {
                        case '10':
                            return 'Sobres';
                        case '20':
                            return 'Pequeño (caja zapatos)';
                        case '30':
                            return 'Mediano (caja folios)';
                        case '40':
                            return 'Grande (caja 80x80x80cm)';
                        case '50':
                            return 'Muy grande (mayor que caja 80x80x80cm)';
                        case '60':
                            return 'Palet';
                    }
                } else if (column == 9) {
                    var htmlObject = jQuery(data);
                    if (htmlObject.val() == '0') {
                        return 'NO';
                    } else if (htmlObject.val() == '1') {
                        return 'SI';
                    }
                } else if (column == 10) {
                    if (data == '<button type="button" class="btn btn-danger btn-sm" disabled>NO</button>') {
                        return 'NO';
                    } else {
                        return data;
                    }
                } else {
                    return data;
                }
            },
        },
    },
};

var select_active_products = getActiveProducts();

// ---- OBTENER PRODUCTOS ACTIVOS DE DATATABLE -------------------------------------------

function getActiveProducts() {
    jQuery.ajax({
        type: 'post',
        url: varsAjax.ajaxUrl,
        data: {
            _nonce: varsAjax.nonce,
            action: 'correosOficialDispacher',
            dispatcher: {
                controller: 'AdminCorreosOficialUtilitiesProcess',
                action: 'getActiveProducts',
            },
        },
        success: function (data) {
            select_active_products = JSON.parse(data);
        },
    });
}

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                DOCUMENTACION ADUANERA                                //
//                                                                                      //
//--------------------------------------------------------------------------------------//

//DATATABLE DOCUMENTACIÓN ADUANERA

jQuery('#DocAduaneraSearchButton').on('click', function () {
    loadDocAduaneraTable();
});


// ---- DATATABLE ERRORES GENERACIÓN DOCUMENTACIÓN ADUANERA ------------------------------

var table_errors_aduanera = jQuery('#datatableResultsAduanera').DataTable({
    paging: false,
    info: false,
    searching: false,
    orderable: true,
    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
    buttons: ['csv', 'excel', 'pdf'],
    columns: [{ data: 'id_order' }, { data: 'reference' }, { data: 'error' }],
    order: [[0, 'desc']],
});

jQuery('#datatable_results_aduanera_container').hide();

// Función para comprobar los productos que aparecen según el remitente
function checkOrderProductsAllowed(productsSelect, delivery_iso_code, sender_iso_code, office = null) {
    // Recorremos options para habilitar/deshabilitar según condiciones
    select_carriers = jQuery('option', productsSelect);
    select_carriers.each(function () {
        // Internacional -> Ocultamos productos nacionales
        if (!delivery_iso_code.includes('ES') && !delivery_iso_code.includes('AD') && !delivery_iso_code.includes('PT')) {
            if (jQuery(this).data('product-type') != 'international') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
        // Si el destino no es Portugal -> Ocultamos Portugal Óptica CEX
        if (!delivery_iso_code.includes('PT')) {
            if (jQuery(this).data('product-type') == 'portugal') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
        // Nacional -> Ocultamos productos internacionales
        if (delivery_iso_code.includes('ES') || delivery_iso_code.includes('AD') || delivery_iso_code.includes('PT')) {
            if (jQuery(this).data('product-type') == 'international') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
        // Origen Portugal y Correos
        if (sender_iso_code.includes('PT')) {
            if (jQuery(this).data('company') == 'Correos') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
        // Origen Andorra y CEX
        if (sender_iso_code.includes('AD')) {
            if (jQuery(this).data('company') == 'CEX') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
        // Si no es Oficina/Citypaq
        if (office == null) {
            if (jQuery(this).data('product-type') == 'office' || jQuery(this).data('product-type') == 'citypaq') {
                if (this.value != 0) {
                    jQuery(this).remove();
                }
            }
        }
    });
}

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                        COMUN                                         //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// ---- Truncate a string ----------------------------------------------------------------

function strtrunc(str, max, add) {
    add = add || '...';
    return typeof str === 'string' && str.length > max ? str.substring(0, max) + add : str;
}

// ---- Auto Scroll ----------------------------------------------------------------------

function autoScrollButtons(divName) {
    jQuery('html, body').animate(
        {
            scrollTop: jQuery(divName).offset().top,
        },
        550
    );
}

//////////////////////////////////////
////// FUERA DE DOCUMENT READY //////
////////////////////////////////////

function loadGestionMasivaTable() {
    if(tableRegOrders == null || tableRegOrders == '') {
        jQuery('#card1').show();
        tableRegOrders = jQuery('#GestionDataTable').DataTable({
            processing: true,
            serverSide: true,
            colReorder: true,
            ajax: {
                url: dataTableVars.dataTableurl,
                type: 'POST',
                data: function (d) {
                    d.FromDateOrdersReg = jQuery('#inputFromDateOrdersReg').val();
                    d.ToDateOrdersReg = jQuery('#inputToDateOrdersReg').val();
                    d.action = 'dataTableAjax';
                    d.nonce = dataTableVars.dataTableNonce;
                    d.tab = 'GestionDataTable';
                },
            },
            language: {
                url: co_path_to_module + '/views/js/datatables/Spanish.json',
            },
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
            buttons: [
                jQuery.extend(true, {}, inputs_tableRegOrders, {
                    extend: 'csv',
                    title: 'Correos eCommerce - Envíos ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 13],
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 1) {
                                    // Referencia
                                    return jQuery(node).find('a').text() || data;
                                } else if (column === 2) {
                                    // Productos
                                    return data.replace(/,/g, ',').replace(/\.\.\./g, '...');
                                } else if (column === 9) {
                                    // Remitente
                                    return jQuery(node).find('select').children('option:selected').text() || data;
                                } else if (column === 10) {
                                    // Bultos
                                    return jQuery(node).find('input').val() || data;
                                }
                                // Por defecto, devolver el dato original
                                return data;
                            },
                        },
                    },
                }),
                jQuery.extend(true, {}, inputs_tableRegOrders, {
                    extend: 'excel',
                    title: 'Correos eCommerce - Envíos ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 13],
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 1) {
                                    // Referencia
                                    return jQuery(node).find('a').text() || data;
                                } else if (column === 2) {
                                    // Productos
                                    return data.replace(/,/g, ',').replace(/\.\.\./g, '...');
                                } else if (column === 9) {
                                    // Remitente
                                    return jQuery(node).find('select').children('option:selected').text() || data;
                                } else if (column === 10) {
                                    // Bultos
                                    return jQuery(node).find('input').val() || data;
                                }
                                // Por defecto, devolver el dato original
                                return data;
                            },
                        },
                    },
                }),
                jQuery.extend(true, {}, inputs_tableRegOrders, {
                    extend: 'pdf',
                    title: 'Correos eCommerce - Envíos ' + co_fecha.toLocaleString(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    //footer: true,
                    customize: function (doc) {
                        doc.styles.tableHeader = {
                            fillColor: '#002E6D',
                            color: '#FFF',
                            fontSize: '11',
                            alignment: 'center',
                            bold: true,
                        };
                        doc['footer'] = function (page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'center',
                                        text: [
                                            {
                                                text: page.toString(),
                                                italics: true,
                                            },
                                            ' de ',
                                            {
                                                text: pages.toString(),
                                                italics: true,
                                            },
                                        ],
                                    },
                                ],
                                margin: [10, 0],
                            };
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.content[1].margin = [40, 10, 40, 0];
                        doc.pageMargins = [0, 30, 0, 60];
                    },
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 13],
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 1) {
                                    // Referencia
                                    return jQuery(node).find('a').text() || data;
                                } else if (column === 2) {
                                    // Productos
                                    return data.replace(/,/g, ',').replace(/\.\.\./g, '...');
                                } else if (column === 9) {
                                    // Remitente
                                    return jQuery(node).find('select').children('option:selected').text() || data;
                                } else if (column === 10) {
                                    // Bultos
                                    return jQuery(node).find('input').val() || data;
                                }
                                // Por defecto, devolver el dato original
                                return data;
                            },
                        },
                    },
                }),
            ],
            columnDefs: [
                {
                    orderable: false,
                    searcheable: false,
                    targets: 0,
                    defaultContent: '',
                    render: function (data, type, full, meta) {
                        //  Eliminamos la comprobación de full.name != null ya que debemos permitir que todos los pedidos se puedan gestionar
                        // También lo hacemos en todos los let disabled = 'disabled';
                        if (full.shipping_number == null || full.shipping_number == '') {
                            if (activateDimensionsByDefault == true || (full.product_type !== 'citypaq' && full.codigoProducto !== 'S0179')) {
                                return '<input type="checkbox" class="mycheckbox">';
                            } else {
                                return '<input type="checkbox" title="' + title_must_specify_measurements + '" class="mycheckbox" disabled>';
                            }
                        } else {
                            return '<input type="checkbox" title="' + title_must_cancel_preregister + '" class="mycheckbox" disabled>';
                        }
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        if (platform == 'wc') {
                            order_link = '<a href="' + co_url_base_wpadmin + 'post.php?post=' + full.id_order + '&action=edit"' + ' target="_blank">' + data + '</a>';
                        } else if (platform == 'ps') {
                            order_link = '<a href="' + location.protocol + '//' + window.location.hostname + window.location.pathname + '/sell/orders/' + full.id_order + '/view?_token=' + order_token + '"' + 'target="_blank">' + data + '</a>';
                        }
                        return order_link;
                    },
                },
                {
                    targets: 9,
                    render: function (data, type, full, meta) {
                        if (type === 'display') {
                            data = strtrunc(data, 35);
                        }
        
                        return data;
                    },
                },
                {
                    type: 'html-input',
                    targets: 12,
                    render: function (data, type, full, meta) {
                        let disabled = 'disabled';
                        if (full.shipping_number == null || full.shipping_number == '') {
                            disabled = '';
                        }
                        return '<select class="custom-select select_product" id="select_option_' + full.id_order + '" name="select_option_' + full.id_order + '" required ' + disabled + '>' + select_active_products + '</select>';
                    },
                },
                {
                    type: 'html-input',
                    targets: 13,
                    render: function (data, type, full, meta) {
                        let disabled = 'disabled';
                        if (full.shipping_number == null || full.shipping_number == '') {
                            disabled = '';
                        }
                        if (data != null) {
                            return '<input type="number" max="10" min="1" id="input_text_' + full.id_order + '" name="input_text_' + full.id_order + '" value="' + data + '"' + disabled + '>';
                        } else {
                            return '<input type="number" max="10" min="1" value="1" disabled>';
                        }
                    },
                },
                {
                    type: 'html-input',
                    targets: 14,
                    render: function (data, type, full, meta) {
                        let disabled = 'disabled';
                        if (full.name != null || full.company == 'Correos') {
                            if (full.shipping_number == null || full.shipping_number == '') {
                                disabled = '';
                            }
                        }
                        // Muestra N/A. El campo codigoAT se informa como campo oculto a vacío
                        let not_apply = '<span id="nApply' + full.id_order + '">N/A</span>' + '<input type="hidden" id="AT_code' + full.id_order + '" name="AT_code' + full.id_order + '" value="' + (data == null ? '' : data) + '"' + disabled + '>';
        
                        // NO aplica a Correos ni a CEX en caso de tener un remitente o destinatario diferente de Portugal
                        if (full.company == 'Correos' || (full.company == 'CEX' && full.sender_iso_code.includes('PT') && !full.delivery_iso_code.includes('PT')) || (full.company == 'CEX' && !full.sender_iso_code.includes('PT') && full.delivery_iso_code.includes('PT'))) {
                            return not_apply;
                        }
        
                        // Si es CEX, Si codigoAT esta habilitado y tiene destinatario
                        if (full.company == 'CEX' && full.sender_iso_code != null) {
                            // Si aún no tiene AT_code
                            if (full.AT_code == '' || (full.AT_code == null && (full.sender_iso_code.includes('PT') || full.delivery_iso_code.includes('PT')))) {
                                return '<span id="nApply' + full.id_order + '" class="co_hidden_atcode">N/A</span>' + '<input title="' + AT_Code_Only_CEX_and_Portugal + '" type="text" id="AT_code' + full.id_order + '" name="AT_code' + full.id_order + '" maxlength="30" required value=""' + disabled + '>';
                            }
                        }
        
                        // Si ya tiene AT_code se informa en el campo de texto
                        if (full.carrier_type && full.carrier_type == 'CEX' && full.sender_iso_code.includes('PT') && full.delivery_iso_code.includes('PT')) {
                            return (
                                '<span id="nApply' +
                                full.id_order +
                                '" class="co_hidden_atcode">N/A</span>' +
                                '<input title="' +
                                AT_Code_Only_CEX_and_Portugal +
                                '" type="text" id="AT_code' +
                                full.id_order +
                                '" name="AT_code' +
                                full.id_order +
                                '" maxlength="30" required value="' +
                                data +
                                '"' +
                                disabled +
                                '>'
                            );
                        }
        
                        return not_apply;
                    },
                },
                {
                    targets: [11, 12],
                    orderable: false,
                },
            ],
            //Función para cambiar entre el string "N/A" o el input text en la columna del AT Code
            rowCallback: function (row, data) {
                let product = jQuery(row).find('.select_product');
                let sender = jQuery(row).find('.select_sender');
        
                // Ocultar según scope de compañía de sender seleccionado por defecto
                let sender_selected = sender.find('option:selected').data('scope');
                product.find('option[data-company="CEX"], option[data-company="Correos"]').prop('disabled', false);
                if (sender_selected === 'Correos') {
                    product.find('option[data-company="CEX"]').prop('disabled', true);
                } else if (sender_selected === 'CEX') {
                    product.find('option[data-company="Correos"]').prop('disabled', true);
                }
        
                sender.on('change', function () {
                    // Reseteamos el selector de productos
                    let htmlObject = jQuery(product);
                    htmlObject.empty();
                    htmlObject.append(select_active_products);
        
                    let selected_carrier = htmlObject.find('option:selected');
                    let selected_company = selected_carrier.data('company');
                    let selected_sender_company = jQuery(this).find('option:selected').data('scope');
                    let selected_sender_iso = jQuery(this).find('option:selected').data('iso');
        
                    let order_company = data.company;
                    let order_derivery_iso_code = data.delivery_iso_code;
        
                    let idOrder = data.id_order;
        
                    checkOrderProductsAllowed(htmlObject, order_derivery_iso_code, selected_sender_iso, data.office);
        
                    // Ocultar según scope de compañía
                    if (selected_sender_company === 'Correos') {
                        product.find('option[data-company="CEX"]').remove();
                    } else if (selected_sender_company === 'CEX') {
                        product.find('option[data-company="Correos"]').remove();
                    }
        
                    // Obtenemos compañia o del selector de productos si está alguno seleccionado
                    if (!selected_company) {
                        selected_company = selected_sender_company;
                    }
        
                    //Si la compania es igual a "Correos" y no tiene remitente o destinatario de PT
                    //se deja el string en la columna de AT_code a "N/A".
                    if (selected_company == 'Correos' || (selected_company == 'all' && (!selected_sender_iso.includes('PT') || !data.delivery_iso_code.includes('PT')))) {
                        jQuery('#nApply' + idOrder).removeClass('co_hidden_atcode');
                        document.getElementById('AT_code' + idOrder).setAttribute('type', 'hidden');
                    }
        
                    //Si la compania es igual a CEX y el remitente no es null
                    if (selected_company == 'CEX' || (selected_company == 'all' && selected_sender_iso != null)) {
                        // Si At_code viene vacio, el remitente y destinatario son de Portugal
                        // Sino se comprueba que el remitente o destinatario sea distinto de Portugal
                        if ((data.AT_code == '' || data.AT_code == null) && selected_sender_iso.includes('PT') && data.delivery_iso_code.includes('PT')) {
                            jQuery('#nApply' + idOrder).addClass('co_hidden_atcode');
                            document.getElementById('AT_code' + idOrder).setAttribute('type', 'text');
                        } else if (!selected_sender_iso.includes('PT') || !data.delivery_iso_code.includes('PT')) {
                            jQuery('#nApply' + idOrder).removeClass('co_hidden_atcode');
                            document.getElementById('AT_code' + idOrder).setAttribute('type', 'hidden');
                        }
                    }
                });
        
                product.on('change', function () {
                    let htmlObject = jQuery(product);
                    let selected_carrier = htmlObject.find('option:selected');
                    let company = selected_carrier.data('company');
                    let idOrder = data.id_order;
        
                    let htmlObjectSender = jQuery(sender);
                    let selected_sender = htmlObjectSender.find('option:selected');
                    let selected_sender_iso = selected_sender.data('iso');
                    let selected_sender_company = selected_sender.data('scope');
        
                    // Nos aseguramos que obtenemos el ISO preferible desde el selector de senders
                    if (!selected_sender_iso) {
                        selected_sender_iso = data.sender_iso_code;
                    }
        
                    // Si el selector de compañía no tiene valor, se obtiene el valor de la columna de la tabla
                    if (company == undefined) {
                        if (selected_sender_company != 'all') {
                            company = selected_sender_company;
                        } else {
                            company = data.company;
                        }
                    }
        
                    //Si la compania es igual a "Correos" y no tiene remitente o destinatario de PT
                    //se deja el string en la columna de AT_code a "N/A".
                    if (company == 'Correos' || (company == 'all' && (!selected_sender_iso.includes('PT') || !data.delivery_iso_code.includes('PT')))) {
                        jQuery('#nApply' + idOrder).removeClass('co_hidden_atcode');
                        document.getElementById('AT_code' + idOrder).setAttribute('type', 'hidden');
                    }
        
                    //Si la compania es igual a CEX y el remitente no es null
                    if (company == 'CEX' && selected_sender_iso != null) {
                        // Si At_code viene vacio, el remitente y destinatario son de Portugal
                        // Sino se comprueba que el remitente o destinatario sea distinto de Portugal
                        if ((data.AT_code == '' || data.AT_code == null) && selected_sender_iso.includes('PT') && data.delivery_iso_code.includes('PT')) {
                            jQuery('#nApply' + idOrder).addClass('co_hidden_atcode');
                            document.getElementById('AT_code' + idOrder).setAttribute('type', 'text');
                        } else if (!selected_sender_iso.includes('PT') || !data.delivery_iso_code.includes('PT')) {
                            jQuery('#nApply' + idOrder).removeClass('co_hidden_atcode');
                            document.getElementById('AT_code' + idOrder).setAttribute('type', 'hidden');
                        }
                    }
                });
            },
            select: {
                style: 'multi',
                selector: '.mycheckbox',
            },
            order: [[1, 'desc']],
            columns: [
                { data: null },
                { data: 'id_order' },
                { data: 'reference' },
                { data: 'products' },
                { data: 'first_shipping_number', className: 'small_text_cell' },
                { data: 'carrier_type' },
                { data: 'order_state', className: 'small_text_cell' },
                { data: 'customer_name', className: 'small_text_cell' },
                { data: 'date_add' },
                { data: 'office' },
                { data: 'name' },
                { data: 'senders' }, // Aquí vá el selector de sender
                { data: 'id_product' },
                { data: 'bultos' },
                { data: 'AT_code' },
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            createdRow: function (row, data, dataIndex) {
                if (data['name'] != null) {
                    if (data['shipping_number'] === '' || data['shipping_number'] === null) {
                        if (activateDimensionsByDefault == true || (data['product_type'] !== 'citypaq' && data['codigoProducto'] !== 'S0179')) {
                            jQuery(row).addClass('selectable');
                        }
                    }
                }
        
                // Seleccionamos td > select
                row_td = jQuery('td', row).eq(12)[0];
                select_carriers = jQuery('select', row_td);
        
                // Aplicamos filtro de permitidos
                checkOrderProductsAllowed(select_carriers, data.delivery_iso_code, data.sender_iso_code, data.office);
            },
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function () {
                        var column = this;
                        var debounceTimeout;
        
                        jQuery('input', this.footer()).on('keyup', function () {
                            clearTimeout(debounceTimeout); // Limpiar el temporizador anterior
        
                            debounceTimeout = setTimeout(function () {
                                column.search(jQuery('input', column.footer()).val()).draw();
                            }, 300); // Espera 300 ms después de que el usuario deje de escribir
                        });
                    });
            },
        });
    } else {
        let fromDateOrdersReg = jQuery('#inputFromDateOrdersReg').val();
        let toDateOrdersReg = jQuery('#inputToDateOrdersReg').val();

        if (new Date(toDateOrdersReg).getTime() < new Date(fromDateOrdersReg).getTime()) {
            showModalInfoWindow(dateFromIsMinor);
        } else {
            jQuery('#card1').show();
            jQuery('#GestionDataTable').DataTable().ajax.reload();
            let el = jQuery('#table-select-all').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
            jQuery('#reg_orders_errors_container').hide();
            jQuery('#input_tipo_etiqueta_container_gestion').hide();
            jQuery('#print_label_reg_container').hide();
        }
        autoScrollButtons('#massiveProductsTable');
    }
}

function loadEtiquetasTable() {
    if(tableEtiquetas == null || tableEtiquetas == '') {
        jQuery('#card2').show();
        tableEtiquetas = jQuery('#EtiquetasDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataTableVars.dataTableurl,
                type: 'POST',
                data: function (d) {
                    d.FromDateOrdersReg = jQuery('#inputFromDateLabels').val();
                    d.ToDateOrdersReg = jQuery('#inputToDateLabels').val();
                    d.action = 'dataTableAjax';
                    d.nonce = dataTableVars.dataTableNonce;
                    d.tab = 'EtiquetasDataTable';
                    d.printLabelPage = 'active';
                },
            },
            language: {
                url: co_path_to_module + '/views/js/datatables/Spanish.json',
            },
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
            buttons: [
                {
                    extend: 'csv',
                    title: 'Correos eCommerce - Etiquetas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
                {
                    extend: 'excel',
                    title: 'Correos eCommerce - Etiquetas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
                {
                    extend: 'pdf',
                    title: 'Correos eCommerce - Envíos ' + co_fecha.toLocaleString(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    //footer: true,
                    customize: function (doc) {
                        doc.styles.tableHeader = {
                            fillColor: '#002E6D',
                            color: '#FFF',
                            fontSize: '11',
                            alignment: 'center',
                            bold: true,
                        };
                        doc['footer'] = function (page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'center',
                                        text: [
                                            {
                                                text: page.toString(),
                                                italics: true,
                                            },
                                            ' de ',
                                            {
                                                text: pages.toString(),
                                                italics: true,
                                            },
                                        ],
                                    },
                                ],
                                margin: [10, 0],
                            };
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.content[1].margin = [100, 10, 40, 0];
                        doc.pageMargins = [0, 30, 0, 60];
                    },
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
            ],
            columnDefs: [
                {
                    orderable: false,
                    searcheable: false,
                    targets: 0,
                    defaultContent: '',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" class="mycheckbox">';
                    },
                },
            ],
            select: {
                style: 'multi',
                selector: '.mycheckbox',
            },
            order: [[1, 'desc']],
            columns: [{ data: null }, { data: 'id_order' }, { data: 'reference' }, { data: 'products' }, { data: 'first_shipping_number' }, { data: 'company' }, { data: 'customer_name' }, { data: 'customer_address' }, { data: 'date_add' }],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function () {
                        var column = this;
                        var debounceTimeout;
        
                        jQuery('input', this.footer()).on('keyup', function () {
                            clearTimeout(debounceTimeout); // Limpiar el temporizador anterior
        
                            debounceTimeout = setTimeout(function () {
                                column.search(jQuery('input', column.footer()).val()).draw();
                            }, 300); // Espera 300 ms después de que el usuario deje de escribir
                        });
                    });
            },
        });
    } else {
        let fromDateLabel = jQuery('#inputFromDateLabels').val();
        let toDateLabels = jQuery('#inputToDateLabels').val();

        if (new Date(toDateLabels).getTime() < new Date(fromDateLabel).getTime()) {
            showModalInfoWindow(dateFromIsMinor);
        } else {
            jQuery('#card2').show();
            jQuery('#EtiquetasDataTable').DataTable().ajax.reload();
            let el = jQuery('#table-select-all-etiquetas').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
            jQuery('#print_label_errors_container').addClass('hidden-block');
        }
        autoScrollButtons('#reprintLabelTable');
    }
}

function loadSummaryTable() {
    if(tableResumen == null || tableResumen == '') {
        jQuery('#card3').show();
        tableResumen = jQuery('#ResumenDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataTableVars.dataTableurl,
                type: 'POST',
                data: function (d) {
                    d.FromDateOrdersReg = jQuery('#inputFromDateSummary').val();
                    d.ToDateOrdersReg = jQuery('#inputToDateSummary').val();
                    d.SearchByLabelingDate = jQuery('#checkSearchByLabelingDate').is(':checked');
                    d.SearchBySender = jQuery('#inputOrdersSummarySenders').val();
        
                    d.action = 'dataTableAjax';
                    d.nonce = dataTableVars.dataTableNonce;
                    d.tab = 'ResumenDataTable';
                },
            },
            language: {
                url: co_path_to_module + '/views/js/datatables/Spanish.json',
            },
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
            buttons: [
                {
                    extend: 'csv',
                    title: 'Correos eCommerce - Resumen/Manifiesto ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7],
                    },
                },
                {
                    extend: 'excel',
                    title: 'Correos eCommerce - Resumen/Manifiesto ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7],
                    },
                },
                {
                    extend: 'pdf',
                    title: 'Correos eCommerce - Resumen/Manifiesto ' + co_fecha.toLocaleString(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true,
                    messageBottom: function () {
                        let selectedData = tableResumen.rows({ selected: true }).data().toArray();
                        if (selectedData.length == 0) {
                            selectedData = tableResumen.rows().data().toArray();
                        }
                        let total_bultos = 0;
                        selectedData.forEach(function (data) {
                            total_bultos = total_bultos + parseInt(data['bultos']);
                        });
                        return 'Total Envíos: ' + selectedData.length + ' Total Bultos: ' + total_bultos;
                    },
                    customize: function (doc) {
                        doc.styles.tableHeader = {
                            fillColor: '#002E6D',
                            color: '#FFF',
                            fontSize: '11',
                            alignment: 'center',
                            bold: true,
                        };
                        doc.styles.tableFooter = {
                            fillColor: '#002E6D',
                        };
                        doc['footer'] = function (page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'center',
                                        text: [
                                            {
                                                text: page.toString(),
                                                italics: true,
                                            },
                                            ' de ',
                                            {
                                                text: pages.toString(),
                                                italics: true,
                                            },
                                        ],
                                    },
                                ],
                                margin: [10, 0],
                            };
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.content[1].margin = [0, 10, 40, 20];
                        doc.pageMargins = [100, 30, 0, 60];
                    },
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7],
                    },
                },
            ],
            columnDefs: [
                {
                    orderable: false,
                    searchable: false,
                    targets: 0,
                    defaultContent: '',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" class="mycheckbox">';
                    },
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 6,
                    visible: false,
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 14,
                    visible: false,
                },
            ],
            select: {
                style: 'multi',
                selector: '.mycheckbox',
            },
            order: [[1, 'desc']],
            columns: [
                { data: null },
                { data: 'id_order' },
                { data: 'reference' },
                { data: 'first_shipping_number' },
                { data: 'cod_bultos' },
                { data: 'company' },
                { data: 'customer_code' },
                { data: 'customer_name' },
                { data: 'customer_address' },
                { data: 'postal_code' },
                { data: 'date_add' },
                { data: 'labeling_date' },
                { data: 'manifested' },
                { data: 'manifest_date' },
                { data: 'sender' },
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function () {
                        var column = this;
                        var debounceTimeout;
        
                        jQuery('input', this.footer()).on('keyup', function () {
                            clearTimeout(debounceTimeout); // Limpiar el temporizador anterior
        
                            debounceTimeout = setTimeout(function () {
                                column.search(jQuery('input', column.footer()).val()).draw();
                            }, 300); // Espera 300 ms después de que el usuario deje de escribir
                        });
                    });
                jQuery.fn.dataTable.ext.classes.sLengthSelect = 'custom-select';
            },
        });
    } else {
        let fromDateSummary = jQuery('#inputFromDateSummary').val();
        let toDateSummary = jQuery('#inputToDateSummary').val();
        if (new Date(toDateSummary).getTime() < new Date(fromDateSummary).getTime()) {
            showModalInfoWindow(dateFromIsMinor);
        } else {
            jQuery('#card3').show();
            jQuery('#ResumenDataTable').DataTable().ajax.reload();
            let el = jQuery('#table-select-all-resumen').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
        autoScrollButtons('#ordersSummaryTable');
    }
}

function loadPickupTable() { 
    if(tablePickups == null || tablePickups == '') {
        jQuery('#card4').show();
        tablePickups = jQuery('#PickupDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataTableVars.dataTableurl,
                type: 'POST',
                data: function (d) {
                    d.FromDateOrdersReg = jQuery('#inputFromDatePickups').val();
                    d.ToDateOrdersReg = jQuery('#inputToDatePickups').val();
                    d.action = 'dataTableAjax';
                    d.nonce = dataTableVars.dataTableNonce;
                    d.tab = 'EtiquetasDataTable';
                    d.onlyCorreos = 'active';
                },
            },
            language: {
                url: co_path_to_module + '/views/js/datatables/Spanish.json',
            },
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
            buttons: [
                jQuery.extend(true, {}, inputs_tablePickups, {
                    extend: 'csv',
                    title: 'Correos eCommerce - Recogidas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    },
                }),
                jQuery.extend(true, {}, inputs_tablePickups, {
                    extend: 'excel',
                    title: 'Correos eCommerce - Recogidas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    },
                }),
                jQuery.extend(true, {}, inputs_tablePickups, {
                    extend: 'pdf',
                    title: 'Correos eCommerce - Recogidas ' + co_fecha.toLocaleString(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    //footer: true,
                    customize: function (doc) {
                        doc.styles.tableHeader = {
                            fillColor: '#002E6D',
                            color: '#FFF',
                            fontSize: '11',
                            alignment: 'center',
                            bold: true,
                        };
                        doc['footer'] = function (page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'center',
                                        text: [
                                            {
                                                text: page.toString(),
                                                italics: true,
                                            },
                                            ' de ',
                                            {
                                                text: pages.toString(),
                                                italics: true,
                                            },
                                        ],
                                    },
                                ],
                                margin: [10, 0],
                            };
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.content[1].margin = [40, 10, 40, 0];
                        doc.pageMargins = [0, 30, 0, 60];
                    },
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 8) {
                                    let selectedText = jQuery(node).find('select option:selected').text();
                                    return selectedText || '';
                                } else if (column === 9) {
                                    let selectedValue = jQuery(node).find('select option:selected').text();
                                    return selectedValue;
                                }
        
                                if (column === 10) {
                                    if (jQuery(node).find('button').length) {
                                        return jQuery(node).find('button').text().trim();
                                    } else {
                                        return jQuery(node).text().trim();
                                    }
                                }
        
                                // Por defecto, devolver el dato original
                                return data;
                            },
                        },
                    },
                }),
            ],
            columnDefs: [
                {
                    orderable: false,
                    searcheable: false,
                    targets: 0,
                    defaultContent: '',
                    render: function (data, type, full, meta) {
                        if (full.pickup == 1) {
                            return '<input type="checkbox" class="mycheckbox" disabled>';
                        } else {
                            return '<input type="checkbox" class="mycheckbox">';
                        }
                    },
                },
                {
                    type: 'html-input',
                    targets: 9,
                    render: function (data, type, full, meta) {
                        var selected0 = '',
                            selected10 = '',
                            selected20 = '',
                            selected30 = '',
                            selected40 = '',
                            selected50 = '',
                            selected60 = '';
                        var disabled = '';
        
                        if (full.package_size == null) {
                            selected0 = 'selected';
                        } else {
                            switch (full.package_size) {
                                case '10':
                                    selected10 = 'selected';
                                    break;
                                case '20':
                                    selected20 = 'selected';
                                    break;
                                case '30':
                                    selected30 = 'selected';
                                    break;
                                case '40':
                                    selected40 = 'selected';
                                    break;
                                case '50':
                                    selected50 = 'selected';
                                    break;
                                case '60':
                                    selected60 = 'selected';
                                    break;
                            }
                        }
        
                        if (full.company == 'CEX') {
                            disabled = 'disabled';
                        }
        
                        if (full.pickup == 1) {
                            disabled = 'disabled';
                        }
        
                        return (
                            '<select class="custom-select tam-recogidas-input-option" id="select_option_tam_recogidas_' +
                            full.id_order +
                            '" name="select_option_tam_recogidas_' +
                            full.id_order +
                            '" required ' +
                            disabled +
                            '>' +
                            '<option ' +
                            selected0 +
                            ' value="0">&nbsp;</option>' +
                            '<option ' +
                            selected10 +
                            ' value="10">Sobres</option>' +
                            '<option ' +
                            selected20 +
                            ' value="20">Pequeño (caja zapatos)</option>' +
                            '<option ' +
                            selected30 +
                            ' value="30">Mediano (caja folios)</option> ' +
                            '<option ' +
                            selected40 +
                            ' value="40">Grande (caja 80x80x80cm)</option>' +
                            '<option ' +
                            selected50 +
                            ' value="50">Muy grande (mayor que caja 80x80x80cm)</option>' +
                            '<option ' +
                            selected60 +
                            ' value="60">Palet</option>' +
                            '</select>'
                        );
                    },
                },
                {
                    type: 'html-input',
                    targets: 10,
                    render: function (data, type, full, meta) {
                        var selectedS = '',
                            selectedN = '';
                        var disabled = '';
        
                        switch (full.print_label) {
                            case 'S':
                                selectedS = 'selected';
                                break;
                            case 'N':
                                selectedN = 'selected';
                                break;
                        }
        
                        if (full.company == 'CEX') {
                            disabled = 'disabled';
                        }
        
                        if (full.pickup == 1) {
                            disabled = 'disabled';
                        }
        
                        return (
                            '<select class="custom-select imp-recogidas-input-option" id="select_option_imp_recogidas_' +
                            full.id_order +
                            '"name="select_option_imp_recogidas' +
                            full.id_order +
                            '" required ' +
                            disabled +
                            '>' +
                            '<option selected value="N">&nbsp;</option>' +
                            '<option ' +
                            selectedN +
                            ' value="N">No</option>' +
                            '<option ' +
                            selectedS +
                            ' value="S">Si</option>' +
                            '</select>'
                        );
                    },
                },
                {
                    targets: 11,
                    render: function (data, type, full, meta) {
                        if (data == 0) {
                            return '<button type="button" class="btn btn-danger btn-sm" disabled>NO</button>';
                        } else {
                            return full.pickup_number;
                        }
                    },
                },
                {
                    targets: [9, 10],
                    orderable: false,
                },
            ],
            select: {
                style: 'multi',
                selector: '.mycheckbox',
            },
            order: [[1, 'desc']],
            columns: [
                { data: null },
                { data: 'id_order' },
                { data: 'reference' },
                { data: 'first_shipping_number', className: 'small_text_cell' },
                { data: 'company' },
                { data: 'customer_name', className: 'small_text_cell' },
                { data: 'customer_address', className: 'small_text_cell' },
                { data: 'date_add' },
                { data: 'bultos' },
                { data: 'package_size' },
                { data: 'print_label' },
                { data: 'pickup' },
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            createdRow: function (row, data, dataIndex) {
                if (data['pickup'] == 0) {
                    jQuery(row).addClass('selectable');
                } else {
                    jQuery(row).addClass('no-selectable');
                }
            },
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function (index) {
                        // let delayTimer;
                        var column = this;
                        var debounceTimeout;
        
                        jQuery('input', this.footer()).on('keyup', function () {
                            clearTimeout(debounceTimeout); // Limpiar el temporizador anterior
        
                            debounceTimeout = setTimeout(function () {
                                column.search(jQuery('input', column.footer()).val()).draw();
                            }, 300); // Espera 300 ms después de que el usuario deje de escribir
                        });
                    });
                jQuery.fn.dataTable.ext.classes.sLengthSelect = 'custom-select';
            },
        });
    } else {
        let fromDatePickups = jQuery('#inputFromDatePickups').val();
        let toDatePickups = jQuery('#inputToDatePickups').val();
        if (new Date(toDatePickups).getTime() < new Date(fromDatePickups).getTime()) {
            showModalInfoWindow(dateFromIsMinor);
        } else {
            jQuery('#card4').show();
            jQuery('#PickupDataTable').DataTable().ajax.reload();
            let el = jQuery('#table-select-all-pickups').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
        }
        autoScrollButtons('#pickupsTable');
    }
}

function loadDocAduaneraTable() {
    if(tableDocAduanera == null || tableDocAduanera == '') {
        jQuery('#card5').show();
        tableDocAduanera = jQuery('#DocAduaneraDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataTableVars.dataTableurl,
                type: 'POST',
                data: function (d) {
                    d.FromDateOrdersReg = jQuery('#inputFromDateCustomsDoc').val();
                    d.ToDateOrdersReg = jQuery('#inputToDateCustomsDoc').val();
                    d.action = 'dataTableAjax';
                    d.nonce = dataTableVars.dataTableNonce;
                    d.tab = 'DocAduaneraDataTable';
                },
            },
            language: {
                url: co_path_to_module + '/views/js/datatables/Spanish.json',
            },
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',
            buttons: [
                {
                    extend: 'csv',
                    title: 'Correos eCommerce - Aduanas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
                {
                    extend: 'excel',
                    title: 'Correos eCommerce - Aduanas ' + co_fecha.toLocaleString(),
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
                {
                    extend: 'pdf',
                    title: 'Correos eCommerce - Aduanas ' + co_fecha.toLocaleString(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    //footer: true,
                    customize: function (doc) {
                        doc.styles.tableHeader = {
                            fillColor: '#002E6D',
                            color: '#FFF',
                            fontSize: '11',
                            alignment: 'center',
                            bold: true,
                        };
                        doc['footer'] = function (page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'center',
                                        text: [
                                            {
                                                text: page.toString(),
                                                italics: true,
                                            },
                                            ' de ',
                                            {
                                                text: pages.toString(),
                                                italics: true,
                                            },
                                        ],
                                    },
                                ],
                                margin: [10, 0],
                            };
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.content[1].margin = [100, 10, 40, 0];
                        doc.pageMargins = [0, 30, 0, 60];
                    },
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    },
                },
            ],
            columnDefs: [
                {
                    orderable: false,
                    searchable: false,
                    targets: 0,
                    defaultContent: '',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" class="mycheckbox">';
                    },
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 9,
                    visible: false,
                },
            ],
            select: {
                style: 'multi',
                selector: '.mycheckbox',
            },
            order: [[1, 'desc']],
            columns: [{ data: null }, { data: 'id_order' }, { data: 'reference' }, { data: 'first_shipping_number' }, { data: 'company' }, { data: 'customer_name' }, { data: 'customer_address' }, { data: 'customer_country' }, { data: 'date_add' }, { data: 'custom_doc' }],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function (index) {
                        var column = this;
                        var debounceTimeout;
        
                        jQuery('input', this.footer()).on('keyup', function () {
                            clearTimeout(debounceTimeout); // Limpiar el temporizador anterior
        
                            debounceTimeout = setTimeout(function () {
                                column.search(jQuery('input', column.footer()).val()).draw();
                            }, 300); // Espera 300 ms después de que el usuario deje de escribir
                        });
                    });
                jQuery.fn.dataTable.ext.classes.sLengthSelect = 'custom-select';
            },
        });
    } else {
        let fromDateCustomsDoc = jQuery('#inputFromDateCustomsDoc').val();
        let toDateCustomsDoc = jQuery('#inputToDateCustomsDoc').val();
        if (new Date(toDateCustomsDoc).getTime() < new Date(fromDateCustomsDoc).getTime()) {
            showModalInfoWindow(dateFromIsMinor);
        } else {
            jQuery('#card5').show();
            jQuery('#DocAduaneraDataTable').DataTable().ajax.reload();
            var el = jQuery('#table-select-all-doc-aduanera').get(0);
            if (el && el.checked && 'indeterminate' in el) {
                el.indeterminate = true;
            }
            jQuery('#datatable_results_aduanera_container').hide();
        }
        autoScrollButtons('#customsDocumentationTable');
    }
}
