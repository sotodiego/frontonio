if (typeof prestashop !== typeof undefined) {
    var static_token = prestashop.static_token;
} else {
    var static_token = 'token';
}

var historic_table;

jQuery(document).ready(function () {
    /* DATATABLE HISTÓRICO DEL ENVÍO */
    historic_table = jQuery('#historic-table').DataTable({
        paging: false,
        info: false,
        searching: false,
        orderable: false,
        columns: [{ data: 'codEnvio' }, { data: 'codProducto' }, { data: 'desTextoResumen', className: 'text-center' }, { data: 'fecEvento', className: 'text-center' }, { data: 'horEvento', className: 'text-center' }],
        columnDefs: [
            {
                targets: 2,
                render: function (data, type, full, meta) {
                    // Correos
                    switch (data) {
                        // Correos
                        case 'Prerregistrado':
                            return '<div class="preregistrado">' + data + '</div>';
                        case 'Admitido':
                        case 'En tránsito':
                        case 'En reparto':
                        case 'Alta en la unidad de reparto':
                        case 'Clasificado':
                            return '<div class="en_curso">' + data + '</div>';
                        case 'Admisión anulada':
                            return '<div class="anulado">' + data + '</div>';
                        case 'A disposición del destinatario':
                        case 'Entregado':
                            return '<div class="entregado">' + data + '</div>';
                        case 'No informado':
                            return '<div class="no-informado">' + data + '</div>';
                        // CEX
                        case 'SIN RECEPCION':
                            return '<div class="preregistrado">' + data + '</div>';
                        case 'EN REPARTO':
                        case 'DELEGACION DESTINO':
                        case 'EN ARRASTRE':
                            return '<div class="en_curso">' + data + '</div>';
                        case 'ENTREGADO':
                            return '<div class="entregado">' + data + '</div>';
                        default:
                            return '<div class="intermedio">' + data + '</div>';
                    }
                },
            },
        ],
        order: [
            [3, 'desc'],
            [4, 'desc'],
        ],
    });

    setDatatableHistory();
});

/**
 * Obtener datos del envio de pedido.
 */
function setDatatableHistory() {
    jQuery.ajax({
        type: 'post',
        url: varsAjax.ajaxUrl,
        data: {
            action: 'correosOficialDispacher',
            _nonce: varsAjax.nonce,
            dispatcher: {
                controller: 'CorreosOficialAdminOrderModuleFrontController',
                action: 'getOrderStatus',
                order_id: jQuery('#id_order_hidden').val(),
            },
        },
        success: function (data) {
            parsed_data = JSON.parse(data);
            historic_table.clear().draw();
            historic_table.rows.add(parsed_data);
            historic_table.columns.adjust().draw();
            jQuery('.history-container').removeClass('hidden-block');
        },
    });
}

function getFormData($form_id) {
    var config = {};
    jQuery('#' + $form_id + ' input:hidden').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' input:text').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' input:checkbox').each(function () {
        if (jQuery(this).is(':checked')) {
            config[this.name] = 1;
        } else {
            config[this.name] = 0;
        }
    });
    jQuery('#' + $form_id + ' input:radio').each(function () {
        if (jQuery(this).is(':checked')) {
            config[this.name] = 1;
        } else {
            config[this.name] = 0;
        }
    });
    jQuery('#' + $form_id + ' select').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' textarea').each(function () {
        config[this.name] = this.value;
    });
    return config;
}

function disableForm(form_id) {
    jQuery('input', form_id).each(function (event) {
        this.disabled = true;
    });
    jQuery('select', form_id).each(function (event) {
        this.disabled = true;
    });
    jQuery('button', form_id).each(function (event) {
        if (this.id == 'copyOfficeContent' || this.id == 'copyCityPaqContent') {
            this.disabled = false;
        } else {
            this.disabled = true;
        }
    });
    jQuery('textarea', form_id).each(function (event) {
        this.disabled = true;
    });
}

function enableForm(form_id) {
    jQuery('input', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('select', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('button', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('textarea', form_id).each(function (event) {
        this.disabled = false;
    });
}

/**
 * Devuelve la fecha
 * @returns date en formato yyyy-mm-dd
 */
function coGetToday() {
    var date = new Date();
    var day = date.getDate();
    var month = date.getMonth() + 1; // Los monthes van de 0 a 11, sumamos 1 para obtener el month correcto
    var year = date.getFullYear();

    // Agregar ceros a la izquierda si es necesario para mantener el formato yyyy/mm/dd
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }

    return year + '-' + month + '-' + day;
}

function setCorreosRangeDate(inputField) {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    var month = tomorrow.getMonth() + 1;
    var day = tomorrow.getDate();
    var year = tomorrow.getFullYear();

    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    var today_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('min', today_val);
    document.getElementById(inputField).value = year + '-' + month + '-' + day;

    month++;
    if (month > 12) {
        month = 1;
        year++;
    }
    if (month < 10) month = '0' + month;

    var max_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('max', max_val);
}

function setCEXRangeDate(inputField) {
    const today = new Date();

    var month = today.getMonth() + 1;
    var day = today.getDate();
    var year = today.getFullYear();

    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    var today_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('min', today_val);
    document.getElementById(inputField).value = year + '-' + month + '-' + day;

    month++;
    if (month > 12) {
        month = 1;
        year++;
    }
    if (month < 10) month = '0' + month;

    var max_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('max', max_val);
}

function managePrintLabel(bultos) {
    if (bultos > 5) {
        jQuery('#print_label').attr('checked', false);
        jQuery('#print_label').attr('disabled', true);
        jQuery('.alert-more-5-labels').removeClass('hidden-block');
    } else {
        jQuery('#print_label').attr('disabled', false);
        jQuery('.alert-more-5-labels').addClass('hidden-block');
    }
}

function manageDeliverySaturday(company) {
    if (company == 'CEX') {
        jQuery('#delivery_saturday_container').removeClass('hidden-block');
    } else {
        jQuery('#delivery_saturday_container').addClass('hidden-block');
    }
}

function manageReturnCustomDocPackage(company) {
    var require_customs_doc = jQuery('#require_customs_doc_hidden').val();
    if (require_customs_doc) {
        if (company == 'Correos') {
            jQuery('.customs-correos-container-return').removeClass('hidden-block');
            jQuery('.correos-num-parcels-return-container').addClass('hidden-block');
            jQuery('#general-return-pickup-container').removeClass('hidden-block');
            jQuery('#pickupReturnButton').removeClass('hidden-block');
            jQuery('#save-return-pickup-container').addClass('hidden-block');
        } else {
            jQuery('.customs-correos-container-return').addClass('hidden-block');
            jQuery('.correos-num-parcels-return-container').removeClass('hidden-block');
            jQuery('#general-return-pickup-container').removeClass('hidden-block');
            jQuery('#pickupReturnButton').addClass('hidden-block');
            jQuery('#save-return-pickup-container').removeClass('hidden-block');
            jQuery('#correos-options-pickup-return-container').addClass('hidden-block');
            jQuery('#generate_return_pickup').addClass('hidden-block');
        }
    } else {
        jQuery('.customs-correos-container-return').addClass('hidden-block');
    }
}

function manageCodeAT() {
    const selectedCarrier = jQuery('#input_select_carrier').find('option:selected');
    const company = selectedCarrier.data('company');
    const customerCountry = jQuery('#customer_country').val();
    const senderCountry = jQuery('#sender_country').val();

    const codeAtContainer = jQuery('#code_at_container');
    const requireCustomsDoc = jQuery('#require_customs_doc');

    if (company === 'CEX') {
        requireCustomsDoc.addClass('hidden-block');
        if (customerCountry === 'PT' && senderCountry === 'PT') {
            codeAtContainer.removeClass('hidden-block');
        } else {
            codeAtContainer.addClass('hidden-block');
        }
    } else if (company === 'Correos') {
        codeAtContainer.addClass('hidden-block');
        if (customerCountry !== senderCountry) {
            requireCustomsDoc.removeClass('hidden-block');
        } else {
            requireCustomsDoc.addClass('hidden-block');
        }
    }
}

function cleanStatusDatatable() {
    historic_table.clear().draw();
    jQuery('.history-container').removeClass('hidden-block');
}

/**
 * Tabs de documentación aduanera
 */
function showCustomsCode(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_desc_tab_' + n).addClass('hidden-block');
    jQuery('#customs_correos_container' + type + ' #customs_code_tab_' + n).removeClass('hidden-block');
}

function setCustomsCodeActive(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_desc_' + n).removeClass('active');
}

function showCustomsDesc(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_desc_tab_' + n).removeClass('hidden-block');
    jQuery('#customs_correos_container' + type + ' #customs_code_tab_' + n).addClass('hidden-block');
}

function setCustomsDescActive(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_code_' + n).removeClass('active');
}

function getActiveTab(n, type) {
    let tab = 'desc_tab';
    let classList = jQuery('#customs_correos_container' + type + ' #customs_code_' + n)
        .attr('class')
        .split(/\s+/);
    jQuery.each(classList, function (index, item) {
        if (item === 'active') {
            addingDesc = false;
            addingTarriffCode = true;
            tab = 'code_tab';
            return false;
        }
    });
    return tab;
}

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                         GENERAR RECOGIDA   //  DEVOLUCIONES                          //
//                                                                                      //
//--------------------------------------------------------------------------------------//

function generateReturnPickup() {
    jQuery('#processingReturnPickupButtonMsg').removeClass('hidden-block');
    jQuery('#returnPickupButtonMsg').addClass('hidden-block');
    jQuery('#generate_return_pickup').attr('disabled', true);

    let selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
    let company = selected_carrier_return.data('company');
    let id_carrier = 0;

    let print_label = 0;

    if (jQuery('#return_print_label').is(':checked')) {
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
                mode_pickup: 'return',
                order_id: jQuery('#id_order_hidden').val(),
                bultos: jQuery('#correos-num-parcels-return').val(),
                expedition_number: jQuery('#return_exp_number_hidden').val(),
                order_reference: jQuery('#order_reference').val(),
                pickup_date: jQuery('#return_pickup_date').val(),
                sender_from_time: jQuery('#return_sender_from_time').val(),
                sender_to_time: jQuery('#return_sender_to_time').val(),
                sender_address: jQuery('#customer_address').val(),
                sender_city: jQuery('#customer_city').val(),
                sender_cp: jQuery('#customer_cp').val(),
                sender_name: jQuery('#customer_firstname').val() + ' ' + jQuery('#customer_lastname').val(),
                sender_contact: jQuery('#customer_firstname').val() + ' ' + jQuery('#customer_lastname').val(),
                sender_phone: jQuery('#customer_phone').val(),
                sender_email: jQuery('#customer_email').val(),
                sender_nif_cif: jQuery('#customer_dni').val(),
                sender_country: jQuery('#customer_country').val(),
                id_sender: jQuery('#senderSelect').val(),
                producto: selected_carrier_return.val(),
                package_type: jQuery('#return_package_type').val(),
                print_label: print_label,
                company: company,
                id_carrier: id_carrier,
                default_sender_email: jQuery('#default_sender_email').val(),
                customer_cp: jQuery('#sender_cp').val(),
                customer_country: jQuery('#sender_country').val(),
            },
        },
        cache: false,
        processData: true,
        success: function (data) {
            parsed_data = JSON.parse(data);
            if (parsed_data.codigoRetorno == '0') {
                jQuery('#pickup_return_code_hidden').val(parsed_data.codSolicitud);
                location.reload();
                return;
            } else {
                jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                jQuery('#error_register_return').removeClass('hidden-block');
                jQuery('#success_register_return').addClass('hidden-block');
            }
            jQuery('#processingReturnPickupButtonMsg').addClass('hidden-block');
            jQuery('#returnPickupButtonMsg').removeClass('hidden-block');
            jQuery('#generate_return_pickup').attr('disabled', false);
        },
    });
}
