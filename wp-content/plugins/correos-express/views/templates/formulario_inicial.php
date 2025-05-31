<?php if(current_user_can('manage_options')): ?>
<?php wp_enqueue_script("jquery");?>
<?php $this->CEX_styles(); ?>


<div id="CEX">
    <div class="CEX-container container CEX-panel info_content mb-0 shadow-none py-5 px-3" id="fieldset_0">
        <div id="content_entrada">
            <div class="container-fluid">
                <div class="row">
                    <div id="form_salesforce_resp"></div>
                    <div class="col-12 col-xs-12 col-md-12 col-lg-12 text-center">
                        <img src="<?php echo esc_url(plugins_url('/../img/logo-correosexpress-nuevo.png',__FILE__));?>"
                            class="img-fluid mx-auto d-block" width=40% id="img-correos-express">
                    </div>
                    <div class="col-12 col-xs-12 col-md-12 col-lg-6 mt-5">
                        <h3 class="mt-3 mb-4 CEX-text-blue">
                            <?php esc_html_e("Las mejores soluciones de transporte urgente para tus clientes online","cex_pluggin");?>
                        </h3>
                        <div id="form_salesforce_resp"></div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/paq24.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0">
                                    <?php esc_html_e("ENTREGA EN OFICINA DE CORREOS","cex_pluggin");?></h4>
                                <p><?php esc_html_e("Correos Express ofrece, en exclusiva y ","cex_pluggin");?><strong
                                        class="CEX-text-blue"><?php esc_html_e("sin coste adicional","cex_pluggin");?></strong><?php esc_html_e(",el servicio de entrega en oficina de Correos.","cex_pluggin");?>
                                </p>
                                <p><small>(<?php esc_html_e("Servicio gratuito incluido en tu env&iacute;o PAQ24H","cex_pluggin");?>)</small>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/Entrega_Flexible.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0">
                                    <?php esc_html_e("AH&Oacute;RRALES ESPERAS CON LA ENTREGA FLEXIBLE","cex_pluggin");?>
                                </h4>
                                <p><?php esc_html_e("Ahora tu cliente destinatario ya puede elegir cuando recibir sus compras si la franja horaria propuesta no le conviene","cex_pluggin");?>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/localizacion.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0">
                                    <?php esc_html_e("MINIMIZA LAS INCIDENCIAS.","cex_pluggin");?></h4>
                                <p><?php esc_html_e("Localizaci&oacute;n inmediata del destinatario, en caso de ausencia","cex_pluggin");?>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/check.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0">
                                    <?php esc_html_e("ELIMINA INCERTIDUMBRE.","cex_pluggin");?></h4>
                                <p><?php esc_html_e("Si no est&aacute;n en casa, contactamos con ellos para una segunda entrega. Gesti&oacute;n proactiva de incidencias.","cex_pluggin");?>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/sms.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0 ">
                                    <?php esc_html_e("REG&Aacute;LALES TRANQUILIDAD.","cex_pluggin");?></h4>
                                <p><?php esc_html_e("Avisamos a tus clientes del estado de su env&iacute;o, v&iacute;a SMS.","cex_pluggin");?>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex aling-items-center">
                            <div class="col-2 col-xs-2 text-center my-auto">
                                <img class="CEX-icon"
                                    src="<?php echo esc_url(plugins_url('/../img/calendario.png',__FILE__));?>">
                            </div>
                            <div class="col-10 col-xs-10">
                                <h4 class="CEX-text-blue mt-0"><?php esc_html_e("ENTREGA CUANTO ANTES.","cex_pluggin");?>
                                </h4>
                                <p><?php esc_html_e("Porque hay cosas que no pueden esperar, entregamos tu pedido en s&aacute;bado.","cex_pluggin");?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xs-12 col-md-12 col-lg-6 mt-5 mb-0 pt-3">
                        <div id="CEX-esCliente" class="jumbotron p-4 rounded-0 d-block d-sm-flex mb-0">
                            <h3 class="d-block d-sm-inline-block my-auto CEX-font-size-1-5">
                                <strong><?php _e("Hola,<br/>¿Eres cliente<br/> de <span class='CEX-text-blue'>Correos</span> <span class='CEX-text-blue'>Express</span>?","cex_pluggin");?></strong>
                            </h3>
                            <button
                                class="d-block d-sm-inline-block CEX-btn btn-large CEX-button-yellow m-0 text-uppercase float-sm-right mt-5 ml-3 px-2 py-3"
                                onclick="window.location ='../wp-admin/admin.php?page=correosexpress-ajustes'">
                                <?php esc_html_e("S&iacute;, configurar mi m&oacute;dulo","cex_pluggin");?>
                            </button>
                        </div>
                        <div class="CEX-card mt-0 rounded-0 shadow-none">
                            <h3 class="text-center CEX-text-yellow text-uppercase">
                                <?php esc_html_e("Soy Nuevo, ¡Llamadme!","cex_pluggin");?></h3>
                            <div id="informacion_comercial" class="form-group">
                                <h4 class="CEX-text-yellow text-center">
                                <?php esc_html_e("D&eacute;janos tus datos y te llamamos con una oferta de servicios espec&iacute;fica para ti.","cex_pluggin");  ?>
                                    <br/>
                                    <?php esc_html_e("¡Sin compromiso!","cex_pluggin");?>
                                </h4>
                            </div>
                            <form id="CEX-form_info">
                                <div class="form-group w-75 mx-auto">
                                    <input type="text" class="form-control" name="nombre" id="nombre"
                                        placeholder="<?php esc_html_e("Nombre:","cex_pluggin");?> *" required>
                                </div>
                                <div class="form-group w-75 mx-auto">
                                    <input type="tel" class="form-control" name="telefono" id="telefono" maxlength="9"
                                        placeholder="<?php esc_html_e("Tel&eacute;fono de contacto *","cex_pluggin");?>"
                                        required>
                                </div>
                                <div class="form-group w-75 mx-auto">
                                    <input type="email" class="form-control" name="correoelectronico"
                                        id="correoelectronico"
                                        placeholder="<?php esc_html_e("Correo electr&oacute;nico *","cex_pluggin");?>"
                                        required>
                                    <input type="hidden" id="lead_source" name="lead_source" value="Wordpress" />
                                    <input type="hidden" id="00Nb00000040KMY" name="00Nb00000040KMY"
                                        value="0-NO INFORMADA" />
                                    <input type="hidden" id="URL" maxlength="80" name="URL" size="20"
                                        value='<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] ?>'>
                                    <input type=hidden id="oid" name="oid" value="00Db0000000Ykj6">
                                    <input type=hidden id="resp" name="resp"
                                        value="{$formPublic_nuevoCliente|escape:'htmlall':'UTF-8'}">
                                    <input type=hidden name="retURL"
                                        value='<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] ;?>'>
                                </div>
                                <div class="form-group w-75 mx-auto CEX-text-yellow">
                                    <span
                                        class="label_required_title_field"><?php esc_html_e("Los campos con ","cex_pluggin");?></span>
                                    <span class="label_required CEX-text-yellow">*</span><span
                                        class="label_required_title_field"><?php esc_html_e(" son requeridos.","cex_pluggin");?></span>
                                </div>
                                <!--<div class="form-group d-flex">
									<input type="checkbox" class="form-control d-inline-block m-0 mr-2" name="newsletter" value="1" aria-invalid="false">
									<label for="newsletter" class="d-inline-block CEX-text-weight-normal m-0"> Suscribirme al newsletter</label>			
								</div>-->
                                <div class="form-group d-flex w-75 mx-auto CEX-text-yellow">
                                    <input type="checkbox" class="form-control d-inline-block m-0 mr-2" name="politica"
                                        id="politica" value="1" aria-invalid="false" required>
                                    <label for="politica" class="CEX-text-weight-normal d-inline-block m-0">
                                        <?php _e('He le&iacute;do y acepto la <a target="_blank" href="https://www.correosexpress.com/web/correosexpress/politica-de-proteccion-de-datos">pol&iacute;tica de privacidad</a> la <a href="https://www.correosexpress.com/web/correosexpress/politica-de-proteccion-de-datos" target="_blank">pol&iacute;tica de protecci&oacute;n de datos</a> y el <span id="data-show" class="text-uppercase" onclick="jQuery(\'#CEX-gdpr-info\').toggle();"><strong>Detalle legal del formulario</strong></span>','cex_pluggin');?></label>
                                </div>
                                <div id="CEX-gdpr" class="form-group">
                                    <div id="CEX-gdpr-info" class="CEX-text-yellow">
                                        <span class="closed" onclick="jQuery('#CEX-gdpr-info').toggle();"><span
                                                class="fas fa-times" aria-hidden="true"></span></span>
                                        <h5><?php esc_html_e("Informaci&oacute;n b&aacute;sica sobre protecci&oacute;n de datos","cex_pluggin");?>
                                        </h5>
                                        <ul>
                                            <li><strong><?php esc_html_e("Responsable:","cex_pluggin");?></strong>
                                                <?php esc_html_e("CORREOS EXPRESS S.L.","cex_pluggin");?></li>
                                            <li><strong><?php esc_html_e("Finalidad:","cex_pluggin");?></strong>
                                                <?php esc_html_e("Comercial","cex_pluggin");?></li>
                                            <li><strong><?php esc_html_e("Legitimaci&oacute;n:","cex_pluggin");?></strong>
                                                <?php esc_html_e("Consentimiento del interesado","cex_pluggin");?></li>
                                            <li><strong><?php esc_html_e("Destinatarios:","cex_pluggin");?></strong>
                                                <?php esc_html_e("Encargados del tratamiento.","cex_pluggin");?></li>
                                            <li><strong><?php esc_html_e("Derechos del Interesado:","cex_pluggin");?></strong>
                                                <?php esc_html_e("Acceso, rectificaci&oacute;n, supresi&oacute;n, cancelaci&oacute;n, limitaci&oacute;n y portabilidad de los datos.","cex_pluggin");?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="form-group w-75 mx-auto">
                                    <button id="guardar_form" class="CEX-btn CEX-button-yellow" name="Envio"
                                        value="<?php esc_html_e("ENVIAR","cex_pluggin");?>"
                                        onclick="sendForm(event);"><?php esc_html_e("ENVIAR","cex_pluggin");?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	$this->CEX_scripts(); 
?>

<script type="text/javascript">
function revisar(e) {
    var input = (jQuery)(e);
    input.parent().removeClass('has-error');
    if (!(input.attr('onkeypress'))) {
        input.removeAttr('onclick');
    } else {
        input.removeAttr('onkeypress');
    }
}

function sendForm(event) {
    event.preventDefault();
    var telefono = CEXvalidateTelefono((jQuery)('#telefono').val());
    var correo = CEXvalidateCorreo((jQuery)('#correoelectronico').val());
    var politica = CEXvalidatePolitica();
    if ((jQuery)('#nombre').val() != '' && (jQuery)('#telefono').val() != '' && (jQuery)('#correoelectronico').val() !=
        '' && (jQuery)('#politica').prop('checked')) {
        if (telefono == true && correo == true && politica == true) {
            PNotify.prototype.options.styling = "bootstrap3";
            new PNotify({
                title: '<?php esc_html_e("Confirma la operaci&oacute;n","cex_pluggin");?>',
                text: '<?php esc_html_e("¿Est&aacute;s seguro querer que le visite un comercial?","cex_pluggin");?>',
                icon: 'fas fa-question-circle',
                type: 'warning',
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
                            crossDomain: true,
                            dataType: "jsonp",
                            url: 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8',
                            data: {
                                'company': (jQuery)('#compania').val(),
                                'first_name': (jQuery)('#nombre').val(),
                                'last_name': '',
                                'phone': (jQuery)('#telefono').val(),
                                'email': (jQuery)('#correoelectronico').val(),
                                '00Nb00000040JtK': (jQuery)('#observaciones').val(),
                                'lead_source': (jQuery)('#lead_source').val(),
                                '00Nb00000040KMY': (jQuery)('#00Nb00000040KMY').val(),
                                'URL': (jQuery)('#URL').val(),
                                'oid': (jQuery)('#oid').val(),
                                'nonce': '<?php echo wp_create_nonce( 'cex-nonce' ); ?>'
                            },
                            success: function(msg) {
                                if (msg.readyState == 4 && msg.status == 200) { 
                                    (jQuery)('#CEX-form_info').prepend('<div class="alert alert-success"><?php esc_html_e("LA PETICIÓN HA SIDO CURSADA","cex_pluggin");?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');                                   
									//alert('<?php esc_html_e("LA PETICIÓN HA SIDO CURSADA","cex_pluggin");?>');

                                } else {
                                    (jQuery)('#CEX-form_info').prepend('<div class="alert alert-danger"><?php esc_html_e("EN ESTOS MOMENTOS NO SE HA PODIDO CURSAR LA PETICIÓN","cex_pluggin");?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');                                   
                                   //alert('<?php esc_html_e("EN ESTOS MOMENTOS NO SE HA PODIDO CURSAR LA PETICIÓN","cex_pluggin");?>');
                                }
                            },
                            error: function(msg) {
                                if (msg.readyState == 4 && msg.status == 200) {
                                    (jQuery)('#CEX-form_info').prepend('<div class="alert alert-success"><?php esc_html_e("LA PETICIÓN HA SIDO CURSADA","cex_pluggin");?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');                                   
                                    //alert('<?php esc_html_e("LA PETICIÓN HA SIDO CURSADA","cex_pluggin");?>');
                                } else {
                                    (jQuery)('#CEX-form_info').prepend('<div class="alert alert-danger"><?php esc_html_e("EN ESTOS MOMENTOS NO SE HA PODIDO CURSAR LA PETICIÓN","cex_pluggin");?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');                                   
                                    //alert('<?php esc_html_e("EN ESTOS MOMENTOS NO SE HA PODIDO CURSAR LA PETICIÓN","cex_pluggin");?>');
                                }
                            }
                        });
                    }).on('pnotify.cancel', function() {
                    //alert('ok. Chicken, chicken, clocloclo.');
                });
            }
            else {
                CEXmarcarErrores(telefono, correo, politica);
            }
        } else {
            CEXmarcarErrores(telefono, correo, politica);
        }
    }

    function mostrar() {
        document.getElementById('informacion_comercial').style.display = 'block';
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
        });
    }

    function CEXvalidateTelefono(telefono) {
        if (!(isNaN(telefono)) && telefono.length == 9) {
            return true;
        } else {
            return false;
        }
    }

    function CEXvalidateCorreo(correo) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(correo)) {
            return false;
        } else {
            return true;
        }
    }

    function CEXvalidatePolitica() {
        if (!(jQuery)('#politica').prop('checked')) {
            return false;
        } else {
            return true;
        }
    }

    function CEXmarcarErrores(telefono = false, correo = false, politica = false) {
        if ((jQuery)('#telefono').val() == '' || telefono == false) {
            (jQuery)('#telefono').parent().addClass('has-error');
        } else {
            (jQuery)('#telefono').parent().removeClass('has-error');
            (jQuery)('#telefono').parent().addClass('has-success');
        }
        if ((jQuery)('#correoelectronico').val() == '' || correo == false) {
            (jQuery)('#correoelectronico').parent().addClass('has-error');
        } else {
            (jQuery)('#correoelectronico').parent().removeClass('has-error');
            (jQuery)('#correoelectronico').parent().addClass('has-success');
        }
        if ((jQuery)('#nombre').val() == '') {
            (jQuery)('#nombre').parent().addClass('has-error');
        }
        if ((!(jQuery)('#politica').prop('checked') && !(jQuery)('#politica').prop('checked')) || politica == false) {
            (jQuery)('#politica').parent().addClass('has-error');
        } else {
            (jQuery)('#politica').parent().removeClass('has-error');
            (jQuery)('#politica').parent().addClass('has-success');
        }
        if ((jQuery)(".has-error").length > 0) {
            (jQuery)(".has-error").each(function() {
                var input = (jQuery)(this).find('input');
                if (input.attr('type') == 'checkbox') {
                    input.attr('onclick', 'revisar(this)');
                } else {
                    input.attr('onkeypress', 'revisar(this)');
                }
            });
        }
    }
</script>

<?php else: ?>
<p><?php esc_html_e("NO TIENES ACCESO A ESTA SECCI&Oacute;N","cex_pluggin");?></p>
<?php endif; ?>
