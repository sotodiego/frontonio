/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    CorreosExpress/Departamento de integracion y desarrollo
 *  @copyright 2015-2018 Correos Express - Grupo Correos
 *  @license   LICENSE.txt
 *  @email peticiones@correosexpress.com
 */

/*
    SECCION GRABACION MASIVA
 */
function changeCustomOptions(intro){   
    intro.setOption('nextLabel', '<i class="fas fa-chevron-right"></i>');
    intro.setOption('prevLabel', '<i class="fas fa-chevron-left"></i>');
    intro.setOption('skipLabel', '<i class="fas fa-times"></i>');
    intro.setOption('doneLabel', '<i class="fas fa-check"></i>');
}

function introjsTourGrabacion(){  
   //(jQuery)("#fila1").removeClass('d-none');
   //(jQuery)("#fila2").removeClass('d-none');
   (jQuery)("#contenedor_generar_envio").removeClass('d-none');
   (jQuery)("#contenedor_respuesta_buscador_pedidos").removeClass('d-none'); 
   (jQuery)("#contenedor_etiquetas_grabacion").removeClass('d-none'); 
   (jQuery)("#respuesta_buscador_pedidos").removeClass('d-none');     
   (jQuery)("#posicion_etiqueta_masiva").removeClass('d-none');
   (jQuery)("#introjsPosicionEtiqueta2").removeClass('d-none');

    


   var cabecera = "<table style='width:100%' border=1>"+
   "<thead><tr>"+
   "<th>"+idTablaGrab+"</th>"+
   "<th>"+refEnvioTablaGrab+"</th>"+
   "<th>"+estadoTablaGrab+"</th>"+
   "<th>"+clienteTablaGrab+"</th>"+
   "<th>"+fechaTablaGrab+"</th>"+
   "<th>"+numEnvioTablaGrab+"</th>"+
   "<th>"+codOficinaTablaGrab+"</th>"+
   "<th>"+bultosTablaGrab+"</th>"+
   "</tr></thead>";
    var cierre = "</table>";
    var elementos = '';

    (jQuery)('#respuesta_buscador_pedidos').html(cabecera+elementos+cierre);
    setTimeout('introjsTourGrabacionStart();',100);
}

function introjsTourGrabacionStart(){  
    var intro = introJs();
    changeCustomOptions(intro);
    intro.setOptions({
        steps: [
        { 
            element: document.querySelector('#introjsGrabacionMasiva'),
            intro: introjsGrabacionMasiva
        },       
        { 
            element: document.querySelector('#fila1'),
            intro: fila1
        },
        { 
            element: document.querySelector('#fila2'),
            intro: fila2
        },
        { 
            element: document.querySelector('#respuesta_buscador_pedidos'),
            intro: respuesta_buscador_pedidos
        },
        { 
            element: document.querySelector('#introjsSeleccionarTodos1'),
            intro: introjsSeleccionarTodos1
        },
        {
            element: document.querySelector('#introjsTipoEtiqueta'),
            intro: introjsTipoEtiqueta
        },
        {
            element: document.querySelector('#introjsPosicionEtiqueta2'),
            intro: introjsPosicionEtiqueta
        },
        {
            element: document.querySelector('#introjsGenerarGrabacionEnvio'),
            intro: introjsGenerarGrabacionEnvio
        }]
    });
    intro.start();

    intro.oncomplete(function(){
       //(jQuery)("#fila1").addClass('d-none');
       //(jQuery)("#fila2").addClass('d-none');
       (jQuery)("#contenedor_generar_envio").addClass('d-none');        
       (jQuery)("#contenedor_etiquetas_grabacion").addClass('d-none');
       (jQuery)("#respuesta_buscador_pedidos").addClass('d-none');
       (jQuery)("#contenedor_respuesta_buscador_pedidos").addClass('d-none');
       
    });

    // clicking 'Skip'
    intro.onexit(function(){
       //(jQuery)("#fila1").addClass('d-none');
       //(jQuery)("#fila2").addClass('d-none');
       (jQuery)("#contenedor_generar_envio").addClass('d-none');        
       (jQuery)("#contenedor_etiquetas_grabacion").addClass('d-none');
       (jQuery)("#respuesta_buscador_pedidos").addClass('d-none');
       (jQuery)("#contenedor_respuesta_buscador_pedidos").addClass('d-none');

    });
}

function checkGrabacionIntroJS(){
    if( (jQuery)('#toggleGrabacionIntroJS').prop('checked') ) {
       (jQuery)("#manualInteractivoGrabacion").disabled = false;
       (jQuery)("#manualInteractivoGrabacion").removeClass('d-none');
    }else{
       (jQuery)("#manualInteractivoGrabacion").disabled = true;
       (jQuery)("#manualInteractivoGrabacion").addClass('d-none');

    }
}

/*
    SECCION REIMPRESION DE ETIQUETAS
 */
function introjsTourReimpresion(){  

    (jQuery)('#contenedor_pedidos').removeClass('d-none');
    (jQuery)("#contenedor_pedidos_reimpresion").removeClass('d-none');
    (jQuery)("#contenedor_etiquetas_reimpresion").removeClass('d-none');
    (jQuery)('#introjsPosicionEtiqueta2').removeClass('d-none');
    (jQuery)("#introjsPosicionEtiqueta").removeClass('d-none');

    var cabecera = "<table style='width:100%' border=1>"+
        "<thead><tr>"+
        "<th>"+idTabla+"</th>"+
        "<th>"+refEnvioTabla+"</th>"+
        "<th>"+codEnvioTabla+"</th>"+
        "<th>"+nomDestinatarioTabla+"</th>"+
        "<th>"+dirDestinatarioTabla+"</th>"+
        "<th>"+fechaCreacionTabla+"</th>"+
        "</tr></thead>";
    var cierre = "</table>";
    var elementos = '';
    
    (jQuery)('#contenedor_pedidos_reimpresion').html(cabecera+elementos+cierre);
    setTimeout('introjsTourReimpresionStart();',100);
}

function introjsTourReimpresionStart(){  
    var intro = introJs();
    changeCustomOptions(intro);
    intro.setOptions({
        steps: [
        {
            element: document.querySelector('#introjsReimpresion'),
            intro: introjsReimpresion
        },
        {
            element: document.querySelector('#fecha'),
            intro: fecha
        },
        {
            element: document.querySelector('#contenedor_pedidos'),
            intro: contenedor_pedidos
        },
        { 
            element: document.querySelector('#introjsSeleccionarTodos2'),
            intro: introjsSeleccionarTodos2
        },
        { 
            element: document.querySelector('#select_tipo_etiqueta'),
            intro: select_tipo_etiqueta
        },
        {
            element: document.querySelector('#posicion_etiquetas'),
            intro: posicion_etiquetas
        },
        {
            element: document.querySelector('#grabar_etiqueta'),
            intro: grabar_etiqueta
        }]
    });

    intro.start();
    
    intro.oncomplete(function(){
        (jQuery)('#contenedor_pedidos').addClass('d-none');
        (jQuery)('#contenedor_pedidos_reimpresion').addClass('d-none');
        (jQuery)("#contenedor_etiquetas_reimpresion").addClass('d-none');
        (jQuery)('#introjsPosicionEtiqueta2').addClass('d-none');
    });

    intro.onexit(function(){
        (jQuery)('#contenedor_pedidos').addClass('d-none');
        (jQuery)('#contenedor_pedidos_reimpresion').addClass('d-none');
        (jQuery)("#contenedor_etiquetas_reimpresion").addClass('d-none');
        (jQuery)('#introjsPosicionEtiqueta2').addClass('d-none');
    });
}

function checkReimpresionIntroJS(){
    if( (jQuery)('#toggleReimpresionIntroJS').prop('checked') ) {
        (jQuery)('#toggleReimpresionIntroJS').addClass('before');
       (jQuery)("#manualInteractivoReimpresion").disabled = false;
       (jQuery)("#manualInteractivoReimpresion").removeClass('d-none');
    }else{
        (jQuery)('#toggleReimpresionIntroJS:before').css('display','none');
       (jQuery)("#manualInteractivoReimpresion").disabled = true;
       (jQuery)("#manualInteractivoReimpresion").addClass('d-none');

    }
}

/*
    GENERACION DE RESUMEN DE PEDIDOS
 */
function introjsTourResumen(){  

    (jQuery)("#contenedor_resumen").removeClass('d-none');
    (jQuery)("#contenedor_resumen_pedidos").removeClass('d-none');
    (jQuery)("#opcionesResumen").removeClass('d-none');

    var cabecera = "<table style='width:100%' border=1>"+
        "<thead><tr>"+
        "<th>"+idTabla+"</th>"+
        "<th>"+refEnvioTabla+"</th>"+
        "<th>"+codEnvioTabla+"</th>"+
        "<th>"+nomDestinatarioTabla+"</th>"+
        "<th>"+dirDestinatarioTabla+"</th>"+
        "<th>"+fechaCreacionTabla+"</th>"+
        "</tr></thead>";
    var cierre = "</table>";
    var elementos = '';

   (jQuery)('#contenedor_resumen_pedidos').html(cabecera+elementos+cierre);

    setTimeout('introjsTourResumenStart();',100);
}

function introjsTourResumenStart(){  
    var intro = introJs();
    changeCustomOptions(intro);
    intro.setOptions({
        steps: [
        {
            element: document.querySelector('#introjsResumen'),
            intro: introjsResumen
        },
        {
            element: document.querySelector('#fecha_resumen'),
            intro: fecha_resumen
        },
        {
            element: document.querySelector('#contenedor_resumen'),
            intro: contenedor_resumen
        },
        { 
            element: document.querySelector('#marcarResumen'),
            intro: marcarResumen
        },
        { 
            element: document.querySelector('#boton_resumen'),
            intro: boton_resumen
        }]
    });

    intro.start();

        intro.oncomplete(function(){
           (jQuery)("#contenedor_resumen").addClass('d-none');     
           (jQuery)("#contenedor_resumen_pedido").addClass('d-none');  
           (jQuery)("#opcionesResumen").addClass('d-none');    
             
        });
    
         intro.onexit(function(){
           (jQuery)("#contenedor_resumen").addClass('d-none');
           (jQuery)("#contenedor_resumen_pedido").addClass('d-none');
           (jQuery)("#opcionesResumen").addClass('d-none');
        });
    }
    
    function checkResumenIntroJS(){
        if( (jQuery)('#toggleResumenIntroJS').prop('checked') ) {
            (jQuery)('#toggleResumenIntroJS').addClass('before');
           (jQuery)("#manualInteractivoResumen").disabled = false;
           (jQuery)("#manualInteractivoResumen").removeClass('d-none');
        }else{
            (jQuery)('#toggleResumenIntroJS:before').css('display','none');
           (jQuery)("#manualInteractivoResumen").disabled = true;
           (jQuery)("#manualInteractivoResumen").addClass('d-none');
    
        }
    }
