<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:03
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-customer-data.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d7ea7194_71814202',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd788861e4c3ff5d867208a0f41d0f66e6dda97d1' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-customer-data.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d7ea7194_71814202 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="customer-data" class="accordion-body">
    <div class="row justify-content-md-center">
        <div class="col-sm-12 table-users">
            <div class="card">
                <div class="card-header"><?php echo $_smarty_tpl->tpl_vars['ACTIVE_CUSTOMERS']->value;?>
</div>
                <div class="card-body card-body-custom">
                    <table id="CustomerDataDataTable" class="table">
                        <thead>
                            <tr>
                                <th><?php echo $_smarty_tpl->tpl_vars['id']->value;?>
</th>
                                <th><?php echo $_smarty_tpl->tpl_vars['Status']->value;?>
</th>
                                <th><?php echo $_smarty_tpl->tpl_vars['Customer_Code']->value;?>
</th>
                                <th><?php echo $_smarty_tpl->tpl_vars['Company']->value;?>
</th>
                                <th><?php echo $_smarty_tpl->tpl_vars['Edit']->value;?>
</th>
                                <th><?php echo $_smarty_tpl->tpl_vars['Delete']->value;?>
</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="add-new-contract-container">
                        <button id="add-new-contract" type="button" class="btn btn-secondary"><span class="add-new-contract-plus">+ </span><?php echo $_smarty_tpl->tpl_vars['New_contract']->value;?>
</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="cocexUserLoggin" class="row justify-content-md-center hidden-block">
        <div id="Correos" class="col-sm-6 col-correos">
            <img src="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/commons/img/logos/correos_ind_blue.png" class="correos-logo"
                alt="Logo Correos" />
            <div class="connected" id="connected-correos">
                <span id="CorreosConnected"><?php echo $_smarty_tpl->tpl_vars['Connected']->value;?>
</span>
            </div>
            <div class="noconnected">
                <span id="CorreosNoConnected"><?php echo $_smarty_tpl->tpl_vars['No_connected']->value;?>
</span>
            </div>
            <form id="CorreosCustomerDataForm" name="CorreosCustomerDataForm" method="POST">
                <fieldset>
                    <input id="CorreosCompany" name="CorreosCompany" value="Correos" type="hidden"
                        class="form-control" />
                    <input id="idCorreos" name="idCorreos" value="" type="hidden" class="form-control" />
                    <input name="operation" value="CorreosCustomerDataForm" type="hidden" class="form-control" />
                    <div class="row justify-content-md-center">
                        <div class="col-sm-10">
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Contract_number']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="Recibido en correo electrónico de correos@correos.com, asunto: Alta Cuenta Sistema (PETGSVS…)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosContract" name="CorreosContract" value="" type="text"
                                    class="form-control" aria-label="Número de Contrato"
                                    aria-describedby="CorreosContract" placeholder="8 números, ej.: 12345678" />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Customer_number']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Recibido en correo electrónico de correos@correos.com, asunto: Alta Cuenta Sistema (PETGSVS…)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosCustomer" name="CorreosCustomer" value="" type="text"
                                    class="form-control" aria-label="Número de Cliente"
                                    aria-describedby="CorreosCustomer" placeholder="8 números, ej.: 87654321"
                                    required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Correos_Key']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Recibido en correo electrónico de correos@correos.com, asunto: Alta Cuenta Sistema (PETGSVS…)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosKey" name="CorreosKey" value="" type="text" class="form-control"
                                    aria-label="Código etiquetador" aria-describedby="CorreosKey"
                                    placeholder="4 dígitos, ej.: 1A4H" required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Systems_Customer_Account']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="Recibido en correo electrónico de correos@correos.com, asunto: Alta Cuenta Sistema (PETGSVS…)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosUser" name="CorreosUser" value="" type="text" class="form-control"
                                    aria-label="Cuenta de cliente de Sistemas" aria-describedby="CorreosUser"
                                    placeholder="13 dígitos, comienza por W, ej.: W876543211A4H" required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Account_Password']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Recibido en correo electrónico de correos@correos.com, asunto: Alta Cuenta Sistema (PETGSVS…)">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosPassword" name="CorreosPassword" value="" type="password"
                                    class="form-control" aria-label="Contaseña Cuenta"
                                    aria-describedby="CorreosPassword" placeholder="8 dígitos, ej.: Qs7(Gn8%"
                                    required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['My_Office_User']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Email con el que esté registrado en la Oficina Virtual de Correos">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CorreosOv2Code" name="CorreosOv2Code" value="" type="email"
                                    class="form-control" aria-label="Usuario Mi Oficina"
                                    aria-describedby="CorreosOv2Code"
                                    placeholder="email con el que se registró en Mi Oficina" required />
                            </div>

                            <div class="d-flex justify-content-md-end gap-2 mb-4">
                                <input class="btn btn-primary" name="CorreosCustomerDataSaveButton"
                                    id="CorreosCustomerDataSaveButton" type="submit"
                                    value="<?php echo $_smarty_tpl->tpl_vars['Add']->value;?>
" />
                                <button class="btn btn-danger" name="CorreosCustomerDataCancelButton"
                                    id="CorreosCustomerDataCancelButton" type="button">
                                    <?php echo $_smarty_tpl->tpl_vars['CANCEL']->value;?>

                                </button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

        <div id="CEX" class="col-sm-6 col-cex">
            <img src="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/commons/img/logos/cex_ind_yellow.png" class="correos-express-logo"
                alt="Logo Correos Express" />
            <div class="connected" id="connected-cex">
                <span id="CEXConnected"><?php echo $_smarty_tpl->tpl_vars['Connected']->value;?>
</span>
            </div>
            <div class="noconnected">
                <span id="CEXNoConnected"><?php echo $_smarty_tpl->tpl_vars['No_connected']->value;?>
</span>
            </div>
            <form id="CEXCustomerDataForm" name="CEXCustomerDataForm" method="POST">
                <fieldset>
                    <input id="CEXCompany" name="CEXCompany" value="CEX" type="hidden" class="form-control" />
                    <input id="idCEX" name="idCEX" value="" type="hidden" class="form-control" />
                    <input name="operation" value="CEXCustomerDataForm" type="hidden" class="form-control" />
                    <div class="row justify-content-md-center">
                        <div class="col-sm-10">
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Customer_Code']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Datos facilitado por Correos Express. Presente en su contrato">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CEXCustomer" name="CEXCustomer" value="" type="text" class="form-control"
                                    aria-label="Código cliente" aria-describedby="CEXCustomer" required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['User']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Dato facilitado por Correos Express">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CEXUser" name="CEXUser" value="" type="text" class="form-control"
                                    aria-label="Usuario" aria-describedby="CEXUser" required />
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-addon input-group-text-custom">
                                    <span class="input-group-text input-group-text-color">
                                        <?php echo $_smarty_tpl->tpl_vars['Password']->value;?>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3"
                                            class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Dato facilitado por Correos Express">
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="CEXPassword" name="CEXPassword" value="" type="password" class="form-control"
                                    aria-label="Password" aria-describedby="CEXPassword" required />
                            </div>

                            <div class="d-flex justify-content-md-end gap-2 mb-4">
                                <input class="btn btn-primary" name="CEXCustomerDataSaveButton"
                                    id="CEXCustomerDataSaveButton" type="submit"
                                    value="<?php echo $_smarty_tpl->tpl_vars['Add']->value;?>
" />
                                <button class="btn btn-danger" name="CEXCustomerDataCancelButton"
                                    id="CEXCustomerDataCancelButton" type="button">
                                    <?php echo $_smarty_tpl->tpl_vars['CANCEL']->value;?>

                                </button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
</div>
</div>

<?php echo '<script'; ?>
>
    var wantDeleteCustomer = "<?php echo $_smarty_tpl->tpl_vars['Do_you_want_to_delete_this_customer_']->value;?>
";
    var customerHaveSender = "<?php echo $_smarty_tpl->tpl_vars['Action_not_allowed__The_account_you_want_to_delete_has_an_associated_sender']->value;?>
";
    var noCustomersActive = "<?php echo $_smarty_tpl->tpl_vars['There_is_no_customers_actives_']->value;?>
";
    var requiredCustomMessage = "<?php echo $_smarty_tpl->tpl_vars['Required_field']->value;?>
";
    var minLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_at_least']->value;?>
";
    var maxLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_no_more_than']->value;?>
";
    var characters = "<?php echo $_smarty_tpl->tpl_vars['characters']->value;?>
";
    var confirmationTitle = "<?php echo $_smarty_tpl->tpl_vars['Confirmation_']->value;?>
";

    var contractNumberMsj = "<?php echo $_smarty_tpl->tpl_vars['The_contract_number_must_have___numbers__for_example_________']->value;?>
";
    var customerNumberMsj = "<?php echo $_smarty_tpl->tpl_vars['The_Customer_Number_must_have___numbers__e_g__________']->value;?>
";
    var labelingCodeMsj = "<?php echo $_smarty_tpl->tpl_vars['The_labelling_code_must_be___characters__letters_and_or_numbers__e_g___A_H']->value;?>
";
    var systemsAccountMsj = "<?php echo $_smarty_tpl->tpl_vars['The_Client_Account_Systems_must_have_format_w_________or_W_________A_H']->value;?>
";
    var systemsPasswordMsj = "<?php echo $_smarty_tpl->tpl_vars['Account_Password_must_be___alphanumeric_characters__e_g___Qs__Gn___']->value;?>
";
    var invalidEmailMsj = "<?php echo $_smarty_tpl->tpl_vars['The_My_Office_User_must_be_an_email_address']->value;?>
";

    var title200 = "<?php echo $_smarty_tpl->tpl_vars['Your_credentials_are_correct_']->value;?>
";
    var description200 = "<?php echo $_smarty_tpl->tpl_vars['Please__save_your_username_and_password_in_a_safe_way_']->value;?>
";
    var title401 = "<?php echo $_smarty_tpl->tpl_vars['Failed_to_validate_credentials_']->value;?>
";
    var description401 = "<?php echo $_smarty_tpl->tpl_vars['The_username_and_password_are_not_correct_']->value;?>
";
    var title404 = "<?php echo $_smarty_tpl->tpl_vars['Failed_to_validate_credentials_']->value;?>
";
    var description404 = "<?php echo $_smarty_tpl->tpl_vars['Service_Temporaly_unavailable__Please__try_again_later_']->value;?>
";
    var title999 = "<?php echo $_smarty_tpl->tpl_vars['Failed_to_validate_credentials_']->value;?>
";
    var description999 = "<?php echo $_smarty_tpl->tpl_vars['Service_Temporaly_unavailable__Please__try_again_later_']->value;?>
";
    var customer_technical_error =
        'Error al enviar el formulario de alta de cliente.\r\n\
    Revise su configuración. En caso de persistir el error\r\n\
    por favor, póngase en contacto con el Soporte Técnico de Correos';

    var statusConnected = "<?php echo $_smarty_tpl->tpl_vars['Connected']->value;?>
";
    var statusNotConnected = "<?php echo $_smarty_tpl->tpl_vars['No_connected']->value;?>
";
    var soapFeatureInstallErrorMessage = "<?php echo $_smarty_tpl->tpl_vars['ERROR________To_use_webservice_credentials__you_must_have_the_SOAP_feature_installed__Please_contact_your_hosting_for_more_information']->value;?>
";
<?php echo '</script'; ?>
>
<?php }
}
