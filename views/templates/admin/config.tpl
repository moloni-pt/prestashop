<section id="moloni">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}compiled.min.css">

    {if $moloni.message_alert != null}
        <div class="moloni-message moloni-message__success">
            <strong>
                {l s='Your options were saved :)' mod='moloni'}
            </strong>
        </div>
    {/if}

    <form method='POST' id='moloniOptions' action='{$moloni.configurations.formSave|escape:'html':'UTF-8'}'>
        <div class="panel">
            <div class="panel-heading">
                {l s='Documents' mod='moloni'}
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <!-------------------------- Série de Documentos (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Document set' mod='moloni'}
                        </label>
                        <select name='options[document_set]'>
                            <option value='' disabled selected>{l s='Select your document set' mod='moloni'}</option>
                            {foreach from=$moloni.configurations.document_set.options item=opt}
                                <option value='{$opt.document_set_id|escape:'html':'UTF-8'}' {if $moloni.configurations.document_set.value == $opt.document_set_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                    </div>

                    <!-------------------------- Tipo de Documento (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Document type' mod='moloni'}
                        </label>
                        <select name='options[document_type]'>
                            <option value='' disabled selected>{l s='Document type' mod='moloni'}</option>
                            <option value='invoices' {if $moloni.configurations.document_type.value == 'invoices'} selected {/if}>{l s='Invoice' mod='moloni'}</option>
                            <option value='invoiceReceipts' {if $moloni.configurations.document_type.value == 'invoiceReceipts'} selected {/if}>{l s='Invoice/Receipt' mod='moloni'}</option>
                            <option value='purchaseOrder' {if $moloni.configurations.document_type.value == 'purchaseOrder'} selected {/if}>{l s='Order Note' mod='moloni'}</option>
                            <option value='estimates' {if $moloni.configurations.document_type.value == 'estimates'} selected {/if}>{l s='Estimate' mod='moloni'}</option>
                            <option value='billsOfLading' {if $moloni.configurations.document_type.value == 'billsOfLading'} selected {/if}>{l s='Bill of Landing' mod='moloni'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Estado do documento (rascunho/fechado) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Document status' mod='moloni'}
                        </label>
                        <select name='options[document_status]'>
                            <option value='' disabled selected>{l s='Moloni document status' mod='moloni'}</option>
                            <option value='0' {if $moloni.configurations.document_status.value == '0'} selected {/if}>{l s='Draft' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.document_status.value == '1'} selected {/if}>{l s='Closed' mod='moloni'}</option>
                        </select>
                    </div>

                    <!-------------------------- Show shipping information (yes/no) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Shipping information' mod='moloni'}
                        </label>
                        <select name='options[show_shipping_information]'>
                            {if 'show_shipping_information'|array_key_exists:$moloni.configurations}
                                {assign var="showShippingInformation" value=$moloni.configurations.show_shipping_information.value}
                            {else}
                                {assign var="showShippingInformation" value=""}
                            {/if}

                            <option value='' disabled selected>
                                {l s='Show shipping information' mod='moloni'}
                            </option>
                            <option value='1' {if $showShippingInformation == "1"} selected {/if}>
                                {l s='Yes' mod='moloni'}
                            </option>
                            <option value='0' {if $showShippingInformation == "0"} selected {/if}>
                                {l s='No' mod='moloni'}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                {l s='Orders' mod='moloni'}
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <!-------------------------- Encomendas por gerar com o estado X ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Status of pending orders' mod='moloni'}
                        </label>
                        <div style="overflow: auto; max-height: 200px;">
                            {foreach from=$moloni.configurations.order_status.options item=opt}
                                {assign var="checked" value=""}

                                {foreach from=$moloni.configurations.order_status.value key="key" item="value"}
                                    {if $value == $opt.id}
                                        {assign var="checked" value="checked"}

                                        {break}
                                    {/if}
                                {/foreach}

                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               name="options[order_status][{$opt.id|escape:'html':'UTF-8'}]"
                                               value="1"
                                                {$checked}>
                                        {$opt.name|escape:'html':'UTF-8'}
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                    </div>

                    <!-------------------------- Encomendas por gerar desde dia X ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Show orders since' mod='moloni'}
                        </label>
                        <input type="text"
                               class="datepicker"
                               name='options[after_date]'
                               id='after_date'
                               placeholder="yyyy-mm-dd"
                               value='{$moloni.configurations.after_date.value|escape:'html':'UTF-8'}'>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                {l s='Default Values' mod='moloni'}
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <!-------------------------- Razão de isenção a ser usada quando o artigo não tem IVA (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Exemption reason' mod='moloni'}
                        </label>
                        <select name='options[exemption_reason]'>
                            <option value='' selected>
                                {l s='Select an option' mod='moloni'}
                            </option>
                            {foreach from=$moloni.configurations.exemption_reason.options item=opt}
                                <option value='{$opt.code|escape:'html':'UTF-8'}' {if $moloni.configurations.exemption_reason.value == $opt.code} selected {/if}>
                                    {$opt.name|escape:'html':'UTF-8'} ({$opt.code|escape:'html':'UTF-8'})
                                </option>
                            {/foreach}
                        </select>
                    </div>

                    <!-------------------------- Razão de isenção para os portes (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Shipping exemption reason' mod='moloni'}
                        </label>
                        <select name='options[exemption_reason_shipping]'>
                            <option value='' selected>
                                {l s='Select an option' mod='moloni'}
                            </option>
                            {foreach from=$moloni.configurations.exemption_reason.options item=opt}
                                <option value='{$opt.code|escape:'html':'UTF-8'}' {if $moloni.configurations.exemption_reason_shipping.value == $opt.code} selected {/if}>
                                    {$opt.name|escape:'html':'UTF-8'} ({$opt.code|escape:'html':'UTF-8'})
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Unidade de medida a ser usada por defeito ao inserir artigos (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Measure unit' mod='moloni'}
                        </label>
                        <select name='options[measure_unit]'>
                            <option value='' disabled selected>{l s='Default measure unit' mod='moloni'}</option>
                            {foreach from=$moloni.configurations.measure_unit.options item=opt}
                                <option value='{$opt.unit_id|escape:'html':'UTF-8'}' {if $moloni.configurations.measure_unit.value == $opt.unit_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                    </div>

                    <!-------------------------- Prazo de vencimento (from Moloni) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Maturity date' mod='moloni'}
                        </label>
                        <select name='options[maturity_date]'>
                            <option value='' disabled selected>{l s='Maturity date' mod='moloni'}</option>
                            {foreach from=$moloni.configurations.maturity_date.options item=opt}
                                <option value='{$opt.maturity_date_id|escape:'html':'UTF-8'}' {if $moloni.configurations.maturity_date.value == $opt.maturity_date_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Tipo de artigo (From AT) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Product type' mod='moloni'}
                        </label>
                        <select name='options[at_category]'>
                            <option value='' disabled selected>{l s='Product type' mod='moloni'}</option>
                            <option value='SS' {if $moloni.configurations.at_category.value == "SS"} selected {/if}>
                                Serviço (S/ Stock)
                            </option>
                            <option value='M' {if $moloni.configurations.at_category.value == "M"} selected {/if}>
                                Mercadorias
                            </option>
                            <option value='P' {if $moloni.configurations.at_category.value == "P"} selected {/if}>
                                Matérias-primas, subsidiárias e de consumo
                            </option>
                            <option value='A' {if $moloni.configurations.at_category.value == "A"} selected {/if}>
                                Produtos acabados e intermédios
                            </option>
                            <option value='S' {if $moloni.configurations.at_category.value == "S"} selected {/if}>
                                Subprodutos, desperdícios e refugos
                            </option>
                            <option value='T' {if $moloni.configurations.at_category.value == "T"} selected {/if}>
                                Produtos e trabalhos em curso
                            </option>
                        </select>
                    </div>

                    <!-------------------------- Actualizar dados do cliente caso já exista (Sim/Não) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Update client' mod='moloni'}
                        </label>
                        <select name='options[update_customer]'>
                            <option value='' disabled selected>{l s='Update client info' mod='moloni'}?</option>
                            <option value='1' {if $moloni.configurations.update_customer.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>
                            <option value='0' {if $moloni.configurations.update_customer.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Zona fiscal (Empresa, Morada envio, Morada faturação) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Taxes Fiscal zone' mod='moloni'}
                        </label>

                        {assign var="fiscalZoneBasedOn" value=""}

                        {if array_key_exists('fiscal_zone_based_on', $moloni.configurations)}
                            {assign var="fiscalZoneBasedOn" value=$moloni.configurations.fiscal_zone_based_on.value}
                        {/if}

                        <select name='options[fiscal_zone_based_on]'>
                            <option value='' disabled selected>{l s='Taxes Fiscal zone' mod='moloni'}?</option>
                            <option value='billing' {if $fiscalZoneBasedOn == "billing"} selected {/if}>
                                {l s='Billing' mod='moloni'}
                            </option>
                            <option value='shipping' {if $fiscalZoneBasedOn == "shipping"} selected {/if}>
                                {l s='Shipping' mod='moloni'}
                            </option>
                            <option value='company' {if $fiscalZoneBasedOn == "company"} selected {/if}>
                                {l s='Company' mod='moloni'}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                {l s='Automation' mod='moloni'}
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <!-------------------------- Gerar documento automaticamente (Sim, quando paga/Sim, num dos estados escolhidos/Não) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Create document automatically' mod='moloni'}
                        </label>
                        <select name='options[invoice_auto]'>
                            <option value='' disabled
                                    selected>{l s='Create document automatically' mod='moloni'}</option>
                            <option value='2' {if $moloni.configurations.invoice_auto.value == "2"} selected {/if}>{l s='Yes, when in one of the selected status (on option: "Status of pending orders")' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.invoice_auto.value == "1"} selected {/if}>{l s='Yes, when paid' mod='moloni'}</option>
                            <option value='0' {if $moloni.configurations.invoice_auto.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>
                        </select>
                    </div>

                    <!-------------------------- Enviar email ao cliente quando é gerado o documento fechado (Sim/Não) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Send email' mod='moloni'}
                        </label>
                        <select name='options[email_send]'>
                            <option value='' disabled
                                    selected>{l s='Send by email (document must be closed)' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.email_send.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>
                            <option value='0' {if $moloni.configurations.email_send.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Adicionar produtos novos automaticamente ao Moloni (Sim/Não) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Add new products' mod='moloni'}
                        </label>
                        <select name='options[auto_add_product]'>
                            <option value='' disabled selected>{l s='Add new products to Moloni' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.auto_add_product.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>
                            <option value='0' {if $moloni.configurations.auto_add_product.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                {l s='Advanced' mod='moloni'}
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <!-------------------------- Envio de erros via e-mail ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Alert e-mail' mod='moloni'}
                        </label>

                        {assign var="alertEmail" value=""}

                        {if array_key_exists('alert_email', $moloni.configurations)}
                            {assign var="alertEmail" value=$moloni.configurations.alert_email.value}
                        {/if}

                        <input type="text"
                               name='options[alert_email]'
                               id='alert_email'
                               placeholder="example@email.com ({l s='Receive alerts when an error occurs in the module' mod='moloni'})"
                               value='{$alertEmail}'>
                    </div>
                </div>

                <div class="form-group row">
                    <!-------------------------- Criar webservice de sincronização de artigos (Sim/Não) ------------------------------>
                    <div class="col-sm-6">
                        <label>
                            {l s='Create webservice to allow product syncronization with cron' mod='moloni'}
                        </label>

                        {assign var="enableProductSyncWebservice" value=""}

                        {if array_key_exists('enable_product_sync_webservice', $moloni.configurations)}
                            {assign var="enableProductSyncWebservice" value=$moloni.configurations.enable_product_sync_webservice.value}
                        {/if}

                        <select name='options[enable_product_sync_webservice]'>
                            <option value='' selected>
                                {l s='Create webservice to allow product syncronization' mod='moloni'}
                            </option>
                            <option value='0' {if $enableProductSyncWebservice == "0"} selected {/if}>
                                {l s='No' mod='moloni'}
                            </option>
                            <option value='1' {if $enableProductSyncWebservice == "1"} selected {/if}>
                                {l s='Yes' mod='moloni'}
                            </option>
                        </select>
                    </div>

                    <!-------------------------- Mostrar URL do webservice de sincronização de artigos  ------------------------------>
                    {if $enableProductSyncWebservice == "1"}
                        <div class="col-sm-6">
                            <label>
                                {l s='Webservice url' mod='moloni'}
                            </label>
                            <br />

                            <button type="button"
                                    class="btn btn-primary"
                                    data-toggle="modal"
                                    data-target="#create_cron_link_modal">
                                {l s='Get URL' mod='moloni'}
                            </button>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </form>

    <div class="pull-right">
        <a type="button" class="btn btn-danger btn-lg" href="{$moloni.configurations.logout|escape:'html':'UTF-8'}">
            {l s='Logout from account' mod='moloni'}
        </a>
    </div>

    {* Overlays *}
    {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/settings/createCronLinkOverlay.tpl"}
</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}compiled.min.js?v={$moloni.version|escape:'html':'UTF-8'}"></script>
<script>
    var translations = {
        save_changes: "{l s='Save Changes' mod='moloni'}"
    }

    var currentAction = "{Context::getContext()->link->getAdminLink('MoloniConfiguracao', true)}";

    $(document).ready(function() {
        pt.moloni.Settings.init(translations, currentAction);
    });
</script>
