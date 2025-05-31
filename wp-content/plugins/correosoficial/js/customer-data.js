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
    var customerTable;
    var customerToBeRemoved;
    var row;
    const errorMessage = document.querySelector(".notice.notice-error.is-dismissible");

    // Ocultamos contenedores de conectado
    jQuery('.connected').hide();

    validateCorreosUser();

    // ---- VALIDACIONES CORREOS -------------------------------------------------------------

    jQuery('#CorreosCustomerDataForm').validate({
        rules: {
            CorreosContract: {
                required: true,
                minlength: 8,
                maxlength: 8,
            },
            CorreosCustomer: {
                required: true,
                minlength: 8,
                maxlength: 8,
            },
            CorreosKey: {
                required: true,
            },
            CorreosUser: {
                required: true,
                minlength: 3,
                maxlength: 20,
            },
            CorreosPassword: {
                required: true,
                maxlength: 20,
            },
            CorreosOv2Code: {
                required: true,
                email: true,
                minlength: 3,
                maxlength: 150,
            },
        },
        messages: {
            CorreosContract: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 8 ' + characters,
                maxlength: maxLengthMessage + ' 8 ' + characters,
            },
            CorreosCustomer: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 8 ' + characters,
                maxlength: maxLengthMessage + ' 8 ' + characters,
            },
            CorreosKey: {
                required: requiredCustomMessage,
            },
            CorreosUser: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 20 ' + characters,
            },
            CorreosPassword: {
                required: requiredCustomMessage,
                maxlength: maxLengthMessage + ' 20 ' + characters,
            }
        },
        submitHandler: function () {
            let formElement = document.getElementById('CorreosCustomerDataForm');
            let sourceForm = new FormData(formElement);
            let destinyForm = new FormData();

            // Agregar los campos adicionales requeridos al objeto FormData
            destinyForm.append('action', 'correosOficialDispacher');
            destinyForm.append('_nonce', varsAjax.nonce);
            destinyForm.append('dispatcher[controller]', 'AdminCorreosOficialCustomerDataProcess');
            destinyForm.append('dispatcher[operation]', 'CorreosCustomerDataForm');

            sourceForm.forEach((value, key) => {
                destinyForm.append('dispatcher[' + key + ']', value);
            });

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: destinyForm,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    let obj = JSON.parse(data);
                    console.log(obj);
                    if (obj.error == 'ERROR 100501') {
                        showModalErrorWindow(obj.desc);
                    } else if(obj.status_code == '409') {
                        showModalErrorWindow('Error ' + obj.codigoRetorno + ': ' + obj.mensajeRetorno);
                    } else {
                        jQuery('#idCorreos').val(data);
                        jQuery('#CustomerDataDataTable').DataTable().ajax.reload();

                        jQuery('#SendersDataTable').DataTable().ajax.reload();
                        reloadSenderContractsSelects();

                        if (jQuery('#CorreosContract').prop('disabled') == false) {
                            if (soapIsDisabled()) {
                                signUpCorreosCustomer(false, data)
                                .then((isConnected) => {
                                    if (isConnected) {
                                        disableCorreosForm();
                                        jQuery('#cocexUserLoggin').addClass('hidden-block');
                                    } else {
                                        jQuery('#CorreosCustomerDataSaveButton').val(editButton.toUpperCase());
                                        customerStatus('Correos', 'off');
                                        jQuery('#cocexUserLoggin').addClass('hidden-block');
                                    }
                                })
                                .catch((error) => {
                                    showModalErrorWindow(error);
                                });
                            } else {
                                showModalErrorWindow(errorMessage.innerText);
                            }
                        } else {
                            enableCorreosForm(data ? editButton.toUpperCase() : false);
                        }
                    }
                },
                error: function (e) {
                    showModalErrorWindow('ERROR 10502: ' + customer_technical_error);
                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                },
            });
        },
    }); // Fin Validaciones Correos

    // ---- VALIDACIONES CEX -----------------------------------------------------------------

    jQuery('#CEXCustomerDataForm').validate({
        rules: {
            CEXCustomer: {
                required: true,
                minlength: 9,
                maxlength: 9,
            },
            CEXUser: {
                required: true,
                minlength: 3,
                maxlength: 20,
            },
            CEXPassword: {
                required: true,
                minlength: 3,
                maxlength: 20,
            },
        },
        messages: {
            CEXCustomer: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 9 ' + characters,
                maxlength: maxLengthMessage + ' 9 ' + characters,
            },
            CEXUser: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 20 ' + characters,
            },
            CEXPassword: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 20 ' + characters,
            },
        },

        submitHandler: function () {
            let formElement = document.getElementById('CEXCustomerDataForm');
            let sourceForm = new FormData(formElement);
            let destinyForm = new FormData();

            // Agregar los campos adicionales requeridos al objeto FormData
            destinyForm.append('action', 'correosOficialDispacher');
            destinyForm.append('_nonce', varsAjax.nonce);
            destinyForm.append('dispatcher[controller]', 'AdminCorreosOficialCustomerDataProcess');
            destinyForm.append('dispatcher[operation]', 'CEXCustomerDataForm');

            sourceForm.forEach((value, key) => {
                destinyForm.append('dispatcher[' + key + ']', value);
            });

            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: destinyForm,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    let obj = JSON.parse(data);

                    if (obj.error == 'ERROR 100501') {
                        showModalErrorWindow(obj.desc);
                    } else if (obj.status_code == '409') {
                        showModalErrorWindow('Error ' + obj.codigoRetorno + ': ' + obj.mensajeRetorno);
                    } else {
                        jQuery('#idCEX').val(data);
                        jQuery('#CustomerDataDataTable').DataTable().ajax.reload();

                        jQuery('#SendersDataTable').DataTable().ajax.reload();
                        reloadSenderContractsSelects();

                        if (jQuery('#CEXCustomer').prop('disabled') == false) {
                            if (soapIsDisabled()) {
                                signUpCexCustomer(false, data)
                                    .then((isConnected) => {
                                        if (isConnected) {
                                            disableCEXForm();
                                            jQuery('#cocexUserLoggin').addClass('hidden-block');
                                        } else {
                                            jQuery('#CEXCustomerDataSaveButton').val(editButton.toUpperCase());
                                            customerStatus('CEX', 'off');
                                            jQuery('#cocexUserLoggin').addClass('hidden-block');
                                        }
                                    })
                                    .catch((error) => {
                                        showModalErrorWindow(error);
                                    });
                            } else {
                                showModalErrorWindow(errorMessage.innerText);
                            }
                        } else {
                            enableCEXForm(data ? editButton.toUpperCase() : false);
                        }
                    }
                },
                error: function (e) {
                    showModalErrorWindow('ERROR 10503: ' + customer_technical_error);
                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                },
            });
        },
    }); // Fin Validaciones CEX

    /** Muestra estado del cliente del conectado/no conectado */
    function customerStatus(customer, status) {
        if (status == 'on') {
            jQuery('#' + customer + ' .connected').show();
            jQuery('#' + customer + ' .connected').css('display', 'inline');
            jQuery('#' + customer + ' .noconnected').hide();
        } else {
            jQuery('#' + customer + ' .connected').hide();
            jQuery('#' + customer + ' .noconnected').show();
        }
    }

    function soapIsDisabled() { 
        if (errorMessage && errorMessage.innerText.includes("ERROR 12050")) {
            return false;
        } else {
            return true;
        }
    }

    /* *******************************************************************************************************
     *                                 DATA TABLE
     **********************************************************************************************************/

    // Datatable Clientes
    jQuery('#CustomerDataDataTable').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        ajax: {
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCorreosOficialCustomerDataProcess',
                    action: 'getDataTableCustomerList',
                },
            },
            dataSrc: '',
        },
        language: {
            'url:': co_path_to_module + '/views/js/datatables/Spanish.json',
            emptyTable: noCustomersActive,
        },
        columns: [
            { data: 'id' },
            {
                data: null,
                render: function (data, type, row) {
                    if (row.status == true) {
                        return '<div class="connected-status"><span>' + statusConnected + '</span></div>';
                    } else {
                        return '<div class="noconnected-status"><span>' + statusNotConnected + '</span></div>';
                    }
                },
                orderable: false,
            },
            { data: 'customer_code' },
            { data: 'company' },
            {
                data: null,
                className: 'dt-center editor-edit',
                defaultContent: '<a class="btn btn-primary"><i class="far fa-edit edit"></i></a>',
                orderable: false,
            },
            {
                data: null,
                className: 'dt-center editor-delete',
                defaultContent: '<a class="btn btn-danger"><i class="far fa-trash-alt remove"></i></a>',
                orderable: false,
            },
        ],
        columnDefs: [
            {
                targets: [0],
                visible: false,
            },
        ],
    });

    // Añadir
    jQuery('#add-new-contract').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        logginForm = jQuery('#cocexUserLoggin');

        if(logginForm.hasClass('hidden-block')) {
            logginForm.removeClass('hidden-block');
        } else if (!logginForm.hasClass('hidden-block')) {
            logginForm.addClass('hidden-block');
        }

        const position = jQuery('#customer_data').offset().top;
        animateScroll(position, 500);

       
        disableCorreosForm(false);
        disableCEXForm(false);
    });

    // Modificar
    jQuery('#CustomerDataDataTable').on('click', 'td.editor-edit', function (e) {
        e.preventDefault();

        const position = jQuery('#customer_data').offset().top;
        animateScroll(position, 500);

        // obtenemos la fila del datatable
        customerTable = jQuery('#CustomerDataDataTable').DataTable();
        row = customerTable.row(jQuery(this).parent('tr'));
        var id = customerTable.row(row).data().id;
        jQuery('#cocexUserLoggin').removeClass('hidden-block');

        // Obtenemos datos a editar
        new Promise(function (resolve, reject) {
            jQuery.ajax({
                type: 'post',
                url: varsAjax.ajaxUrl,
                data: {
                    action: 'correosOficialDispacher',
                    _nonce: varsAjax.nonce,
                    dispatcher: {
                        controller: 'AdminCorreosOficialCustomerDataProcess',
                        action: 'getCustomerCode',
                        id: id,
                    },
                },
                success: function (data) {
                    resolve(data);
                },
                error: function (error) {
                    reject(error);
                },
            });
        })
            .then(function (data) {
                var obj = JSON.parse(data);

                if (obj.company == 'Correos') {
                    /** @todo Logeo no deberia ser necesario al pulsar este boton, */
                    /*signUpCorreosCustomer(false, obj.id).then((isConnected) => {
                        if (isConnected) {
                            customerStatus('Correos', 'on');
                        } else {
                            customerStatus('Correos', 'off');
                        }
                    });*/

                    enableCorreosForm();
                    disableCEXForm();
                    jQuery('#CorreosCompany').val(obj.company);
                    jQuery('#idCorreos').val(obj.id);
                    jQuery('#CorreosContract').val(obj.CorreosContract);
                    jQuery('#CorreosCustomer').val(obj.CorreosCustomer);
                    jQuery('#CorreosKey').val(obj.CorreosKey);
                    jQuery('#CorreosOv2Code').val(obj.CorreosOv2Code);
                } else if (obj.company == 'CEX') {
                    /** @todo Logeo no deberia ser necesario al pulsar este boton, */
                    /*signUpCexCustomer(false, obj.id).then((isConnected) => {
                        if (isConnected) {
                            customerStatus('CEX', 'on');
                        } else {
                            customerStatus('CEX', 'off');
                        }
                    });*/

                    enableCEXForm();
                    disableCorreosForm();
                    jQuery('#CEXCompany').val(obj.company);
                    jQuery('#idCEX').val(obj.id);
                    jQuery('#CEXCustomer').val(obj.CEXCustomer);
                    jQuery('#CEXUser').val(obj.CEXUser);
                } else {
                    alert('ERROR CORREOS OFICIAL 10014: No se ha seleccionado ningún cliente');
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    });

    // Eliminar
    jQuery('#CustomerDataDataTable').on('click', 'td.editor-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let deleteAllowed = true;

        customerTable = jQuery('#CustomerDataDataTable').DataTable();
        row = customerTable.row(jQuery(this).parent('tr'));

        var id = customerTable.row(row).data().id;
        customerToBeRemoved = jQuery(this).prev().prev().html();

        // Comprobamos remitentes asociados
        if (!sga_module) {
            let sendersTableData = jQuery('#SendersDataTable').DataTable().ajax.json();
            sendersTableData.forEach(function (sender) {
                if (sender.correos_code == id || sender.cex_code == id) {
                    deleteAllowed = false;
                    return;
                }
            });
        }
        
        // Limpiamos formularios si hemos borrado.
        if (customerToBeRemoved == 'Correos') {
            disableCorreosForm();
        } else if (customerToBeRemoved == 'CEX') {
            disableCEXForm();
        }

        jQuery('#myModal').data('id', id).modal('show');

        if (deleteAllowed) {
            jQuery('#myModalTitle').html(confirmationTitle);
            jQuery('#myModalDescription p').html(wantDeleteCustomer);
            jQuery('#myModalActionButtonCustomerData').html(deleteButton);
            jQuery('#myModal').find('.myModalActionButton').hide();
            jQuery('#myModalCancelButton').show();
            jQuery('#myModalActionButtonCustomerData').show();
        } else {
            jQuery('#myModalTitle').html(errorTitle);
            jQuery('#myModalDescription p').html(customerHaveSender);
            jQuery('#myModal').find('.myModalActionButton').hide();
            jQuery('#myModalCancelButton').show();
        }

        // Cancelar
        /* En back.js */
    });

    // Aceptar
    jQuery('body').on('click', '#myModalActionButtonCustomerData', function (ev) {
        ev.preventDefault();
        ev.stopPropagation();
        var id = jQuery('#myModal').data('id');
        customerTable.row(row).remove().draw();
        jQuery('#myModal').modal('hide');

        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCorreosOficialCustomerDataProcess',
                    action: 'DeleteCustomerCode',
                    CorreosOficialCustomerCode: id,
                },
            },
            success: function (response) {
                if (customerToBeRemoved == 'CEX') {
                    enableCEXForm();
                    disableProducts('CEX');
                    jQuery('#CEXCustomerDataSaveButton').val(addButton);
                    customerStatus('CEX', 'off');
                    jQuery('#CEXCustomerDataForm').find('input[type=text], textarea').val('');
                    reloadSenderContractsSelects();
                } else if (customerToBeRemoved == 'Correos') {
                    enableCorreosForm();
                    disableProducts('Correos');
                    jQuery('#CorreosCustomerDataSaveButton').val(addButton);
                    customerStatus('Correos', 'off');
                    jQuery('#CorreosCustomerDataForm').find('input[type=number], textarea').val('');
                    jQuery('#CorreosCustomerDataForm').find('input[type=text], textarea').val('');
                    jQuery('#CorreosCustomerDataForm').find('input[type=email], textarea').val('');
                    reloadSenderContractsSelects();
                } else {
                    alert('ERROR CORREOS OFICIAL 10015: No se ha seleccionado ningún cliente');
                }
            },
        });
    });

    // Table Draw
    jQuery('#CustomerDataDataTable').on('draw.dt', function () {
        let customerTableData = jQuery('#CustomerDataDataTable').DataTable().ajax.json();

        // Comprobamos productos de correos
        let findCorreos = customerTableData.find(function (code) {
            return code.company === 'Correos';
        });

        if (findCorreos) {
            activeProducts('Correos');
        } else {
            disableProducts('Correos');
        }

        // Comprobamos productos de cex
        let findCEX = customerTableData.find(function (code) {
            return code.company === 'CEX';
        });

        if (findCEX) {
            activeProducts('CEX');
        } else {
            disableProducts('CEX');
        }

        // Show hiden Aviso
        if (!findCorreos && !findCEX) {
            jQuery('#products_container_general').addClass('hidden-block');
            jQuery('#advice_no_products').removeClass('hidden-block');
        } else {
            jQuery('#advice_no_products').addClass('hidden-block');
            jQuery('#products_container_general').removeClass('hidden-block');
        }
    });

    jQuery('#CorreosCustomerDataCancelButton').on('click', function() {
        jQuery('#cocexUserLoggin').addClass('hidden-block');
    });

    jQuery('#CEXCustomerDataCancelButton').on('click', function() {
        document.getElementById('CEXCustomerDataForm').reset();
        jQuery('#cocexUserLoggin').addClass('hidden-block');
    });
});

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                    FUERA DE AMBITO                                   //
//                                                                                      //
//--------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------//
//                                                                                      //
//                                    LOGIN USUARIOS                                    //
//                                                                                      //
//--------------------------------------------------------------------------------------//

// ---- LOGEAR USUARIO CORREOS -----------------------------------------------------------

function signUpCorreosCustomer(pageReady, id_code = null) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCorreosSOAPRequest',
                    action: 'alta_cliente_Correos',
                    codes_id: id_code,
                },
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.message.type == 'success') {
                    if (pageReady == false) {
                        showResponseMessage(obj.message.error_code);
                    }

                    resolve(true);
                } else {
                    showResponseMessage(obj.message.error_code);

                    resolve(false);
                }
            },
            error: function () {
                // Manejar errores de la llamada AJAX
                reject(soapFeatureInstallErrorMessage);
            },
        });
    });
}

// ---- LOGEAR USUARIO CEX ---------------------------------------------------------------

function signUpCexCustomer(pageReady, id_code = null) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCEXRestRequest',
                    action: 'alta_cliente_CEX',
                    codes_id: id_code,
                },
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.message.type == 'success') {
                    if (pageReady == false) {
                        showResponseMessage(obj.message.error_code);
                    }

                    resolve(true);
                } else {
                    showResponseMessage(obj.message.error_code);

                    resolve(false);
                }
            },
            error: function () {
                // Manejar errores de la llamada AJAX
                reject(soapFeatureInstallErrorMessage);
            },
        });
    });
}

// ---- MENSAJE DE RESPUESTA -------------------------------------------------------------

function showResponseMessage(errorCode) {
    switch (errorCode) {
        case '200':
            var title = title200;
            var description = description200;
            showModalInfoWindow(title + ' ' + description);
            // location.reload();
            break;
        case '404':
            var title = title404;
            var description = description404;
            showModalErrorWindow(title + ' ' + description);
            break;
        case '401':
            var title = title401;
            var description = description401;
            showModalErrorWindow(title + ' ' + description);
            break;
        case '999':
            var title = title999;
            var description = description999;
            showModalErrorWindow(title + ' ' + description);
            break;
        default:
            alert(errorCode);
    }
}

// ---- Muestra estado del cliente del conectado/no conectado ----------------------------

function customerStatus(customer, status) {
    if (status == 'on') {
        jQuery('#' + customer + ' .connected').show();
        jQuery('#' + customer + ' .connected').css('display', 'inline');
        jQuery('#' + customer + ' .noconnected').hide();
    } else {
        jQuery('#' + customer + ' .connected').hide();
        jQuery('#' + customer + ' .noconnected').show();
    }
}

// ---- DESHABILITAR FORMULARIO CORREOS --------------------------------------------------

function disableCorreosForm(disabled = true) {
    // Limpiar validaciones
    jQuery('#CorreosCustomerDataForm').validate().resetForm();

    jQuery('#idCorreos').val('');
    jQuery('#CorreosContract').prop('disabled', disabled).val('');
    jQuery('#CorreosCustomer').prop('disabled', disabled).val('');
    jQuery('#CorreosKey').prop('disabled', disabled).val('');
    jQuery('#CorreosUser').prop('disabled', disabled).val('');
    jQuery('#CorreosPassword').prop('disabled', disabled).val('');
    jQuery('#CorreosUser').prop('disabled', disabled).val('');
    jQuery('#CorreosPassword').prop('disabled', disabled).val('');
    jQuery('#CorreosOv2Code').prop('disabled', disabled).val('');
    if (disabled) {
        jQuery('#CorreosCustomerDataSaveButton').attr('disabled');
        jQuery('#CorreosCustomerDataSaveButton').addClass('disabled');
    } else {
        jQuery('#CorreosCustomerDataSaveButton').removeClass('disabled');
    }
    jQuery('#CorreosCustomerDataSaveButton').val(addButton);
    customerStatus('Correos', 'off');
}

// ---- HABILITAR FORMULARIO CORREOS -----------------------------------------------------

function enableCorreosForm() {
    // Limpiar validaciones
    jQuery('#CorreosCustomerDataForm').validate().resetForm();

    jQuery('#CorreosContract').prop('disabled', false);
    jQuery('#CorreosCustomer').prop('disabled', false);
    jQuery('#CorreosKey').prop('disabled', false);
    jQuery('#CorreosUser').prop('disabled', false);
    jQuery('#CorreosPassword').prop('disabled', false);
    jQuery('#CorreosUser').val('');
    jQuery('#CorreosPassword').val('');
    jQuery('#CorreosOv2Code').prop('disabled', false);
    jQuery('#CorreosCustomerDataSaveButton').removeClass('disabled');
    jQuery('#CorreosCustomerDataSaveButton').val(editButton.toUpperCase());
}

// ---- DESHABILITAR FORMULARIO CEX ------------------------------------------------------

function disableCEXForm(disabled = true) {
    // Limpiar validaciones
    jQuery('#CEXCustomerDataForm').validate().resetForm();

    jQuery('#idCEX').val('');
    jQuery('#CEXCustomer').prop('disabled', disabled).val('');
    jQuery('#CEXUser').prop('disabled', disabled).val('');
    jQuery('#CEXPassword').prop('disabled', disabled).val('');
    jQuery('#CEXUser').prop('disabled', disabled).val('');
    jQuery('#CEXPassword').prop('disabled', disabled).val('');
    if (disabled) {
        jQuery('#CEXCustomerDataSaveButton').attr('disabled');
        jQuery('#CEXCustomerDataSaveButton').addClass('disabled');
    } else {
        jQuery('#CEXCustomerDataSaveButton').removeClass('disabled');
    }
    jQuery('#CEXCustomerDataSaveButton').val(addButton);
    customerStatus('CEX', 'off');
}

// ---- HABILITAR FORMULARIO CEX ---------------------------------------------------------

function enableCEXForm() {
    // Limpiar validaciones
    jQuery('#CEXCustomerDataForm').validate().resetForm();

    jQuery('#CEXCustomer').prop('disabled', false);
    jQuery('#CEXUser').prop('disabled', false);
    jQuery('#CEXPassword').prop('disabled', false);
    jQuery('#CEXUser').val('');
    jQuery('#CEXPassword').val('');
    jQuery('#CEXCustomerDataSaveButton').removeClass('disabled');
    jQuery('#CEXCustomerDataSaveButton').val(editButton.toUpperCase());
}

// ---- PRODUCTOS ACTIVOS ----------------------------------------------------------------

function activeProducts(jQuerycompany) {
    if (jQuerycompany == 'Correos') {
        jQuery('#products_container_correos').removeClass('hidden-block');
    } else if (jQuerycompany == 'CEX') {
        jQuery('#products_container_cex').removeClass('hidden-block');
    }
}

// ---- DESHABILITAR PRODUCTOS -----------------------------------------------------------

function disableProducts(jQuerycompany) {
    if (jQuerycompany == 'Correos') {
        jQuery('#products_container_correos').addClass('hidden-block');
    } else if (jQuerycompany == 'CEX') {
        jQuery('#products_container_cex').addClass('hidden-block');
    }
}

// ---- UTILIDADES -----------------------------------------------------------------------

function animateScroll(position, timeSeq) {
    jQuery('html, body').animate(
        {
            scrollTop: position,
        },
        timeSeq
    );
}

function reloadSenderContractsSelects() {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            type: 'post',
            url: varsAjax.ajaxUrl,
            data: {
                action: 'correosOficialDispacher',
                _nonce: varsAjax.nonce,
                dispatcher: {
                    controller: 'AdminCorreosOficialCustomerDataProcess',
                    action: 'getCustomerCodes',
                },
            },
            success: function (data) {
                resolve(data);
            },
            error: function (error) {
                reject(error);
            },
        });
    })
        .then(function (data) {
            let res = JSON.parse(data);

            // Eliminamos las optiones para actualizar las nuevas
            let selectCorreosCode = jQuery('#correos_code');
            selectCorreosCode.find('option[value!=""]').remove();

            res.correos.forEach(function (element) {
                selectCorreosCode.append('<option value="' + element.id + '">' + element.CorreosContract + '/' + element.CorreosCustomer + '</option>');
            });

            // si tenemos resultaos seleciconado el id más pequeño
            if (res.correos.length > 0) {
                selectCorreosCode.val(res.correos[0].id);
            }

            let selectCEXCode = jQuery('#cex_code');
            selectCEXCode.find('option[value!=""]').remove();

            // Añadimos las opciones
            res.cex.forEach(function (element) {
                selectCEXCode.append('<option value="' + element.id + '">' + element.CEXCustomer + '</option>');
            });

            // si tenemos resultaos seleciconado el id más pequeño
            if (res.cex.length > 0) {
                selectCEXCode.val(res.cex[0].id);
            }
        })
        .catch(function (error) {
            console.error(error);
        });
}
