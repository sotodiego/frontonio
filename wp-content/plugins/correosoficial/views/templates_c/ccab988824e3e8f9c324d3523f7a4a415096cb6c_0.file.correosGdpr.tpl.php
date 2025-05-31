<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:38:36
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\correosGdpr.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97bce45ac5_43610989',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ccab988824e3e8f9c324d3523f7a4a415096cb6c' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\correosGdpr.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./header.tpl' => 1,
  ),
),false)) {
function content_681c97bce45ac5_43610989 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:./header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="correos-content-gdpr">
    <h4 class="correos-title-gdpr">
        <?php echo $_smarty_tpl->tpl_vars['To_use_our_module_you_must_accept_our_conditions_']->value;?>

    </h4>
    <form id="comment_form" name="comment_form" class="needs-validation"></form>
    <form id="correos-form-gdpr" method="post" class="correos-form-gdpr">
        <input type="hidden" name="gdpr_nonce" id="gdpr_nonce" value="<?php echo $_smarty_tpl->tpl_vars['gdpr_nonce']->value;?>
">
        <div class="col-6 col-xs-6 px-4 correos-checks-gdpr">
            <div class="input-group correos-check-gdpr">
                <input type="checkbox" id="correos-gdpr-check" name="correos-gdpr-check" class="correos-input-gdpr" required>
                <label for="correos-gdpr-check" class="correos-text-gdpr">
                    <?php echo $_smarty_tpl->tpl_vars['I_have_read_and_accept_the_']->value;?>
 
                    <a href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/gdpr/condiciones_servicio.pdf" target="_blank">
                        <?php echo $_smarty_tpl->tpl_vars['terms_and_conditions']->value;?>

                    </a>
                </label>
            </div>
            <div class="input-group correos-check-gdpr">
                <input type="checkbox" id="correos-dataProtect-check" name="correos-dataProtect-check" class="correos-input-gdpr" required>
                <label for="correos-betatester-check" class="correos-text-gdpr">
                <?php echo $_smarty_tpl->tpl_vars['I_have_read_and_accept_the_']->value;?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/gdpr/proteccion_datos.pdf" target="_blank">
                        <?php echo $_smarty_tpl->tpl_vars['data_protection_policy_']->value;?>

                    </a>
                </label>
            </div>
        </div>
        <div class="col-6 col-xs-6 correos-checks-button-gdpr">
            <button type="submit" class="btn btn-lg correos-button-gdpr">
                <?php echo $_smarty_tpl->tpl_vars['I_ACCEPT']->value;?>

            </button>
        </div>
    </form>
</div><?php }
}
