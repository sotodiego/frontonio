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

var section1 = '<div id="woocommerce-history-cex" class="postbox " style=""><div class="postbox-header"><h2 class="hndle ui-sortable-handle">Histórico Envío Correos Express</h2><div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="woocommerce-history-cex-handle-order-higher-description"><span class="screen-reader-text">Subir</span><span class="order-higher-indicator" aria-hidden="true"></span></button><span class="hidden" id="woocommerce-history-cex-handle-order-higher-description">Mover la caja Histórico Envío Correos Express arriba</span><button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="woocommerce-history-cex-handle-order-lower-description"><span class="screen-reader-text">Bajar</span><span class="order-lower-indicator" aria-hidden="true"></span></button><span class="hidden" id="woocommerce-history-cex-handle-order-lower-description">Mover la caja Histórico Envío Correos Express abajo</span><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Alternar panel: Histórico Envío Correos Express</span><span class="toggle-indicator" aria-hidden="true"></span></button></div></div><div class="inside"><input type="hidden" id="cex-nonce" name="cex-nonce" value="6d387c9610"><input type="hidden" name="_wp_http_referer" value="/WP_Sprint38/wp-admin/post.php?post=21&amp;action=edit"><div id="CEX"><div id="history" class="row mt-3 mx-1"><div class="col-12 col-md-12 col-lg-12 p-0 rounded CEX-overflow-y-hidden">';
var tabla = '<table id="tabla_historico" border="1" class="table w-100"><thead><tr><th>Seguimiento</th><th>Fecha</th><th>Ref.Pedido</th><th>Tipo</th><th>Identificador</th><th>Recogida desde</th><th>Fecha de Recogida</th><th>Hora Recogida desde</th><th>Hora Recogida hasta</th><th>Estado</th><th>Acciones</th></tr></thead><tbody><tr><td><a href="#"">CorreosExpress</a></td><td>aaaa-mm-dd</td><td>0</td><td>Envio</td><td>0000000000000000</td><td>Sede Recogida</td><td></td><td></td><td></td><td>Grabado</td><td><a href="#"><i class="fa fa-trash"></i></a></td></tr><tr><td><a href="#">CorreosExpress</a></td><td>aaaa-mm-dd</td><td>0</td><td>Recogida</td><td>Automatica</td><td>Sede Recogida</td><td></td><td></td><td></td><td>Grabado</td><td><a href="#""><i class="fa fa-trash"></i></a></td></tr></tbody><tfoot><tr><th>Seguimiento</th><th>Fecha</th><th>Ref.Pedido</th><th>Tipo</th><th>Identificador</th><th>Recogida desde</th><th>Fecha de Recogida</th><th>Hora Recogida desde</th><th>Hora Recogida hasta</th><th>Estado</th><th>Acciones</th></tr></tfoot></table>';
var section2 = '</div></div>';
var section = section1+tabla+section2;
//var historico = jQuery("#woocommerce-history-cex").length;

function changeCustomOptions(intro){   
    intro.setOption('nextLabel', '<i class="fas fa-chevron-right"></i>');
    intro.setOption('prevLabel', '<i class="fas fa-chevron-left"></i>');
    intro.setOption('skipLabel', '<i class="fas fa-times"></i>');
    intro.setOption('doneLabel', '<i class="fas fa-check"></i>');
}


function introjsOrder(){ 
    if ((jQuery)("#select_etiqueta").val()=='3') {
        (jQuery)("#introjsPosicionEtiquetas").removeClass('d-none');
    }   

    if(historico == 0){
        jQuery("#advanced-sortables").append(section);
    }

    if((jQuery)('#select_paises').val() != "PT" && (jQuery)('#select_paisrte').val() != "PT"){
        (jQuery)('#introjsAtPortugal').removeClass("d-none");
    }

    var intro = introJs();
    changeCustomOptions(intro);
    intro.setOptions({
        steps: [
        { 
            element: document.querySelector('#introjsFormRemitente'),
            intro: introjsFormRemitente
        },
        { 
            element: document.querySelector('#introjsCopiarRemitente'),
            intro: introjsCopiarRemitente
        },
        { 
            element: document.querySelector('#introjsRemitente'),
            intro: introjsRemitente
        },
        { 
            element: document.querySelector('#introjsValoresRemitente'),
            intro: introjsValoresRemitente
        },
        { 
            element: document.querySelector('#introjsObservacionesRemitente'),
            intro: introjsObservacionesRemitente
        },
        {
            element: document.querySelector('#introjsFormDestinatario'),
            intro: introjsFormDestinatario
        },
        {
            element: document.querySelector('#introjsDevolucion'),
            intro: introjsDevolucion
        },
        {
            element: document.querySelector('#introjsValoresDestinatario'),
            intro: introjsValoresDestinatario
        },
        {
            element: document.querySelector('#introjsObservacionesEntrega'),
            intro: introjsObservacionesEntrega
        },
        {
            element: document.querySelector('#introjsFormExtra'),
            intro: introjsFormExtra
        },
        {
            element: document.querySelector('#introjsCodCliente'),
            intro: introjsCodCliente
        },
        {
            element: document.querySelector('#introjsRefEnvio'),
            intro: introjsRefEnvio
        },
        {
            element: document.querySelector('#introjsAtPortugal'),
            intro: introjsAtPortugal
        },
        {
            element: document.querySelector('#introjsFechaEntrega'),
            intro: introjsFechaEntrega
        },
        {
            element: document.querySelector('#introjsHHMM'),
            intro: introjsHHMM
        },
        {
            element: document.querySelector('#introjsBultosKilos'),
            intro: introjsBultosKilos
        },
       /* {
            element: document.querySelector('#introjsPaisDestino'),
            intro: introjsPaisDestino
        },*/
        {
            element: document.querySelector('#introjsContrareembolso'),
            intro: introjsContrareembolso
        },
        {
            element: document.querySelector('#introjsValorContrareembolso'),
            intro: introjsValorContrareembolso
        },
        {
            element: document.querySelector('#introjsValorAsegurado'),
            intro: introjsValorAsegurado
        },
        {
            element: document.querySelector('#introjsModalidadEnvio'),
            intro: introjsModalidadEnvio
        },
        {
            element: document.querySelector('#introjsTipoEtiquetas'),
            intro: introjsTipoEtiquetas
        },
        {
            element: document.querySelector('#introjsPosicionEtiquetas'),
            intro: introjsPosicionEtiquetas
        },
        {
            element: document.querySelector('#grabar_recogida'),
            intro: grabar_recogida
        },
        {
            element: document.querySelector('#grabar_envio'),
            intro: grabar_envio
        },
        {
            element: document.querySelector('#tabla_historico'),
            intro: tabla_historico
        }]
    });
    intro.start();
    (jQuery)('.introjs-skipbutton').hide();
}

function checkIntroJS(){
    if( (jQuery)('#toggleOrderIntroJS').prop('checked') ) {        
        (jQuery)("#manualInteractivoOrder").removeClass('d-none');
    }else{
        (jQuery)("#manualInteractivoOrder").addClass('d-none');

    }
}