<?php if (current_user_can('manage_options')) :?>
<?php 
$this->CEX_stylesBootstrap();
$this->CEX_styles();
wp_enqueue_script("jquery");
?>

    <div id="CEX" class="CEX-panel">
        <div id="configuracion" class="CEX-container container pt-5 px-3 pr-sm-5">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div id="CEX-cabecera" class="row mb-4 mb-sm-1 d-flex align-items-center">
                        <!--Inicio Cabecera menu opciones-->
                        <div class="col-6 col-sm-6 my-auto">
                            <h2 id="step1" class="CEX-text-blue my-0">
                                <?php esc_html_e('Configuraci&oacute;n', 'cex_pluggin');?></h2>                            
                        </div>
                        <div class="col-6 col-sm-6 text-right my-0">
                            <img class="img-fluid w-50"
                                src="<?php echo esc_url(plugins_url('/../img/logo-correosexpress-nuevo.png',__FILE__));?>">
                        </div>
                    </div>
                    <div class="row mb-5 d-flex align-items-center">
                        <div class="col-12 col-sm-12">
                            <div id="CEX-manualInteractivo" class="CEX-manual">
                                <fieldset class="rounded CEX-background-white border CEX-border-blue px-3">
                                    <legend
                                        class="p-2 ml-2 CEX-background-blue CEX-text-white rounded w-auto border CEX-border-primary mb-3">
                                        <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                    </legend>
                                    <div id="contenidoManual" class="form-group mb-3 w-auto row">
                                     <div id="parteIzqManual" class="col-lg-6 d-flex">
                                        <input id="toggleIntroJS" checked="" type="checkbox" class="form-control mt-1 my-auto" onchange="checkIntroJS();">
                                        <label for="toggleIntroJS" class="m-0 my-auto mr-1 mr-sm-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                        <button id="manualInteractivo" class="px-2 CEX-btn btn-large CEX-button-info my-auto" href="javascript:void(0)" onclick="introConfiguracionTPL();">
                                            <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>                                    
                                        </button>
                                     </div>
        
                                     <div id="parteDerechaManual" class="col-lg-6">
                                        <a id="quickSupport" class="px-2 CEX-btn btn-large CEX-button-info my-auto" style="float:right" href="https://get.teamviewer.com/6mxd5fj" target="_blank">
                                             <?php esc_html_e('Soporte Rápido', 'cex_pluggin');?> 
                                        </a>
                                     </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div id="CEX-manualCodigoCliente" class="CEX-manual d-none">
                                <fieldset class="rounded CEX-background-white border-0 px-3">
                                    <legend
                                        class="p-2 ml-2 CEX-background-blue CEX-text-white rounded w-auto border CEX-border-primary mb-3">
                                        <?php esc_html_e('Manual Edici&oacute;n C&oacute;digo Cliente', 'cex_pluggin');?>
                                    </legend>
                                    <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
                                        <input id="toggleCodigoClienteJS" checked type="checkbox"
                                            class="form-control mt-1 my-auto" onchange="checkEdicionCodigoClienteJS();">
                                        <label for="toggleCodigoClienteJS"
                                            class="m-0 my-auto mr-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                        <button id="manualCodigoCliente" class="CEX-btn btn-large CEX-button-grey my-auto"
                                            href="javascript:void(0)" onclick="buttonIntroEdicionCodigoCliente();">
                                            <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                        </button>
                                    </div>
                                </fieldset>
                            </div>
                            <div id="CEX-manualRemitente" class="CEX-manual d-none">
                                <fieldset class="rounded CEX-background-white border-0 px-3">
                                    <legend
                                        class="p-2 ml-2 CEX-background-blue CEX-text-white rounded w-auto border CEX-border-primary mb-3">
                                        <?php esc_html_e('Manual Edici&oacute;n Remitente', 'cex_pluggin');?>
                                    </legend>
                                    <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
                                        <input id="toggleRemitenteJS" checked type="checkbox"
                                            class="form-control mt-1 my-auto" onchange="checkEdicionRemitenteJS();">
                                        <label for="toggleRemitenteJS"
                                            class="m-0 my-auto mr-2 mr-sm-5 CEX-text-blue"><?php esc_html_e('Activar / Desactivar', 'cex_pluggin');?></label>
                                        <button id="manualRemitente" class="CEX-btn btn-large CEX-button-info my-auto"
                                            href="javascript:void(0)" onclick="buttonIntroEdicionRemitente();">
                                            <?php esc_html_e('Manual interactivo', 'cex_pluggin');?>
                                        </button>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <!--Fin cabecera menu opciones-->
                    </div>
                </div>
            </div>
        </div>
        <div id="ajustes" class="CEX-container container mt-1 pr-sm-5">
            <div class="row mt-1">
                <div class="col-12 col-md-12 col-lg-12 CEX-paneles" id="CEX-paneles">
                    <!--Contenedor de codigos de cliente-->
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step2">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_codigo_cliente" aria-expanded="true"
                                    aria-controls="panel_codigo_cliente" onclick="animacionBoton('#panel_codigo_cliente');">
                                    <?php esc_html_e('GESTIONAR C&Oacute;DIGOS DE CLIENTE', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconCodigoCliente" class="CEX-iconoInfo"
                                    title="Hacer click para ver el manual de esta secci&oacute;n"><i
                                        class="fas fa-info-circle"
                                        onclick="checkIntroCodigoCliente('#panel_codigo_cliente');"></i></span>
                            </div>

                            <div id="panel_codigo_cliente" class="collapse" aria-labelledby="panel_codigo_cliente"
                                data-parent="">
                                <div class="card-body border">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 col-lg-5">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('C&oacute;digo cliente', 'cex_pluggin');?></span>
                                                    </div>
                                                    <input class="form-control rounded-left-0 rounded-right m-0"
                                                        type="text" id="customer_code" name='customer_code' maxlength="9" minlength="9"
                                                        onchange='calcular_codigo_customer();'
                                                        placeholder="<?php esc_html_e('C&oacute;digo cliente', 'cex_pluggin'); ?>" autocomplete="off">
                                                    <input class="form-control rounded-left-0 rounded-right m-0"
                                                        type="hidden" id="code_demand" name='code_demand'>
                                                </div>
                                                <button id="guardar_cod_cliente"
                                                    class="CEX-btn CEX-button-yellow mx-auto my-3"
                                                    onclick='guardarCodigoCliente();'>
                                                    <?php esc_html_e('A&ntilde;adir c&oacute;digos', 'cex_pluggin') ;?>
                                                </button>
                                            </div>
                                            <div class="col-12 col-lg-7">
                                                <div class="row">
                                                    <div id="saved_codes"
                                                        class="col-12 d-none CEX-border-blue rounded p-0 overflow-hidden">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step3">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_remitentes" aria-expanded="true" aria-controls="panel_remitentes"
                                    onclick="animacionBoton('#panel_remitentes');">
                                    <?php esc_html_e('REMITENTES', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconRemitente" class="CEX-iconoInfo"
                                    title="Hacer click para ver el manual de esta secci&oacute;n"><i
                                        class="fas fa-info-circle" onclick="checkIntroRemitente();"></i></span>
                            </div>

                            <div id="panel_remitentes" class="collapse" aria-labelledby="panel_remitentes" data-parent="">
                                <div class="card-body border p-0 pb-3">
                                    <div class="container-fluid">                                    
                                        <form id="formCrearRemt" class="w-100" onsubmit="return false">
                                            <div class="row CEX-background-white px-1 py-4 mb-3 mt-0 rounded-0">
                                                <div class="col-8 offset-lg-2">
                                                    <div id="div_codigo_cliente" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('C&oacute;digo cliente', 'cex_pluggin');?></span>
                                                        </div>
                                                        <select id="codigo_cliente" name="codigo_cliente"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            required></select>
                                                    </div>
                                                </div>
                                            </div>        
                                            <div class="row" id="div_datos_cliente">
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">        
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Nombre remitente', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="name_sender" name="name_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            type="text"
                                                            placeholder="<?php esc_html_e('Nombre remitente', 'cex_pluggin');?>"
                                                            required>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Persona contacto', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0"
                                                            type="text" id="contact_sender" name="contact_sender"
                                                            placeholder="<?php esc_html_e('Persona contacto', 'cex_pluggin');?>"
                                                            required>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Direcci&oacute;n recogida', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="address_sender" name="address_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            type="text"
                                                            placeholder="<?php esc_html_e('Direcci&oacute;n recogida', 'cex_pluggin');?>"
                                                            required>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Poblaci&oacute;n', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="city_sender" name="city_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            type="text" placeholder="<?php esc_html_e('Poblaci&oacute;n', 'cex_pluggin');?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('C&oacute;digo Postal', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="postcode_sender" name="postcode_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            pattern="\d*"
                                                            placeholder="<?php esc_html_e('C&oacute;digo Postal', 'cex_pluggin');?>"
                                                            maxlength="8" type="text" required>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Pa&iacute;s', 'cex_pluggin') ;?></span>
                                                        </div>
                                                        <select id="country_sender" name="country_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0" required="">
                                                            <option value="ES"><?php esc_html_e("España", "cex_pluggin");?>
                                                            </option>
                                                            <option value="PT">
                                                                <?php esc_html_e("Portugal", "cex_pluggin");?>
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Tel&eacute;fono', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="phone_sender" name="phone_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0" type="tel"
                                                            pattern="\d*" maxlength="9" minlength="9"
                                                            placeholder="<?php esc_html_e('Tel&eacute;fono', 'cex_pluggin');?>"
                                                            required>
                                                    </div>
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span
                                                                class="input-group-text"><?php esc_html_e('Correo electr&oacute;nico', 'cex_pluggin');?></span>
                                                        </div>
                                                        <input id="email_sender" name="email_sender"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            type="email"
                                                            placeholder="<?php esc_html_e('Correo electr&oacute;nico', 'cex_pluggin');?>"
                                                            required>
                                                    </div>
                                                </div>
                                                <div id="guardarRemitenteMsn"
                                                    class="col-12 col-sm-12 col-md-12 col-lg-12 d-none text-center mb-3">
                                                    <span class="text-danger">
                                                        <?php esc_html_e('Para guardar los datos del remitente, primero hay que a&ntilde;adir un c&oacute;digo de cliente.', 'cex_pluggin') ;?>
                                                    </span>
                                                </div>
                                            </div>    
                                            <div id="bloqueHoraDesdeHasta" class="row">
                                                <div id="introHoraDesde" class="col-12 col-sm-6 col-lg-3 offset-lg-3 mb-3 mb-md-0">
                                                    <div class="col-12 CEX-border-blue rounded p-0 overflow-hidden">
                                                        <div class="row-fluid CEX-background-blue CEX-text-white p-1 d-flex align-items-center">
                                                            <?php esc_html_e('Desde', 'cex_pluggin') ;?></div>
                                                        <div class="panel-body py-3">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label for="fromHH_sender"
                                                                            class="control-label d-block"><?php esc_html_e('Hora', 'cex_pluggin');?></label>
                                                                        <input type="number" class="form-control"
                                                                            id="fromHH_sender" placeholder="0"
                                                                            value="" size="2" name="fromHH_sender"
                                                                            min=0 max=24 required>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="fromMM_sender"
                                                                            class="control-label d-block"><?php esc_html_e('Minutos', 'cex_pluggin') ;?></label>
                                                                        <input type="number" class="form-control"
                                                                            id="fromMM_sender" placeholder="0"
                                                                            value="0" size="2" name="fromMM_sender"
                                                                            min=0 max=60>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="introHoraHasta" class="col-12 col-sm-6 col-md-6 col-lg-3">
                                                    <div class="col-12 CEX-border-blue rounded p-0 overflow-hidden">
                                                        <div class="row-fluid CEX-background-blue CEX-text-white p-1 d-flex align-items-center">
                                                            <?php esc_html_e('Hasta', 'cex_pluggin');  ?></div>
                                                        <div class="panel-body py-3">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label for="toHH_sender"
                                                                            class="control-label d-block"><?php esc_html_e('Hora', 'cex_pluggin') ;?></label>
                                                                        <input type="number"
                                                                            class="form-control rounded-right"
                                                                            id="toHH_sender" placeholder="0"
                                                                            value="" size="2" name="toHH_sender"
                                                                            min=0 max=24 required>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="toMM_sender"
                                                                            class="control-label d-block"><?php esc_html_e('Minutos', 'cex_pluggin');?></label>
                                                                        <input type="number"
                                                                            class="form-control rounded-right"
                                                                            id="toMM_sender" placeholder="0"
                                                                            value="0" size="2" name="toMM_sender"
                                                                            min=0 max=60>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3 mb-5">
                                                <div class="col-6 text-right">
                                                    <button id="guardarRemitente" data-toggle=" tooltip" data-placement="top"
                                                        title="<?php esc_html_e('Necesitas  tener códigos de remitente', 'cex_pluggin') ; ?>" name=" guardarRemitente"
                                                        class="CEX-btn CEX-button-success py-2 px-5"  onclick="crearRemitente(event);">
                                                        <?php esc_html_e('Guardar', 'cex_pluggin') ;?>
                                                    </button>
                                                </div>
                                                <div class="col-6 ">
                                                    <button id="cancelar" type="reset" class="CEX-btn CEX-button-cancel px-5 py-2">
                                                        <?php esc_html_e('Cancelar','cex_pluggin') ; ?>
                                                    </button>
                                                </div>    
                                            </div>
                                        </form>
                                        <div class="col-12 col-lg-12 my-3">
                                            <div id="tableSavedSenders"
                                                class="p-0 rounded table-responsive CEX-overflow-y-hidden">
                                                <table cellspacing="0" cellpadding="0" id="savedsenders"
                                                    class="table table-striped m-0">
                                                </table>
                                            </div>
                                        </div>    
                                        <div class="row CEX-background-white pt-3 pb-2">
                                            <div class="col-12 col-md-6 col-lg-6 offset-lg-2">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Remitente por defecto', 'cex_pluggin'); ?>
                                                            </span>
                                                    </div>
                                                    <select name="MXPS_DEFAULTSEND" id="MXPS_DEFAULTSEND"
                                                        class="form-control rounded-left-0 rounded-right m-0"
                                                        required>
                                                        <option value="" disabled="disabled">
                                                            <?php esc_html_e("No hay remitentes dados de alta", "cex_pluggin");?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2 col-lg-2">
                                                <div class="input-group my-3">
                                                    <button id="guardarRemitenteDefecto"
                                                        class="CEX-btn CEX-button-yellow d-block"
                                                        onclick="guardarRemitenteDefecto();">
                                                        <?php esc_html_e('Guardar Remitente por defecto', 'cex_pluggin'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step4">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_usuario" aria-expanded="true" aria-controls="panel_usuario"
                                    onclick="animacionBoton('#panel_usuario');">
                                    <?php esc_html_e('CONFIGURACI&Oacute;N DE USUARIO', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconConfiguracionUsuario" class="CEX-iconoInfo"
                                    title="Hacer click para ver el manual de esta secci&oacute;n"><i
                                        class="fas fa-info-circle" onclick="checkIntroConfiguracionUsuario();"></i></span>
                            </div>

                            <div id="panel_usuario" class="collapse" aria-labelledby="panel_usuario" data-parent="">
                                <div class="card-body border p-0 pt-3">
                                    <div class="container-fluid">
                                        <div class="row" id="stepUser0">
                                            <div id="stepUser1" class="col-12 col-sm-3 col-md-3 col-lg-3">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Usuario', 'cex_pluggin'); ?></span>
                                                    </div>
                                                    <input class="form-control rounded-left-0 rounded-right m-0" type="text"
                                                        id="MXPS_USER" name="MXPS_USER"
                                                        placeholder="<?php esc_html_e('Usuario', 'cex_pluggin'); ?>"
                                                        autocomplete="off" 
                                                        required>
                                                </div>
                                            </div>
                                            <div id="stepUser2" class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Password', 'cex_pluggin'); ?></span>
                                                    </div>
                                                    <input class="form-control rounded-left-0 rounded-right m-0"
                                                        type="password" id="MXPS_PASSWD" name="MXPS_PASSWD"
                                                        placeholder="<?php esc_html_e('Password', 'cex_pluggin'); ?>"
                                                        autocomplete="off" 
                                                        required>
                                                </div>
                                            </div>
                                            <div id="stepUser21" class="col-6 col-sm-6 col-md-6 col-lg-4 mt-3 d-none">
                                                <h6 id="cex_account_title"></h6>
                                                <span id="cex_username" class="d-inline-block"></span> / <span id="cex_passw" class="d-inline-block"></span>
                                            </div>
                                            <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                                                <button class="btn CEX-button-success my-3" id="guardarCredenciales" onclick="guardarCredenciales();"><?php esc_html_e('Guardar Credenciales', 'cex_pluggin'); ?>
                                                </button>
                                                <button class="btn CEX-button-success my-3 d-none" id="editarCredenciales" onclick="editarCredenciales();"><?php esc_html_e('Editar Credenciales', 'cex_pluggin'); ?>
                                                </button>
                                            </div>
                                        </div>

                                        <div clas="row">  
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-none CEX-background-white p-3 my-3 rounded-0">
                                                <div class="input-group my-3">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                WebService                                                       
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_DEFAULTWS" id="MXPS_DEFAULTWS" class="form-control rounded-left-0 rounded-right m-0" onchange="mostrarUrlWebService()">
                                                            <option value="SOAP" selected="">SOAP</option>
                                                            <option value="REST" selected="selected">REST</option>
                                                        </select>
                                                    </div>                                               
                                                </div>
                                                <div id="mostrarSoap">
                                                    <div id="stepUser3" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (envío)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURL" name="MXPS_WSURL" value="" placeholder="URL Web Service (envío)" required="">
                                                    </div>
                                                    <div id="stepUser4" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (recogida)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURLREC" name="MXPS_WSURLREC" value="" placeholder="URL Web Service (recogida)" required="">
                                                    </div>
                                                    <div id="stepUser5" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (seguimiento)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURLSEG" name="MXPS_WSURLSEG" value="" placeholder="URL Web Service (seguimiento)" required="">
                                                    </div>
                                                   
                                                </div>
                                                <div id="mostrarRest">
                                                    <div id="stepUser3_rest" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (envío)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURL_REST" name="MXPS_WSURL" value="" placeholder="URL Web Service (envío)" required="">
                                                    </div>
                                                     <div id="stepUser4_rest" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (recogida)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURLREC_REST" name="MXPS_WSURLREC" value="" placeholder="URL Web Service (recogida)" required="">
                                                    </div>
                                                    <div id="stepUser5_rest" class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL Web Service (seguimiento)</span>
                                                        </div>
                                                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" id="MXPS_WSURLSEG_REST" name="MXPS_WSURLSEG" value="" placeholder="URL Web Service (seguimiento)" required="">          
                                                    </div>                                                
                                                </div>
                                            </div>
                                            <div class="row">
                                            <div id="stepUser6" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Bultos por defecto', 'cex_pluggin'); ?></span>
                                                    </div>
                                                    <input class="form-control rounded-left-0 rounded-right m-0"
                                                        type="number" id="MXPS_DEFAULTBUL" name="MXPS_DEFAULTBUL"
                                                        placeholder="<?php esc_html_e('N&uacute;mero de bultos por defecto', 'cex_pluggin'); ?>"
                                                        required>
                                                </div>
                                            </div>
                                            <div id="stepUser7" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Tipo de etiqueta por defecto', 'cex_pluggin');?></span>
                                                    </div>
                                                    <select name="MXPS_DEFAULTPDF" id="MXPS_DEFAULTPDF"
                                                        class="form-control rounded-left-0 rounded-right m-0" required>
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
                                            <div id="stepUser8" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('Datos de env&iacute;o para la etiqueta', 'cex_pluggin');?></span>
                                                    </div>
                                                    <select name="MXPS_DEFAULTDELIVER" id="MXPS_DEFAULTDELIVER"
                                                        class="form-control rounded-left-0 rounded-right m-0" required>
                                                        <option value="ENVIO">
                                                            <?php esc_html_e("Env&iacute;o", "cex_pluggin");?>
                                                        </option>
                                                        <option value="FACTURACION">
                                                            <?php esc_html_e("Facturaci&oacute;n", "cex_pluggin");?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="stepUser9" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                <div class="input-group my-3">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text"><?php esc_html_e('M&eacute;todo de pago contrareembolso', 'cex_pluggin');?></span>
                                                    </div>
                                                    <select name="MXPS_DEFAULTPAYBACK" id="MXPS_DEFAULTPAYBACK"
                                                        class="form-control rounded-left-0 rounded-right m-0">
                                                        <option value="ninguno"><?php _e("Ninguno", "cex_pluggin")?>
                                                        </option>
                                                        <option value="ps_checkpayment">
                                                            <?php _e("Pagos por cheque", "cex_pluggin")?></option>
                                                        <option value="ps_wirepayment">
                                                            <?php _e("Pagos por transferencia bancaria", "cex_pluggin")?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="row CEX-background-white px-0 my-3 rounded-0">

                                            <div class="col-12">
                                                <div id="stepUserLog" class="row">
                                                    <div class="col-12">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><input type="checkbox"
                                                                        class="form-control m-0" name="MXPS_CHECK_LOG"
                                                                        id="MXPS_CHECK_LOG"></span>
                                                            </div>
                                                            <input readonly type="text"
                                                                class="form-control rounded-left-0 rounded-right m-0"
                                                                aria-label="Activar la generación de archivos de log"
                                                                value="<?php esc_html_e('Activar la generación de archivos de log', 'cex_pluggin'); ?>">
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>    

                                            <div class="col-12">
                                                <div id="stepUser10" class="row">
                                                    <div class="col-12">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><input type="checkbox"
                                                                        class="form-control m-0" name="MXPS_ENABLESHIPPINGTRACK"
                                                                        id="MXPS_ENABLESHIPPINGTRACK"></span>
                                                            </div>
                                                            <input readonly type="text"
                                                                class="form-control rounded-left-0 rounded-right m-0"
                                                                aria-label="Activar enlace de seguimiento en el historial de compras del cliente"
                                                                value="<?php esc_html_e('Activar enlace de seguimiento en el historial de compras del cliente', 'cex_pluggin'); ?>">
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>    
                                            <div class="col-12">
                                                <div class="row">
                                                    <div id="stepUser11" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><input type="checkbox"
                                                                        class="form-control m-0" name="MXPS_LABELSENDER"
                                                                        id="MXPS_LABELSENDER"class="form-control m-0"
                                                                        onclick="mostrarRemitenteAlternativo();">
                                                                </span>
                                                            </div>
                                                            <input readonly type="text"
                                                                class="form-control rounded-left-0 rounded-right m-0"
                                                                aria-label="Texto alternativo del remitente"
                                                                value="<?php esc_html_e('Texto alternativo del remitente', 'cex_pluggin') ;?>">
                                                        </div>
                                                    </div>
                                                    <div id="remitenteAlt" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text"><?php esc_html_e('Texto alternativo etiqueta', 'cex_pluggin'); ?></span>
                                                            </div>
                                                            <input class="form-control rounded-left-0 rounded-right m-0" maxlength="50"
                                                                type="text" id="MXPS_LABELSENDER_TEXT" name="MXPS_LABELSENDER_TEXT"
                                                                placeholder="<?php esc_html_e('texto alternativo', 'cex_pluggin'); ?>"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>        
                                            <div class="col-12">
                                                <div class="row">
                                                    <div id="stepUser12" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="MXPS_DEFAULTKG_SPAN">
                                                                    <input type="checkbox" name="MXPS_ENABLEWEIGHT"
                                                                        id="MXPS_ENABLEWEIGHT" class="form-control m-0"
                                                                        onclick="mostrarPesoDefecto();">
                                                                </span>
                                                            </div>
                                                            <input readonly type="text" class="form-control rounded-left-0      rounded-right m-0"
                                                                aria-label="<?php esc_html_e('Activar peso por defecto', 'cex_pluggin'); ?>"
                                                                value="<?php esc_html_e('Activar peso por defecto', 'cex_pluggin'); ?>">
                                                        </div>
                                                    </div>
                                                    <div id="pesodefecto" class="col-12 col-sm-6 col-md-6 col-lg-6 d-none">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span id="unidadMedida"
                                                                    class="input-group-text"><?php esc_html_e('Peso por defecto ', 'cex_pluggin'); ?>
                                                                    <span  class="ml-1"></span>
                                                                </span>
                                                            </div>
                                                            <input class="form-control rounded-left-0 rounded-right m-0"
                                                                type="number" id="MXPS_DEFAULTKG" name='MXPS_DEFAULTKG' value=""
                                                                step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">       
                                                    <div id="stepUser14" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="MXPS_NODATAPROTECTION_SPAN">                                                       
                                                                <i class="fas fa-info-circle"
                                                                        data-toogle="tooltip" title="<?php esc_html_e('Información legal pertinente a mostrar' , 'cex_pluggin');?>"></i>
                                                                    <input type="checkbox" name="MXPS_NODATAPROTECTION"
                                                                        id="MXPS_NODATAPROTECTION" class="form-control m-0 ml-1"
                                                                        onchange="mostrarProteccionDatos();">
                                                                </span>
                                                            </div>
                                                            <input readonly type="text" class="form-control rounded-left-0 rounded-right m-0" aria-label="<?php esc_html_e('Acepto alterar la referencia de los productos en los envios.' , 'cex_pluggin');?>"
                                                            value="<?php esc_html_e('Acepto alterar la referencia de los productos en los envios' , 'cex_pluggin');?>">
                                                        </div>
                                                    </div>      
                                                    <div id="proteccionDatos" class="col-12 col-sm-6 col-md-6 col-lg-6 d-none">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <?php esc_html_e('Datos a mostrar en etiqueta' , 'cex_pluggin');?>
                                                                </span>
                                                            </div>
                                                            <select name="MXPS_DATAPROTECTIONVALUE" id="MXPS_DATAPROTECTIONVALUE"
                                                                class="form-control rounded-left-0 rounded-right m-0">
                                                                <option value="1"><?php esc_html_e('ID' , 'cex_pluggin');?></option>
                                                                <option value="2"><?php esc_html_e('NOMBRE' , 'cex_pluggin');?></option>
                                                                <option value="3"><?php esc_html_e('VAC&Iacute;O' , 'cex_pluggin');?></option>
                                                                <option value="4"><?php esc_html_e('SKU' , 'cex_pluggin');?></option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">                                      
                                                    <div id="wpsn" class="col-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <?php esc_html_e('Referencias para las ordenes' , 'cex_pluggin');?>
                                                                </span>
                                                            </div>
                                                            <select name="MXPS_REFETIQUETAS" id="MXPS_REFETIQUETAS"
                                                                class="form-control rounded-left-0 rounded-right m-0">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <div id="cex_logo" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                        <div class="input-group my-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="MXPS__SPAN">
                                                                    <input type="checkbox" name="MXPS_CHECKUPLOADFILE"
                                                                        id="MXPS_CHECKUPLOADFILE" accept="image/*" class="form-control m-0">
                                                                </span>
                                                            </div>
                                                            <input readonly type="text" class="form-control rounded-left-0 rounded-right m-0"
                                                                aria-label="<?php esc_html_e('Cambiar Logo de las Etiquetas', 'cex_pluggin'); ?>"
                                                                value="<?php esc_html_e('Cambiar Logo de las Etiquetas', 'cex_pluggin'); ?>">
                                                        </div>                              
                                                    </div>      
                                                    <div id="mostrarLogo" class="col-12 col-sm-6 col-md-6 col-lg-6 my-3 d-none">   
                                                        <div class="input-group my-3">    
                                                            <div>
                                                                <div class="d-none">
                                                                    <input type="file" id="MXPS_UPLOADFILE" name="MXPS_UPLOADFILE" class="CEX-file-input">
                                                                </div>
                                                            </div>  
                                                            <div id="mostrarImagenLogo" class="d-none">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend" id="divImagenLogo">
                                                                        <img class="img-logo w-50" id="imagenLogoEtiqueta" src="">
                                                                        <div class="row d-block">
                                                                            <div class="col">
                                                                                <i class="fas fa-file-upload fa-2x ml-3 mt-2 CEX-text-blue" data-toogle="tooltip" title="<?php esc_html_e('Cambiar Imagen','cex_pluggin');?>" onclick="abrirBuscarImagen();"></i>
                                                                            </div>
                                                                            <div class="col">
                                                                                <i class="fas fa-trash-alt fa-2x ml-3 mt-2 text-danger" data-toogle="tooltip" title="<?php esc_html_e('Eliminar Imagen','cex_pluggin');?>" onclick="eliminarImagen();"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>                      
                                                                </div>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                          
                                        </div>
                                        <div class="row mt-5 mb-3">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <button id="guardarDatosCliente" class="CEX-btn CEX-button-yellow"
                                                    onclick="guardarDatosUser();">
                                                    <?php esc_html_e('Guardar datos de cliente', 'cex_pluggin');?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step5">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_metodos_cron" aria-expanded="true"
                                    aria-controls="panel_metodos_cron" onclick="animacionBoton('#panel_metodos_cron');">
                                    <?php esc_html_e('OPCIONES CRON', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconCron" class="CEX-iconoInfo"
                                    title="Hacer click para ver el manual de esta secci&oacute;n"><i
                                        class="fas fa-info-circle" onclick="checkIntroOpcionesCron();"></i></span>
                            </div>

                            <div id="panel_metodos_cron" class="collapse" aria-labelledby="panel_metodos_cron"
                                data-parent="">
                                <div class="card-body border p-0 pt-3">
                                    <div class="container-fluid">
                                        <form id="formDatosCron" class="w-100">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12" id="IntroJS_MXPS_SAVEDSTATUS">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="MXPS_SAVEDSTATUS_SPAN">
                                                                <input type="checkbox" name="MXPS_SAVEDSTATUS"
                                                                    id="MXPS_SAVEDSTATUS" class="form-control m-0"
                                                                    onclick="mostrarEstadoGrabacion();">
                                                            </span>
                                                        </div>
                                                        <input readonly type="text"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            aria-label="<?php esc_html_e('Activar cambio de estado de la orden tras grabaci&oacute;n', 'cex_pluggin');?>"
                                                            value="<?php esc_html_e('Activar cambio de estado de la orden tras grabaci&oacute;n', 'cex_pluggin');?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="estado_grabacion" class="row d-none">
                                                <div
                                                    class="col-12 col-sm-12 col-md-12 col-lg-12 CEX-background-white p-3 my-1 rounded-0">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text rounded-left">
                                                                <?php esc_html_e('Estado del pedido tras la grabaci&oacute;n', 'cex_pluggin');?>
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_RECORDSTATUS" id="MXPS_RECORDSTATUS"
                                                            class="form-control rounded-left-0 rounded-right m-0" required>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12" id="IntroJS_MXPS_TRACKINGCEX">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="MXPS_TRACKINGCEX_SPAN">
                                                                <i id="info1" class="fas fa-info-circle"
                                                                    data-toogle="tooltip"></i>
                                                                <input type="checkbox" name="MXPS_TRACKINGCEX"
                                                                    id="MXPS_TRACKINGCEX" class="form-control m-0 ml-1"
                                                                    onchange="activarCambioEstado();">
                                                            </span>
                                                        </div>
                                                        <input readonly type="text"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            aria-label="Activar tracking autom&aacute;tico"
                                                            value="<?php esc_html_e('Activar tracking autom&aacute;tico', 'cex_pluggin');?>">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12" id="IntroJS_MXPS_CHANGESTATUS_SPAN">
                                                    <div class="input-group my-3 pl-4">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="MXPS_CHANGESTATUS_SPAN">
                                                                <i id="info2" class="fas fa-info-circle"
                                                                    data-toogle="tooltip"></i>
                                                                <input type="checkbox" name="MXPS_CHANGESTATUS"
                                                                    onclick="mostrarEstadosCron();"
                                                                    class="form-control m-0 ml-1" id="MXPS_CHANGESTATUS">
                                                            </span>
                                                        </div>
                                                        <input readonly type="text"
                                                            class="form-control rounded-left-0 rounded-right m-0"
                                                            aria-label="Activar cambio de estado de la orden"
                                                            value="<?php esc_html_e('Activar cambio de estado de la orden', 'cex_pluggin');?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="estados_cron"
                                                class="row jumbotron CEX-background-white p-4 my-3 rounded-0 d-none">
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text rounded-left">
                                                                <?php esc_html_e('Estado en curso', 'cex_pluggin');?>
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_SENDINGSTATUS" id="MXPS_SENDINGSTATUS"
                                                            class="form-control rounded-left-0 rounded-right m-0" required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text rounded-left">
                                                                <?php esc_html_e('Estado Entregado', 'cex_pluggin');?>
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_DELIVEREDSTATUS" id="MXPS_DELIVEREDSTATUS"
                                                            class="form-control rounded-left-0 rounded-right m-0" required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text rounded-left">
                                                                <?php esc_html_e('Estado Anulado', 'cex_pluggin');?>
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_CANCELEDSTATUS" id="MXPS_CANCELEDSTATUS"
                                                            class="form-control rounded-left-0 rounded-right m-0" required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="input-group my-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text rounded-left">
                                                                <?php esc_html_e('Estado Devuelto', 'cex_pluggin');?>
                                                            </span>
                                                        </div>
                                                        <select name="MXPS_RETURNEDSTATUS" id="MXPS_RETURNEDSTATUS"
                                                            class="form-control rounded-left-0 rounded-right m-0" required>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-5 mb-3">
                                                <div class="col-12 col-md-6 mt-3">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-1 d-md-flex">
                                                            <label class="mr-2 mb-0 font-size16"><?php esc_html_e('Tiempo de Actualización de los Estados', 'cex_pluggin');?></label>
                                                            <input type="range" name="MXPS_CRONINTERVAL" id="MXPS_CRONINTERVAL" max="8" min="2" onchange="horasIntervalo();" class="w-10 mb-2 mt-2">  
                                                            <span name="MXPS_CRONINTERVAL_TEXT" id="MXPS_CRONINTERVAL_TEXT" class="ml-2 font-size16"> </span>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                            <small><?php esc_html_e('El tiempo seleccionado determina con que frecuencia se actualizan los estados de los pedidos.', 'cex_pluggin');?>                              
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="botonesLogCron" class="col-12 col-md-2 mt-3">
                                                    <label class="font-size16 d-block"><?php esc_html_e('Descarga de informes:', 'cex_pluggin');?></label>
                                                    <a id="generarArchivoCron" 
                                                       name="generarArchivoCron" 
                                                       class="px-2 CEX-btn btn-large CEX-button-info my-2 d-block" 
                                                       onclick="generarArchivoCron(event,'log_cron');">
                                                            <?php esc_html_e('Cron', 'cex_pluggin');?>
                                                    </a>
                                                    <a id="generarArchivoPeticionCron" 
                                                       name="generarArchivoPeticionCron" 
                                                       class="px-2 CEX-btn btn-large CEX-button-info my-2 d-block" 
                                                       onclick="generarArchivoCron(event,'peticion');">
                                                            <?php esc_html_e('Request', 'cex_pluggin');?>
                                                    </a>
                                                    <a id="generarArchivoRespuestaCron" 
                                                       name="generarArchivoRespuestaCron" 
                                                       class="px-2 CEX-btn btn-large CEX-button-info my-2 d-block" 
                                                       onclick="generarArchivoCron(event,'respuesta');">
                                                            <?php esc_html_e('Respuesta WS', 'cex_pluggin');?>
                                                    </a>                                                    
                                                </div>
                                            </div>  

                                            <div class="row mt-5 mb-3">
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">                                                
                                                    <button id="guardarDatosCron" data-toggle="tooltip" data-placement="top" 
                                                    name="guardarDatosCron"
                                                    class="CEX-btn CEX-button-yellow" onclick="guardarValidarDatosCron(event);">
                                                        <?php esc_html_e('Guardar configuraci&oacute;n Cron', 'cex_pluggin');?>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step6">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_productos_cex" aria-expanded="true"
                                    aria-controls="panel_productos_cex" onclick="animacionBoton('#panel_productos_cex');">
                                    <?php esc_html_e('PRODUCTOS CEX', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconProductos" class="CEX-iconoInfo"
                                    title="Hacer click para ver el manual de esta secci&oacute;n"><i
                                        class="fas fa-info-circle" onclick="checkIntroProductosCEX();"></i></span>
                            </div>

                            <div id="panel_productos_cex" class="collapse" aria-labelledby="panel_productos_cex"
                                data-parent="">
                                <div class="card-body border">
                                    <div class="container-fluid my-3">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group" id="productos_cex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-5 mb-3">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <button id="guardarProductosCex" class="CEX-btn CEX-button-yellow"
                                                    onclick="guardarProductosCex();">
                                                    <?php esc_html_e('Guardar productos', 'cex_pluggin');?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step7">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_relacion_trans" aria-expanded="true"
                                    aria-controls="panel_relacion_trans" onclick="animacionBoton('#panel_relacion_trans');">
                                    <?php esc_html_e('RELACIONAR ZONAS DE ENV&Iacute;O Y TRANSPORTISTAS', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                            class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconRelacionTransportistas" class="CEX-iconoInfo"
                                    title="<?php esc_html_e('Hacer click para ver el manual de esta secci&oacute;n', 'cex_pluggin');?>"><i
                                        class="fas fa-info-circle" onclick="checkIntroZonasTransportistas();"></i></span>
                            </div>

                            <div id="panel_relacion_trans" class="collapse" aria-labelledby="panel_relacion_trans"
                                data-parent="">
                                <div class="card-body border p-0">
                                    <div class="container-fluid mb-3">
                                        <form id="transportistas"></form>
                                        <div class="row mt-2 mb-3">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <button id="guardarTransportistas" class="CEX-btn CEX-button-yellow"
                                                    onclick='mapear_transportistas();'>
                                                    <?php esc_html_e('Guardar relaciones de transportistas', 'cex_pluggin');?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="CEX-loading" class="modal d-none"></div>
                    </div>
                    <div id="acordeonSoporte" class="accordion CEX-panel CEX-panel-primary">
                        <div class="card m-0 p-0 w-100 mw-100 border-0">
                            <div class="card-header m-0 p-0 CEX-panel-heading" id="step8">
                                <a class="mb-0 CEX-text-white p-2 d-block" data-toggle="collapse"
                                    data-target="#panel_soporte" aria-expanded="true"
                                    aria-controls="panel_soporte" onclick="animacionBoton('#panel_soporte');">
                                    <?php esc_html_e('SOPORTE', 'cex_pluggin');?>
                                    <span id="Cex-arrow" class="float-right clickable"><i
                                        class="fas fa-chevron-down"></i></span>
                                </a>
                                <span id="iconSoporte" class="CEX-iconoInfo"
                                title="<?php esc_html_e('Hacer click para ver el manual de esta sección', 'cex_pluggin');?>"><i
                                    class="fas fa-info-circle" onclick="checkIntroSoporte();"></i></span>
                            </div>

                            <div id="panel_soporte" class="collapse" aria-labelledby="panel_soporte" data-parent="">
                                <div class="card-body border p-0">
                                    <div class="container-fluid mb-3">
                                        <div class="row mt-5 mb-3">
                                            <div id="buttonHistorico" class="col-6">
                                                <label class="font-size16 d-block"><?php esc_html_e('Descarga de informes de las bases de datos:', 'cex_pluggin');?></label>
                                                <div class="form-group my-2 d-flex">
                                                    <select class="form-control rounded-left-0 rounded-right mr-2" style="width: 30%;" id="datos_tablas" name="datos_tablas" required>
                                                        <option value="cex_savedships"><?php esc_html_e('Savedships', 'cex_pluggin');?></option>
                                                        <option value="cex_history"><?php esc_html_e('History', 'cex_pluggin');?></option>
                                                        <option value="cex_migrations"><?php esc_html_e('Migrations', 'cex_pluggin');?></option>
                                                    </select>
                                                    <button id="buttonBBDD" class="px-2 CEX-btn btn-large CEX-button-info my-auto d-block" onclick="generarLogBBDD();">
                                                        <?php esc_html_e('Descargar', 'cex_pluggin');?>
                                                    </button>
                                                    <a id="descargaBBDD" href="" download d-none></a>
                                                </div>
                                                <a id="descargaBBDD" href="<?php echo esc_url(plugins_url('/../class_correosexpress.php',__FILE__));?>" download d-none></a>
                                            </div>
                                            <div id="shb_cex" class="col-6 d-none">
                                                <button id="ejecutarUpdateButton" name="ejecutarUpdateButton" onclick="ejecutarUpdate();" class="px-2 CEX-btn btn-large CEX-button-info my-auto">
                                                    <?php esc_html_e('Ejecutar actualizaciones', 'cex_pluggin');?>
                                                </button>
                                                 <button id="ejecutarCronButton" name="ejecutarCronButton" onclick="ejecutarCron();" class="px-2 CEX-btn btn-large CEX-button-info my-auto">
                                                    Cron
                                                </button>
                                                <button id="btnBorrarCarpetaLog" class="px-2 CEX-btn btn-large CEX-button-info my-auto" onclick="BorrarCarpetaLog()">Delete Log Folder</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a id="log_cron" download="log_cron.pdf" class="d-none" href=""></a>

    <!-- JAVASCRIPT -->

    <script type="text/javascript">
    //Cabeceras de la configuraci&oacute;n
    var step1 =
        "<?php esc_html_e('Este es la secci&oacute;n de configuraci&oacute;n para m&oacute;dulo de correos express. Es la parte mas importante de nuestro m&oacute;dulo y su correcto funcionamiento depende de ella.', 'cex_pluggin');?>";
    var step2 =
        "<?php esc_html_e('Gesti&oacute;n de c&oacute;digo de cliente, un c&oacute;digo activo por tienda. El c&oacute;digo de cliente activo se determina mediante el “Remitente por defecto”.', 'cex_pluggin');?>";
    var step3 =
        "<?php esc_html_e('Gesti&oacute;n de remitentes, podr&aacute;s tener todos los que quieras. La tienda tendr&aacute; solo uno activo, que se determinar&aacute; con el “Remitente por defecto”.', 'cex_pluggin');?>";
    var step4 = "<?php esc_html_e('Configuraci&oacute;n de usuario de la tienda.', 'cex_pluggin');?>";
    var step5 =
        "<?php esc_html_e('Este apartado nos permitir&aacute; configurar los cambios de estado autom&aacute;ticos para nuestros pedidos.', 'cex_pluggin');?>";
    var step6 =
        "<?php esc_html_e('Listado de los productos que tiene a su disposici&oacute;n Correos Express.', 'cex_pluggin');?>";
    var step7 =
        "<?php esc_html_e('Apartado en el que vincularemos los transportistas con nuestros productos.', 'cex_pluggin');?>";

    //Configuraci&oacute;n de usuario
    var stepUser1 =
        "<?php esc_html_e('Apartado en el que guardamos las credenciales facilitadas por Correos Express. En caso de no disponer de él, pongase en contacto con Correos Express', 'cex_pluggin');?>";
    var stepUser2 =
        "<?php esc_html_e('Credencial facilitada por Correos Express. En caso de no disponer de ella, pongase en contacto con Correos Express.', 'cex_pluggin');?>";
    var stepUser3 = "<?php esc_html_e('URL del web service de env&iacute;o.', 'cex_pluggin');?>";
    var stepUser4 = "<?php esc_html_e('URL del web service de recogida.', 'cex_pluggin');?>";
    var stepUser5 = "<?php esc_html_e('URL del web service de seguimiento de env&iacute;o.', 'cex_pluggin');?>";
    var stepUser6 =
        "<?php esc_html_e('Cantidad de bultos predeterminados para cada env&iacute;o, este valor podr&aacute; ser modificado m&aacute;s adelante en el detalle del pedido.', 'cex_pluggin');?>";
    var stepUser7 = "<?php esc_html_e('A elegir entre: Adhesiva, medio folio y t&eacute;rmica.', 'cex_pluggin');?>";
    var stepUser8 =
        "<?php esc_html_e('La informaci&oacute;n de los clientes de la etiqueta se obtiene de una de las dos direcciones de una orden', 'cex_pluggin');?>";
    var stepUser9 =
        "<?php esc_html_e('Seleccione el m&eacute;todo de pago contra reembolso en caso de que se utilice esta modalidad en caso contrario seleccione “Ninguno”.', 'cex_pluggin');?>";
    var stepUser10 =
        "<?php esc_html_e('Esta opci&oacute;n permite activar el enlace de seguimiento, en la secci&oacute;n pedidos.', 'cex_pluggin');?>";
    var stepUser11 =
        "<?php esc_html_e('Si marca esta opci&oacute;n, las etiquetas se generar&aacute;n sin remitente.', 'cex_pluggin');?>";
    var stepUser12 =
        "<?php esc_html_e('Cantidad de kilos predeterminados para cada env&iacute;o, este valor podr&aacute; ser modificado m&aacute;s adelante en el detalle del pedido.', 'cex_pluggin');?>";
    var stepUser14 =
        "<?php esc_html_e('Datos a mostrar en la etiqueta.', 'cex_pluggin');?>";
    var wpsn =
        "<?php esc_html_e('Referencias para las órdenes.', 'cex_pluggin');?>";
    var cex_logo =
        "<?php esc_html_e('Cambiar el logo que aparece en las etiquetas.', 'cex_pluggin');?>";
    var guardarDatosCliente =
        "<?php esc_html_e('Bot&oacute;n para guardar nuestra configuraci&oacute;n de usuario.', 'cex_pluggin');?>";

    //Configuraci&oacute;n C&oacute;digo cliente
    var customer_code =
        "<?php esc_html_e('Codigo entregado por Correos Express, consta de 9 d&iacute;gitos. En caso de no disponer de &eacute;l, pongase en contacto con Correos Express.', 'cex_pluggin');?>";
    var saved_codes =
        "<?php esc_html_e('Tabla en la que se nos muestran nuestros c&oacute;digos de cliente, asi como los botones de borrado y modificaci&oacute;n del mismo.', 'cex_pluggin');?>";
    var guardar_cod_cliente =
        "<?php esc_html_e('Desde aqu&iacute; guardaremos nuestro c&oacute;digo de cliente.', 'cex_pluggin');?>";

    //Configuraci&oacute;n Edici&oacute;n C&oacute;digo cliente
    var customer_code_modal =
        "<?php esc_html_e('Codigo entregado por Correos Express, consta de 9 d&iacute;gitos. En caso de no disponer de &eacute;l, pongase en contacto con Correos Express.', 'cex_pluggin');?>";
    var guardar_cod_cliente_modal =
        "<?php esc_html_e('Desde aqu&iacute; guardaremos nuestro c&oacute;digo de cliente.', 'cex_pluggin');?>";
    var cerrar_cod_cliente_modal =
        "<?php esc_html_e('Desde aqu&iacute; cerraremos la edici&oacute;n de nuestro c&oacute;digo de cliente, sin guardar.', 'cex_pluggin');?>";

    //Configuraci&oacute;n de Remitente
    var MXPS_DEFAULTSEND =
        "<?php esc_html_e('Selecci&oacute;n del remitente por defecto, ir&aacute; asociado a la tienda en la que se encuentre y solo a ella. Este ser&aacute; el remitente que utilizar&aacute; la tienda en los pedidos. Es un campo obligatorio.', 'cex_pluggin');?>";
    var literalGuardarRemitenteDefecto =
        "<?php esc_html_e('Bot&oacute;n mediante el que guardaremos nuestro remitente por defecto.', 'cex_pluggin');?>";
    var div_codigo_cliente =
        "<?php esc_html_e('C&oacute;digo de cliente que se asociar&aacute; al remitente, no se puede cambiar de c&oacute;digo asociado una vez se genere el remitente.', 'cex_pluggin');?>";
    var div_datos_cliente =
        "<?php esc_html_e('Datos personales del remitente.', 'cex_pluggin');?>";
    var div_bloqueHoraDesdeHasta =
        "<?php esc_html_e('Intervalo de horas para la recogida.', 'cex_pluggin');?>";
    var introHoraDesde =
        "<?php esc_html_e('Hora desde la que est&aacute; disponible para la recogida.', 'cex_pluggin');?>";
    var introHoraHasta =
        "<?php esc_html_e('Hora hasta la que est&aacute; disponible para la recogida.', 'cex_pluggin');?>";
    var bloqueHoraDesdeHasta =
        "<?php esc_html_e('Debe haber un m&iacute;nimo de dos horas de diferencia.', 'cex_pluggin');?>";
    var guardarRemitente =
        "<?php esc_html_e('Bot&oacute;n desde el que guardaremos el remitente creado.', 'cex_pluggin');?>";
    var cancelar = "<?php esc_html_e('Reiniciaremos el formulario.', 'cex_pluggin');?>";
    var savedsenders =
        "<?php esc_html_e('Tabla que muestra todos los remitentes creados para la tienda en curso.', 'cex_pluggin');?>";

    //Configuraci&oacute;n Edici&oacute;n Remitente                
    var remitente_codigo_cliente_modal =
        "<?php esc_html_e('C&oacute;digo de cliente que se asociar&aacute; al remitente, no se puede cambiar de c&oacute;digo asociado una vez se genere el remitente.', 'cex_pluggin');?>";
    var EdicionHoraDesde =
        "<?php esc_html_e('Hora desde la que est&aacute; disponible para la recogida.', 'cex_pluggin');?>";
    var EdicionHoraHasta =
        "<?php esc_html_e('Hora hasta la que est&aacute; disponible para la recogida.', 'cex_pluggin');?>";
    var EdicionHoras = "<?php esc_html_e('Debe haber un m&iacute;nimo de dos horas de diferencia.', 'cex_pluggin');?>";
    var guardar_modal_remitente =
        "<?php esc_html_e('Bot&oacute;n desde el que guardaremos los cambios del remitente.', 'cex_pluggin');?>";
    var cerrar_modal_remitente = "<?php esc_html_e('Cerrar el formulario, sin guardar.', 'cex_pluggin');?>";

    //Configuraci&oacute;n de Productos
    var panel_productos_cex =
        "<?php esc_html_e('Listado de los productos que tiene a su disposici&oacute;n Correos Express. Marque &uacute;nicamente los productos pactados con su comercial. Si se selecciona alg&uacute;n producto no contratado, se producir&aacute; un error al grabar el env&iacute;o.', 'cex_pluggin');?>";
    var literalGuardarProductosCex =
        "<?php esc_html_e('Guardaremos los productos seleccionados, para posteriormente asignarlos a los transportistas.', 'cex_pluggin');?>";

    //Relaci&oacute;n transportistas productos
    var panel_relacion_trans =
        "<?php esc_html_e('En este formulario se relacionan los transportistas con los productos de Correos Express contratados.', 'cex_pluggin');?>";
    var nombreProductos =
        "<?php esc_html_e('Listado de productos seleccionados en la secci&oacute;n “Productos Cex”.', 'cex_pluggin');?>";
    var nombreCarriers =
        "<?php esc_html_e('Los transportistas/m&eacute;todos de env&iacute;o son aquellos configurados en la secci&oacute;n “Env&iacute;o” de Woocommerce.', 'cex_pluggin');?>";
    var nombreCarriersProductos =
        "<?php esc_html_e('La relaci&oacute;n entre ambos es 1:1, es decir un transportista esta asociado a un producto. Si se cambia el producto asociado a un transportista, se modificar&aacute; todo el hist&oacute;rico de pedidos relacionado con &eacute;l.', 'cex_pluggin');?>";
    var guardarTransportistas =
        "<?php esc_html_e('Guardaremos la configuraci&oacute;n de transportistas para utilizarlos en nuestra tienda.', 'cex_pluggin');?>";

    //Configuraci&oacute;n CRON
    var introPanelCron =
        "<?php esc_html_e('En este formulario nos encargamos de configurar el CRON, el cual se encarga de modificar el estado del pedido autom&aacute;ticamente cuando se cumplan las condiciones dadas.', 'cex_pluggin');?>";
    var MXPS_SAVEDSTATUS =
        "<?php esc_html_e('Al activar esta opci&oacute;n, el pedido cambiar&aacute; al estado seleccionado tras la grabaci&oacute;n y obtenci&oacute;n del n&uacute;mero de env&iacute;o.', 'cex_pluggin');?>";
    var MXPS_TRACKINGCEX =
        "<?php esc_html_e('Al activar el tracking autom&aacute;tico de pedidos, la tienda consultar&aacute; de forma peri&oacute;dica en que estado se encuentra el pedido y lo mostrar&aacute; en la tabla de hist&oacute;rico en la administraci&oacute;n de las ordenes.', 'cex_pluggin');?>";
    var MXPS_CHANGESTATUS_SPAN =
        "<?php esc_html_e('Si se activa, actualizar&aacute; el estado de la orden en funci&oacute;n del CRON y la selecci&oacute;n de estados realizada.', 'cex_pluggin');?>";
    var literalGuardarDatosCron =
        "<?php esc_html_e('Guardaremos la configuraci&oacute;n de nuestro Cron.', 'cex_pluggin');?>";

    var descargarArchivoCron =
        "<?php esc_html_e(' Descargar el log de la última ejecución del cron.', 'cex_pluggin');?>";
    var descargarArchivoPeticion =
        "<?php esc_html_e('Descargar el log de la última petición al WS.', 'cex_pluggin');?>";
    var descargarArchivoRespuesta =
        "<?php esc_html_e('Descargar el log de la última respuesta del WS.', 'cex_pluggin');?>";

    var titleErrorActualizarCliente = "<?php esc_html_e('C&oacute;digo de cliente invalido', 'cex_pluggin');?>";
    var textErrorActualizarCliente = "<?php esc_html_e('El c&oacute;digo de cliente debe tener 9 d&iacute;gitos', 'cex_pluggin');?>";

    var titleErrorTamañoImagen = "<?php esc_html_e('Imagen logo demasiado grande', 'cex_pluggin');?>";
    var textErrorTamañoImagen = "<?php esc_html_e('El logo es demasiado grande, debe ser menor de 400 kB', 'cex_pluggin');?>"; 

    // Seccion Soporte
    var panel_soporte = "<?php esc_html_e('En esta sección se pueden descargar los informes para el soporte con Corres Express.', 'cex_pluggin');?>";
    var step8 = "<?php esc_html_e('En esta sección se pueden descargar los informes para el soporte con Corres Express.', 'cex_pluggin');?>";
    var buttonHistorico = "<?php esc_html_e('Descarga los últimos registros de la base de datos histórico, saveships y migrations con la información de las últimas peticiones y respuestas del WS.','cex_pluggin');?>";

    var guardarProd = "<?php esc_html_e('Guardar Producto','cex_pluggin');?>";
    var unProducto = "<?php esc_html_e('Debe seleccionar algún producto para continuar','cex_pluggin');?>";
    var guardarTransport = "<?php esc_html('Guardar Transportistas','cex_pluggin');?>";
    var unTransport = "<?php esc_html_e('Debe seleccionar algún producto para configurar los transportistas','cex_pluggin');?>";


    (jQuery)(document).ready(function($) {
        inicializarForms();
        inicializarDatosUsuario();
        (jQuery)("body").css('overflow-y','scroll');
    });

    function horasIntervalo(){
        var valor=(jQuery)('#MXPS_CRONINTERVAL').val();       
        switch(valor){
            case '2':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("2 horas");
            break;
            case '3':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("3 horas");
            break;
            case '4':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("4 horas");
            break;
            case '5':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("5 horas");
            break;
            case '6':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("6 horas");
            break;
            case '7':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("7 horas");
            break;
            case '8':
            (jQuery)('#MXPS_CRONINTERVAL_TEXT').html("8 horas");
            break;
        }         
    }

    function ejecutarUpdate() {

        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_ejecutarUpdateDB',
                'token': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
            },
            error: function(msg) {
            }
        });

    }

    function inicializarForms() {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_get_init_form',
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'

            },
            success: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function inicializarDatosUsuario() {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_get_user_config',
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarEstadosFormularioUser(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
                horasIntervalo();
            },
            error: function(msg) {
                pintarEstadosFormularioUser(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    document.addEventListener('mouseover', function(e) {
        (jQuery)('#info1').attr('title',
            '<?php esc_html_e('Esta opción activa/desactiva el seguimiento de los pedidos', 'cex_pluggin');?>'
        );
        (jQuery)('#info1').attr('data-toggle', 'tooltip');
        (jQuery)('#info2').attr('title',
            '<?php esc_html_e('Esta opción determina si cuando el pedido se actualiza a medida que pasa por los diferentes estados de seguimiento', 'cex_pluggin');?>'
        );
        (jQuery)('#info2').attr('data-toggle', 'tooltip');
    }, false);

    //CODIGOS CLIENTE

    function validarCodigo(){        
        if((jQuery)('#customer_code').val().length==9){
            return true;
        }

        var mensaje = new Array();
        mensaje['title'] = '<?php esc_html_e('Error de Código de Cliente', 'cex_pluggin');?>';
        mensaje['mensaje'] ='<?php esc_html_e('El número de dígitos no corresponden con ningún Código Cliente', 'cex_pluggin');?>';
        mensaje['type'] = "error";

        pintarNotificacion(mensaje);
        return false;
    }
    function guardarCodigoCliente() {
        if(validarCodigo()){
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'action': 'cex_guardar_codigo_cliente',
                    'customer_code': (jQuery)('#customer_code').val(),
                    'code_demand': (jQuery)('#code_demand').val(),
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
            (jQuery)('#customer_code').val('');
            (jQuery)('#code_demand').val('');
        }
    }

    function pedirCodigoCliente(id) {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_retornar_codigo_cliente',
                'id': id,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                var cliente = JSON.parse(msg);
                abrirModalCodigoCliente(cliente);
            },
            error: function(msg) {
            }
        });
    }

    function actualizarCodigoCliente() {
        if (document.getElementById("codigo_cliente_modal").checkValidity()) {
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'id': (jQuery)('#id_cod_cliente_modal').val(),
                    'action': 'cex_actualizar_codigo_cliente',
                    'customer_code': (jQuery)('#codigo_cliente_modal').val(),
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
            (jQuery)('#cerrar_modal_codigo').click();
        }else{
            PNotify.prototype.options.styling = "bootstrap3";
            new PNotify({
                title: '<?php esc_html_e("C&oacute;digo de cliente invalido", 'cex_pluggin');?>',
                text: '<?php esc_html_e("El c&oacute;digo de cliente debe tener 9 d&iacute;gitos", 'cex_pluggin');?>',
                type: "error",
                stack: myStack
            })
        }
    }

    function borrarCodigoCliente(id) {
        PNotify.prototype.options.styling = "bootstrap3";
        new PNotify({
            title: '<?php esc_html_e("Confirma el borrado", "cex_pluggin");?>',
            text: '<?php esc_html_e("¿Estas seguro de borrar el c&oacute;digo de cliente y sus remitentes asociados?", "cex_pluggin");?>',
            icon: 'fas fa-question-circle',
            hide: false,
            confirm: {
                confirm: true
            },
            buttons: {
                closer: false,
                sticker: false
            }
        }).get().on('pnotify.confirm', function() {
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'action': 'cex_borrar_codigo_cliente',
                    'id': id,
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
        }).on('pnotify.cancel', function() {
            //alert('ok. Chicken, chicken, clocloclo.');
        });
    }

    function calcular_codigo_customer() {
        var codigo_costumer = (jQuery)('#customer_code').val();
        (jQuery)('#code_demand').val("W" + codigo_costumer);
    }

    // REMITENTES
    function validarRemitente(){
        var validate = 0;
        (jQuery)("#formCrearRemt input").each(function () {
            var input = (jQuery)(this);
            if (input.val() != "" && input.val() != "undefined") {
                (jQuery)(this).parent().removeClass("has-error");
            } else {
                validate++;
                (jQuery)(this).parent().addClass("has-error");
            }
            if(validate == 0){
                (jQuery)("#guardarRemitente").prop("disabled", false);
            }else {
                (jQuery)("#guardarRemitente").prop("disabled", true);
            }
        });
    }
    (jQuery)("#formCrearRemt input").blur(function(event) {
        validarRemitente();
    });
    (jQuery)("#formCrearRemt").click(function(event) {
        validarRemitente();
    });
    (jQuery)('#guardarRemitente').mouseover(function(event) {
        validarRemitente();
    });

    function validarEditarRemitente(){
        var validateEditar = 0;
        (jQuery)("#formUpdateRemt input").each(function () {
            var input = (jQuery)(this);
            if (input.val() != "" && input.val() != "undefined") {
                (jQuery)(this).parent().removeClass("has-error");
            } else {
                validateEditar++;
                (jQuery)(this).parent().addClass("has-error");
            }
            if(validateEditar == 0){
                (jQuery)("#guardar_modal_remitente").prop("disabled", false);
            }else {
                (jQuery)("#guardar_modal_remitente").prop("disabled", true);
            }
        });
    }

    function crearRemitente(event) {
        event.preventDefault();
        // Si el valor de codigo_cliente == ' ' no enviar, decir que tiene que dar de alta cod_clien
        var validate = {};
        (jQuery)('#formCrearRemt select').each(function() {
            var select = (jQuery)(this);
            if (select.val() != '' && select.val() != 'undefined') {
                validate[this.name] = true;
                (jQuery)(this).parent().removeClass('has-error');
            } else {
                validate[this.name] = false;
                (jQuery)(this).parent().addClass('has-error');
            }
        });
        (jQuery)('#formCrearRemt input').each(function() {
            var input = (jQuery)(this);
            if (input.val() != '' && input.val() != 'undefined') {
                validate[this.name] = true;
                (jQuery)(this).parent().removeClass('has-error');
            } else {
                validate[this.name] = false;
                (jQuery)(this).parent().addClass('has-error');
            }
        });
        var save = true;
        (jQuery).each(validate, function(item, value) {
            if (value == false) {
                save = false;
                return save;
            } else {
                save = true;
            }
        });
        if (save == true) {
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'action': 'cex_guardar_remitente',
                    'name': (jQuery)('#name_sender').val(),
                    'address': (jQuery)('#address_sender').val(),
                    'postcode': (jQuery)('#postcode_sender').val(),
                    'city': (jQuery)('#city_sender').val(),
                    'iso_code': (jQuery)('#country_sender').val(),
                    'contact': (jQuery)('#contact_sender').val(),
                    'phone': (jQuery)('#phone_sender').val(),
                    'email': (jQuery)('#email_sender').val(),
                    'from_hour': (jQuery)('#fromHH_sender').val(),
                    'from_minute': (jQuery)('#fromMM_sender').val(),
                    'to_hour': (jQuery)('#toHH_sender').val(),
                    'to_minute': (jQuery)('#toMM_sender').val(),
                    'codigo_cliente': (jQuery)('#codigo_cliente').val(),
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
            (jQuery)('#name_sender').val('');
            (jQuery)('#address_sender').val('');
            (jQuery)('#postcode_sender').val('');
            (jQuery)('#city_sender').val('');
            (jQuery)('#contact_sender').val('');
            (jQuery)('#phone_sender').val('');
            (jQuery)('#email_sender').val('');
            (jQuery)('#fromHH_sender').val('');
            (jQuery)('#fromMM_sender').val('');
            (jQuery)('#toHH_sender').val('');
            (jQuery)('#toMM_sender').val('');
        }
    }

    function pedirRemitente(id) {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_retornar_remitente',
                'id': id,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                var remitente = JSON.parse(msg);
                abrirModalRemitente(remitente);
            },
            error: function(msg) {
            }
        });
        (jQuery)('#id').val('');
    }

    function guardarRemitenteDefecto() {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'MXPS_DEFAULTSEND': (jQuery)('#MXPS_DEFAULTSEND').find(":selected").val(),
                'action': 'cex_guardarRemitenteDefecto',
                'campo': 'MXPS_DEFAULTSEND',
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    function actualizarRemitente() {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'id': (jQuery)('#id_modal').val(),
                'action': 'cex_actualizar_remitente',
                'name': (jQuery)('#name_sender_modal').val(),
                'address': (jQuery)('#address_sender_modal').val(),
                'postcode': (jQuery)('#postcode_sender_modal').val(),
                'city': (jQuery)('#city_sender_modal').val(),
                'iso_code': (jQuery)('#country_sender_modal').val(),
                'contact': (jQuery)('#contact_sender_modal').val(),
                'phone': (jQuery)('#phone_sender_modal').val(),
                'email': (jQuery)('#email_sender_modal').val(),
                'from_hour': (jQuery)('#fromHH_sender_modal').val(),
                'from_minute': (jQuery)('#fromMM_sender_modal').val(),
                'to_hour': (jQuery)('#toHH_sender_modal').val(),
                'to_minute': (jQuery)('#toMM_sender_modal').val(),
                'codigo_cliente': (jQuery)('#codigo_cliente_modal').val(),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
        (jQuery)('#cerrar_modal_remitente').click();
    }

    function borrarRemitente(id) {
        PNotify.prototype.options.styling = "bootstrap3";
        new PNotify({
            title: '<?php esc_html_e("Confirma el borrado", "cex_pluggin");?>',
            text: '<?php esc_html_e("¿Estas seguro de borrar el remitente?", "cex_pluggin");?>',
            icon: 'fas fa-question-circle',
            hide: false,
            confirm: {
                confirm: true
            },
            buttons: {
                closer: false,
                sticker: false
            },
        }).get().on('pnotify.confirm', function() {
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'action': 'cex_borrar_remitente',
                    'id': id,
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
            (jQuery)('#id').val('');
        }).on('pnotify.cancel', function() {
            //alert('ok. Chicken, chicken, clocloclo.');
        });
    }

    ////////////////////////// Logo ///////////////////////////////////
    (jQuery)('#MXPS_CHECKUPLOADFILE').click(function(event) {
      mostrarFormLogo(' ');
    });
    
    (jQuery)('#MXPS_UPLOADFILE').click(function(event) {
      cambiarImagen(event);
    });
    
    (jQuery)('#MXPS_UPLOADFILE').change(function(event) {
      cambiarImagen(event);
    });

    function mostrarFormLogo(url) {        
        if ((jQuery)('#MXPS_CHECKUPLOADFILE').prop('checked') == true) { 
            if((jQuery)('#mostrarImagenLogo').hasClass('d-none')){
                (jQuery)('#MXPS_UPLOADFILE').click();
                (jQuery)('#mostrarLogo').removeClass('d-none');
                (jQuery)('#mostrarImagenLogo').removeClass('d-none');
                (jQuery)('#mostrarLogo').addClass('d-flex');
                (jQuery)("#imagenLogoEtiqueta").attr("src", url);                
            }            
        } else {
            (jQuery)('#mostrarLogo').addClass('d-none');
            (jQuery)('#mostrarImagenLogo').addClass('d-none');
            (jQuery)('#mostrarLogo').removeClass('d-flex');
            eliminarImagen();
        }
    }
        
    function abrirBuscarImagen(){
        (jQuery)("#MXPS_UPLOADFILE").click();
    }
    
    function eliminarImagen(){
        var formdata = new FormData();
        formdata.append("action", "cex_eliminar_logo");
        formdata.append('nonce', '<?php echo wp_create_nonce('cex-nonce'); ?>');
  
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            processData: false,
            contentType: false,
            data: formdata,
            success: function(msg) {               
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
                (jQuery)('#MXPS_CHECKUPLOADFILE').prop('checked',false);
                (jQuery)('#mostrarLogo').addClass('d-none');
                (jQuery)('#mostrarImagenLogo').addClass('d-none');
                (jQuery)('#mostrarLogo').removeClass('d-flex');
            },
            error: function(msg) {
                
            }
        });
    }

    function cambiarImagen(e){
        if(e.target.files.length <= 0){
            //(jQuery)('#divImagenLogo').addClass('d-none');
            //(jQuery)('#MXPS_CHECKUPLOADFILE').prop('checked',false); 
            return;
        }else{ 
            //(jQuery)('#divImagenLogo').removeClass('d-none');
            //(jQuery)('#MXPS_CHECKUPLOADFILE').prop('checked',true); 
            var fileSize = e.target.files[0].size;
            var sizekiloByte = parseInt(fileSize / 1024);
                   
            if (sizekiloByte > 400) {
                PNotify.prototype.options.styling = "bootstrap3";
                new PNotify({
                    title: titleErrorTamañoImagen,
                    text:  textErrorTamañoImagen,
                    type: "error",
                    stack: myStack
                })
            }

            var url = URL.createObjectURL(e.target.files[0]);
            var random = Math.floor(Math.random() * 100);    
            pintarImagen(url);

            var formdata = new FormData();
            formdata.append("action", "cex_guardar_imagen_logo");
            formdata.append('MXPS_UPLOADFILE', (jQuery)('#MXPS_UPLOADFILE').prop("files")[0]);      
            formdata.append('MXPS_CHECKUPLOADFILE', (jQuery)('#MXPS_CHECKUPLOADFILE').prop('checked'));
            formdata.append('nonce', '<?php echo wp_create_nonce('cex-nonce'); ?>');
  
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                processData: false,
                contentType: false,
                data: formdata,
                success: function(msg) {               
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                    if(msg.imagenLogo){                    
                        var random = Math.floor(Math.random() * 100);
                        pintarImagen(msg.imagenLogo);
                    }         
                },
                error: function(msg) {
                    
                }
            });
        }
    }

    function pintarImagen(url){
        var img = "<img class='img-logo w-50' id='imagenLogoEtiqueta' src='"+url+"'>";
        var iconos = '<div class="row d-block"><div class="col"><i class="fas fa-file-upload fa-2x ml-3 mt-2 CEX-text-blue" data-toogle="tooltip" title="Subir una nueva imagen" onclick="abrirBuscarImagen();"></i></div><div class="col"><i class="fas fa-trash-alt fa-2x ml-3 mt-2 text-danger" data-toogle="tooltip" title="Eliminar Imagen" onclick="eliminarImagen();"></i></div></div><a href="" title=""></a>';
        (jQuery)('#divImagenLogo').html(img+iconos);
    }

    //DATOS DE CONFIGURACION
    function guardarDatosUser() {
        
        var checkLog = (jQuery)('#MXPS_CHECK_LOG').prop('checked');
        mostrarOcultarLog(checkLog);
       
        var formdata = new FormData();
        var MXPS_DEFAULTBUL= (jQuery)('#MXPS_DEFAULTBUL').val();
        MXPS_DEFAULTBUL=MXPS_DEFAULTBUL.replace(/^0+/, '');
        formdata.append("action", "cex_guardar_customer_options");        
		formdata.append('MXPS_DEFAULTWS', (jQuery)('#MXPS_DEFAULTWS').find(":selected").val());
        formdata.append('MXPS_WSURL', (jQuery)('#MXPS_WSURL').val());
        formdata.append('MXPS_WSURLREC', (jQuery)('#MXPS_WSURLREC').val());
        formdata.append('MXPS_WSURLSEG', (jQuery)('#MXPS_WSURLSEG').val());
        formdata.append('MXPS_WSURL_REST', (jQuery)('#MXPS_WSURL_REST').val());
        formdata.append('MXPS_WSURLREC_REST', (jQuery)('#MXPS_WSURLREC_REST').val());
        formdata.append('MXPS_WSURLSEG_REST', (jQuery)('#MXPS_WSURLSEG_REST').val());
        formdata.append('MXPS_ENABLEWEIGHT', (jQuery)('#MXPS_ENABLEWEIGHT').prop('checked'));
        formdata.append('MXPS_DEFAULTKG', (jQuery)('#MXPS_DEFAULTKG').val());
        formdata.append('MXPS_CHECK_LOG', checkLog);
        formdata.append('MXPS_ENABLESHIPPINGTRACK', (jQuery)('#MXPS_ENABLESHIPPINGTRACK').prop('checked'));
        formdata.append('MXPS_DEFAULTBUL', MXPS_DEFAULTBUL);

        if((jQuery)('#MXPS_LABELSENDER').prop('checked')){
        formdata.append('MXPS_LABELSENDER_TEXT',(jQuery)('#MXPS_LABELSENDER_TEXT').val());
        }
        else {
            formdata.append('MXPS_LABELSENDER_TEXT',' ');
            (jQuery)('#MXPS_LABELSENDER_TEXT').val(' ');
        }

        formdata.append('MXPS_DEFAULTPDF', (jQuery)('#MXPS_DEFAULTPDF').find(":selected").val());
        formdata.append('MXPS_DEFAULTPAYBACK', (jQuery)('#MXPS_DEFAULTPAYBACK').find(":selected").val());
        formdata.append('MXPS_DEFAULTDELIVER', (jQuery)('#MXPS_DEFAULTDELIVER').find(":selected").val());
        formdata.append('MXPS_LABELSENDER', (jQuery)('#MXPS_LABELSENDER').prop('checked'));
        formdata.append('MXPS_NODATAPROTECTION', (jQuery)('#MXPS_NODATAPROTECTION').prop('checked'));
        formdata.append('MXPS_DATAPROTECTIONVALUE', (jQuery)('#MXPS_DATAPROTECTIONVALUE').find(":selected").val());
        formdata.append('MXPS_REFETIQUETAS', (jQuery)('#MXPS_REFETIQUETAS').find(":selected").val());        
        formdata.append('nonce', '<?php echo wp_create_nonce('cex-nonce'); ?>');
  
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            processData: false,
            contentType: false,
            data: formdata,
            success: function(msg) {               
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
                var retorno = JSON.parse(msg);
                if(retorno.imagenLogo){                    
                    var random = Math.floor(Math.random() * 100);
                    retorno.imagenLogo = retorno.imagenLogo.replace(/\\/g,"/");    
                    pintarImagen(retorno.imagenLogo);                   
                }                
            },
            error: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }


    function mostrarOcultarLog(activado){
        if(activado || activado == "true"){
            (jQuery)("#botonesLogCron").removeClass('d-none');
            (jQuery)("#acordeonSoporte").removeClass('d-none');
        }else{
            (jQuery)("#botonesLogCron").addClass('d-none');
            (jQuery)("#acordeonSoporte").addClass('d-none');
        }
    }
     // DATOS DE USUARIO


    function validarCredenciales(){
        (jQuery)('#CEX-loading').removeClass('d-none');
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_validar_credenciales',
                'user' : (jQuery)('#MXPS_USER').val(),
                'pass' : (jQuery)('#MXPS_PASSWD').val(),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(validation) {                
                return validation;
            },
            error: function(validation) {
                return false;
            }
        });
    }

    function editarCredenciales(){
        (jQuery)('#MXPS_USER').val('');
        (jQuery)('#MXPS_PASSWD').val('');                                
        (jQuery)('#stepUser1').removeClass('d-none');
        (jQuery)('#stepUser2').removeClass('d-none');
        (jQuery)('#stepUser21').addClass('d-none');
        (jQuery)('#stepUser3').addClass('d-none');
        (jQuery)('#guardarCredenciales').removeClass('d-none');
        (jQuery)('#editarCredenciales').addClass('d-none');
    }

    (jQuery)('#panel_usuario').click(function() {
        validarCredencialesYBultosVacio();
    });
    (jQuery)('#panel_usuario input').blur(function() {
        validarCredencialesYBultosVacio();
    });
    (jQuery)('#guardarCredenciales').mouseover(function(event) {
        validarCredencialesYBultosVacio();
    });
    (jQuery)('#guardarDatosCliente').mouseover(function(event) {
        validarCredencialesYBultosVacio();
    });

    function validarCredencialesYBultosVacio(){
        var errores = 0
        var comprobar = ['MXPS_USER','MXPS_PASSWD'];

        for (var i = 0; i < comprobar.length; i++) {
            if((jQuery)('#'+comprobar[i]).val() == ""){
                (jQuery)('#'+comprobar[i]).parent().addClass('has-error');
                errores++;
            }else{
                (jQuery)('#'+comprobar[i]).parent().removeClass('has-error');
            }
        }
        errores == 0 ? (jQuery)('#guardarCredenciales').prop("disabled", false) : (jQuery)('#guardarCredenciales').prop("disabled", true);

        if((jQuery)('#MXPS_DEFAULTBUL').val() == ""){
            (jQuery)('#MXPS_DEFAULTBUL').parent().addClass('has-error');
            (jQuery)('#guardarDatosCliente').prop("disabled", true);
            errores++;
        }else{
            (jQuery)('#MXPS_DEFAULTBUL').parent().removeClass('has-error');
            (jQuery)('#guardarDatosCliente').prop("disabled", false);
        }
    }

   function guardarCredenciales(){
        (jQuery)('#CEX-loading').removeClass('d-none');
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_validar_credenciales',
                'user' : (jQuery)('#MXPS_USER').val(),
                'pass' : (jQuery)('#MXPS_PASSWD').val(),
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) { 
                mensaje=JSON.parse(msg);     
                    
                if (mensaje.validacion){
                    pintarNotificacion(mensaje.mensaje);
                    (jQuery).ajax({
                        type: "POST",
                        url: 'admin-ajax.php',
                        data: {
                            'action': 'cex_guardar_credenciales',
                            'MXPS_USER' : (jQuery)('#MXPS_USER').val(),
                            'MXPS_PASSWD' : (jQuery)('#MXPS_PASSWD').val(),
                            'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                        },
                        success: function(msg) {
                            inicializarDatosUsuario();
                            mensaje=JSON.parse(msg); 
                            pintarRespuestaAjax(mensaje);
                            (jQuery)('#CEX-loading').addClass('d-none');
                        },
                        error: function(msg) {
                            mensaje=JSON.parse(msg); 
                            pintarRespuestaAjax(mensaje);
                            (jQuery)('#CEX-loading').addClass('d-none');
                        }
                    });
                }else{                                
                    pintarNotificacion(mensaje.mensaje);
                }
                (jQuery)('#CEX-loading').addClass('d-none');  
            },
            error: function(validation) {
                (jQuery)('#CEX-loading').addClass('d-none');  
                return false;
            }
        });                      
    }

    function cambiar_estado_enableOfficeDelivery() {
        (jQuery)('#activar_entrega_oficina_group').toggle();
        if ((jQuery)('#MXPS_ENABLEOFFICEDELIVERY').prop('checked') == false) {
        }
    }

    function pintarEstadosFormularioUser(msg) {
        (jQuery)('#CEX-loading').removeClass('d-none');
        retorno = JSON.parse(msg);        
        // INPUTS 
        //(jQuery)('#MXPS_USER').val('');
        //(jQuery)('#MXPS_PASSWD').val('');        
        if(retorno.MXPS_USER !='' && retorno.MXPS_USER !='*****' && retorno.MXPS_PASSWD!=''){  
            (jQuery)('#cex_account_title').html('<i id="cex_account_connect" class="fas fa-check-circle mr-2"></i> Cuenta Conectada');         
            (jQuery)('#cex_username').html('<strong>Usuario:</strong> '+retorno.MXPS_USER);
            (jQuery)('#cex_passw').html('<strong>Password:</strong> '+retorno.MXPS_PASSWD);
            (jQuery)('#MXPS_USER').val('');
            (jQuery)('#MXPS_PASSWD').val('');                                
            (jQuery)('#stepUser1').addClass('d-none');
            (jQuery)('#stepUser2').addClass('d-none');
            (jQuery)('#stepUser21').removeClass('d-none');
            (jQuery)('#stepUser3').removeClass('d-none');
            (jQuery)('#guardarCredenciales').addClass('d-none');
            (jQuery)('#editarCredenciales').removeClass('d-none');
        }    
        //select webservices
        (jQuery)('#MXPS_DEFAULTWS').val(retorno.MXPS_DEFAULTWS);   
        mostrarUrlWebService();    
        (jQuery)('#MXPS_WSURL').val(retorno.MXPS_WSURL);    
        (jQuery)('#MXPS_WSURL').val(retorno.MXPS_WSURL);
        (jQuery)('#MXPS_WSURLREC').val(retorno.MXPS_WSURLREC);
        (jQuery)('#MXPS_WSURLSEG').val(retorno.MXPS_WSURLSEG); 
        (jQuery)('#MXPS_WSURL_REST').val(retorno.MXPS_WSURL_REST);        
        (jQuery)('#MXPS_WSURLREC_REST').val(retorno.MXPS_WSURLREC_REST);
        (jQuery)('#MXPS_WSURLSEG_REST').val(retorno.MXPS_WSURLSEG_REST);
        (jQuery)('#MXPS_WSURL').val(retorno.MXPS_WSURL);        
        (jQuery)('#MXPS_WSURLREC').val(retorno.MXPS_WSURLREC);
        (jQuery)('#MXPS_WSURLSEG').val(retorno.MXPS_WSURLSEG);              
        (jQuery)('#MXPS_DEFAULTKG').val(retorno.MXPS_DEFAULTKG);
        (jQuery)('#MXPS_DEFAULTBUL').val(retorno.MXPS_DEFAULTBUL);
        (jQuery)('#MXPS_LABELSENDER_TEXT').val(retorno.MXPS_LABELSENDER_TEXT);
        mostrarRemitenteAlternativo();
        (jQuery)('#MXPS_RECORDSTATUS').val(retorno.MXPS_RECORDSTATUS);
        (jQuery)('#MXPS_SENDINGSTATUS').val(retorno.MXPS_SENDINGSTATUS);
        (jQuery)('#MXPS_DELIVEREDSTATUS').val(retorno.MXPS_DELIVEREDSTATUS);
        (jQuery)('#MXPS_CANCELEDSTATUS').val(retorno.MXPS_CANCELEDSTATUS);
        (jQuery)('#MXPS_RETURNEDSTATUS').val(retorno.MXPS_RETURNEDSTATUS);

        //Range de retorno del intervalo del cron
        (jQuery)('#MXPS_CRONINTERVAL').val(retorno.MXPS_CRONINTERVAL);       
        (jQuery)('#MXPS_CRONINTERVAL_TEXT').val(retorno.MXPS_CRONINTERVAL_TEXT);
        //CHECKBOXES        
        if (retorno.MXPS_ENABLEWEIGHT == "true") {
            (jQuery)("#MXPS_ENABLEWEIGHT").attr("checked", "checked");
            mostrarPesoDefecto();
        } else {
            (jQuery)("#MXPS_ENABLEWEIGHT").attr("checked", null);
        }
        if (retorno.MXPS_CHECK_LOG == "true") {
            (jQuery)("#MXPS_CHECK_LOG").attr("checked", "checked");
            mostrarOcultarLog(true);
        } else {
            (jQuery)("#MXPS_CHECK_LOG").attr("checked", null);
            mostrarOcultarLog(false);
        }
        if (retorno.MXPS_ENABLESHIPPINGTRACK == "true") {
            (jQuery)("#MXPS_ENABLESHIPPINGTRACK").attr("checked", "checked");
        } else {
            (jQuery)("#MXPS_ENABLESHIPPINGTRACK").attr("checked", null);
        }
        if (retorno.MXPS_LABELSENDER == "true") {
            (jQuery)("#MXPS_LABELSENDER").attr("checked", "checked");
        } else {
            (jQuery)("#MXPS_LABELSENDER").attr("checked", null);
        }
        if (retorno.MXPS_TRACKINGCEX == "true") {
            (jQuery)("#MXPS_TRACKINGCEX").attr("checked", "checked");
            activarCambioEstado();
        } else {
            (jQuery)("#MXPS_TRACKINGCEX").attr("checked", null);
            activarCambioEstado();
        }
        if(retorno.MXPS_NODATAPROTECTION == "true"){
            (jQuery)("#MXPS_NODATAPROTECTION").attr("checked","checked");
            (jQuery)("#proteccionDatos").removeClass('d-none');
        }else{
            (jQuery)("#MXPS_NODATAPROTECTION").attr("checked", null);
        }
        (jQuery)('#MXPS_DATAPROTECTIONVALUE option[value='+retorno.MXPS_DATAPROTECTIONVALUE+']').attr('selected','selected');
        if (retorno.MXPS_SAVEDSTATUS == "true") {
            (jQuery)("#MXPS_SAVEDSTATUS").attr("checked", "checked");
            (jQuery)("#estado_grabacion").removeClass('d-none');
        } else {
            (jQuery)("#MXPS_SAVEDSTATUS").attr("checked", null);
            (jQuery)("#estado_grabacion").addClass('d-none');
        }
        if (retorno.MXPS_CHANGESTATUS == "true") {
            (jQuery)("#MXPS_CHANGESTATUS").attr("checked", "checked");
            (jQuery)("#estados_cron").removeClass('d-none');
        } else {
            (jQuery)("#MXPS_CHANGESTATUS").attr("checked", null);
            (jQuery)("#estados_cron").addClass('d-none');
        }
        
        //(jQuery)("#MXPS_UPLOADFILE").val(retorno.MXPS_UPLOADFILE); 
        if (retorno.MXPS_CHECKUPLOADFILE == "true") {
            (jQuery)("#MXPS_CHECKUPLOADFILE").prop("checked", true);
            (jQuery)('#mostrarLogo').removeClass('d-none');
            (jQuery)('#mostrarImagenLogo').removeClass('d-none');
            (jQuery)('#mostrarLogo').addClass('d-flex');
            (jQuery)("#imagenLogoEtiqueta").attr("src", retorno.MXPS_UPLOADFILE);  
        } 

        //selects inferiores
        (jQuery)('#MXPS_DEFAULTPDF').val(retorno.MXPS_DEFAULTPDF);
        //(jQuery)('#MXPS_DEFAULTSEND').val(retorno.MXPS_DEFAULTSEND);
        (jQuery)('#MXPS_DEFAULTPAYBACK').val(retorno.MXPS_DEFAULTPAYBACK);

        

    }

    function mostrarUrlWebService(){
        if ((jQuery)('#MXPS_DEFAULTWS').val()==="SOAP"){
            (jQuery)('#mostrarSoap').removeClass('d-none');            
            (jQuery)('#mostrarRest').addClass('d-none');
            
        }
        else{
            (jQuery)('#mostrarRest').removeClass('d-none');            
            (jQuery)('#mostrarSoap').addClass('d-none');

        }

    }

    function mostrarPesoDefecto() {
        if ((jQuery)('#MXPS_ENABLEWEIGHT').prop('checked') == true) {
            (jQuery)('#pesodefecto').removeClass('d-none');
        } else {
            (jQuery)('#pesodefecto').addClass('d-none');
        }
    }

    function mostrarRemitenteAlternativo() {
        if ((jQuery)('#MXPS_LABELSENDER').prop('checked') == true) {
            (jQuery)('#remitenteAlt').removeClass('d-none');
        } else {
            (jQuery)('#remitenteAlt').addClass('d-none');
        }
    }

    function mostrarProteccionDatos() {
        if ((jQuery)('#MXPS_NODATAPROTECTION').prop('checked') === true) {
            (jQuery)('#proteccionDatos').removeClass('d-none');
        } else {
            (jQuery)('#proteccionDatos').addClass('d-none');
        }
    }

    function activarCambioEstado() {
        if ((jQuery)('#MXPS_TRACKINGCEX').prop('checked') == true) {
            (jQuery)("#MXPS_CHANGESTATUS").prop("disabled", false);
        } else {
            (jQuery)("#MXPS_CHANGESTATUS").prop('checked', false);
            (jQuery)("#MXPS_CHANGESTATUS").prop('disabled', true);            
            mostrarEstadosCron();
        }
    }

    function mostrarEstadosCron() {
        if ((jQuery)('#MXPS_CHANGESTATUS').prop('checked') == true) {
            (jQuery)('#estados_cron').removeClass('d-none');
        } else {
            (jQuery)('#estados_cron').addClass('d-none');
            (jQuery)('#MXPS_SENDINGSTATUS').prop('selectedIndex',0);
            (jQuery)('#MXPS_DELIVEREDSTATUS').prop('selectedIndex',0);
            (jQuery)('#MXPS_CANCELEDSTATUS').prop('selectedIndex',0);
            (jQuery)('#MXPS_RETURNEDSTATUS').prop('selectedIndex',0);
        }
    }

    function mostrarEstadoGrabacion() {
        if ((jQuery)('#MXPS_SAVEDSTATUS').prop('checked') == true) {
            (jQuery)('#estado_grabacion').removeClass('d-none');
        } else {
            (jQuery)('#estado_grabacion').addClass('d-none');            
            (jQuery)('#MXPS_RECORDSTATUS').prop('selectedIndex',0);            
        }
    }

    function datosCronErrores(contenedor){
        var validate = {};        
        contenedor.forEach(function(value){            
            if(!(jQuery)('#'+value).hasClass('d-none')){
                (jQuery)('#'+value+' select').each(function() {            
                    var select = (jQuery)(this);            
                    if (select.val() != '' && select.val() != 'undefined' && select.val()!=null) {
                        validate[this.name] = true;
                        (jQuery)(this).parent().removeClass('has-error');                
                    } else {
                        validate[this.name] = false;
                        (jQuery)(this).parent().addClass('has-error');                
                    }                
                });              
            }else{
                return false;
            }
        });
        return validate;
    }

    function generarArchivoCron(event, file) { 
        event.preventDefault();
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'nombre'    : file,
                'action'    : 'cex_generar_informe_cron',                
                'nonce'     : '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {                
                (jQuery)("#log_cron").attr("download", file+".txt");
                (jQuery)("#log_cron").attr("href", 'data:text/plain;charset=utf-8,' + encodeURIComponent(msg));
                (jQuery)("#log_cron")[0].click(); 
            },
            error: function() {
            }
        });
    }

    //DATOS DE CRON
    function guardarValidarDatosCron(event) {           
        event.preventDefault();
        // Si el valor de codigo_cliente == ' ' no enviar, decir que tiene que dar de alta cod_clien
        var validate= datosCronErrores(['estado_grabacion','estados_cron']);      
        var save = true;
        if (validate!=false){
            (jQuery).each(validate, function(item, value) {
                if (value == false) {
                    save = false;
                    return save;
                } else {
                    save = true;
                }
            });   
        }
        if(save==true){
            (jQuery).ajax({
                type: "POST",
                url: 'admin-ajax.php',
                data: {
                    'action': 'cex_guardar_options_cron',
                    'MXPS_SAVEDSTATUS': (jQuery)('#MXPS_SAVEDSTATUS').prop('checked'),
                    'MXPS_RECORDSTATUS': (jQuery)('#MXPS_RECORDSTATUS').val(),
                    'MXPS_TRACKINGCEX': (jQuery)('#MXPS_TRACKINGCEX').prop('checked'),
                    'MXPS_CHANGESTATUS': (jQuery)('#MXPS_CHANGESTATUS').prop('checked'),
                    'MXPS_SENDINGSTATUS': (jQuery)('#MXPS_SENDINGSTATUS').val(),
                    'MXPS_DELIVEREDSTATUS': (jQuery)('#MXPS_DELIVEREDSTATUS').val(),
                    'MXPS_CANCELEDSTATUS': (jQuery)('#MXPS_CANCELEDSTATUS').val(),
                    'MXPS_RETURNEDSTATUS': (jQuery)('#MXPS_RETURNEDSTATUS').val(), 
                    'MXPS_CRONINTERVAL': (jQuery)('#MXPS_CRONINTERVAL').val(),                
                    'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
                },
                success: function(msg) {                    
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');

                },
                error: function(msg) {
                    pintarRespuestaAjax(msg);
                    (jQuery)('#CEX-loading').addClass('d-none');
                }
            });
        } 
    }

    //PRODUCTOS CEX
    function guardarProductosCex() {
        nodosActivos = '';
        var checkeds = (jQuery)('.check_productos');
        for (i = 0; i < checkeds.length; i++) {
            if (checkeds[i].checked) {
                nodosActivos += checkeds[i].value + ';';
            }
        }

        nodosActivos = nodosActivos.substr(0, nodosActivos.length - 1);

        if(nodosActivos == ""){
            var msg = {
                title  : guardarProd,
                mensaje: unProducto,
                type   : 'error'
            };
            pintarNotificacion(msg);
            return false;
        }
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_guardar_productos',
                'productos': nodosActivos,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
                (jQuery)('#CEX-loading').addClass('d-none');
            }
        });
    }

    // ZONAS DE ENV&Iacute;O
    function mapear_transportistas() {
        var transportistas = (jQuery)('#transportistas').serializeArray();
        if(transportistas == ""){
            var msg = {
                title  : guardarTransport,
                mensaje: unTransport,
                type   : 'error'
            };
            pintarNotificacion(msg);
            return false;
        }
        //funcion AJAX que lo envie al controlador y que guarde las que tiene que guardar
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_guardar_mapeo_transportistas',
                'formulario': transportistas,
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            success: function(msg) {
                pintarRespuestaAjax(msg);
                (jQuery)('#CEX-loading').addClass('d-none');
            },
            error: function(msg) {
            }
        });
    }

    function generarLogBBDD(nombre_archivo) {
        var valueSelect = (jQuery)("#datos_tablas").val();
        var nombreArchivo = valueSelect;        

        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
              'action':'generarLogSoporte',
              'nombre': nombreArchivo,              
              'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            showLoader: true,
            success: function (msg) {
                (jQuery)("#descargaBBDD").attr("download", nombreArchivo);
                (jQuery)("#descargaBBDD").attr("href", "data:text/plain;msg.responseText," + msg);
                (jQuery)("#descargaBBDD")[0].click();
            },
            error: function(msg) {
            }
          }
        );
    }

       // EVENTOS ANIMACIONES MENSAJES DE ERROR Y COSAS VARIAS
    function animacionBoton(panel) {
        var icono = (jQuery)(panel).parent().find('span#Cex-arrow i.fas');
        (jQuery)(icono).toggleClass('fas fa-chevron-up fas fa-chevron-down');
        inicializarDatosUsuario();
        /*var iconoInfo = (jQuery)(panel).parent().find('span i.fa-info-circle');*/
        /* iconoInfo.click(function() {
             (jQuery)(this).data('clicked', true);
         });*/

        /*
        if (!iconoInfo.data('clicked')) {
            var icono = (jQuery)(panel).parent().find('span#Cex-arrow i.fas');
            if ((jQuery)(panel).hasClass('show')) {
                //(jQuery)(panel).toggle('slow');
                (jQuery)(icono).toggleClass('fas fa-chevron-up fas fa-chevron-down');
            } else {
                (jQuery)(panel).toggle('slow');
                //(jQuery)(panel).toggle('slow');
                icono.toggleClass('fas fa-chevron-down fas fa-chevron-up');
            }
        }*/
    }

    function pintarRespuestaAjax(msg) {        
        (jQuery)('#CEX-loading').removeClass('d-none');
        var retorno = JSON.parse(msg);        
        (jQuery)('#resultados').css('display', 'block');

        if (retorno.remitentes != 'undefined' && retorno.remitentes != null)
            (jQuery)('#savedsenders').html(retorno.remitentes);

        /*(jQuery)('#saved_codes').html(retorno.codigos);
        if ((jQuery)('#saved_codes').css('display') != 'block') {
            (jQuery)('#saved_codes').toggle('slow');
        }*/

        if (retorno.codigos != 'undefined' && retorno.codigos != null){                                    
            (jQuery)('#saved_codes').html(retorno.codigos);
            if (retorno.codigos.length > 0){
                (jQuery)('#saved_codes').removeClass('d-none');
            }else{               
                (jQuery)('#saved_codes').addClass('d-none');                
            }
        } 

       /* console.log(retorno.codigos);
        if (retorno.codigos != 'undefined' && retorno.codigos != null){
            (jQuery)('#saved_codes').html(retorno.codigos);
            //(jQuery)('#saved_codes').toggle('slow');
            (jQuery)('#saved_codes').removeClass('d-none');            
        }else{
            (jQuery)('#saved_codes').html(retorno.codigos);
            if ((jQuery)('.cex_borrar_codigo_cliente').length == 0) {
                //(jQuery)('#saved_codes').toggle('slow');                
                (jQuery)('#saved_codes').addClass('d-none');
            }
        } */

        if (retorno.selectCodCliente != 'undefined' && retorno.selectCodCliente != null)
            (jQuery)('#codigo_cliente').html(retorno.selectCodCliente);

        if (retorno.selectRemitentes != 'undefined' && retorno.selectRemitentes != null)
            (jQuery)('#MXPS_DEFAULTSEND').html(retorno.selectRemitentes);

        if (retorno.productos != 'undefined' && retorno.productos != null)
            (jQuery)('#productos_cex').html(retorno.productos);

        if (retorno.selectEstados != 'undefined' && retorno.selectEstados != null) {
            (jQuery)('#MXPS_RECORDSTATUS').html(retorno.selectEstados);
            (jQuery)('#MXPS_SENDINGSTATUS').html(retorno.selectEstados);
            (jQuery)('#MXPS_DELIVEREDSTATUS').html(retorno.selectEstados);
            (jQuery)('#MXPS_CANCELEDSTATUS').html(retorno.selectEstados);
            (jQuery)('#MXPS_RETURNEDSTATUS').html(retorno.selectEstados);

        }

        if(retorno.selectReferencias != 'undefined' && retorno.selectReferencias!=null){
            (jQuery)('#MXPS_REFETIQUETAS').html(retorno.selectReferencias);
        }

        if(retorno.selectDestinatarios != 'undefined' && retorno.selectDestinatarios!=null){
            (jQuery)('#MXPS_DEFAULTDELIVER').html(retorno.selectDestinatarios);
        }        

        if (retorno.active_gateways != 'undefined' && retorno.active_gateways != null) {
            (jQuery)('#MXPS_DEFAULTPAYBACK').html(retorno.active_gateways);
        }

        if (retorno.selectTransportistas != 'undefined' && retorno.selectTransportistas != null) {
            (jQuery)('#transportistas').html(retorno.selectTransportistas);
        }

        if (retorno.mensaje != 'undefined' && retorno.mensaje != null)
            pintarNotificacion(retorno.mensaje);

        //comprobar si viene habilitado o no para habilitar el boton.
        if (retorno.selectCodCliente != 'undefined' && retorno.selectCodCliente != null) {
            if (retorno.selectCodCliente.indexOf("disabled") != -1) {
                (jQuery)('#guardarRemitente').prop("disabled", true);
                (jQuery)('#guardarRemitenteMsn').removeClass('d-none');
            } else {
                (jQuery)('#guardarRemitente').prop("disabled", false);
                (jQuery)('#guardarRemitenteMsn').addClass('d-none');
            }
        }

        if (retorno.unidadMedida != 'undefined' && retorno.unidadMedida != null) {
            (jQuery)('#unidadMedida span').html("  ("+retorno.unidadMedida+")");
        }

        //if (retorno.MXPS_CHECKUPLOADFILE != 'undefined' && retorno.mensaje != null)
        //    mostrarFormLogo(retorno.MXPS_UPLOADFILE);
    }

    var myStack = {
        "dir1": "down",
        "dir2": "right",
        "push": "top"
    };

    function pintarNotificacion(msg) {
        PNotify.prototype.options.styling = "bootstrap3";
        new PNotify({
            title: msg.title,
            text: msg.mensaje,
            type: msg.type,
            stack: myStack
        })
    }

    function abrirModalCodigoCliente(cliente) {
        (jQuery)('#codigo_cliente_modal').val(cliente.customer_code);
        (jQuery)('#id_cod_cliente_modal').val(cliente.id);
        (jQuery)('#ajustes').addClass('d-none');
        (jQuery)('#modal_codigo_client').removeClass('d-none');
        (jQuery)('#CEX-manualInteractivo').addClass('d-none');
        (jQuery)('#CEX-manualRemitente').addClass('d-none');
        (jQuery)('#CEX-manualCodigoCliente').removeClass('d-none');
        (jQuery)('#toggleIntroJS').prop('checked', true);
        checkIntroJS();
    }

    function borrarModalCodigoCliente() {
        (jQuery)('#ajustes').removeClass('d-none');
        (jQuery)('#modal_codigo_client').removeClass('d-none');
        (jQuery)('#modal_codigo_client').addClass('d-none');
        (jQuery)('#CEX-manualRemitente').addClass('d-none');
        (jQuery)('#CEX-manualCodigoCliente').addClass('d-none');
        (jQuery)('#CEX-manualInteractivo').removeClass('d-none');
    }

    function abrirModalRemitente(remitente) {
        (jQuery)('#id_modal').val(remitente.id_sender);
        (jQuery)('#name_sender_modal').val(remitente.name);
        (jQuery)('#address_sender_modal').val(remitente.address);
        (jQuery)('#postcode_sender_modal').val(remitente.postcode);
        (jQuery)('#city_sender_modal').val(remitente.city);
        (jQuery)('#country_sender_modal').val(remitente.iso_code_pais);
        (jQuery)('#contact_sender_modal').val(remitente.contact);
        (jQuery)('#phone_sender_modal').val(remitente.phone);
        (jQuery)('#email_sender_modal').val(remitente.email);
        (jQuery)('#fromHH_sender_modal').val(remitente.from_hour);
        (jQuery)('#fromMM_sender_modal').val(remitente.from_minute);
        (jQuery)('#toHH_sender_modal').val(remitente.to_hour);
        (jQuery)('#toMM_sender_modal').val(remitente.to_minute);
        (jQuery)('#remitente_codigo_cliente_modal').val(remitente.customer_code);
        (jQuery)('#ajustes').addClass('d-none');
        (jQuery)('#remitente').removeClass('d-none');
        (jQuery)('#CEX-manualInteractivo').addClass('d-none');
        (jQuery)('#CEX-manualCodigoCliente').addClass('d-none');
        (jQuery)('#CEX-manualRemitente').removeClass('d-none');
        (jQuery)('#toggleIntroJS').prop('checked', true);
        checkIntroJS();
        (jQuery)("#formUpdateRemt input").blur(function(event) {
            validarEditarRemitente();
        });
        (jQuery)("#formUpdateRemt").click(function(event) {
            validarEditarRemitente();
        });
        (jQuery)('#guardar_modal_remitente').mouseover(function(event) {
            validarEditarRemitente();
        });
    }

    function borrarModalRemitente() {
        //NotifyRemitente.remove();
        (jQuery)('#toggleEdicionRemitenteJS').prop('checked', true);
        checkEdicionRemitenteJS();
        (jQuery)('#ajustes').removeClass('d-none');
        (jQuery)('#remitente').addClass('d-none');
        (jQuery)('#CEX-manualRemitente').addClass('d-none');
        (jQuery)('#CEX-manualCodigoCliente').addClass('d-none');
        (jQuery)('#CEX-manualInteractivo').removeClass('d-none');
    }

    function show(){
        (jQuery)("#shb_cex").toggleClass('d-none');
    }

    function ejecutarCron(){
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
              'action':'cex_cron_function',
              'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            showLoader: true,
            success: function (msg) {               
                console.log(msg);
            },
            error: function(msg) {
            }
          }
        );
    }

    function BorrarCarpetaLog() {
        (jQuery).ajax({
            type: "POST",
            url: 'admin-ajax.php',
            data: {
                'action': 'cex_borrar_carpeta_log',          
                'nonce': '<?php echo wp_create_nonce('cex-nonce'); ?>'
            },
            showLoader: true,
                success: function (msg) {               
                    mensaje = JSON.parse(msg);   
                    pintarNotificacion(mensaje.mensaje);      
                },
                error: function(msg) {
                    console.log(msg);
                }
        });
    }

    </script>
    <!--VENTANAS MODALES -->
    <!-- Trigger the modal with a button -->
    <button type="button" data-toggle="modal" data-target="#modal_codigo_cliente" id="modal2" class="d-none"></button>
    <!-- Modal -->
    <div id="modal_codigo_client" class="CEX-container container  mt-1 mb-3 pr-5 d-none" role="dialog">
        <div class="row">
            <div class="CEX-background-white CEX-text-blue p-3 rounded col-12">
                <div class="modal-header border-0">
                    <h4 class="modal-title text-center CEX-text-blue">
                        <?php esc_html_e("Actualizar c&oacute;digo de cliente", "cex_pluggin");?>
                    </h4>
                </div>
                <div class="modal-body pb-0">
                    <input type="hidden" name="id_cod_cliente_modal" id="id_cod_cliente_modal">
                    <input type="hidden" name="code_demand_modal" id="code_demand_modal">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded-left">
                                <?php esc_html_e("C&oacute;digo Cliente", "cex_pluggin");?></span>
                        </div>
                        <input class="form-control rounded-left-0 rounded-right m-0" type="text" maxlength="9" minlength="9" 
                            id="codigo_cliente_modal" name="codigo_cliente_modal" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" id="guardar_modal_codigo" class="CEX-btn CEX-button-success"
                        onclick="actualizarCodigoCliente();">
                        <?php esc_html_e("Guardar", "cex_pluggin");?></button>
                    <button type="button" id="cerrar_modal_codigo" name="cerrar_modal_codigo"
                        class="CEX-btn CEX-button-grey"
                        onclick="borrarModalCodigoCliente();"><?php esc_html_e("Cerrar", "cex_pluggin");?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Trigger the modal with a button -->
    <button type="button" data-toggle="modal" data-target="#remitente" id="modal" class="d-none"></button>
    <!-- Modal -->

    <div id="remitente" class="CEX-container CEX-paneles container  mt-1 mb-3 pr-5 d-none" role="dialog">
        <div class="row">
            <div class="CEX-background-white CEX-text-blue p-3 rounded col-12">
                <!-- Modal content-->
                <div class="modal-header border-0">
                    <h4 class="modal-title text-center CEX-text-blue">
                        <?php esc_html_e("Actualizar remitente", "cex_pluggin");?>
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="formUpdateRemt" onsubmit="return false">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="input-group my-3">
                                        <input type="hidden" name="id_modal" id="id_modal">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('C&oacute;digo cliente', 'cex_pluggin');?></span>
                                        </div>
                                        <input disabled type="text"
                                            class="form-control rounded-left-0 rounded-right m-0"
                                            id="remitente_codigo_cliente_modal" name="remitente_codigo_cliente_modal"
                                            autocomplete="off" required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Nombre remitente', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="text" class="form-control rounded-left-0 rounded-right m-0"
                                            id="name_sender_modal" name="name_sender_modal"
                                            placeholder="<?php esc_html_e('Nombre remitente', 'cex_pluggin');?>"
                                            required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Persona contacto', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="text" class="form-control rounded-left-0 rounded-right m-0"
                                            id="contact_sender_modal" name="contact_sender_modal"
                                            placeholder="<?php esc_html_e('Persona contacto', 'cex_pluggin');?>"
                                            required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Direcci&oacute;n recogida', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="text" class="form-control rounded-left-0 rounded-right m-0"
                                            id="address_sender_modal" name="address_sender_modal"
                                            placeholder="<?php esc_html_e('Direcci&oacute;n recogida', 'cex_pluggin');?>"
                                            required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Poblaci&oacute;n', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="text" class="form-control rounded-left-0 rounded-right m-0"
                                            id="city_sender_modal" name="city_sender_modal"
                                            placeholder="<?php esc_html_e('Poblaci&oacute;n', 'cex_pluggin');?>"
                                            required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('C&oacute;digo Postal', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="text" class="form-control rounded-left-0 rounded-right m-0"
                                            id="postcode_sender_modal" name="postcode_sender_modal" pattern="\d*"
                                            placeholder="<?php esc_html_e('C&oacute;digo Postal', 'cex_pluggin');?>"
                                            maxlength="8" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Pa&iacute;s', 'cex_pluggin') ;?></span>
                                        </div>
                                        <select class="form-control rounded-left-0 rounded-right m-0"
                                            id="country_sender_modal" name="country_sender_modal" required>
                                            <option value="ES"><?php esc_html_e("España", "cex_pluggin");?></option>
                                            <option value="PT"><?php esc_html_e("Portugal", "cex_pluggin");?></option>
                                        </select>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Tel&eacute;fono', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="tel" class="form-control rounded-left-0 rounded-right m-0"
                                            id="phone_sender_modal" name="phone_sender_modal" pattern="\d*"
                                            minlength="9" maxlength="9"
                                            placeholder="<?php esc_html_e("Tel&eacute;fono", "cex_pluggin");?>"
                                            required>
                                    </div>
                                    <div class="input-group my-3">
                                        <div class="input-group-prepend">
                                            <span
                                                class="input-group-text rounded-left"><?php esc_html_e('Correo electr&oacute;nico', 'cex_pluggin');?></span>
                                        </div>
                                        <input type="email" class="form-control rounded-left-0 rounded-right m-0"
                                            id="email_sender_modal" name="email_sender_modal"
                                            placeholder="<?php esc_html_e("Correo electr&oacute;nico", "cex_pluggin");?>"
                                            required>
                                    </div>
                                    <div id="EdicionHoras" class="row">
                                        <div id="EdicionHoraDesde" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="CEX-panel CEX-panel-primary">
                                                <div class="CEX-panel-heading">
                                                    <?php esc_html_e('Desde', 'cex_pluggin') ;?></div>
                                                <div class="panel-body py-3">
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="fromHH_sender"
                                                                    class="control-label d-block"><?php esc_html_e('Hora', 'cex_pluggin');?></label>
                                                                <input type="number" class="form-control"
                                                                    id="fromHH_sender_modal" placeholder="0" value=""
                                                                    size="2" name="fromHH_sender_modal" min="0" max="24"
                                                                    required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="fromMM_sender"
                                                                    class="control-label d-block"><?php esc_html_e('Minutos', 'cex_pluggin') ;?></label>
                                                                <input type="number" class="form-control"
                                                                    id="fromMM_sender_modal" placeholder="0" value="0"
                                                                    size="2" name="fromMM_sender_modal" min="0"
                                                                    max="60">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="EdicionHoraHasta" class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="CEX-panel CEX-panel-primary">
                                                <div class="CEX-panel-heading">
                                                    <?php esc_html_e('Hasta', 'cex_pluggin');  ?></div>
                                                <div class="panel-body py-3">
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="toHH_sender"
                                                                    class="control-label d-block"><?php esc_html_e('Hora', 'cex_pluggin') ;?></label>
                                                                <input type="number" class="form-control"
                                                                    id="toHH_sender_modal" placeholder="0" value=""
                                                                    size="2" name="toHH_sender_modal" min="0" max="24"
                                                                    required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="toMM_sender"
                                                                    class="control-label d-block"><?php esc_html_e('Minutos', 'cex_pluggin');?></label>
                                                                <input type="number" class="form-control"
                                                                    id="toMM_sender_modal" placeholder="0" value="0"
                                                                    size="2" name="toMM_sender_modal" min="0" max="60">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 text-center">
                    <button type="button" id="guardar_modal_remitente" class="CEX-btn CEX-button-success"
                        onclick='actualizarRemitente();'><?php esc_html_e("Guardar", "cex_pluggin");?> </button>
                    <button type="button" id="cerrar_modal_remitente" class="CEX-btn CEX-button-grey"
                        onclick="borrarModalRemitente();"
                        data-dismiss="modal"><?php esc_html_e("Cerrar", "cex_pluggin");?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->CEX_scripts();     
    $this->CEX_introJS('configure');
?>
<?php else : ?>
<p><?php esc_html_e("NO TIENES ACCESO A ESTA SECCI&Oacute;N", "cex_pluggin");?></p>
<?php endif ; ?>
</div>
