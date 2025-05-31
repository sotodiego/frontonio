<!DOCTYPE html>
<html class="loading" lang="es" data-textdirection="ltr">
	<head>
	    <?=$adpnsy->header($info)?>
	    <script type="text/javascript">window.unik = '<?=wp_create_nonce("user_nologer");?>';</script>
	</head>
	<body class="horizontal-layout page-header-light horizontal-menu preload-transitions 1-column login-bg blank-page blank-page" data-open="click" data-menu="horizontal-menu" data-col="1-column">
	    <div class="row">
	        <div class="col l6 s12">
	            <div class="container">
	            	<div class="login-bg-pre l0"></div>
	                <div id="login-page">
	                    <div class="login-card">
	                    	<?php if($info->lglogin_vn){echo'<img class="logo-login" src="'.$info->lglogin_vn.'" alt="'.$info->logo_texto.'">';}else{echo'<h5 class="ml-4">'.$info->logo_texto.'</h5>';} ?>
	                        <form class="login-form viwer-form" method="post" action="#" id="registro">
	                            <div class='error-login<?=$mensaje?" show":"";?>'><?=$mensaje;?></div>
	                            <?php if(!$mensaje){ ?>
	                            	<input id="user_code" type="hidden" name='user_code' value="<?=$_GET['inv']?>" >
	                            	<div class="row margin">
	                            		<p class="dnc_bienvenida">Hola nos alegra mucho que estés aquí, la familia de <?=$academia;?> te espera para que te unas a ellos y a nosotros, por favor rellena con tus datos para completar el registro, esta invitación solo es valida para: <?=$correo;?></p>
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">person_outline</i>
		                                    <input id="user_name" type="text" name='user_name' >
		                                    <label for="user_name" class="center-align active">Tu nombre</label>
		                                </div>
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">person_outline</i>
		                                    <input id="user_lastname" type="text" name='user_lastname' >
		                                    <label for="user_lastname" class="center-align active">Tus apellidos</label>
		                                </div>
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">smartphone</i>
		                                    <input id="user_telefono" type="text" name='user_telefono' >
		                                    <label for="user_telefono" class="center-align active">Tu teléfono</label>
		                                </div>
		                                <button id="sp_1" class="btn col mt-2 s12 waves-effect waves-light white-text">Siguiente</button>
		                            </div>

	                            	<div class="row margin hide">
	                            		<label class="dropify-label center display-block" for="imagen_registro">Tu foto (opcional)</label>
		                                <input type="file" id="imagen_registro" accept="image/*" />
		                                <div class="row mt-6">
			                                <div class="col s6"><button id="sp_2" class="btn col s12 waves-effect waves-light white-text">anterior</button></div>
			                                <div class="col s6"><button id="sp_3" class="btn col s12 waves-effect waves-light white-text">siguiente</button></div>
			                            </div>
		                            </div>
		                            
		                            <div class="row margin hide">
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">lock_outline</i>
		                                    <input id="user_password" type="password" name='user_password' required>
		                                    <label for="user_password">Tu contraseña</label>
		                                </div>
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">lock_outline</i>
		                                    <input id="user_password_2" type="password" name='user_password_2' required>
		                                    <label for="user_password_2">Repite tu contraseña</label>
		                                </div>
		                                <div class="row">
		                                <div class="col s12 m12 l12 ml-2 mt-1">
			                                    <p>
			                                        <label>
			                                            <input type="checkbox" name='aceptar' id="aceptar" />
			                                            <span>Acepto las Condiciones Generales y <a href="#" id="politicas" target="_blank">Políticas de privacidad</a></span>
			                                        </label>
			                                    </p>
			                                </div>
			                            </div>
		                                <div class="row">
			                                <div class="col s6"><button id="sp_4" class="btn col s12 waves-effect waves-light white-text">anterior</button></div>
			                                <div class="col s6"><button id="sp_5" class="btn col s12 waves-effect waves-light white-text">registrarme</button></div>
			                            </div>
		                            </div>
		                        <?php } ?>
	                        </form>
	                    </div>
	                </div>
	            </div>
	            <div class="content-overlay"></div>
	            <div class="adpn_modal" id="politica_modal">
				    <div class="adpn_modal_content">
				        <div class="adpn_modal_header degradado">
				            <h3>Términos y Condiciones</h3>
				            <a href="#" class="adpn_modal_close"><i class="material-icons">close</i></a>
				        </div>
				        <div class="adp_modal_body2">
				            <?php include ADPNSY_PATH . '/templates/terminos.html'; ?>
				        </div>
				        <div class="adpn_modal_botton">
				            <button class="btn adp_modal_cancel">Cancelar</button>
				            <button class="btn adp_modal_politicas">Aceptar</button>
				        </div>
				    </div>
				</div>
	        </div>
	        <div class="col l6 s0 degradado height-100vh"></div>
	    </div>
	    <?=$adpnsy->footer()?>
		<script src="<?=ADPNSY_URL;?>js/registro.js?<?=filemtime(ADPNSY_PATH . '/js/registro.js');?>"></script>
	</body>
</html>