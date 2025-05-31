<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:04
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-user-configuration.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d8038be7_37763780',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '024578b0e2a97e3573d5b49f4c8e4ba634212c9a' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-user-configuration.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d8038be7_37763780 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\vendor\\smarty\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
?>
<div id="UserConfigurationBlock" class="accordion-body">
    <form id="UserConfigurationDataForm" name="UserConfigurationDataForm" method="POST" enctype="multipart/form-data">
        <fieldset>
            <div class="row">

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Default_packages']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="Número de bultos predeterminado que se utilizará en el preregistro de envíos">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input type="number" max="10" min="1" name="DefaultPackages" id="DefaultPackages"
                                    value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['DefaultPackages']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="form-control">
                            </div>
                        <?php }?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Default_label_type']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Tipo de etiqueta que se utilizará en la impresión de etiquetas">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <select class="co_dropdown" name="DefaultLabel" id="DefaultLabel">
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['select_label_options']->value,'selected'=>$_smarty_tpl->tpl_vars['DefaultLabel']->value),$_smarty_tpl);?>

                                </select>
                            </div>
                        <?php }?>
                        </div>
                    </div>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['sga_module']->value) {?>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Payment_method_for_COD_orders']->value;?>

                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                                class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Los pedidos con la forma de pago seleccionada tienen envío contra reembolso">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="CashOnDeliveryMethod" id="CashOnDeliveryMethod">
                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['select_payment_method']->value,'selected'=>$_smarty_tpl->tpl_vars['payment_method_selected']->value),$_smarty_tpl);?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-4" id="BankAndIBANBlock">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Bank_account_number___IBAN']->value;?>
 </br>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="Sólo para envíos con Correos y método de pago contrareembolso (Módulos Contrareembolso, Codfee, Megareembolso)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                        <small
                                            class="notice-small"><?php echo $_smarty_tpl->tpl_vars['Required_for_COD_shipments_with_Correos__Input_IBAN_without_blank_spaces']->value;?>
</small>
                                    </span>
                                </div>
                                <input type="text" name="BankAccNumberAndIBAN" id="BankAccNumberAndIBAN"
                                    value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['BankAccNumberAndIBAN']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="form-control"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Google_Maps_API_Key']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Clave proporcionada por Google. Añada su clave para que los compradores puedan disponer de un mapa al elegir los métodos de envío Oficina o Citypaq en el proceso de compra.">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input type="text" class="form-control" name="GoogleMapsApi" id="GoogleMapsApi"
                                    value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['GoogleMapsApi']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox" name="ActivateTrackingLink"
                                        id="ActivateTrackingLink" <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ActivateTrackingLink']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
>
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Activate_tracking_link_in_the_customer_purchase_history']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Muestra historial de seguimiento en el perfil del comprador">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox" name="LabelObservations"
                                        id="LabelObservations" <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['LabelObservations']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 />
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Order_remarks_to_the_label___max_____characters_']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Imprime los comentarios del comprador en la etiqueta de envío">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        <?php }?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox" name="CustomerAlternativeText"
                                        id="CustomerAlternativeText"
                                        <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['CustomerAlternativeText']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
>
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Alternative_text_for_sender']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Se imprimirá en la etiqueta en lugar del nombre del remitente">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        <?php }?>
                        </div>
                        <div class="col-sm-6">
                            <div id="LabelAlternativeTextInput" class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span
                                        class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Alternative_label_text']->value;?>
</span>
                                </div>
                                <input type="text" class="form-control" name="LabelAlternativeText"
                                    id="LabelAlternativeText" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['LabelAlternativeText']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input id="ActivateWeightByDefault" class="form-check-input mt-0" type="checkbox"
                                        name="ActivateWeightByDefault" id="ActivateWeightByDefault"
                                        <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ActivateWeightByDefault']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 disabled checked>
                                </div>
                                <span
                                    class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Activate_default_weight']->value;?>
</span>
                            </div>
                        <?php }?>
                        </div>
                        <div class="col-sm-6">
                            <div id="ActivateWeightByDefaultInput" class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Default_weight__Kg_']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Peso que se utilizará para cada paquete en el preregistro de envíos en el caso de que no haya un peso especificado">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input type="number" class="form-control" name="WeightByDefault" step="0.1"
                                    id="WeightByDefault" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['WeightByDefault']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input id="ActivateDimensionsByDefault" class="form-check-input mt-0"
                                        type="checkbox" name="ActivateDimensionsByDefault"
                                        id="ActivateDimensionsByDefault"
                                        <?php if ((isset($_smarty_tpl->tpl_vars['ActivateDimensionsByDefault']->value)) && $_smarty_tpl->tpl_vars['ActivateDimensionsByDefault']->value == true) {?>checked<?php }?>>
                                </div>
                                <span
                                    class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Activate_default_dimensions']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Solo aplica a envíos Paq Ligero y Entrega en Citypaq. Si todos sus envíos comparten las mismas dimensiones, puede establecerlas por defecto. Permanecerán editables en datos del envío para manejar las excepciones.">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        <?php }?>
                        </div>
                        <div class="col-sm-6">
                            <div id="ActivateDimensionsByDefaultBLock" class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Height__cm_']->value;?>

                                    </span>
                                </div>
                                <input type="number" class="form-control" name="DimensionsByDefaultHeight"
                                    id="DimensionsByDefaultHeight" <?php if ((isset($_smarty_tpl->tpl_vars['DimensionsByDefaultHeight']->value))) {?>
                                    value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['DimensionsByDefaultHeight']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" <?php }?> min="0"
                                    step="1">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Width__cm_']->value;?>

                                    </span>
                                </div>
                                <input type="number" class="form-control" name="DimensionsByDefaultWidth"
                                    id="DimensionsByDefaultWidth"
                                    <?php if ((isset($_smarty_tpl->tpl_vars['DimensionsByDefaultWidth']->value))) {?>value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['DimensionsByDefaultWidth']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"
                                    <?php }?> min="0" step="1">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Large__cm_']->value;?>

                                    </span>
                                </div>
                                <input type="number" class="form-control" name="DimensionsByDefaultLarge"
                                    id="DimensionsByDefaultLarge"
                                    <?php if ((isset($_smarty_tpl->tpl_vars['DimensionsByDefaultWidth']->value))) {?>value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['DimensionsByDefaultLarge']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"
                                    <?php }?> min="0" step="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox" name="ChangeLogoOnLabel"
                                        id="ChangeLogoOnLabel" <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ChangeLogoOnLabel']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 />
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Change_logo_on_labels']->value;?>
 </br>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Puede subir el logo de su empresa para que se imprima en las etiquetas de los envíos de Correos Express">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                    <small class="notice-small"><?php echo $_smarty_tpl->tpl_vars['Only_CEX_shippings']->value;?>
</small>
                                </span>
                            </div>
                        <?php }?>
                        </div>
                        <div class="col-sm-6">
                            <div id="UploadLogoLabelsBlock" class="input-group mb-3">
                                <div class="input-group-addon input-group-text-custom">
                                    <input class="form-control form-control-sm" id="UploadLogoLabels"
                                        name="UploadLogoLabels" type="file" />
                                    <div class="col-sm-12 d-flex background-logo-input">
                                        <?php if ((isset($_smarty_tpl->tpl_vars['baseLabel']->value))) {?>
                                            <img alt="LabelLogo" class="image-preview" id="UploadLogoLabelsImg"
                                                src="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
media/logo_label/<?php echo $_smarty_tpl->tpl_vars['baseLabel']->value;?>
" class="upload-preview"
                                                width="150" />
                                        <?php } elseif ($_smarty_tpl->tpl_vars['UploadLogoLabels']->value) {?>
                                            <img alt="LabelLogo" class="image-preview" id="UploadLogoLabelsImg"
                                                src="<?php echo $_smarty_tpl->tpl_vars['UploadLogoLabels']->value;?>
" class="upload-preview" width="150" />
                                        <?php }?>
                                    </div>
                                    <?php if ((isset($_smarty_tpl->tpl_vars['UploadLogoLabelsName']->value)) && (isset($_smarty_tpl->tpl_vars['ErrorLogoLabels']->value))) {?>
                                        <div class="col-sm-12 d-flex background-logo-input">
                                            <span id="UploadLogoLabelsText"><?php echo $_smarty_tpl->tpl_vars['UploadLogoLabelsName']->value;?>
</span>
                                            <span id="ErrorLogoLabels"><?php echo $_smarty_tpl->tpl_vars['ErrorLogoLabels']->value;?>
</span>
                                        </div>
                                    <?php }?>
                                </div>
                                <a class="btn btn-danger clean-upload ml-2" id="clean-upload"><i
                                        class="far fa-trash-alt remove"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                        <?php if (!$_smarty_tpl->tpl_vars['sga_module']->value) {?>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox" name="SSLAlternative"
                                        id="SSLAlternative" <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['SSLAlternative']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 />
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Activate_SSL_Certificate']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Método alternativo de conexión. (Puede ser una solución si encontrase un problema de conexión al webservice por este motivo)">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        <?php }?>
                        </div>
                    </div>
                </div>

                                <div class="col-sm-12">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-3" id="AutomaticTrackingBlock">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input name="ActivateAutomaticTracking" id="ActivateAutomaticTracking"
                                        <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ActivateAutomaticTracking']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

                                        class="form-check-input mt-0" type="checkbox"
                                        aria-label="Checkbox for following text input">
                                </div>
                                <span
                                    class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Activate_automatic_tracking']->value;?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-4" id="ShippingStatusProcessBlock">
                                <div class="input-group-addon input-group-checkbox-custom">
                                    <input class="form-check-input mt-0" type="checkbox"
                                        name="ShowShippingStatusProcess" id="ShowShippingStatusProcess"
                                        <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ShowShippingStatusProcess']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 />
                                </div>
                                <span class="input-group-text input-group-text-color">
                                    <?php echo $_smarty_tpl->tpl_vars['Show_shipment_status_progress_in_shop']->value;?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                        class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Define qué estados desesas mostrar en tu pedidos en función de la información devuelta por el envío">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="ShippingStatusProcess">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Shipment_pre_registered']->value;?>

                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="ShipmentPreregistered" id="ShipmentPreregistered">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['select_shipment_status_options']->value, 'ssso');
$_smarty_tpl->tpl_vars['ssso']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ssso']->value) {
$_smarty_tpl->tpl_vars['ssso']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['ssso']->value['id_order_state'];?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['ssso']->value['id_order_state'] == $_smarty_tpl->tpl_vars['ShipmentPreregistered']->value->value) {?> selected<?php }?>>
                                                <?php echo $_smarty_tpl->tpl_vars['ssso']->value['name'];?>

                                            </option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Shipment_Canceled']->value;?>

                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="ShipmentCanceled" id="ShipmentCanceled">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['select_shipment_status_options']->value, 'ssso');
$_smarty_tpl->tpl_vars['ssso']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ssso']->value) {
$_smarty_tpl->tpl_vars['ssso']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['ssso']->value['id_order_state'];?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['ssso']->value['id_order_state'] == $_smarty_tpl->tpl_vars['ShipmentCanceled']->value->value) {?> selected<?php }?>>
                                                <?php echo $_smarty_tpl->tpl_vars['ssso']->value['name'];?>

                                            </option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Shipment_in_progress']->value;?>

                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="ShipmentInProgress" id="ShipmentInProgress">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['select_shipment_status_options']->value, 'ssso');
$_smarty_tpl->tpl_vars['ssso']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ssso']->value) {
$_smarty_tpl->tpl_vars['ssso']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['ssso']->value['id_order_state'];?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['ssso']->value['id_order_state'] == $_smarty_tpl->tpl_vars['ShipmentInProgress']->value->value) {?> selected<?php }?>>
                                                <?php echo $_smarty_tpl->tpl_vars['ssso']->value['name'];?>

                                            </option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Shipment_delivered']->value;?>

                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="ShipmentDelivered" id="ShipmentDelivered">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['select_shipment_status_options']->value, 'ssso');
$_smarty_tpl->tpl_vars['ssso']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ssso']->value) {
$_smarty_tpl->tpl_vars['ssso']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['ssso']->value['id_order_state'];?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['ssso']->value['id_order_state'] == $_smarty_tpl->tpl_vars['ShipmentDelivered']->value->value) {?> selected<?php }?>>
                                                <?php echo $_smarty_tpl->tpl_vars['ssso']->value['name'];?>

                                            </option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['Shipment_returned']->value;?>

                                        </span>
                                    </div>
                                    <select class="co_dropdown" name="ShipmentReturned" id="ShipmentReturned">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['select_shipment_status_options']->value, 'ssso');
$_smarty_tpl->tpl_vars['ssso']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ssso']->value) {
$_smarty_tpl->tpl_vars['ssso']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['ssso']->value['id_order_state'];?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['ssso']->value['id_order_state'] == $_smarty_tpl->tpl_vars['ShipmentReturned']->value->value) {?> selected<?php }?>>
                                                <?php echo $_smarty_tpl->tpl_vars['ssso']->value['name'];?>

                                            </option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6 CronIntervalBlock">
                            <div class="col-sm-6 StatusUpdateTime">
                                <label
                                    class="font-size16 mb-1 mr-2"><?php echo $_smarty_tpl->tpl_vars['Status_update_time']->value;?>
</label>
                                <input type="range" name="CronInterval" id="CronInterval" class="w-10 mx-2" max="8"
                                    min="2" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['CronInterval']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                            </div>
                            <div class="col-sm-2">
                                <span class="CronInterval_TEXT"
                                    id="CronInterval_TEXT"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['CronInterval']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

                                    <?php echo $_smarty_tpl->tpl_vars['hours']->value;?>
</span>
                            </div>
                            <div class="col-sm-12 input-group CronIntervalText">
                                <small><?php echo $_smarty_tpl->tpl_vars['The_time_selected_determines_how_often_order_statuses_are_updated_']->value;?>
</small>
                            </div>
                        </div>
                        <div class="CronButtons col-sm-6">
                            <div class="col-md-6 offset-md-6">
                                <a class="cron-button" href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
log/log_cron_register.txt"
                                    download><?php echo $_smarty_tpl->tpl_vars['Download_log']->value;?>
</a>
                                <a class="cron-button" href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
log/log_cron_error_update.txt"
                                    download><?php echo $_smarty_tpl->tpl_vars['Download_update_errors']->value;?>
</a>
                                <a class="cron-button" href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
log/log_cron_last_request.txt"
                                    download><?php echo $_smarty_tpl->tpl_vars['Download_last_request']->value;?>
</a>
                            </div>
                        </div>
                    </div>
                </div>
                                                <?php if ((isset($_smarty_tpl->tpl_vars['showNIF']->value)) && $_smarty_tpl->tpl_vars['showNIF']->value == 'true') {?>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-checkbox-custom">
                                        <?php if ((isset($_smarty_tpl->tpl_vars['ActivateNifFieldCheckout']->value))) {?>
                                            <input class="form-check-input mt-0" type="checkbox" name="ActivateNifFieldCheckout"
                                                id="ActivateNifFieldCheckout"
                                                <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ActivateNifFieldCheckout']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
>
                                        <?php }?>
                                    </div>
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Add_VAT_Number_field_at_checkout']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Controla el comportamiento del campo NIF en el checkout">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" id="NifFieldRadioBlock">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-checkbox-custom">
                                        <input class="form-check-input mt-0" type="radio" name="NifFieldRadio"
                                            id="NifFieldOptional" value="optional"
                                            <?php echo $_smarty_tpl->tpl_vars['NifFieldRadio']->value === 'OPTIONAL' ? 'checked' : '';?>
>
                                    </div>
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['VAT_Number_Optional']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Activar para hacer opcional el campo NIF en el proceso de pago">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-checkbox-custom">
                                        <input class="form-check-input mt-0" type="radio" name="NifFieldRadio"
                                            id="NifFieldObligatory" value="obligatory"
                                            <?php echo $_smarty_tpl->tpl_vars['NifFieldRadio']->value === 'OBLIGATORY' ? 'checked' : '';?>
>
                                    </div>
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['VAT_Number_Obligatory']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Activar para hacer obligatorio el campo NIF en el proceso de pago">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group mb-4">
                                    <div class="input-group-addon input-group-checkbox-custom">
                                        <input class="form-check-input mt-0" type="radio" name="NifFieldRadio"
                                            id="NifFieldPersonalized" value="personalized"
                                            <?php echo $_smarty_tpl->tpl_vars['NifFieldRadio']->value === 'PERSONALIZED' ? 'checked' : '';?>
>
                                    </div>
                                    <div class="input-group-addon input-group-text-custom">
                                        <span class="input-group-text input-group-text-color">
                                            <?php echo $_smarty_tpl->tpl_vars['VAT_Number_Personalized']->value;?>

                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                                class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                                data-bs-toggle="tooltip" data-bs-placement="right"
                                                title="Indicar el ID del campo NIF si usted ha añadido un campo personalizado en el proceso de pago">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <input type="text" name="NifFieldPersonalizedValue" id="NifFieldPersonalizedValue"
                                        class="form-control" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['NifFieldPersonalizedValue']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
                                                <div class="col-sm-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="co_primary_button" name="UserConfigurationSaveButton"
                            id="UserConfigurationSaveButton" type="submit">
                            <span id="ProcessingUserConfigButton" class="hidden-block">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span role="status" aria-hidden="true"><?php echo $_smarty_tpl->tpl_vars['Processing']->value;?>
</span>
                            </span>
                            <span class="label-message" id="MsgUserConfigButton"
                                role="status"><?php echo $_smarty_tpl->tpl_vars['SAVE_USER_DATA']->value;?>
</span>
                        </button>
                    </div>
                </div>

            </div>
        </fieldset>
    </form>
</div>

<?php echo '<script'; ?>
>
    var userConfigurationSaved = "<?php echo $_smarty_tpl->tpl_vars['User_data_successfully_saved']->value;?>
";
    var requiredCustomMessage = "<?php echo $_smarty_tpl->tpl_vars['Required_field']->value;?>
";
    var minValue1 = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_a_value_greater_than_or_equal_to__']->value;?>
";
    var maxValue10 = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_a_value_less_than_or_equal_to___']->value;?>
";
    var valuesWeightDefault= "<?php echo $_smarty_tpl->tpl_vars['Allowable_value_between___and____kg']->value;?>
";
    var valuesDimensionDefault= "<?php echo $_smarty_tpl->tpl_vars['The_minimum_dimensions_of_a_shipment_are____x____x___cm_']->value;?>
";
    var wrongACCAndIBAN = "<?php echo $_smarty_tpl->tpl_vars['Please_specify_a_valid_Bank_Account_number_IBAN']->value;?>
";
    var minLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_at_least']->value;?>
";
    var maxLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_no_more_than']->value;?>
";
    var characters = "<?php echo $_smarty_tpl->tpl_vars['characters']->value;?>
";
    var hours = "<?php echo $_smarty_tpl->tpl_vars['hours']->value;?>
";
    var co_base_dir = "<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
";
<?php echo '</script'; ?>
><?php }
}
