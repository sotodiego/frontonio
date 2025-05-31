
window.unik = $("#unik").val();

document.addEventListener('DOMContentLoaded', function() {
    let action = "calendario";

    $("#filtrar_juego").on("change", function(){
        window.location.href = "?id_actividad=" + $(this).val();
    });

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {

        firstDay: 1,
        height: 'calc(100vh - 128px)',
        navLinks: true,
        nowIndicator: true,
        dayMaxEvents: true,
        defaultTimedEventDuration: '00:00:01', // Duración de 1 segundo
        displayEventTime: false, // Oculta la hora en los eventos

        buttonText: {
            today: 'Hoy',
            dayGridMonth: 'Mes',
            dayGridWeek: 'Semana',
            dayGridDay: 'Día',
        },

        headerToolbar: {
            left: 'dayGridMonth,dayGridWeek,dayGridDay',
            right: 'title',
            center: 'prev,next today'
        },

        locales: 'es',
        initialView: 'dayGridMonth',
        displayEventEnd: true,

        events: function(fetchInfo, successCallback, failureCallback) {
            fetchEvents(fetchInfo.startStr, fetchInfo.endStr, successCallback, failureCallback);
        },

        eventDidMount: function(info) {
            $(info.el).css("background-color", info.backgroundColor);
        },

        moreLinkContent: function(args) {
            return '+' + args.num + ' más';
        },

        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });
    calendar.render();

    function fetchEvents(startStr, endStr, successCallback, failureCallback) {
        let data = {
            id_actividad: "__php__",
            start: startStr,
            end: endStr
        }

        loading("Cargando");
        
        SendAjax(action, 0, data).then(rsp => {
            loading_end();
            if(rsp.r){
                successCallback(rsp.eventos);
            }else{
                alert(rsp.m);
            }
        }).catch(error => {
            loading_end();
            alert(error);
            failureCallback('Hubo un error al cargar los eventos');
        });
    }







    $("body").on('click', '.fc_event_click', function(){
        let clases_reserva = $(this).attr('class').split(' ');
        clases_reserva = (Array.isArray(clases_reserva)) ? clases_reserva.find((el)=> el.includes('_id_reserva-')) : false;

        let id_reserva = clases_reserva.replace("_id_reserva-", "");

        if( !id_reserva ){
            alert('Hubo un error al cargar la información de la reserva.');
            return false;
        }
        let data = {"id_reserva":id_reserva};

        loading('Cargando');
        SendAjax(action, 2, data).then(rsp => {
            loading_end();
            if(rsp.r){
                let modal = modalsp(
                    "Información de la reserva",
                    load_info_reserva(rsp.reserva, rsp.html),
                    () => mostrar_popup_reserva(rsp.reserva.id, rsp.url),
                    "Ver reserva",
                    1,
                );
                modal.open();
            }else{
                alert(rsp.m);
            }
        }).catch(e => {
            loading_end();
            alert('Hubo un error al cargar la información de la reserva.');
            console.log(e);
        });
    });

    function mostrar_popup_reserva(id, url){
        let footer = `<div><button class="btn" style="width:100%;margin-top:24px;" onclick="window.cerrar_sp_popup('info_reserva')">Cerrar reserva</button></div>`;
        window.crear_sp_popup("info_reserva", "Reserva #"+id, `<iframe src="${url}" sandbox="allow-scripts allow-same-origin" referrerpolicy="no-referrer"></iframe>` + footer);
    }

    function load_info_reserva(reserva, html){
        let contenido = $("<div />", {class: 'row'});
        let disabled = {disabled:"disabled"};

        contenido.append(modalspElement(1, "Nombre del cliente", "person", "_nombre_", reserva.nombre , null, 2, {...disabled}));
        contenido.append(modalspElement(1, "Apellidos del cliente", "person", "_apellidos_", reserva.apellidos , null, 2, {...disabled}));
        contenido.append(modalspElement(1, "Correo electrónico", "email", "_email_", reserva.email , null, 2, {...disabled}));
        contenido.append(modalspElement(1, "Teléfono", "phone", "_telefono_", reserva.telefono , null, 2, {...disabled}));

        contenido.append(modalspElement(1, "Fecha", "calendar_month", "_fecha_", formatear_fecha_yyyy_mm_dd_to_dd_mm_yyyy(reserva.fecha) , null, 2, {...disabled}));
        contenido.append(modalspElement(1, "Número de personas", "group", "_numero_personas_", reserva.numero_personas , null, 2, {...disabled}));

        contenido.append(modalspElement(1, "Precio total", "euro", "_precio_total_", reserva.precio_total + "€" , null, 1, {...disabled}));
        contenido.append(modalspElement(1, "Precio señal", "euro", "_precio_senial_", reserva.precio_senial + "€" , null, 2, {...disabled}));
        contenido.append(modalspElement(1, "Precio pago final", "euro", "_precio_pago_final_", reserva.precio_pago_final + "€" , null, 2, {...disabled}));


        contenido.append(modalspElement(1, "Cupón de descuento", "local_activity", "_cupon_descuento", reserva.cupon_descuento , null, 1, {...disabled}));
        contenido.append(modalspElement(1, "Estado", "tune", "_estado_", render_estado(reserva.estado) , null, 1, {...disabled}));


        contenido.append($(`<div class="row"><div class="col s12">${html}</div></div>`));
        
        if(reserva.observaciones_cliente){
            contenido.append($(`<div class="row"><div class="col s12"><p style="margin-top: 40px"><strong>Observaciones (cliente)</strong></p></div></div>`));
            contenido.append($(`<div class="row"><div class="col s12">${reserva.observaciones_cliente}</div></div>`));
        }

        if(reserva.observaciones_internas){
            contenido.append($(`<div class="row"><div class="col s12"><p style="margin-top: 40px"><strong>Observaciones (internas)</strong></p></div></div>`));
            contenido.append($(`<div class="row"><div class="col s12">${reserva.observaciones_internas}</div></div>`));
        }

        return contenido;
    }

    function render_estado(estado){
        let estados = {
            "pendiente_pago": "Pendiente de pagar señal",
            "pendiente_pago_agencia": "Pendiente de pagar completamente",
            "reserva_pagada": "Señal pagada",
            "pago_completo": "Pago completo",
            "cancelada": "Cancelada"
        }

        return estados[estado];
    }


});
