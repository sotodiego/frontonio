<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:04
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-customs-processing.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d809d705_97396867',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '65011cb67997070e172fb7cab8f93c4c2cca1a79' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-customs-processing.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d809d705_97396867 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\vendor\\smarty\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
?>
<div class="accordion-body accordion-body">

    <form id="CustomProcessingForm" name="CustomProcessingForm" method="POST">
    <fieldset>
        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
        <div class="input-group mb-4">
            <div class="input-group-addon input-group-text-custom">
                <span class="input-group-text input-group-text-color">
                    <?php echo $_smarty_tpl->tpl_vars['Reference_of_customs_consignor']->value;?>

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Deja establecida por defecto su referencia aduanera por si necesita indicarla en envíos con trámite aduanero.">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                    </svg>
                </span>
            </div>
            <input type="text" name="ShippCustomsReference" id="ShippCustomsReference" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['ShippCustomsReference']->value;?>
" required>
        </div>
        <?php }?>
        <div class="input-group mb-4">
        <div class="input-group-addon input-group-checkbox-custom">
                    <input class="form-check-input mt-0" type="checkbox" name="MessageToWarnBuyer" id="MessageToWarnBuyer" <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['MessageToWarnBuyer']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 />
            </div>
            <span class="input-group-text input-group-text-color">
                <?php echo $_smarty_tpl->tpl_vars['Message_to_warn_the_buyer_about_customs_formalities__max______characters_']->value;?>

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Al activarlo, el comprador será advertido durante el proceso de pago de que su envío podría verse afectado por trámites aduaneros. Usted podrá definir el mensaje que quiere mostrar.">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>
            </span>
        </div>

        <div id="MessageToWarnBuyer2" class="row justify-content-md-center">
            <div class="col-sm-10">
                <div class="input-group mb-4">
                    <div class="input-group-addon input-group-text-custom">
                        <span class="input-group-text input-group-text-color">
                            <?php echo $_smarty_tpl->tpl_vars['Message_to_warn_the_buyer']->value;?>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Este mensaje se mostrará en el checkout">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="TranslatableInput" name="TranslatableInput" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['TranslatableInput']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" placeholder="<?php echo $_smarty_tpl->tpl_vars['This_shipment_is_subject_to_customs_clearance__The_price_of_the_shipment_may_be_increased']->value;?>
">
                    <input type="hidden" class="form-control" id="TranslatableInputH" name="TranslatableInputH" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['TranslatableInputH']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                </div>
            </div>
            <div class="col-sm-1">
                <div class="input-group mb-4">
                    <span class="form_switch_language">
                        <select class="custom-select" name="FormSwitchLanguage" id="FormSwitchLanguage">
                            <option selected disabled value=""></option>
                            <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['array_languages']->value,'selected'=>$_smarty_tpl->tpl_vars['selected_language_id']->value),$_smarty_tpl);?>

                        </select>
                    </span>
                </div>
            </div>
        </div>
        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
        <label><?php echo $_smarty_tpl->tpl_vars['Select_Customs_Description_or_Default_Tariff_Number']->value;?>
</label>
        
        <div id="tabs_customs_doc" class="tabs_customs_doc" class="row">
            <ul class="nav nav-pills">
                <li class="nav-item">
                  <a id="customs_desc" class="nav-link  <?php if ($_smarty_tpl->tpl_vars['config_default_aduanera']->value == 0) {?>active<?php }?> customs_desc" data-type="customs_desc" aria-current="page" href="#">
                    <?php echo $_smarty_tpl->tpl_vars['Customs_default_description']->value;?>

                </a>
                </li>
                <li class="nav-item">
                  <a id="customs_code" class="nav-link <?php if ($_smarty_tpl->tpl_vars['config_default_aduanera']->value == 1) {?>active<?php }?> customs_code" data-type="customs_code" href="#">
                    <?php echo $_smarty_tpl->tpl_vars['Tariff_Code']->value;?>

                  </a>
                </li>
              </ul>
        </div>

        <div id="customs_desc_tab" class="row content-tab <?php if ($_smarty_tpl->tpl_vars['config_default_aduanera']->value == 1) {?>hidden-block<?php }?> ">
            <div class="col-sm-6">
                <div class="input-group mb-4">
                        <input class="form-check-input" type="radio" name="CustomsDesriptionAndTariff[]" id="DescriptionRadio" value="0">
                    <div class="input-group-addon input-group-text-custom">
                        <label class="input-group-text input-group-text-color">
                            <?php echo $_smarty_tpl->tpl_vars['Default_customs_description']->value;?>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Utilice este campo si todos (o una gran parte de sus envíos) se ajusta a una única descripción. Se usará por defecto en sus envíos con trámite aduanero pero la podrá modificar en los casos que considere durante la gestión del envío.">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                        </label>
                    </div>
                    <select class="co_dropdown" name="DefaultCustomsDescription" id="DefaultCustomsDescription">
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['customs_desc_array']->value,'selected'=>$_smarty_tpl->tpl_vars['customs_desc_selected']->value),$_smarty_tpl);?>

                    </select>
                </div>
            </div>
         </div>

         <div id="customs_code_tab" class="row content-tab <?php if ($_smarty_tpl->tpl_vars['config_default_aduanera']->value == 0) {?>hidden-block<?php }?>">
            <div class="col-sm-3">
                <div class="input-group mb-1">
                        <input class="form-check-input" type="radio" name="CustomsDesriptionAndTariff[]" id="TariffRadio" value="1">
                    <div class="input-group-addon input-group-text-custom">
                        <label class="input-group-text input-group-text-color">
                            <?php echo $_smarty_tpl->tpl_vars['Tariff_code']->value;?>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Utilice este campo si todos (o una gran parte de sus envíos) se ajusta a una única descripción. Se usará por defecto en sus envíos con trámite aduanero pero lo podrá modificar en los casos que considere durante la gestión del envío.">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                        </label>
                    </div>
                    <input type="text" class="form-control" name="Tariff" id="Tariff" value="<?php echo $_smarty_tpl->tpl_vars['Tariff']->value;?>
"/>
                </div>
            </div>
    
            <div class="col-sm-6">
                <div class="input-group mb-4">
                    <div class="input-group-addon input-group-text-custom">
                        <span class="input-group-text input-group-text-color" id="basic-addon1">
                            <?php echo $_smarty_tpl->tpl_vars['Description']->value;?>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Utilice este campo si todos (o una gran parte de sus envíos) se ajusta a una única descripción. Se usará por defecto en sus envíos con trámite aduanero pero la podrá modificar en los casos que considere durante la gestión del envío.">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                        </span>
                    </div>
                    <input type="text" name="TariffDescription" id="TariffDescription" class="form-control" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['TariffDescription']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" required>
                </div>
            </div>
        </div>
        <?php }?>


    <div class="col-sm-12">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <input class="co_primary_button" name="CustomsProcessingSaveButton" id="CustomsProcessingSaveButton" type="submit" value="<?php echo $_smarty_tpl->tpl_vars['SAVE_CUSTOMS_PROCESSING']->value;?>
">
        </div>
    </div>

    </fieldset>
    </form>
</div>

<?php echo '<script'; ?>
>
    var customsProcessingSaved = "<?php echo $_smarty_tpl->tpl_vars['Customs_Processing_successfully_saved']->value;?>
";
    var requiredCustomMessage = "<?php echo $_smarty_tpl->tpl_vars['Required_field']->value;?>
";
    var minLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_at_least']->value;?>
";
    var maxLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_no_more_than']->value;?>
";
    var tariffLength = "<?php echo $_smarty_tpl->tpl_vars['Input_data_must_be______or____characters_long']->value;?>
";
    var characters = "<?php echo $_smarty_tpl->tpl_vars['characters']->value;?>
";
    var sga_module = "<?php echo $_smarty_tpl->tpl_vars['sga_module']->value;?>
";
<?php echo '</script'; ?>
>
<?php }
}
