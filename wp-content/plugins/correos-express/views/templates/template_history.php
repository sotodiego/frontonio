<?php 
$CEX=new Correosexpress();
$CEX->CEX_styles_datatable();?>
<div id="CEX">
<div id="history" class="row mt-3 mx-1">
    <div class="col-12 col-md-12 col-lg-12 p-0 rounded CEX-overflow-y-hidden">
        <table id="tabla_historico" border="1" class="table w-100">
        </table>
    </div>
</div>
<div id="modificar_recogida" class="cexmodal d-none">
    <div id="modal_modificar_recogida" class="cexmodal-content CEX-background-bluelight2 CEX-text-blue rounded"  style="overflow-y:hidden;">
        <span class="cexclose" onclick="cerrarModalEditarEnvios(event);">&times;</span>
        <h3 class="mt-1 mb-4 CEX-text-blue">{l s='Modificar Recogida/Envio' mod='correosexpress'}</h3>
        <div class="row">            
            <div class="col-4 col-md-4 col-lg-4 form-group">
                <label for="codigo_postal_ofi">{l s='Fecha Recogida' mod='correosexpress'}</label>
                <input type="date" class="form-control" name="codigo_postal_ofi" id="codigo_postal_ofi">
            </div>
            <div class="col-4 col-md-4 col-lg-4 form-group">
                <label for="poblacion_ofi">{l s='Desde:' mod='correosexpress'}</label>
                <input type="time" class="form-control" name="poblacion_ofi" id="poblacion_ofi">
            </div>
            <div class="col-4 col-md-4 col-lg-4 form-group">
                <label for="poblacion">{l s='Hasta:' mod='correosexpress'}</label>
                <input type="time" class="form-control" name="poblacion_ofi" id="poblacion_ofi">
            </div>
            <input type="hidden" class="form-control" name="numcollect" id="numcollect_modif_recog">
            <input type="hidden" class="form-control" name="numship" id="numship_modif_recog">
        </div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <button class="CEX-btn CEX-button-red" name="buscar_oficina" value="" onclick="enviarModificarPeticionCex(event)">
                    {l s='Modificar recogida' mod='correosexpress'}
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<?php $CEX->CEX_scripts_datatable();?>
 
<script type="text/javascript">

(jQuery)('#woocommerce-history-cex').hide();
(jQuery)('#woocommerce-history-cex-hide').hide();
(jQuery)('label[for="woocommerce-history-cex-hide"]').hide();

(jQuery)(document).ready(function($) {
    inicio();          
});

function inicio() {
    var body = (jQuery)("body");

    (jQuery).ajax({
        type: "POST",
        url: 'admin-ajax.php',
        data: {
            'action': 'cex_retornar_savedships_orden_id',
            'id': getQueryVariable('post'),
            'nonce': (jQuery)('#cex-nonce').val(),
        },
        success: function(msg) {
            pintarTablaHistorico(msg);
        },
        error: function(msg) {
        }
    });
}

function pintarTablaHistorico(msg) {
    var retorno = JSON.parse(msg);

    if (retorno != 'undefined' && retorno != null){
        (jQuery)('#tabla_historico').html(retorno);
        //declareDataTablePedido('tabla_historico');
    }
}

function modificarPeticionCex(numship, numcollect){
        (jQuery)('#modificar_recogida').removeClass('d-none');
        (jQuery)('html,body').animate({
                scrollTop: (jQuery)("#modal_modificar_recogida").offset().top-20
            }, 'slow');

        (jQuery)('#numship_modif_recog').val(numship);
        (jQuery)('#numcollect_modif_recog').val(numcollect);
    }

function cerrarModalEditarEnvios(){
        (jQuery)('#modificar_recogida').addClass("d-none");
    }

var myStack = {
        "dir1": "down",
        "dir2": "right",
        "push": "top"
};

    function borrarPeticionEnvio(numship,numcollect,event) {
        event.preventDefault();
        PNotify.prototype.options.styling = "bootstrap3";
            new PNotify({
                title: "<?php esc_html_e('Confirma la operación', 'cex_pluggin');?>",
                text: "<?php esc_html_e('¿Borrar datos?', 'cex_pluggin');?>",
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                stack: myStack,
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                }
            }).get().on('pnotify.confirm', function(){
                //(jQuery)('#CEX-loading').removeClass("d-none");
                (jQuery).ajax({
                    type: "POST",
                    url: 'admin-ajax.php',
                    data: {
                        'action': 'cex_form_pedido_borrar',
                        'numship': numship,
                        'numcollect': numcollect, 
                        'nonce': (jQuery)('#cex-nonce').val(),
                    },
                    success: function(msg) {                    
                        pintarNotificacionBorrado(msg);
                        location.reload();
                    },
                    error: function(msg) {
                    }
                    });
            }).on('pnotify.cancel', function(){
        });        
    }

    function enviarModificarPeticionCex(event){
        event.preventDefault();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php', 
            data:
            {
                'action'        :'cexFormPedidoModificar',
                'numship'       : $('#numship_modif_recog').val(),
                'numcollect'    : $('#numcollect_modif_recog').val(),
                'idiomaContexto':"{$lang_iso}",
                'token'         :'',
            },
            success: function(msg){
                pintarNotificacionBorrado(msg);
                //quitar esto y recaragar datatable
                location.reload();
            },
            error: function(msg){

            }
        });
    }

function pintarNotificacionBorrado(msg) {    
    var mensaje = JSON.parse(msg);
    PNotify.prototype.options.styling = "bootstrap3";
    new PNotify({
        title: "<?php esc_html_e('Borrado' , 'cex_pluggin');?>",
        text: mensaje.mensaje,
        type: 'success',
        stack: myStack
    })
}

function declareDataTablePedido(dataTable){
   
        var table = (jQuery)('#'+dataTable).DataTable({
            dom: 'Bfrtlip',
            responsive:true,
            language:{
                "sProcessing":     "<?php esc_html_e('Procesando...' , 'cex_pluggin');?>",
                "sLengthMenu":     "<?php esc_html_e('Mostrar _MENU_ registros' , 'cex_pluggin');?>",
                "sZeroRecords":    "<?php esc_html_e('No se encontraron resultados' , 'cex_pluggin');?>",
                "sEmptyTable":     "<?php esc_html_e('Ningún dato disponible en esta tabla' , 'cex_pluggin');?>",
                "sInfo":           "<?php esc_html_e('Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros' , 'cex_pluggin');?>",
                "sInfoEmpty":      "<?php esc_html_e('Mostrando registros del 0 al 0 de un total de 0 registros' , 'cex_pluggin');?>",
                "sInfoFiltered":   "<?php esc_html_e('(filtrado de un total de _MAX_ registros)' , 'cex_pluggin');?>",
                "sInfoPostFix":    "",
                "sSearch":         "<?php esc_html_e('Buscar:' , 'cex_pluggin');?>",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "<?php esc_html_e('Cargando...' , 'cex_pluggin');?>",
                "oPaginate": {
                    "sFirst":    "<?php esc_html_e('Primero' , 'cex_pluggin');?>",
                    "sLast":     "<?php esc_html_e('Último' , 'cex_pluggin');?>",
                    "sNext":     "<?php esc_html_e('Siguiente' , 'cex_pluggin');?>",
                    "sPrevious": "<?php esc_html_e('Anterior' , 'cex_pluggin');?>"
                },
                "oAria": {
                    "sSortAscending":  "<?php esc_html_e(': Activar para ordenar la columna de manera ascendente' , 'cex_pluggin');?>",
                    "sSortDescending": "<?php esc_html_e(': Activar para ordenar la columna de manera descendente' , 'cex_pluggin');?>"
                }
            },
            buttons:[
                {
                    extend: 'colvis',
                    text: "<?php esc_html_e('Mostrar/Ocultar columnas' , 'cex_pluggin');?>",
                },
                {
                    extend: 'excelHtml5',
                    text: "<?php esc_html_e('Exportar a excel' , 'cex_pluggin');?>",
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: "<?php esc_html_e('Exportar a csv' , 'cex_pluggin');?>",
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: "<?php esc_html_e('Exportar a pdf' , 'cex_pluggin');?>",
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });
        (jQuery)('#'+dataTable+' tfoot th').each( function (index,value) {
            var title = (jQuery)(this).text();
            (jQuery)(this).html( '<button class="CEX-btn CEX-button-red w-100 activarBuscador" id="activarBuscador'+index+'"><i class="fa fa-search w-100"></i></button><input id="inputBuscador'+index+'" type="text" class="form-control w-100 d-none inputBuscador" />' );
        });
        var data = table.buttons.exportData( {
            columns: ':visible'
        });
        table.columns().every( function () {
            var that = this; 
            (jQuery)( 'input', this.footer() ).on( 'keyup change', function () {                
                if(this.value!=''){
                    if ( that.search() !== this.value ) {
                            that.search( this.value ).draw();
                    }
                }else{
                    (jQuery)(this).addClass('d-none');
                    (jQuery)(this).parent().find('.activarBuscador').removeClass('d-none');
                    that.search( this.value ).draw();
                }
            });
        });
        (jQuery)('.activarBuscador').click(function(event) {  
            event.preventDefault(); 
            event.stopPropagation(); 
            (jQuery)(this).addClass('d-none');
            (jQuery)(this).parent().find('.inputBuscador').removeClass('d-none');        
                 
        });      
        (jQuery)('.inputBuscador').click(function(event) {  
            event.stopPropagation();                      
        });
        
        (jQuery)('html').click( function(event) { 
            //event.preventDefault(); 
            if((jQuery)('.activarBuscador').length>0){
                (jQuery)('.activarBuscador').each(function(index,value){
                    var input=(jQuery)(this).parent().find('.inputBuscador');
                    if(input.val()==''){                        
                        (jQuery)(this).parent().find('.activarBuscador').removeClass('d-none'); 
                        input.addClass('d-none');
                    }
                });
            }            
        });   
   
}
</script>
