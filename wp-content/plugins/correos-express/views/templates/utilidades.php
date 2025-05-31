<?php if (current_user_can('manage_woocommerce')) : ?>
<?php
    /*******************VARIABLES DE LA PAGINA ******************/
    $prefijo = 'cex_';
    $nonce = wp_create_nonce('mi_nonce_de_seguridad');
    $CEX=new Correosexpress();

    /********************** FIN VARIABLES ************************/

    /*********************FIN RECEPCION FORMULARIO ******************/
    $this->CEX_styles();
    $this->CEX_stylesBootstrap();    
    $this->CEX_styles_datepicker();  
    $this->CEX_scripts_datepicker();  
    $this->CEX_styles_datatable();    
?>

<div id="CEX">
    <div id="utilidades" class="CEX-container CEX-panel py-5 px-3 container pr-sm-5">
        <?php //<div id="resultados" class='notice' style=""></div>?>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="row mb-1 d-flex align-items-center">
                    <!--Inicio Cabecera menu opciones-->
                    <div class="col-6 col-sm-6 my-auto">
                        <h2 class="CEX-text-blue"><?php esc_html_e('Utilidades', 'cex_pluggin');?></h2>                        
                    </div>
                    <div class="col-6 col-sm-6 text-right my-auto">
                        <img class="img-fluid w-50"
                            src="<?php echo esc_url(plugins_url('/../img/logo-correosexpress-nuevo.png',__FILE__));?>">
                    </div>
                </div>
                <div class="row mb-5 d-flex align-items- center">
                    <div id="Manuales" class="col-12 col-sm-12 my-auto">
                        <div id="grabar_enviosManual" class="CEX-manual">
                            <fieldset class="rounded-2 CEX-background-white border CEX-border-blue px-3">
                                <legend
                                    class="p-2 ml-2 CEX-background-blue CEX-text-white rounded-2 w-auto border-0 mb-3">
                                    <?php esc_html_e('Manual interactivo Grabaci&oacute;n masiva', 'cex_pluggin');?>
                                </legend>
                                <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
                                    <input id="toggleGrabacionIntroJS" type="checkbox" class="form-control mt-1 my-auto"
                                        onchange="checkGrabacionIntroJS();">
                                    <label for="toggleGrabacionIntroJS"
                                        class="m-0 my-auto mr-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                    <button id="manualInteractivoGrabacion"
                                        class="CEX-btn btn-large CEX-button-info my-auto d-none"
                                        href="javascript:void(0)" onclick="introjsTourGrabacion();">
                                        <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                    </button>
                                </div>
                            </fieldset>
                        </div>
                        <div id="cex_generar_etiquetasManual" class="CEX-manual d-none">
                            <fieldset class="rounded-2 CEX-background-white border CEX-border-blue px-3">
                                <legend
                                    class="p-2 ml-2 CEX-background-blue CEX-text-white rounded-2 w-auto border-0 mb-3">
                                    <?php esc_html_e('Manual interactivo Reimpresi&oacute;n', 'cex_pluggin');?></legend>
                                <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
                                    <input id="toggleReimpresionIntroJS" type="checkbox"
                                        onchange="checkReimpresionIntroJS();" class="form-control mt-1 my-auto">
                                    <label for="toggleReimpresionIntroJS"
                                        class="m-0 my-auto mr-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                    <button id="manualInteractivoReimpresion"
                                        class="CEX-btn btn-large CEX-button-info my-auto d-none"
                                        href="javascript:void(0)" onclick="introjsTourReimpresion();">
                                        <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                    </button>
                                </div>
                            </fieldset>
                        </div>
                        <div id="cex_generar_resumenManual" class="CEX-manual d-none">
                            <fieldset class="rounded-2 CEX-background-white border CEX-border-blue px-3">
                                <legend
                                    class="p-2 ml-2 CEX-background-blue CEX-text-white rounded-2 w-auto border-0 mb-3">
                                    <?php esc_html_e('Manual interactivo Resumen pedidos', 'cex_pluggin');?></legend>
                                <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
                                    <input id="toggleResumenIntroJS" type="checkbox" onchange="checkResumenIntroJS();"
                                        class="form-control mt-1 my-auto">
                                    <label for="toggleResumenIntroJS"
                                        class="m-0 my-auto mr-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                    <button id="manualInteractivoResumen"
                                        class="CEX-btn btn-large CEX-button-info my-auto d-none"
                                        href="javascript:void(0)" onclick="introjsTourResumen();">
                                        <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                    </button>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <!--Fin cabecera menu opciones-->
                </div>
                <div class="mt-5">
                    <ul id="CEX-tabs-menu" class="nav nav-tabs border-0" role="tablist">
                        <li id="introjsGrabacionMasiva" class="CEX-mi-nav-item">
                            <a class="nav-link active px-2 CEX-text-blue CEX-mi-border" data-toggle="tab" role="tab" href="#grabar_envios"
                               onclick="activarManual('grabar_envios',1);" aria-controls="grabar_envios"
                               aria-selected="true">
                                <?php esc_html_e("Grabaci&oacute;n masiva de pedidos", "cex_pluggin");?>
                            </a>
                        </li>
                        <li id="introjsReimpresion" class="CEX-mi-nav-item">
                            <a class="nav-link px-2 CEX-text-blue CEX-mi-border" data-toggle="tab" role="tab" href="#cex_generar_etiquetas"
                               onclick="activarManual('cex_generar_etiquetas',2);"
                               aria-controls="cex_generar_etiquetas" aria-selected="false">
                                <?php esc_html_e("Reimpresi&oacute;n de etiquetas", "cex_pluggin");?>
                            </a>
                        </li>
                        <li id="introjsResumen" class="CEX-mi-nav-item">
                            <a class="nav-link px-2 CEX-text-blue CEX-mi-border" data-toggle="tab" role="tab" href="#cex_generar_resumen"
                               onclick="activarManual('cex_generar_resumen',3);" aria-controls="cex_generar_resumen" aria-selected="false">
                                <?php esc_html_e("Generaci&oacute;n de resumen de pedidos", "cex_pluggin");?>
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="CEX-tab-content"
                    class="tab-content border CEX-border-blue CEX-background-white p-md-3 rounded-md-top-left-0 rounded-2 d-block"
                    role="tablist">
                    <div id="grabar_envios" class="tab-pane fade show active" role="tabpanel"
                        aria-labelledby="tab-grabar_envios">
                        <div class="tab-header" role="tab" id="heading-grabar_envios">
                            <a data-toggle="collapse" href="#collapse-grabar_envios"
                                aria-expanded="true" aria-controls="collapse-grabar_envios"
                                onclick="activarManual('grabar_envios',1);">
                                <?php esc_html_e("Grabaci&oacute;n masiva de pedidos", "cex_pluggin");?>
                            </a>
                        </div>
                        <div id="collapse-grabar_envios" class="collapse show" data-parent="#CEX-tab-content" role="tabpanel" aria-labelledby="heading-grabar_envios" eq="0">
                            <div class="b-body p-0 container-fluid pb-3">
                                <div id="fila1" class="row m-0">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <h2 class="text-center mb-5">
                                            <?php esc_html_e('Buscador de Pedidos', 'cex_pluggin');?>
                                        </h2>
                                    </div>
                                    <div class="col-6 col-sm-6 offset-md-3 col-md-3 col-lg-3 form-group">
                                        <label for="fecha_desde"><?php esc_html_e('Desde:', 'cex_pluggin');?></label>
                                        <div class="input-group date" id="fecha_desde" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#fecha_desde"/>
                                            <div class="input-group-append" data-target="#fecha_desde" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-3 col-lg-3 form-group">
                                        <label for="fecha_hasta"><?php esc_html_e('Hasta:', 'cex_pluggin');?></label>
                                        <div class="input-group date" id="fecha_hasta" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#fecha_hasta"/>
                                            <div class="input-group-append" data-target="#fecha_hasta" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fila2"
                                        class="col-12 col-xs-12 col-md-12 col-lg-12 text-center form-group mt-5">
                                        <button class="CEX-btn CEX-button-yellow"
                                            onclick="obtenerPedidosBusqueda(event,0);">
                                            <?php esc_html_e("Buscar Env&iacute;os", "cex_pluggin");?>
                                        </button>
                                    </div>
                                </div>

                                <div id="contenedor_respuesta_buscador_pedidos" class="row mx-0 mt-3 d-none">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <div id="respuesta_buscador_pedidos" class=""></div>
                                    </div>
                                </div>
                                <div id="contenedor_etiquetas_grabacion" class="d-none">
                                    <div class="row">
                                        <div id="introjsSeleccionarTodos1"
                                            class="col-12 col-xs-12 col-md-3 col-lg-3 form-group text-left d-flex">
                                            <img
                                                src="<?php echo esc_url(plugins_url('../img/arrow_ltr.png',__FILE__));?>" />
                                            <input type="checkbox" id="todosGa" name="todosGa"
                                                class="form-control mt-1 my-auto marcarTodos"
                                                data-parent="grabar_envios">
                                            <label class="my-auto"
                                                for="todosGa"><?php esc_html_e("Seleccionar todos", "cex_pluggin");?></label>
                                        </div>
                                        <div class="col-12 col-xs-12 col-md-9 col-lg-9 text-right">
                                            <p>
                                                <small>
                                                    <?php esc_html_e("**Los pedidos que no se pueden seleccionar ya tienen n&uacute;mero de env&iacute;o, solo se podr&aacute;n reimprimir las etiquetas. (Ya se encuentran grabados)", "cex_pluggin");?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div id="introjsTipoEtiqueta" class="col-4 col-xs-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label for="select_tipo_etiqueta2" class="input-group-text">
                                                        <?php esc_html_e("Tipo de etiqueta", "cex_pluggin");?>
                                                    </label>
                                                </div>
                                              <select id="select_tipo_etiqueta2" class="form-control rounded-left-0 rounded-right m-0"
                                                    onchange="pintarSelectPosicion2()">
                                                    <option value="1"><?php esc_html_e("Adhesiva", "cex_pluggin");?>
                                                    </option>
                                                    <option value="2"><?php esc_html_e("Medio Folio", "cex_pluggin");?>
                                                    </option>
                                                    <option value="3"><?php esc_html_e("Termica", "cex_pluggin");?>
                                                    </option>
                                                </select>
                                            </div>


                                        </div>
                                        <div id="introjsPosicionEtiqueta2" class="col-4 col-xs-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label for="posicion_etiqueta_masiva" class="input-group-text">
                                                        <?php esc_html_e("Posici&oacute;n de la etiqueta", "cex_pluggin");?>
                                                    </label>
                                                </div>
                                                <select id="posicion_etiqueta_masiva" class="form-control rounded-left-0 rounded-right m-0">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div id="introjsGenerarGrabacionEnvio"
                                            class="col-12 col-xs-12 col-sm-12 col-lg-12 form-group text-center">
                                            <button class="CEX-btn CEX-button-success"
                                                onclick="generarNumerosEnvio(event);">
                                                <?php _e("Generar Env&iacute;os", "cex_pluggin");?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="contenedor_errores" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cex_generar_etiquetas" class="tab-pane fade" role="tabpanel"
                        aria-labelledby="tab-cex_generar_etiquetas">
                        <div class="tab-header" role="tab" id="heading-cex_generar_etiquetas">
                            <a class="collapsed" data-toggle="collapse" href="#collapse-cex_generar_etiquetas"
                                aria-expanded="false"
                                aria-controls="collapse-cex_generar_etiquetas"
                                onclick="activarManual('cex_generar_etiquetas',2);">
                                <?php esc_html_e("Reimpresi&oacute;n de etiquetas", "cex_pluggin");?>
                            </a>
                        </div>
                        <div id="collapse-cex_generar_etiquetas" class="collapse" role="tabpanel"
                            aria-labelledby="heading-cex_generar_etiquetas" data-parent="#CEX-tab-content" eq="1">
                            <div class="b-body container-fluid pb-3">
                                <div class="row text-center m-0">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <h2><?php esc_html_e("Reimpresi&oacute;n de etiquetas", "cex_pluggin");?></h2>
                                    </div>
                                    <div id="fecha" class="col-12 col-xs-12 col-md-6 col-lg-6 offset-md-3 form-group">
                                        <label for="selector_fecha"><?php esc_html_e('Selecciona la fecha de la que quieres ver los pedidos', 'cex_pluggin');?></label>
                                        <div class="input-group date" id="selector_fecha" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#selector_fecha"/>
                                            <div class="input-group-append" data-target="#selector_fecha" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12 form-group">
                                        <button type="button" class="CEX-btn CEX-button-yellow"
                                            onclick="buscar_pedido(event);"><?php esc_html_e("Buscar", "cex_pluggin");?></button>
                                    </div>
                                </div>
                                <div id="contenedor_pedidos" class="row mx-0 mt-3 d-none">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <div id="contenedor_pedidos_reimpresion"></div>
                                    </div>
                                </div>
                                <div id="contenedor_etiquetas_reimpresion" class="d-none">
                                    <div class="row">
                                        <div id="introjsSeleccionarTodos2"
                                            class="col-12 col-xs-12 col-md-3 col-lg-3 form-group d-flex">
                                            <img
                                                src="<?php echo esc_url(plugins_url('../img/arrow_ltr.png',__FILE__));?>" />
                                            <input type="checkbox" id="todosRe" name="todosRe" 
                                            class="form-control mt-1 my-auto marcarTodos"
                                                data-parent="cex_generar_etiquetas">
                                            <label for="todosRe"
                                                class="my-auto"><?php esc_html_e("Seleccionar todos", "cex_pluggin");?></label>
                                        </div>
                                        <div class="col-12 col-xs-12 col-md-9 col-lg-9 text-center">
                                            <p>
                                                <?php esc_html_e("(*Los pedidos que no tengan la opci&oacute;n de reimprimir la etiqueta ya han pasado el peri&oacute;do de validez de 7 d&iacute;as desde su grabaci&oacute;n.)", "cex_pluggin");?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4 col-xs-4 col-md-4 col-lg-4 ">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label for="select_tipo_etiqueta" class="input-group-text">
                                                        <?php esc_html_e("Tipo de etiqueta", "cex_pluggin");?>
                                                    </label>
                                                </div>
                                                <select id="select_tipo_etiqueta" class="form-control rounded-left-0 rounded-right m-0"
                                                    onchange="pintarSelectPosicion()">
                                                    <option value="1"><?php esc_html_e("Adhesiva", "cex_pluggin");?>
                                                    </option>
                                                    <option value="2"><?php esc_html_e("Medio Folio", "cex_pluggin");?>
                                                    </option>
                                                    <option value="3">
                                                        <?php esc_html_e("T&eacute;rmica", "cex_pluggin");?>
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="introjsPosicionEtiqueta" class="col-4 col-xs-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label for="posicion_etiquetas" class="input-group-text">
                                                        <?php esc_html_e("Posici&oacute;n de la etiqueta", "cex_pluggin");?>
                                                    </label>
                                                </div>
                                                <select id="posicion_etiquetas" class="form-control rounded-left-0 rounded-right m-0">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-xs-12 col-md-12 col-lg-12 form-group text-center">
                                            <button type="button" id="grabar_etiqueta"
                                                class="CEX-btn CEX-button-success" onclick="generarEtiquetas(event);">
                                                <?php esc_html_e("Imprimir Etiquetas", "cex_pluggin");?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cex_generar_resumen" class="tab-pane fade" role="tabpanel"
                        aria-labelledby="tab-cex_generar_resumen">
                        <div class="tab-header" role="tab" id="heading-cex_generar_resumen">
                            <a class="collapsed" data-toggle="collapse" href="#collapse-cex_generar_resumen"
                                aria-expanded="false"
                                aria-controls="collapse-cex_generar_resumen"
                                onclick="activarManual('cex_generar_resumen',3);">
                                <?php esc_html_e("Generaci&oacute;n de resumen de pedidos", "cex_pluggin");?>
                            </a>
                        </div>
                        <div id="collapse-cex_generar_resumen" class="collapse" role="tabpanel"
                            aria-labelledby="heading-cex_generar_resumen" data-parent="#CEX-tab-content" eq="2">
                            <div class="b-body container-fluid pb-3">
                                <div class="row text-center m-0">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <h2><?php esc_html_e("Generaci&oacute;n de resumen de pedidos", "cex_pluggin");?>
                                        </h2>
                                    </div>
                                    <div class="col-12 col-xs-12 col-md-6 col-lg-6 offset-md-3 form-group text-center">
                                         <label for="fecha_resumen"><?php esc_html_e('Selecciona la fecha de la que quieres imprimir el resumen de pedidos', 'cex_pluggin');?></label>
                                        <div class="input-group date" id="fecha_resumen" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#fecha_resumen"/>
                                            <div class="input-group-append" data-target="#fecha_resumen" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12 form-group">
                                        <button type="button" class="CEX-btn CEX-button-yellow"
                                            onclick="buscar_resumen(event);">
                                            <?php esc_html_e("Buscar", "cex_pluggin");?>
                                        </button>
                                        <p>
                                            <small>
                                                <?php esc_html_e("(* La fecha seleccionada ser&aacute; la fecha para la que se grabaron los pedidos)", "cex_pluggin");?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                                <div id="contenedor_resumen" class="row mx-0 mt-3 d-none">
                                    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
                                        <div id="contenedor_resumen_pedidos"></div>
                                    </div>
                                </div>
                                <div id="opcionesResumen" class="row d-none mt-5">
                                    <div id="marcarResumen" class="col-6 col-xs-6 col-md-3 col-lg-3 form-group d-flex"
                                        id="marcarResumen">
                                        <img
                                            src="<?php echo esc_url(plugins_url('../img/arrow_ltr.png',__FILE__));?>" />
                                        <input type="checkbox" id="todosGr" name="todosGr" class="form-control my-auto marcarTodos" 
                                            data-parent="cex_generar_resumen">
                                        <label class="my-auto"
                                            for="todosGr"><?php esc_html_e("Seleccionar todos", "cex_pluggin");?></label>
                                    </div>
                                    <div id="boton_resumen" class="col-6 col-xs-6 col-md-6 col-lg-6 text-center"
                                        id="boton_resumen">
                                        <button class="CEX-btn CEX-button-success" onclick="imprimirResumen(event);">
                                            <?php esc_html_e("Imprimir resumen", "cex_pluggin");?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="CEX-loading" class="modal d-none"></div>

    <a id="etiquetas" download="etiquetas.pdf" class="d-none" href=""></a>
    <a id="resumen" download="resumen_orden.pdf" class="d-none" href=""> </a>

    <?php
    $this->CEX_scripts();  
    $this->CEX_introJS('utilidades');
    $this->CEX_scripts_datatable();              
    ?>


    <!-- PreventDefault a los button para que no hagan submit -->
    <script type="text/javascript">
    // Grabacion masiva de pedidos
    var introjsGrabacionMasiva =
        "<?php esc_html_e('Utilidad mediante la que grabaremos nuestros pedidos de forma masiva.' , "cex_pluggin");?>";
    var introjsBuscadorPedidos =
        "<?php esc_html_e('Desplegable con las tiendas disponibles, deberemos seleccionar la tienda sobre la que queremos trabajar.' , "cex_pluggin");?>";
    var fila1 =
        "<?php esc_html_e('Filtro mediante el que realizar la b&uacute;squeda de nuestros pedidos.' , "cex_pluggin");?>";
    var fila2 = "<?php esc_html_e('Detalle del resultado de la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var respuesta_buscador_pedidos =
        "<?php esc_html_e('Detalle del resultado de la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var introjsSeleccionarTodos1 =
        "<?php esc_html_e('Seleccionaremos todos los pedidos de la tabla.' , "cex_pluggin");?>";
    var introjsTipoEtiqueta =
        "<?php esc_html_e('Desplegable mediante el que seleccionaremos el tipo de etiqueta que queramos obtener.' , "cex_pluggin");?>";
    var introjsPosicionEtiqueta =
        "<?php esc_html_e('Desplegable con las posiciones posibles para el tipo de etiqueta seleccionado.' , "cex_pluggin");?>";
    var introjsGenerarGrabacionEnvio =
        "<?php esc_html_e('Bot&oacute;n para grabar todas las ordenes seleccionadas.' , "cex_pluggin");?>";

    // Tabla Grabacion masiva
    var idTablaGrab = "<?php esc_html_e('ID' , "cex_pluggin");?>";
    var refEnvioTablaGrab = "<?php esc_html_e('REFERENCIA' , "cex_pluggin");?>";
    var estadoTablaGrab = "<?php esc_html_e('ESTADO' , "cex_pluggin");?>";
    var clienteTablaGrab = "<?php esc_html_e('CLIENTE' , "cex_pluggin");?>";
    var fechaTablaGrab = "<?php esc_html_e('FECHA' , "cex_pluggin");?>";
    var numEnvioTablaGrab = "<?php esc_html_e('N. ENV&Iacute;O' , "cex_pluggin");?>";
    var codOficinaTablaGrab = "<?php esc_html_e('COD. OFICINA' , "cex_pluggin");?>";
    var bultosTablaGrab = "<?php esc_html_e('BULTOS' , "cex_pluggin");?>";

    // Reimpresion de etiquetas
    var introjsReimpresion =
        "<?php esc_html_e('Utilidad mediante la que reimprimiremos cualquier etiqueta generada previamente.' , "cex_pluggin");?>";
    var fecha = "<?php esc_html_e('Fecha sobre la que realizaremos la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var contenedor_pedidos =
        "<?php esc_html_e('Detalle del resultado de la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var introjsSeleccionarTodos2 =
        "<?php esc_html_e('Seleccionaremos todos los pedidos de la tabla.' , "cex_pluggin");?>";
    var select_tipo_etiqueta =
        "<?php esc_html_e('Desplegable mediante el que seleccionaremos el tipo de etiqueta que queramos obtener.' , "cex_pluggin");?>";
    var posicion_etiquetas =
        "<?php esc_html_e('Desplegable con las posiciones posibles para el tipo de etiqueta seleccionado.' , "cex_pluggin");?>";
    var grabar_etiqueta =
        "<?php esc_html_e('Bot&oacute;n para imprimir todas las etiquetas seleccionadas.' , "cex_pluggin");?>";

    // Generacion de resumen de pedidos
    var introjsResumen =
        "<?php esc_html_e('Utilidad mediante la que imprimiremos el resumen de nuestros pedidos.' , "cex_pluggin");?>";
    var fecha_resumen =
        "<?php esc_html_e('Fecha sobre la que realizaremos la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var contenedor_resumen =
        "<?php esc_html_e('Detalle del resultado de la b&uacute;squeda de pedidos.' , "cex_pluggin");?>";
    var marcarResumen = "<?php esc_html_e('Seleccionaremos todos los pedidos de la tabla.' , "cex_pluggin");?>";
    var boton_resumen =
        "<?php esc_html_e('Bot&oacute;n para imprimir el resumen todas los pedidos seleccionados.' , "cex_pluggin");?>";

    // Tabla reimpresion y resumen
    var idTabla = "<?php esc_html_e('ID' , "cex_pluggin");?>";
    var refEnvioTabla = "<?php esc_html_e('REF ENV&Iacute;O' , "cex_pluggin");?>";
    var codEnvioTabla = "<?php esc_html_e('C&Oacute;DIGO ENV&Iacute;O' , "cex_pluggin");?>";
    var nomDestinatarioTabla = "<?php esc_html_e('NOMBRE DESTINATARIO' , "cex_pluggin");?>";
    var dirDestinatarioTabla = "<?php esc_html_e('DIRECCI&Oacute;N DESTINATARIO' , "cex_pluggin");?>";
    var fechaCreacionTabla = "<?php esc_html_e('FECHA CREACI&Oacute;N' , "cex_pluggin");?>";
 

    var tablaGrabacion;      


    (jQuery)(document).ready(function($) {
        //var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        declareDatePicker();                
        inicializar_utilidades();
        pintarSelectPosicion2();
        pintarSelectPosicion();        
    });


    function declareDatePicker(){
        (jQuery)('#fecha_desde').datetimepicker({
            locale: '<?php bloginfo('language'); ?>',
            defaultDate: moment(),
            format: convertirFormatoWP('<?php echo get_option("date_format") ?>'),
            weekStartDay:  '<?php echo get_option("start_of_week") ?>'
        });
        
        (jQuery)('#fecha_hasta').datetimepicker({
            locale: '<?php bloginfo('language'); ?>',
            defaultDate: moment(),
            format: convertirFormatoWP('<?php echo get_option("date_format") ?>'),
            weekStartDay:  '<?php echo get_option("start_of_week") ?>'
        });

        (jQuery)('#selector_fecha').datetimepicker({
            locale: '<?php bloginfo('language'); ?>',
            defaultDate: moment(),
            format: convertirFormatoWP('<?php echo get_option("date_format") ?>'),
            weekStartDay:  '<?php echo get_option("start_of_week") ?>'
        });

        (jQuery)('#fecha_resumen').datetimepicker({
            locale: '<?php bloginfo('language'); ?>',
            defaultDate: moment(),
            format: convertirFormatoWP('<?php echo get_option("date_format") ?>'),
            weekStartDay:  '<?php echo get_option("start_of_week") ?>'
        });
    }

    function formatFecha(fecha){
        let formatoBbdd = '<?php echo get_option("date_format"); ?>';
        let fecha1Formateada = moment(fecha,formatoBbdd).format('YYYY-MM-DD');
        return fecha1Formateada;
    }


    function convertirFormatoWP(formato_fecha_wordpress){
        switch(formato_fecha_wordpress){
            case "d/m/Y":
                return "DD/MM/YYYY";
            break;
            case "m/d/Y":
                return "MM/DD/YYYY";
            break;
            case "Y-m-d":
                return "YYYY-MM-DD";
            break;
            case "d-m-Y":
                return "DD-MM-YYYY";
            break;
            default:
                return "DD/MM/YYYY";
            break;
        }
    }

    function declareDataTables(dataTable,orden){    
        var table = (jQuery)('#'+dataTable).DataTable({            
            dom: 'Bfrtlip',
            order:orden,
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
            (jQuery)(this).html( '<button class="CEX-btn CEX-button-yellow w-100 activarBuscador" id="activarBuscador'+index+'"><i class="fa fa-search w-100"></i></button><input id="inputBuscador'+index+'" type="text" class="form-control w-100 d-none inputBuscador" />' );
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


    function declareDataTableGrabacionMasiva(dataTable,orden){    
        var table = (jQuery)('#'+dataTable).DataTable({            
            dom: 'Bfrtlip',
            order:orden,
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
            if( index!= 8 && index!=9)
            (jQuery)(this).html( '<button class="CEX-btn CEX-button-yellow w-100 activarBuscador" id="activarBuscador'+index+'"><i class="fa fa-search w-100"></i></button><input id="inputBuscador'+index+'" type="text" class="form-control w-100 d-none inputBuscador" />' );
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

    function inicializar_utilidades() {
        //event.preventDefault();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_get_init_utilities_form',
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                //pintarEstados(msg);
                pintarEtiquetaDefecto(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function buscar_pedido(event) {
        (jQuery)('#CEX-loading').removeClass('d-none');
        event.preventDefault();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_retornar_refencias_dia',
                'fecha': formatFecha((jQuery)('#selector_fecha').datetimepicker('viewDate')),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarSelectPosicion2();
                (jQuery)('#contenedor_pedidos').removeClass('d-none');
                (jQuery)('#contenedor_pedidos_reimpresion').removeClass('d-none');
                pintarPedido(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
                //pintarSelectPosicion2();
            }
        });
    }

    (jQuery)('.marcarTodos').on('click', function(){                    
        var valor=(jQuery)(this).prop('checked');        
        var padre=(jQuery)(this).attr('data-parent');        
        (jQuery)('#'+padre+' table tr td input[type="checkbox"]').each(function(){           
            if(!(jQuery)(this).prop('disabled'))
                (jQuery)(this).prop('checked', valor);            
        });
    });

    function buscar_resumen(event) {
        (jQuery)('#CEX-loading').removeClass('d-none');
        event.preventDefault();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_retornar_refencias_dia',
                'fecha': formatFecha((jQuery)('#fecha_resumen').datetimepicker('viewDate')),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarResumen(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function obtenerPedidosBusqueda(event, borrado) {        
        (jQuery)('#CEX-loading').removeClass('d-none');
        event.preventDefault();                        
        var desde=formatFecha((jQuery)('#fecha_desde').datetimepicker('viewDate'));
        var hasta=formatFecha((jQuery)('#fecha_hasta').datetimepicker('viewDate'));

        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_obtener_pedidos_busqueda',
                'desde': desde,
                'hasta': hasta,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarSelectPosicion();
                (jQuery)('#respuesta_buscador_pedidos').removeClass('d-none');
                pintarPedidosBusqueda(msg, borrado);
                (jQuery)('#CEX-loading').addClass('d-none');
                pintarSelectPosicion2();

            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }


    function pintarPedidosBusqueda(resultado, borrado) {
        if (borrado == 0) {
            (jQuery)("#contenedor_errores").empty();
        }
        var pedidos = JSON.parse(resultado);
        var tabla = '';
        var cabecera = "<table id='grabacionMasiva' border=0 class='table w-100'>" +
            "<thead><tr>" +
            "<th><?php esc_html_e('ID', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('REFERENCIA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('ESTADO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('CLIENTE', 'cex_pluggin');?></th>" +
            //"<th><?php //_e('Precio', 'cex_pluggin');?></th>"+
            "<th><?php esc_html_e('FECHA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('N. ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('COD. OFICINA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('PRODUCTO SELECCIONADO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('PRODUCTO CEX', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('BULTOS', 'cex_pluggin');?></th>" +
            "</tr></thead>";

        var footer = "<tfoot><tr>" +
            "<th><?php esc_html_e('ID', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('REFERENCIA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('ESTADO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('CLIENTE', 'cex_pluggin');?></th>" +
            //"<th><?php //_e('Precio', 'cex_pluggin');?></th>"+
            "<th><?php esc_html_e('FECHA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('N. ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('COD. OFICINA', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('PRODUCTO SELECCIONADO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('PRODUCTO CEX', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('BULTOS', 'cex_pluggin');?></th>" +
            "</tr></tfoot>";

        var cierre = "</tbody>"+footer+"</table>";
        var elementos = '<tbody>';
        if (pedidos == null || pedidos == '') {
            (jQuery)('#contenedor_etiquetas_grabacion').addClass('d-none');
        } else {
            (jQuery)('#contenedor_etiquetas_grabacion').removeClass('d-none');
            pedidos.forEach(function(element) {
                var checkbox = '';
                if (!element.numeroEnvio)
                    checkbox = "<input type='checkbox' id='" + element.idOrden +
                    "'class='marcarPedidos form-control my-auto'  value='" +
                    element.idOrden + "' title='<?php esc_html_e('Click para seleccionar', 'cex_pluggin');?>'>" + element.idOrden;
                else
                    checkbox = "<input type='checkbox' id='" + element.idOrden +
                    "'class='marcarPedidos form-control my-auto'  value='" +
                    element.idOrden + "' title='<?php esc_html_e('Pedido GRABADO', 'cex_pluggin');?>' disabled>" + element.idOrden;

                elementos += "<tr>" +
                    "<td class='d-flex'>" + checkbox + "</td>" +
                    "<td>" + element.numCollect + "</td>" +
                    "<td>" + element.estado + "</td>" +
                    "<td>" + element.cliente + "</td>" +
                    //"<td>"+element.precio +"</td>"+
                    "<td>" + element.fecha.date + "</td>" +
                    "<td>" + element.numeroEnvio + "</td>" +
                    "<td>" + element.codigoOficina + "</td>" +
                    "<td> "+ element.productoSeleccionado + "</td>" +
                    "<td> "+ element.selectProductos + "</td>" +
                    "<td><input type='number' class='form-control' value='" + element.bultos + "'>" +
                    "</tr>";
            });
        }
        (jQuery)('#respuesta_buscador_pedidos').html(cabecera + elementos + cierre);        
        (jQuery)('#contenedor_respuesta_buscador_pedidos').removeClass('d-none');
        //(jQuery)('#contenedor_errores').html('');   
        $orden=new Array();
        $orden=[[1, 'asc'],[0, 'desc']];     
        declareDataTableGrabacionMasiva('grabacionMasiva',$orden);        
    }

    function pintarSelectPosicion() {
        var option = "";
        if ((jQuery)("#select_tipo_etiqueta").val() == '1') {
            (jQuery)("#introjsPosicionEtiqueta").removeClass('d-none');
            option += "<option value='1'>1</option>" +
                "<option value='2'>2</option>" +
                "<option value='3'>3</option>";
        } else if ((jQuery)("#select_tipo_etiqueta").val() == '2') {
            (jQuery)("#introjsPosicionEtiqueta").removeClass('d-none');
            option+="<option value='1'>1</option>"+
            "<option value='2'>2</option>";
        } else {
            (jQuery)("#introjsPosicionEtiqueta").addClass('d-none');
            option += "<option value='1'>1</option>";
        }
        (jQuery)("#posicion_etiquetas").html(option);

    }


    function pintarSelectPosicion2() {
        var option = "";
        if ((jQuery)("#select_tipo_etiqueta2").val()=='1') {
            (jQuery)("#introjsPosicionEtiqueta2").removeClass('d-none');
            option+="<option value='1'>1</option>"+
            "<option value='2'>2</option>"+
            "<option value='3'>3</option>";
        } else if ((jQuery)("#select_tipo_etiqueta2").val()=='2') {
            (jQuery)("#introjsPosicionEtiqueta2").removeClass('d-none');
            option+="<option value='1'>1</option>"+
            "<option value='2'>2</option>";
        } else {
            (jQuery)("#introjsPosicionEtiqueta2").addClass('d-none');
            option += "<option value='1' selected>1</option>";
        }
        (jQuery)("#posicion_etiqueta_masiva").html(option);

    }

    
    function pintarEtiquetaDefecto(msg){
        
        (jQuery)('#CEX-loading').removeClass("d-none");
        var retorno = JSON.parse(msg);
 
        if (retorno.tipoEtiqueta.MXPS_DEFAULTPDF != 'undefined' && retorno.tipoEtiqueta.MXPS_DEFAULTPDF != null) {
            (jQuery)('#select_tipo_etiqueta2').val(retorno.tipoEtiqueta.MXPS_DEFAULTPDF);
            pintarSelectPosicion2();
        }

        if (retorno.tipoEtiqueta != 'undefined' && retorno.tipoEtiqueta != null) {
            (jQuery)('#select_tipo_etiqueta').val(retorno.tipoEtiqueta.MXPS_DEFAULTPDF);
            pintarSelectPosicion();
        }
    
    }
    

    function generarEtiquetas(event) {
        event.preventDefault();
        var numCollect = obtenerNumCollects();
        var tipoEtiqueta = (jQuery)('#select_tipo_etiqueta').val();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_generar_etiquetas',
                'numCollect': numCollect,
                'tipoEtiqueta': tipoEtiqueta,
                'posicion': (jQuery)("#posicion_etiquetas").val(),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                msg = msg.trim(); 
                if(numCollect.length != 0){
                    (jQuery)('#CEX-loading').addClass('d-none');
                    var base64 = msg.substring(153);
                    var date = new Date();
                    var nombre = 'etiquetas' + date.getTime() + '.pdf';
                    (jQuery)("#etiquetas").attr("download", nombre);
                    (jQuery)("#etiquetas").attr("href", "data:application/pdf;base64," + base64);
                    (jQuery)("#etiquetas")[0].click(); 
                }else{
                    PNotify.prototype.options.styling = "bootstrap3";
                    new PNotify({
                        title: 'Debe seleccionar al menos un envio de la lista',
                        type: 'error',
                        stack: myStack
                    })
                }
                
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function parsearGrabacionMasiva() {
        var respuesta = new Array();
        (jQuery)('#grabacionMasiva input.marcarPedidos:checked').each(function() {
            var linea = getRow(this);
            if(linea != null)
                respuesta.push(linea);
        });
        comprobarNumBultos(respuesta);
        return respuesta;
    }

    function getRow(resp) {
        var row = (jQuery)(resp).parents('tr');
        var producto = row.find('td:eq(8) option:selected').val();
        var retorno = null;
        // si producto 0 =  No corresponde a CEX
        if( producto != 0)
            retorno = {
                'id'            : row.find('td:eq(0) input').val(),
                'oficina'       : row.find('td:eq(6)').text(),
                'productosCEX'  : row.find('td:eq(8) option:selected').val(),
                'bultos'        : row.find('td:eq(9) input').val()
            };
        return retorno;
    }

    function comprobarNumBultos(ordenes) {
        ordenes.find(function(e) {
            if (!(e.bultos >= 1)) {
                alert('<?php esc_html_e('No se ha indicado el n&uacute;mero de bultos de la orden ', 'cex_pluggin');?>' +
                    e
                    .id);
                (jQuery)('#CEX-loading').addClass('d-none');
                throw new Error('<?php esc_html_e('se ha encontrado un error en los datos', 'cex_pluggin');?>');
            }
        });
    }

    function generarNumerosEnvio(event) {
        event.preventDefault();
        (jQuery)('#CEX-loading').removeClass('d-none');        
        var ordenes = parsearGrabacionMasiva();
        var idioma='<?php echo get_user_locale(get_current_user_id()) ?>';
        // comprueba que dentro de las ordenes, todas tengan un numero de bultos.
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_form_pedidos',
                'ordenes': ordenes,
                'idioma' : idioma,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>',
                'posicion': (jQuery)("#posicion_etiqueta_masiva").val(),

            },
            success: function(msg) {
                if(ordenes.length !=0){
                    var aux = JSON.parse(msg);

                    var banderaEtiqueta = false;
                    var arrayEtiquetas =  new Array(); 
                    var banderaErrores = false;
                    var arrayErrores =  new Array(); 
                    aux.forEach( function(valor, indice, array) {
                        if(valor.numShip > 0){
                            banderaEtiqueta = true;
                            arrayEtiquetas.push(valor);
                        }else{
                            banderaErrores = true;
                            arrayErrores.push(valor);
                        }
                    });

                    if(banderaEtiqueta){
                        generarEtiquetasGrabaciones(arrayEtiquetas);
                    }

                    if(banderaErrores){
                        pintarErrores(msg);                       
                    }else{
                        (jQuery)('#contenedor_errores').html('');                
                    }
                }else{
                    (jQuery)('#CEX-loading').addClass('d-none');
                    PNotify.prototype.options.styling = "bootstrap3";
                    new PNotify({
                        title: 'Debe seleccionar al menos un envio de la lista',
                        type: 'error',
                        stack: myStack
                    })
                }
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }
    
    var myStack = {
        "dir1": "down",
        "dir2": "right",
        "push": "top"
    };

    function generarEtiquetasGrabaciones(msg) {
        var retorno = new Array();        
        var checkboxes = (jQuery)('#respuesta_buscador_pedidos input[type=checkbox]:checked');
        var idioma='<?php echo get_user_locale(get_current_user_id()) ?>';
        for (i = 0; i < checkboxes.length; i++) {
            msg.forEach(function(element){
                if(element.id_order==checkboxes[i].value)
                    retorno.push(element.numCollect);
            });
        }
    
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_generar_etiquetas',
                'numCollect': retorno,
                'tipoEtiqueta': (jQuery)('#select_tipo_etiqueta2').val(),
                'posicion': (jQuery)("#posicion_etiqueta_masiva").val(),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
                msg = msg.trim();
                var base64 = msg.substring(153);
                var date = new Date();
                var nombre = 'etiquetas' + date.getTime() + '.pdf';
                (jQuery)("#etiquetas").attr("download", nombre);
                (jQuery)("#etiquetas").attr("href", "data:application/pdf;base64," + base64);
                (jQuery)("#etiquetas")[0].click();
                obtenerPedidosBusqueda(event,1);
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function obtenerNumCollects() {
        //recorrer los elementos del div y coger sus ids
        var retorno = new Array();
        var checkboxes = (jQuery)("#contenedor_pedidos input.marcarEtiquetas:checked");
             for (i = 0; i < checkboxes.length; i++) {
                retorno.push(checkboxes[i].value);
            }

        return retorno;
    }

    function imprimirResumen(event) {
        var date = formatFecha((jQuery)('#fecha_resumen').datetimepicker('viewDate'));
        var retorno = new Array();
        var checkboxes = (jQuery)('#contenedor_resumen input.marcarResumen:checked');
        for (i = 0; i < checkboxes.length; i++) {
            retorno.push(checkboxes[i].value);
        }
        event.preventDefault();

        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_generar_resumen',
                'numCollect': retorno,
                'date': date,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                if(checkboxes.length != 0){
                msg = msg.trim();
                (jQuery)('#CEX-loading').addClass('d-none');
                var base64 = msg.substring(180);
                (jQuery)("#resumen").attr("href", "data:application/pdf;base64," + base64);
                //https://stackoverflow.com/questions/30565512/how-to-click-an-anchor-tag-from-javascript-or-jquery
                (jQuery)("#resumen")[0].click();
                }else{
                   PNotify.prototype.options.styling = "bootstrap3";
                    new PNotify({
                        title: 'Debe seleccionar al menos un envio de la lista',
                        type: 'error',
                        stack: myStack
                    }) 
                }
                
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function pintarEstados(retorno) {
        var retorno = JSON.parse(retorno);
        (jQuery)('#CEX-loading').removeClass('d-none');
        if (retorno.selectEstados != 'undefined' && retorno.selectEstados != null)
            (jQuery)('#estados').html(retorno.selectEstados);

        if (retorno.selectTransportistas != 'undefined' && retorno.selectTransportistas != null)
            var datos = retorno .selectTransportistas;
        for (i = 0; i < datos.length; i++) {
            var option = "<option value='" + datos[i].id_bc + "'>" + datos[i].nombre + "</option";
            (jQuery)('#transportistas').append(option);
        }
    }

    function datediff(fec_pedido) {
        var fec_actual = new Date();
        fec_pedido = Date.parse(fec_pedido);
        return Math.round((fec_actual - fec_pedido) / (1000 * 60 * 60 * 24));
    }

    function pintarPedido(msg) {
        (jQuery)('#CEX-loading').removeClass('d-none');
        var pedidos = JSON.parse(msg);
        var cabecera = "<table id='reimpresionMasiva' border=0 class='table w-100'>" +
            "<thead><tr>" +
            "<th><?php _e('ID', 'cex_pluggin');?></th>" +
            "<th><?php _e('REF ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php _e('C&Oacute;DIGO ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php _e('NOMBRE DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php _e('DIRECCI&Oacute;N DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php _e('FECHA CREACI&Oacute;N', 'cex_pluggin');?></th>" +
            "</tr></thead>";
        var footer = "<tfoot><tr>" +
            "<th><?php _e('ID', 'cex_pluggin');?></th>" +
            "<th><?php _e('REF ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php _e('C&Oacute;DIGO ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php _e('NOMBRE DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php _e('DIRECCI&Oacute;N DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php _e('FECHA CREACI&Oacute;N', 'cex_pluggin');?></th>" +
            "</tr></tfoot>";
        var cierre = "</tbody>"+footer+"</table>";
        var elementos = '<tbody>';
        if (pedidos == null || pedidos == '') {
            (jQuery)('#contenedor_etiquetas_reimpresion').addClass('d-none');
            //elementos +="<tr><td colspan='6' class='text-center'><strong><?php _e('No hay resultados para la b&uacute;squeda', 'cex_pluggin');?></strong></td></tr>";
        } else {
            (jQuery)('#contenedor_etiquetas_reimpresion').removeClass('d-none');
            pedidos.forEach(function(element) {
                if (datediff(element.fecha) >= 7) {
                    var checkbox = "<input type='checkbox' class='form-control my-auto' id='" + element
                        .idOrden + "' value='" + element
                        .numCollect + "' disabled>" + element.idOrden;
                } else {
                    var checkbox = "<input type='checkbox' class='marcarEtiquetas form-control my-auto' id='" +
                        element.idOrden +
                        "' value='" + element.numCollect + "'>" + element.idOrden;
                }
                elementos += "<tr>" +
                    "<td class='d-flex'>" + checkbox + "</td>" +
                    "<td>" + element.numCollect + "</td>" +
                    "<td>" + element.numShip + "</td>" +
                    "<td>" + element.NombreDestinatario + "</td>" +
                    "<td>" + element.direccionDestino + "</td>" +
                    "<td>" + element.fecha + "</td>" +
                    "</tr>";

            });
        }

        (jQuery)('#contenedor_pedidos_reimpresion').html(cabecera + elementos + cierre);
        (jQuery)('#contenedor_pedidos').removeClass('d-none');          
        $orden=new Array();
        $orden=[[1, 'asc'],[0, 'desc']];
        declareDataTables('reimpresionMasiva',$orden);        
    }

    function pintarResumen(msg) {
        (jQuery)('#CEX-loading').removeClass('d-none');
        var pedidos = JSON.parse(msg);
        var cabecera = "<table id='resumenPedidos' border=0 class='table w-100'>" +
            "<thead><tr>" +
            "<th><?php esc_html_e('ID', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('REF ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('C&Oacute;DIGO ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('NOMBRE DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('DIRECCI&Oacute;N DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('FECHA CREACI&Oacute;N', 'cex_pluggin');?></th>" +
            "</tr></thead>";
        var footer = "<tfoot><tr>" +
            "<th><?php esc_html_e('ID', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('REF ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('C&Oacute;DIGO ENV&Iacute;O', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('NOMBRE DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('DIRECCI&Oacute;N DESTINATARIO', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('FECHA CREACI&Oacute;N', 'cex_pluggin');?></th>" +
            "</tr></tfoot>";
        var cierre = "</tbody>"+footer+"</table>";
        var elementos = '<tbody>';
        if (pedidos == null || pedidos == '') {
            (jQuery)('#opcionesResumen').addClass('d-none');
            //elementos +="<tr><td colspan='6' class='text-center'><strong><?php _e('No hay resultados para la b&uacute;squeda', 'cex_pluggin');?></strong></td></tr>";
        } else {
            (jQuery)('#opcionesResumen').removeClass('d-none');
            pedidos.forEach(function(element) {
                var checkbox = "<input type='checkbox' class='marcarResumen form-control my-auto' id='" +
                    element.idOrden +
                    "' value='" +
                    element.numCollect + "'>" + element.idOrden;
                elementos += "<tr>" +
                    "<td class='d-flex'>" + checkbox + "</td>" +
                    "<td>" + element.numCollect + "</td>" +
                    "<td>" + element.numShip + "</td>" +
                    "<td>" + element.NombreDestinatario + "</td>" +
                    "<td>" + element.direccionDestino + "</td>" +
                    "<td>" + element.fecha + "</td>" +
                    "</tr>";
            });
        }

        (jQuery)('#contenedor_resumen_pedidos').html(cabecera + elementos + cierre);
        (jQuery)('#contenedor_resumen').removeClass('d-none');        
        $orden=new Array();
        $orden=[[1, 'asc'],[0, 'desc']];
        declareDataTables('resumenPedidos',$orden);        
    }

    function pintarErrores(msg) {
        var errores = JSON.parse(msg);
        var bandera = false;

        var cabecera =
            "<h4><?php esc_html_e('Gestiona los errores de tu pedido', 'cex_pluggin');?></h4>";

        cabecera += "<table id='erroresUtilidades' class='table w-100'>" +
            "<thead><tr>" +
            "<th><?php esc_html_e('ID ORDEN', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('MENSAJE', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('ENLACE', 'cex_pluggin');?></th>" +
            "</tr></thead>";
        var finTabla = "</table>";
        var contenido = "<tbody>";
        if(errores){        
            errores.forEach(function(elemento) {
                if (elemento.resultado == 1) {
                    contenido += '';
                } else {
                    if(elemento.resultado == 99){
                        var enlace = "<a href='../wp-admin/admin.php?page=correosexpress-ajustes'><?php esc_html_e('EDITAR', 'cex_pluggin');?></a>";
                    }else{
                        var enlace = "<a href='../wp-admin/post.php?post=" + elemento.id_order +
                        "&action=edit'><?php esc_html_e('EDITAR', 'cex_pluggin');?></a>";
                    }
                    contenido += "<tr>" +
                        "<td>" + elemento.id_order + "</td>" +
                        "<td>" + elemento.mensajeRetorno + "</td>" +
                        "<td>" + enlace + "</td>" +
                        "</tr>";
                    bandera = true;
                }
            });
        }
        contenido+='</tbody>';
        var footer="<tfoot><tr>" +
            "<th><?php esc_html_e('ID ORDEN', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('MENSAJE', 'cex_pluggin');?></th>" +
            "<th><?php esc_html_e('ENLACE', 'cex_pluggin');?></th>" +
            "</tr></tfoot>";
        if (bandera){
            (jQuery)('#contenedor_errores').html(cabecera + contenido + finTabla);
            (jQuery)('#contenedor_errores').removeClass('d-none');
        }
        obtenerPedidosBusqueda(event, 1);
    }

    function activarManual(manual, check) {        
        manual += 'Manual';
        (jQuery)('.CEX-manual').addClass('d-none');
        (jQuery)('#' + manual).removeClass('d-none');
        toggleResumenIntroJS
        switch (check) {
            case 1:
                (jQuery)('#toggleGrabacionIntroJS').prop('checked', false);
                checkGrabacionIntroJS();
                break;
            case 2:
                (jQuery)('#toggleReimpresionIntroJS').prop('checked', false);
                checkReimpresionIntroJS();
                break;
            case 3:
                (jQuery)('#toggleResumenIntroJS').prop('checked', false);
                checkResumenIntroJS();
                break;
        }
    }


    (jQuery)('.collapse').on('hidden.bs.collapse', function () {               
        var idAtributo=this.id;
        var eq=(jQuery)('#'+idAtributo).attr('eq');    
        (jQuery)('.collapse').eq(eq).collapse('show');
    })




    function formatearFechaAjax(fecha){
        var language = '<?php echo get_user_locale(get_current_user_id()) ?>';
        let a;
        switch(language){
            case 'es_ES':
            case 'ca':
                a = moment(fecha, "DD/MM/YYYY");
            break;
            case 'pt_PT':
                a = moment(fecha, "YYYY-MM-DD");
            break;
            case 'en_US':
            case 'en_UK':
                a = moment(fecha, "YYYY-MM-DD");
            break;
        }
        return a;
    }



    </script>

    <?php else : ?>
    <p><?php esc_html_e("NO TIENES ACCESO A ESTA SECCI&Oacute;N", "cex_pluggin");?></p>
    <?php endif; ?>
</div>
