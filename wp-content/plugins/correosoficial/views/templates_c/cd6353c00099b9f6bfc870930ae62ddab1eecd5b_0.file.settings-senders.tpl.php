<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:39:03
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\settings-senders.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97d7ee72b1_54874457',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cd6353c00099b9f6bfc870930ae62ddab1eecd5b' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\settings-senders.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97d7ee72b1_54874457 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="accordion-body">
    <fieldset>
        <form id="CorreosSendersForm" name="CorreosSendersForm" class="needs-validation" novalidate>
        <div class="col-sm-6">
            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Sender_name']->value;?>
</span>
                </div>
                <input type="text" id="sender_name" name="sender_name" class="form-control" placeholder="" required>
                <input type="hidden" id="sender_id" name="sender_id" class="form-control" placeholder="sender_id">
            </div>
            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Contact_person']->value;?>
</span>
                </div>
                <input type="text" id="sender_contact" name="sender_contact" class="form-control" placeholder="" required>
            </div>

            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Address']->value;?>
</span>
                </div>
                <input type="text" id="sender_address" name="sender_address" class="form-control" placeholder="" required>
            </div>
            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['City']->value;?>
</span>
                </div>
                <input type="text" id="sender_city" name="sender_city" class="form-control" placeholder="" required>
            </div>
            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Zip_code']->value;?>
</span>
                </div>
                <input type="text" id="sender_cp" name="sender_cp" class="form-control" placeholder="" required>
            </div>
        </div>
        <div class="col-sm-6">
        
            <div class="col-sm-4 p-0">
                <div class="input-group mb-4">
                    <div class="input-group-addon input-group-text-custom">
                        <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Country']->value;?>
</span>
                    </div>
                    
                    <select class="co_dropdown" id="sender_iso_code_pais" name="sender_iso_code_pais" required>
                        <option selected disabled value=""></option>
                        <option value="ES"><?php echo $_smarty_tpl->tpl_vars['Spain']->value;?>
</option>
                        <option value="PT"><?php echo $_smarty_tpl->tpl_vars['Portugal']->value;?>
</option>
                        <option value="AD"><?php echo $_smarty_tpl->tpl_vars['Andorra']->value;?>
</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 p-0">
                <div class="input-group mb-4">
                    <div class="input-group-addon input-group-text-custom">
                        <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Phone_number']->value;?>
</span>
                    </div>
                    <input type="tel" id="sender_phone" name="sender_phone" class="form-control" placeholder="">
                </div>
            </div>

            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Email']->value;?>
</span>
                </div>
                <input type="email" id="sender_email" name="sender_email" class="form-control" placeholder="">
            </div>
            <div class="input-group mb-4">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['NIF_CIF']->value;?>
</span>
                </div>
                <input type="text" id="sender_nif_cif" name="sender_nif_cif" class="form-control" placeholder="" required>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group mb-4" id="">
                        <div class="input-group-addon input-group-text-custom">
                            <span class="input-group-text input-group-text-color">
                                <?php echo $_smarty_tpl->tpl_vars['From_hour']->value;?>

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Escoja el inicio de la franja horaria para las recogidas">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                </svg>
                            </span>
                        </div>
                        <input type="time" id="sender_from_time" name="sender_from_time" class="form-control">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group mb-4" id="">
                        <div class="input-group-addon input-group-text-custom">
                            <span class="input-group-text input-group-text-color">
                                <?php echo $_smarty_tpl->tpl_vars['To_hour']->value;?>

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9b9fa3" class="bi bi-info-circle-fill tt_settings" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="Escoja el final de la franja horaria para las recogidas">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                </svg>
                            </span>
                        </div>
                        <input type="time" id="sender_to_time" name="sender_to_time" class="form-control">
                    </div>
                </div>
            </div>
            <div class="input-group mb-4 correosCountSelect">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['Correos_Account']->value;?>
</span>
                </div>                    
                <select class="co_dropdown" id="correos_code" name="correos_code">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['optionsCorreos']->value, 'optionCorreos');
$_smarty_tpl->tpl_vars['optionCorreos']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['optionCorreos']->value) {
$_smarty_tpl->tpl_vars['optionCorreos']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['optionCorreos']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['optionCorreos']->value['CorreosContract'];?>
/<?php echo $_smarty_tpl->tpl_vars['optionCorreos']->value['CorreosCustomer'];?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <option value=""><?php echo $_smarty_tpl->tpl_vars['At_the_moment_I_am_not_going_to_use_an_Correos_account_']->value;?>
</option>
                </select>
            </div>           
            <div class="input-group mb-4 correosCountSelect">
                <div class="input-group-addon input-group-text-custom">
                    <span class="input-group-text input-group-text-color"><?php echo $_smarty_tpl->tpl_vars['CEX_Account']->value;?>
</span>
                </div>                    
                <select class="co_dropdown" id="cex_code" name="cex_code">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['optionsCex']->value, 'optionCex');
$_smarty_tpl->tpl_vars['optionCex']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['optionCex']->value) {
$_smarty_tpl->tpl_vars['optionCex']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['optionCex']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['optionCex']->value['CEXCustomer'];?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <option value=""><?php echo $_smarty_tpl->tpl_vars['At_the_moment_I_am_not_going_to_use_an_CEX_account_']->value;?>
</option>
                </select>
            </div>
        </div>

        <div class="col-sm-12 card-margin">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <input id="SendersSaveButton" class="btn btn-primary me-md-2" type="submit" value="<?php echo $_smarty_tpl->tpl_vars['ADD']->value;?>
">
                <input id="SendersEditButton" class="btn btn-primary me-md-2" type="button" value="<?php echo $_smarty_tpl->tpl_vars['SAVE']->value;?>
" disabled>
                <input id="SendersCleanButton" class="btn btn-danger me-md-1" type="reset" value="<?php echo $_smarty_tpl->tpl_vars['CANCEL']->value;?>
">
            </div>
        </div>

        <div class="card card-custom card-margin">
            <div class="card-header">
                <?php echo $_smarty_tpl->tpl_vars['_SENDER_LIST']->value;?>

            </div>
            <div class="card-body card-body-custom">
                <table id="SendersDataTable" class="table table-striped" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Name']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Correos_Account']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['CEX_Account']->value;?>
</th> 
                            <th><?php echo $_smarty_tpl->tpl_vars['Address']->value;?>
</th>                            
                            <th><?php echo $_smarty_tpl->tpl_vars['ZP']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['NIF']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['City']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Contact']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Phone']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['From']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['To']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Country']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Email']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Default_sender']->value;?>
</th>
                            <th><?php echo $_smarty_tpl->tpl_vars['Edit']->value;?>
</th> 
                            <th><?php echo $_smarty_tpl->tpl_vars['Delete']->value;?>
</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
        </form>        
    </fieldset>
</div>
<?php echo '<script'; ?>
>
    var senderDefaultSaved = "<?php echo $_smarty_tpl->tpl_vars['Sender_successfully_saved']->value;?>
";
    var wrongDniCif = "<?php echo $_smarty_tpl->tpl_vars['Incorrect_DNI_CIF_number__please_correct_it_before_continuing']->value;?>
";
    var invalidEmail = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_a_valid_email_address']->value;?>
";
    var requiredCustomMessage = "<?php echo $_smarty_tpl->tpl_vars['Required_field']->value;?>
";
    var minLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_at_least']->value;?>
";
    var maxLengthMessage = "<?php echo $_smarty_tpl->tpl_vars['Please_enter_no_more_than']->value;?>
";
    var characters = "<?php echo $_smarty_tpl->tpl_vars['characters']->value;?>
";
    var selectAContract = "<?php echo $_smarty_tpl->tpl_vars['You_must_select_at_least_one_Correos_Account_or_Cex_Account']->value;?>
";
    var sga_module = "<?php echo $_smarty_tpl->tpl_vars['sga_module']->value;?>
";
<?php echo '</script'; ?>
>
<?php }
}
