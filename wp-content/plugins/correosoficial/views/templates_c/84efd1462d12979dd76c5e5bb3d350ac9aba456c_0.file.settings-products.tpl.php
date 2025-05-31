<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:04
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-products.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d805dcd1_82667847',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '84efd1462d12979dd76c5e5bb3d350ac9aba456c' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-products.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d805dcd1_82667847 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="accordion-body">

    <form action="index.php?controller=AdminCorreosOficialProductsProcess" id="CorreosProductsForm" name="CorreosProductsForm" method="POST">
        <fieldset>
            <div id="products_container_general" class="row justify-content-around products_container_general hidden-block">
                <div id="advice_products" class="advice">
                    <h4>
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill" /></svg>
                        <?php echo $_smarty_tpl->tpl_vars['Select_the_products_to_be_displayed_in_the_checkout']->value;?>

                    </h4>
                </div>

                <?php if ((isset($_smarty_tpl->tpl_vars['products_column2']->value)) && $_smarty_tpl->tpl_vars['products_column2']->value) {?>
                <div id="products_container_correos" class="col-sm-5 products_container <?php if ($_smarty_tpl->tpl_vars['correos']->value != true) {?>hidden-block<?php }?>">
                    <h3><?php echo $_smarty_tpl->tpl_vars['Correos']->value;?>
</h3>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products_column2']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                        <div class="form-check">
                            <input name="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" class="form-check-input" id="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->active;?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value->active == 1) {?>checked<?php }?> />
                            <label for="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" class="form-check-label form-check-label-color"> <?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
 </label>
                        </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div>
                <?php }?>
                <?php if ((isset($_smarty_tpl->tpl_vars['products_column1']->value)) && $_smarty_tpl->tpl_vars['products_column1']->value) {?>
                <div id="products_container_cex" class="col-sm-5 products_container <?php if ($_smarty_tpl->tpl_vars['cex']->value != true) {?>hidden-block<?php }?>">
                    <h3><?php echo $_smarty_tpl->tpl_vars['Correos_Express']->value;?>
</h3>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products_column1']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                        <div class="form-check">
                            <input name="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" class="form-check-input" id="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->active;?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value->active == 1) {?>checked<?php }?> />
                            <label for="products[<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
]" class="form-check-label form-check-label-color"> <?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</label>
                        </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div>
                <?php }?>
                <div class="col-sm-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button class="co_primary_button" name="ProductsSaveButton" id="ProductsSaveButton" type="submit"> 
                            <span id="ProcessingProductsButton" class="hidden-block">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span role="status" aria-hidden="true"><?php echo $_smarty_tpl->tpl_vars['Processing']->value;?>
</span>
                            </span>
                            <span class="label-message" id="MsgSaveProductsButton" role="status" aria-hidden="true"><?php echo $_smarty_tpl->tpl_vars['SAVE_PRODUCTS']->value;?>
</span>
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>

    
    <div id="advice_no_products" class="advice hidden-block">
        <h4>
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill" /></svg>
            <?php echo $_smarty_tpl->tpl_vars['No_Customers_Active']->value;?>

            <a id="go_to_customer_data" href="#customer_data"><?php echo $_smarty_tpl->tpl_vars['If_you_already_has_a_Customer_Code__please_go_to_CUSTOMER_DATA']->value;?>
</a>
        </h4>
    </div>

</div>

<?php echo '<script'; ?>
>
    var productsSaved = "<?php echo $_smarty_tpl->tpl_vars['Products_successfully_saved']->value;?>
";
<?php echo '</script'; ?>
>
<?php }
}
