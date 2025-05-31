<html class="loading" lang="es" data-textdirection="ltr">
	<head>
	    <?=$adpnsy->header($info)?>
	</head>
	<body class="horizontal-layout page-header-light horizontal-menu preload-transitions 1-column login-bg blank-page blank-page" data-open="click" data-menu="horizontal-menu" data-col="1-column">
	    <div class="row">
	        <div class="col l6 s12">
	            <div class="container">
	            	<div class="login-bg-pre l0"></div>
	                <div id="login-page" class="row">
	                    <div class="login-card">
	                    	<?php if($info->lglogin_vn){echo'<img class="logo-login" src="'.$info->lglogin_vn.'" alt="'.$info->logo_texto.'">';}else{echo'<h5 class="ml-4">'.$info->logo_texto.'</h5>';} ?>
	                        <form class="recovery-form viwer-form" method="post" action="#" >
	                            <?php if(isset($_GET['key']) && isset($_GET['us'])){
	                            	if($mensaje){ 
										error_log('mensaje: '.print_r($mensaje, true));
										?>
	                            		<div class='error-login<?=$mensaje['m']?" show":"";?>'><?=$mensaje['m'];?></div>
	                            		<div class="row margin">
			                                <div class="input-field col s12">
			                                    <i class="material-icons prefix pt-2">lock_outline</i>
			                                    <input id="password" type="password" name='password1'>
			                                    <label for="password" class="center-align">Contraseña nueva</label>
			                                </div>
			                                <div class="input-field col s12">
			                                    <i class="material-icons prefix pt-2">lock_outline</i>
			                                    <input id="password-2" type="password" name='password2'>
			                                    <label for="password-2" class="center-align">Repite contraseña</label>
			                                </div>
			                                <div class="row">
				                                <div class="input-field col s12">
				                                	<input type="hidden" value='true' name="change">
				                                	<input type="hidden" value='<?=$_GET['key']?>' name="key">
				                                	<input type="hidden" value='<?=$_GET['us']?>' name="us">
				                                    <button type="submit" class="reset_password_no_user btn_low_dancee  btn waves-effect waves-light <?=$info->botton_color?> <?=$info->botton_color_t?> <?=$info->botton_fondo?> <?=$info->botton_tono?> col s12">Cambiar Contraseña</button>
				                                </div>
				                            </div>
			                            </div>
	                            	<?php }else{ ?>
	                            		<div class="row margin">
	                            			<div class="input-field col s12">
	                            				<div class='error-login show'>Este enlace ha caducado o no es valido</div>
	                            			</div>
	                            		</div>
	                            	<?php }
	                            }else{ ?>
		                            <div class='<?=$mensaje?$mensaje['c']:"";?>-login<?=$mensaje?" show":"";?>'><?=$mensaje['m'];?></div>
		                            <div class="row margin">
		                                <div class="input-field col s12">
		                                    <i class="material-icons prefix pt-2">mail_outline</i>
		                                    <input id="username" type="email" name='mail_recovery'>
		                                    <label for="username" class="center-align">Correo electrónico</label>
		                                </div>
		                            </div>
		                            
		                            <div class="row">
		                                <div class="input-field col s12 nmbm">
		                                	<input type="hidden" value='true' name="recovery">
		                                    <button type="submit" id="send_recovery" class="btn waves-effect waves-light col s12">Recuperar Contraseña</button>
		                                </div>
		                            </div>
		                            
		                            <div class="row">
		                                <div class="mt-2 col s12">
		                                    <p class="margin center-align medium-small"><a href="<?=$adpnsy->url_login();?>">Iniciar sesión</a></p>
		                                </div>
		                            </div>

									
		                        <?php } ?>
	                        </form>
	                    </div>
	                </div>
	            </div>
	            <div class="content-overlay"></div>
	        </div>
	        <div class="col l6 s0 degradado height-100vh"></div>
	    </div>
	    <?=$adpnsy->footer()?>
	</body>
</html>