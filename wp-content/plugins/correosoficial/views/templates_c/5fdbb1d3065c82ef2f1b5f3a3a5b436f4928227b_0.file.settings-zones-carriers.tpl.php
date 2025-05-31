<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:04
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-zones-carriers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d80852b5_73018808',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5fdbb1d3065c82ef2f1b5f3a3a5b436f4928227b' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-zones-carriers.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d80852b5_73018808 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="accordion-body accordion-body">
    <form action="index.php?controller=AdminCorreosOficialZonesCarriers" id="CorreosZonesCarriersForm" name="CorreosZonesCarriersForm" method="POST">
        <div class="row">
            <div class="col-sm-12 ZonesAndCarriers">
                <div class="alert alert-secondary d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    <div>
                        <?php echo $_smarty_tpl->tpl_vars['Carriers_with____are_carriers_that_are_not_active']->value;?>
.</br>
                        <?php echo $_smarty_tpl->tpl_vars['It_is_recommended_to_configure_them_for_backward_compatibility']->value;?>
.
                    </div>
                </div>
        
                <div class="input-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showAllCarriersCheck">
                        <label class="form-check-label form-check-label-color" for="showAllCarriersCheck">
                            <?php echo $_smarty_tpl->tpl_vars['Activate_all_carriers']->value;?>

                        </label>
                    </div>
                    <div class="col-sm-12 ProductsAndCarriersList">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['zonesandcarriers']->value, 'zone');
$_smarty_tpl->tpl_vars['zone']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['zone']->value) {
$_smarty_tpl->tpl_vars['zone']->do_else = false;
?>
                        <div>
                            <?php if (!empty($_smarty_tpl->tpl_vars['zone']->value['carriers'])) {?>
                                <div class="zone-name"><?php echo $_smarty_tpl->tpl_vars['zone']->value['zonename'];?>
</div>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['zone']->value['carriers'], 'carrier');
$_smarty_tpl->tpl_vars['carrier']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->value) {
$_smarty_tpl->tpl_vars['carrier']->do_else = false;
?>
                                        <?php if (($_smarty_tpl->tpl_vars['carrier']->value['active'] == 1)) {?>
                                            <div class="input-group mb-3">
                                        <?php } else { ?>
                                            <div class="input-group mb-3 hidden-product-option">
                                        <?php }?>
                                        <div class="input-group-addon input-group-text-custom">
                                            <span class="input-group-text input-group-text-color">
                                                <?php if (($_smarty_tpl->tpl_vars['carrier']->value['active'] == 0)) {?>**<?php }?>
                                                <?php echo $_smarty_tpl->tpl_vars['carrier']->value['name'];?>

                                            </span>
                                        </div>
                                        <select class="co_dropdown scp_products" id="scp_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
_<?php echo $_smarty_tpl->tpl_vars['carrier']->value['id_carrier'];?>
" name="scp_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
_<?php echo $_smarty_tpl->tpl_vars['carrier']->value['id_carrier'];?>
">
                                            <option value=""></option>
                                            <?php if (!empty($_smarty_tpl->tpl_vars['zone']->value['products'])) {?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['zone']->value['products'], 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?> 
                                                    <option value="<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value->product_type == "office" || $_smarty_tpl->tpl_vars['product']->value->product_type == "citypaq") {?> disabled<?php }?> <?php if ($_smarty_tpl->tpl_vars['product']->value->id == $_smarty_tpl->tpl_vars['carrier']->value['product_selected']) {?> selected<?php }?>>
                                                        <?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>

                                                    </option>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <?php }?>
                            </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 mt-3  mb-3">
                <div id="advice_products" class="advice">
                    <h4>
                        <?php echo $_smarty_tpl->tpl_vars['Automatic_product_assignment___Carrier_relationship']->value;?>

                    </h4>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-addon input-group-text-custom">
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['The_title_contains']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings"
                                        viewBox="0 0 16 16" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title=""
                                        data-bs-original-title="<?php echo $_smarty_tpl->tpl_vars['Search_the_carrier_name_in_the_following_text_to_capture_the_automatic_change_']->value;?>
">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z">
                                        </path>
                                    </svg>
                                </span>
                            </div>
                            <input type="text" class="form-control" name="AutomaticProductAssignmentText" id="AutomaticProductAssignmentText" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['AutomaticProductAssignmentText']->value->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <div class="input-group-addon input-group-text-custom">
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Product_to_assign']->value;?>

                                </span>
                            </div>
                            <select class="co_dropdown" id="AutomaticProductAssignmentProduct" name="AutomaticProductAssignmentProduct">
                                <option value=""><?php echo $_smarty_tpl->tpl_vars['Select_Product']->value;?>
</option>
                                <?php if (!empty($_smarty_tpl->tpl_vars['active_products']->value)) {?>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['active_products']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                                        <option value="<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
"
                                            <?php if ($_smarty_tpl->tpl_vars['product']->value->product_type == "office" || $_smarty_tpl->tpl_vars['product']->value->product_type == "citypaq") {?>
                                                disabled<?php }?> <?php if ($_smarty_tpl->tpl_vars['product']->value->id == $_smarty_tpl->tpl_vars['AutomaticProductAssignmentProduct']->value->value) {?>
                                            selected<?php }?>>
                                            <?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>

                                        </option>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <a class="cron-button" href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
log/log_automatic_product_assignment.txt" download><?php echo $_smarty_tpl->tpl_vars['Download_log']->value;?>
</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <input class="co_primary_button" name="ZonesCarriersSaveButton" id="ZonesCarriersSaveButton"
                        type="submit" value="<?php echo $_smarty_tpl->tpl_vars['SAVE_ZONES_AND_CARRIERS']->value;?>
">
                </div>
            </div>
        </div>
    </form>
</div>
<?php echo '<script'; ?>
>
    var zonesCarriersSaved = "<?php echo $_smarty_tpl->tpl_vars['Zones_and_carriers_successfully_saved']->value;?>
";
<?php echo '</script'; ?>
>
<?php }
}
