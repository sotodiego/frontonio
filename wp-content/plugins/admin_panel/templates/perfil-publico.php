<div class="breadcrumbs" id="breadcrumbs-wrapper">
  <div class="container">
    <div class="row">
      <div class="col s12">
        <div class="row">
          <div class="col s12">
            <div class="nav-wrapper">
                <div class="col s12">
                    <a href="/dashboard" class="breadcrumb">Panel de administración</a>
                    <a href="#" class="breadcrumb">Perfil</a>
                </div>
            </div>
          </div>
          <div class="col s12">
            <h5 class="breadcrumbs-title mt-0 mb-0">Perfil</h5>                
          </div>              
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col s12">
    <div class="container">
      <section class="section">
        <div class="card-panel mt-0" style="max-width: 700px; margin: auto;">
          <div class="row">
            <div class="col s12">
              <?php 
                require ADPNSY_PATH . "/admin_ini_list.php";
                $user = wp_get_current_user();
                $_user = $wpdb->get_row("SELECT * FROM $dnc_usuario WHERE id = '$user->ID'");
                if($_user){ $img_user = ($_user->foto) ? "<img src='".WP_CONTENT_URL.$_user->foto."' alt='Foto de perfil ".$_user->correo."'>" : '<i class="material-symbols-outlined">add_photo_alternate</i>'; ?>
                  <div id="container_edit_profile_user" class="pages_academy">
                    <form id="form_edit_profile">
                      <div class="rows_profiles">
                        <label for="img_profile_user">
                          <p>Foto de perfil</p>
                          <span><?= $img_user ?></span>
                        </label>
                        <input type="file" id="img_profile_user" name="img_profile_user" accept=".jpg, .jpeg, .png">    
                      </div>
                      <div class="rows_profiles">
                        <label for="name_user">Nombre *</label>
                        <input type="text" id="name_user" name="name_user" value="<?= (($_user->nombre) ? $_user->nombre : '') ?>">
                      </div>
                      <div class="rows_profiles">
                        <label for="last_name_user">Apellidos *</label>
                        <input type="text" id="last_name_user" name="last_name_user" value="<?= (($_user->apellido) ? $_user->apellido : '') ?>">
                      </div>
                      <div class="rows_profiles">
                        <label for="tel_user">Telefono</label>
                        <input type="text" id="tel_user" name="tel_user" value="<?= (($_user->telefono) ? $_user->telefono : '') ?>">
                      </div>
                      <div class="rows_profiles">
                        <label for="mail_user">Correo electrónico *</label>
                        <input type="text" id="mail_user" name="mail_user" value="<?= (($_user->correo) ? $_user->correo : '') ?>" oldval="<?= (($_user->correo) ? $_user->correo : '') ?>">
                      </div>
                      <div class="rows_profiles">
                        <label for="pass1">Contraseña (dejar en blanco si no se desea editar)</label>
                        <input type="password" id="pass1" name="pass1">
                      </div>
                      <div class="rows_profiles">
                        <label for="pass2">Confirmar contraseña (dejar en blanco si no se desea editar)</label>
                        <input type="password" id="pass2" name="pass2">
                      </div>
                      <button id="save_data_user_ac">Guardar</button>
                    </form>
                  </div>
                <?php }else{ ?>
                  <div class="container">
                    <div class="row">   
                      <div class="col s12">
                        <div class="container">
                          <div class="section" id="user-profile">
                            <div class="row">
                              <div class="col s12">
                                <div class="row">
                                  <div class="card user-card" id="feed">
                                    <div class="card-content card-border-gray" ><h5>Este usuario no posee perfil publico</h5></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div> 
                <?php } 
              ?>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  window.unik = '<?=wp_create_nonce("usuarios_academia");?>';
</script>