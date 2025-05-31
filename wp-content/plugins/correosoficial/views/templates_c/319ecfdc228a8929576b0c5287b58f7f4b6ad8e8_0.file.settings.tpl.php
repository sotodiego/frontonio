<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:03
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d7e75339_28432484',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '319ecfdc228a8929576b0c5287b58f7f4b6ad8e8' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./header.tpl' => 1,
    'file:./settings-customer-data.tpl' => 1,
    'file:./settings-senders.tpl' => 1,
    'file:./settings-user-configuration.tpl' => 1,
    'file:./settings-products.tpl' => 1,
    'file:./settings-zones-carriers.tpl' => 1,
    'file:./settings-customs-processing.tpl' => 1,
  ),
),false)) {
function content_681c97d7e75339_28432484 (Smarty_Internal_Template $_smarty_tpl) {
?><svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>

<?php $_smarty_tpl->_subTemplateRender('file:./header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div id="correos_oficial">
    <div id="settings-container" class="container-fluid">
        <div class="row">
            <div class="row">
                <div class="col-md-12 settings-accordion">
                    <div class="gdpr-links mb-3">
                        <label class="mx-2">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/gdpr/condiciones_servicio.pdf" target="_blank">
                                <?php echo $_smarty_tpl->tpl_vars['_Terms_and_conditions']->value;?>

                            </a>
                        </label>
                        <label>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/gdpr/proteccion_datos.pdf" target="_blank">
                                <?php echo $_smarty_tpl->tpl_vars['_Data_protection_policy_']->value;?>

                            </a>
                        </label>
                    </div>
                    <div class="accordion" id="accordionFlushExample">  
                                                <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button id="customer_data" class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    <?php echo $_smarty_tpl->tpl_vars['_CUSTOMER_DATA']->value;?>

                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <?php $_smarty_tpl->_subTemplateRender('file:./settings-customer-data.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </div>
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?> 
                            <div class="accordion-item" id="sender-anchor">
                                <h2 class="accordion-header" id="flush-headingTwo">
                                    <button id="sender_block" class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                        <?php echo $_smarty_tpl->tpl_vars['_SENDERS']->value;?>

                                    </button>
                                </h2>
                                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                    <?php $_smarty_tpl->_subTemplateRender('file:./settings-senders.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                                </div>
                            </div>
                        <?php }?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingThree">
                                <button class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                    <?php echo $_smarty_tpl->tpl_vars['_USER_CONFIGURATION']->value;?>

                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                <?php $_smarty_tpl->_subTemplateRender('file:./settings-user-configuration.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </div>
                                                <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingFive">
                                <button class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                                    <?php echo $_smarty_tpl->tpl_vars['_PRODUCTS']->value;?>

                                </button>
                            </h2>
                            <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#accordionFlushExample">
                                <?php $_smarty_tpl->_subTemplateRender('file:./settings-products.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingSix">
                                <button class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                                    <?php echo $_smarty_tpl->tpl_vars['_ZONES_AND_CARRIERS']->value;?>

                                </button>
                            </h2>
                            <div id="flush-collapseSix" class="accordion-collapse collapse" aria-labelledby="flush-headingSix" data-bs-parent="#accordionFlushExample">
                                <?php $_smarty_tpl->_subTemplateRender('file:./settings-zones-carriers.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingSeven">
                                <button class="accordion-button collapsed accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSeven" aria-expanded="false" aria-controls="flush-collapseSeven">
                                    <?php echo $_smarty_tpl->tpl_vars['_CUSTOMS_PROCESSING']->value;?>
&nbsp;<span class="co_small">(<?php echo $_smarty_tpl->tpl_vars['Only_for_Correos_shippings']->value;?>
)</span>
                                </button>
                            </h2>
                            <div id="flush-collapseSeven" class="accordion-collapse collapse" aria-labelledby="flush-headingSeven" data-bs-parent="#accordionFlushExample">
                                <?php $_smarty_tpl->_subTemplateRender('file:./settings-customs-processing.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </div>
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 

<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/js/datatables/datatables.min.js"><?php echo '</script'; ?>
>
<?php }
}
