{if $sga_module}
    <div id="correos_oficial_main_container" class="correos-oficial col-lg-12">
        <div class="card card-custom detail-order-container">
            <div class="card-header card-header-oder">
                <img src="{$co_base_dir}views/commons/img/logos/logo-order.png" alt="Correos" class="order-logo">
                <h2 class="order-title">
                    {l s='Order tracking' mod='correosoficial'}
                </h2>
            </div>

            <div class="container-details sga-container">
                <div class="card-header card-header-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-info-square custom-icon" viewBox="0 0 16 16">
                        <path
                            d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z">
                        </path>
                        <path
                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z">
                        </path>
                    </svg>
                    <span>{l s='Shipping status' mod='correosoficial'}</span>
                </div>

                <table id="historic-table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>{l s='Shipping code' mod='correosoficial'}</th>
                            <th>{l s='Carrier' mod='correosoficial'}</th>
                            <th>{l s='Status' mod='correosoficial'}</th>
                            <th>{l s='Date' mod='correosoficial'}</th>
                            <th>{l s='Hour' mod='correosoficial'}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
{/if}
{if !$sga_module}
    <div class="row">
        <div class="col-sm-12">
            <div class="card history-container {if !$order_done}hidden-block{/if}">
                <div class="card-header card-header-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-info-square custom-icon" viewBox="0 0 16 16">
                        <path
                            d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z">
                        </path>
                        <path
                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z">
                        </path>
                    </svg>
                    <span>{l s='Shipping status' mod='correosoficial'}</span>
                </div>
                <div class="card-body">
                    <table id="historic-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>{l s='Shipping code' mod='correosoficial'}</th>
                                <th>{l s='Carrier' mod='correosoficial'}</th>
                                <th>{l s='Status' mod='correosoficial'}</th>
                                <th>{l s='Date' mod='correosoficial'}</th>
                                <th>{l s='Hour' mod='correosoficial'}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/if}
<div>
    <input type="hidden" id="sga_id_order_hidden" name="sga_id_order_hidden" value="{$sga_id_order}" />
</div>