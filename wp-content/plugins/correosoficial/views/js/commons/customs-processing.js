jQuery.validator.addMethod(
    'TariffLength',
    function (element) {
        return element.length == 6 || element.length == 8 || element.length == 10 ? true : false;
    },
    jQuery.validator.format('Input data must be 6, 8 or 10 characters long')
);

jQuery(document).ready(function ($) {
    /* Validaciones */
    $('#CustomProcessingForm').validate({
        rules: {
            ShippCustomsReference: {
                required: false,
                minlength: 3,
                maxlength: 50,
            },
            MessageToWarnBuyer: {
                required: false,
            },
            TranslatableInput: {
                required: true,
                minlength: 3,
                maxlength: 100,
            },
            TariffDescription: {
                required: true,
                minlength: 3,
                maxlength: 100,
            },
            Tariff: {
                required: true,
                TariffLength: true,
            },
        },
        messages: {
            ShippCustomsReference: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 50 ' + characters,
            },
            TranslatableInput: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 100 ' + characters,
            },
            TariffDescription: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 100 ' + characters,
            },
            Tariff: {
                required: requiredCustomMessage,
                TariffLength: tariffLength,
            },
        }, // Fin Validaciones

        submitHandler: function () {
            /** Procesamos el formulario de Aduanas*/
            var formElement = document.getElementById('CustomProcessingForm');

            $.ajax({
                url: AdminCorreosOficialCustomsProcessingProcess,
                type: 'POST',
                data: new FormData(formElement),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    showModalInfoWindow(customsProcessingSaved);
                },
                error: function (e) {
                    alert('ERROR 13000: Error al enviar el formulario Tramitación Aduanera de Envíos');
                },
            });
        },
    });

    // Almacenamos las cadenas de idioma en este objeto.
    var languagesObject;
    // Convertimos la cadena JSON de la bbdd a un objeto JSON (Javascript Object)
    var jsonstring = $('#TranslatableInputH').val();
    languagesObject = $.parseJSON(jsonstring);

    // Al salir del selector actualizamos el input de TranslatableInput
    $('#FormSwitchLanguage').change(function (e) {
        // Recuperamos el id del idioma y lo convertimos a String
        var language_id = $('#FormSwitchLanguage :selected').val();
        var i = language_id.toString();

        $('#TranslatableInput').val(languagesObject[i]);
    });

    // Actualizamos idiomas en el front.
    $('#TranslatableInput').blur(function (e) {
        // Id del lenguaje seleccionado
        var language_id = $('#FormSwitchLanguage :selected').val();
        // Convertimos el id lenguage a string para poder utilizarlo como índice.
        var i = language_id.toString();

        languagesObject[i] = $('#TranslatableInput').val();
        $('#TranslatableInputH').val(JSON.stringify(languagesObject));
        // Añadimos ventana modal informativa.
        /* showModalInfoWindow('El cambio solo se se hará efectivo al guardar'); */
    });

    if ($("input[type='radio'][id='DescriptionRadio']:checked").val()) {
        $('#Tariff').attr('disabled', true);
        $('#Tariff').css('opacity', '.5');
        $('#TariffDescription').attr('disabled', true);
        $('#TariffDescription').css('opacity', '.5');

        $('#DefaultCustomsDescription').attr('disabled', false);
        $('#DefaultCustomsDescription').css('opacity', '1');

        $('#TariffDescription').removeClass('error');
        $('#TariffDescription-error').hide();
    } else if ($("input[type='radio'][id='TariffRadio']:checked").val()) {
        $('#Tariff').attr('disabled', false);
        $('#Tariff').css('opacity', '1');
        $('#TariffDescription').attr('disabled', false);
        $('#TariffDescription').css('opacity', '1');

        $('#DefaultCustomsDescription').attr('disabled', true);
        $('#DefaultCustomsDescription').css('opacity', '.5');
    }

    /** Ocultamos-Mostramos los radios de TRAMITACIÓN ADUANERA*/
    $('Form input:radio').change(function () {
        if ($(this).val() == '0') {
            $('#Tariff').attr('disabled', true);
            $('#Tariff').css('opacity', '.5');
            $('#TariffDescription').attr('disabled', true);
            $('#TariffDescription').css('opacity', '.5');

            $('#DefaultCustomsDescription').attr('disabled', false);
            $('#DefaultCustomsDescription').css('opacity', '1');

            $('#TariffDescription').removeClass('error');
            $('#TariffDescription-error').hide();
        } else if ($(this).val() == '1') {
            $('#Tariff').attr('disabled', false);
            $('#Tariff').css('opacity', '1');
            $('#TariffDescription').attr('disabled', false);
            $('#TariffDescription').css('opacity', '1');

            $('#DefaultCustomsDescription').attr('disabled', true);
            $('#DefaultCustomsDescription').css('opacity', '.5');
        }
    });

    /**
     * Tabs de documentación aduanera
     */
    var addingDesc = true;
    var addingTarriffCode = false;

    jQuery('.nav-link').on('click', function (event) {
        event.preventDefault();
        jQuery(this).addClass('active');

        if (jQuery(this).attr('data-type') == 'customs_desc') {
            addingDesc = true;
            addingTarriffCode = false;
            showCustomsDesc();
            jQuery('#DescriptionRadio').prop('checked', true);
            jQuery('#TariffRadio').prop('checked', false);
        } else if (jQuery(this).attr('data-type') == 'customs_code') {
            addingDesc = false;
            addingTarriffCode = true;
            showCustomsCode();
            jQuery('#TariffRadio').prop('checked', true);
            jQuery('#DescriptionRadio').prop('checked', false);
        }
    });

    function showCustomsDesc() {
        jQuery('#customs_desc_tab').removeClass('hidden-block');
        jQuery('#customs_code_tab').addClass('hidden-block');
        jQuery('#customs_code').removeClass('active');
    }

    function showCustomsCode() {
        jQuery('#customs_desc_tab').addClass('hidden-block');
        jQuery('#customs_code_tab').removeClass('hidden-block');
        jQuery('#customs_desc').removeClass('active');
    }
});
