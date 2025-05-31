<!DOCTYPE html>
<html class="loading" lang="es" data-textdirection="ltr">
	<head>
	    <?=$adpnsy->header($info)?>
	    <?php wp_head(); ?>
	    <script type="text/javascript">
		    window.addEventListener('beforeinstallprompt', (e) => {
		      e.preventDefault();
		        if(!getCookie("ni")){
		        $("#install").fadeIn();
		      }
		      $("#bnnr_to_dlt").css({display: "block"});
		    });
		</script>
	</head>
	<body class="horizontal-layout page-header-light horizontal-menu preload-transitions 1-column login-bg blank-page blank-page" data-open="click" data-menu="horizontal-menu" data-col="1-column">
	    <div class="row">
	        <div class="col l6 s12">
	            <div class="container">
	            	<div class="login-bg-pre l0"></div>
	                <div id="login-page">
	                    <div class="login-card">
	                    	<?php if($info->lglogin_vn){echo'<img class="logo-login" src="'.$info->lglogin_vn.'" alt="'.$info->logo_texto.'">';}else{echo'<h5 class="ml-4">'.$info->logo_texto.'</h5>';} ?>
	                        <form class="login-form viwer-form" method="post" action="#" >
	                        	<?php $adpnsy->mensaje(); ?>
	                            <div class='error-login<?=$mensaje?" show":"";?>'><?=$mensaje;?></div>
	                            <div class="row margin mt-8">
	                                <div class="input-field col s12">
	                                    <i class="material-icons prefix pt-2">person_outline</i>
	                                    <input id="username" type="text" name='user_login'>
	                                    <label for="username" class="center-align">E-mail</label>
	                                </div>
	                            </div>
	                            <div class="row margin">
	                                <div class="input-field col s12">
	                                    <i class="material-icons prefix pt-2">lock_outline</i>
	                                    <input id="password" type="password" name='user_password'>
	                                    <label for="password">Contraseña</label>
	                                    <i class="material-icons showpass">visibility</i>
	                                </div>
	                            </div>
	                            <div class="row">
	                                <div class="col s12 m12 l12 mt-1">
	                                    <p>
	                                        <label>
	                                            <input type="checkbox" name='remember' />
	                                            <span>Recuérdame</span>
	                                        </label>
	                                    </p>
	                                </div>
	                            </div>
	                            <!-- <div class="row">
	                                <div class="col s12 m12 l12 ml-2 mt-1">
	                                    <p>
	                                        <label>
	                                            <input type="checkbox" id='permisos_notificaciones' />
	                                            <span>Activar notificaciones</span>
	                                        </label>
	                                    </p>
	                                </div>
	                            </div> -->
	                            <div class="row">
	                                <div class="input-field col s12 nmbm">
	                                	<input type="hidden" value='true' name="login">
	                                	<input type="hidden" id="user_push" name="user_push">
	                                    <button id="send_login" class="btn col s12 waves-effect waves-light white-text ">Acceder</button>
	                                </div>
	                            </div>

	                            <div class="row">
	                            	<?php  if ($info->Recovery) { ?>
		                                <div class="mt-2 col s12">
		                                    <p class="margin center-align medium-small"><a href="<?=$adpnsy->url_page($info->Recovery);?>">Recuperar contraseña</a></p>
		                                </div>
	                            	<?php } ?>
	                            </div>
	                        </form>
	                    </div>
	                </div>
	            </div>
	            <div class="content-overlay"></div>
	        </div>
	        <div class="col l6 s0 degradado2 height-100vh"></div>
	    </div>
	    <?=$adpnsy->footer()?>

		<div id="install">
		  <div class="banner-install">
		      <img src="<?php echo ADPNSY_URL; ?>/pwa/public/images/icon.png">
		      <p>¿Desea instalar la app de Dancee?</p>
		      <button id="btnAdd">Instalar</button>
		      <a href="#">Más tarde</a>
		  </div>
		</div>

		<div id="installIos">
		  <div class="banner-installIos">
		      <img src="<?php echo ADPNSY_URL; ?>/pwa/public/images/icon.png">
		      <p>¿Desea instalar la app de Dancee?</p>
		      <button id="btnAddIos">Instalar</button>
		      <a href="#" id="ml-tr-ios">No volver a mostrar</a>
		  </div>
		</div>

		<div id="installIosSteps">
		  <div class="banner-installIosSteps">
		    <div class="cntn-stps-mdl">
		      
			    <div class="step_ios_1" id="step_ios_1">
			      <p>Paso 1:</p>
			      <span>En la barra inferior, ubique el botón de descargar y toquelo.</span>
			      <img src="<?php echo ADPNSY_URL; ?>/pwa/public/images/install_ios_p1.png" alt="" class="img_step_1"> 
			      <button id="btn_stp_1">Siguiente</button>
			    </div>

			    <div class="step_ios_2" id="step_ios_2">
			      <p>Paso 2:</p>
			      <span>Desde las opciones, toque la opción Agregar a la pantalla de inicio.</span>
			      <img src="<?php echo ADPNSY_URL; ?>/pwa/public/images/install_ios_p2.png" alt="" class="img_step_2">
			      <button id="btn_stp_2">Siguiente</button>
			    </div>

			    <div class="step_ios_3" id="step_ios_3">
			      <p>Paso 3:</p>
			      <span>Toque el botón de añadir que correponde a la app, y listo!. <br><br>¡Disfruta de Dancee! </span>
			      <button id="x-stps-mdl">Entendido!</button>
			    </div>
		      
		    </div>
		  </div>
		</div>
	</body>
</html>