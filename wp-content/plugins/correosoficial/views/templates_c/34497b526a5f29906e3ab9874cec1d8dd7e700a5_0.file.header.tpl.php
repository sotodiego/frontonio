<?php
/* Smarty version 4.0.0, created on 2025-05-08 11:38:36
  from 'C:\xampp3\htdocs\FRONTONIO\wp-content\plugins\correosoficial\views\templates\admin\header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.0.0',
  'unifunc' => 'content_681c97bce59fa2_22432851',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '34497b526a5f29906e3ab9874cec1d8dd7e700a5' => 
    array (
      0 => 'C:\\xampp3\\htdocs\\FRONTONIO\\wp-content\\plugins\\correosoficial\\views\\templates\\admin\\header.tpl',
      1 => 1746703988,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_681c97bce59fa2_22432851 (Smarty_Internal_Template $_smarty_tpl) {
?><link href="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/commons/css/all.css" rel="stylesheet">

<!-- Modal HTML -->
<div id="myModal" class="modal fadee" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="myModalTitle" class="modal-title"><?php echo $_smarty_tpl->tpl_vars['Confirmation_']->value;?>
</h5>
                            </div>
            <div id="myModalDescription" class="modal-body">
                <p>...</p>
            </div>
            <div class="modal-footer">
                <button id="myModalCancelButton" type="button" class="btn btn-danger" data-bs-dismiss="modal"><?php echo $_smarty_tpl->tpl_vars['Cancel']->value;?>
</button>
                <button id="myModalActionButtonCustomerData" type="button" class="myModalActionButton btn btn-primary"><?php echo $_smarty_tpl->tpl_vars['Action']->value;?>
</button>
                <button id="myModalActionButtonSenders" type="button" class="myModalActionButton btn btn-primary"><?php echo $_smarty_tpl->tpl_vars['Action']->value;?>
</button>
                <button id="myModalAcceptButton" type="button" class="myModalActionButton btn btn-primary"><?php echo $_smarty_tpl->tpl_vars['Action']->value;?>
</button>
            </div>
        </div>
    </div>
</div>

<div id="co_header" class="container-fluid">
    
    <div class="col-md-12 header-logo clearfix">
        <img src="<?php echo $_smarty_tpl->tpl_vars['co_base_dir']->value;?>
views/commons/img/logos/logo-header.png" alt="Correos">
    </div>
    <div class="module_version">
        <span>version <?php echo CORREOS_OFICIAL_VERSION;?>
</span>
    </div>
</div>

<?php echo '<script'; ?>
>
    var addButton    = "<?php echo $_smarty_tpl->tpl_vars['Add']->value;?>
";
    var editButton   = "<?php echo $_smarty_tpl->tpl_vars['Edit']->value;?>
"; 
    var deleteButton = "<?php echo $_smarty_tpl->tpl_vars['Delete']->value;?>
";
    var acceptButton = "<?php echo $_smarty_tpl->tpl_vars['Accept']->value;?>
";
    var informationTitle = "<?php echo $_smarty_tpl->tpl_vars['Information']->value;?>
";
    var errorTitle = "<?php echo $_smarty_tpl->tpl_vars['An_error_has_occurred']->value;?>
";
<?php echo '</script'; ?>
><?php }
}
