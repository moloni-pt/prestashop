{**
* 2016 - Moloni.com
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
* DISCLAIMER 
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Nuno Almeida
*  @copyright Nuno Almeida
*  @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
*}

<section id="moloni">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style.css">
    <link rel="stylesheet" href='{$moloni.path.css|escape:'html':'UTF-8'}materialize.css'>
    <script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}materialize/js/materialize.min.js"></script>


    <div class="row" id='configs'>

        <div class="col s12 " style='margin-top: 5px;'>

            {if $moloni.message_alert != null}
                <div class="col s12 z-depth-1 message_success green">
                    {l s='Your options were saved :)' mod='moloni'} 
                </div>
            {/if}
            {if $moloni.syncResult}
                <ul class="col s12 z-depth-1 collapsible">

                    {if isset($moloni.syncResult.header) && is_array($moloni.syncResult.header)} 
                        <li>
                            <div class="collapsible-header"><b>{l s='Artigos atualizados desde: ' mod='moloni'}{$moloni.syncResult.header.updated_since} ({l s='Encontrados' mod='moloni'}: {$moloni.syncResult.header.products_total})</b></div>                            
                        </li>
                    {/if}
                    {if isset($moloni.syncResult.with_attributes) && is_array($moloni.syncResult.with_attributes)} 
                        <li>
                            <div class="collapsible-header">{l s='Artigos com atributos atualizados' mod='moloni'} <i class="material-icons">arrow_drop_down</i></div>
                            <div class="collapsible-body">
                                <span>
                                    <table class="striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Referência' mod='moloni'} </th>
                                                <th>{l s='Stock anterior' mod='moloni'} </th>
                                                <th>{l s='Stock atualizado' mod='moloni'} </th>
                                                <th>{l s='Stock do artigo pai' mod='moloni'} </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {foreach from=$moloni.syncResult.with_attributes item=prod}
                                                <tr>
                                                    <td>{$prod.reference}</td>
                                                    <td>{$prod.stock_before}</td>
                                                    <td>{$prod.stock_after}</td>
                                                    <td>{$prod.stocl_total}</td>
                                                </tr>            
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </span>
                            </div>
                        </li>
                    {/if}

                    {if isset($moloni.syncResult.update_error)} 
                        <li>
                            <div class="collapsible-header">{l s='Artigos com atributos - erro ao atualizar' mod='moloni'} <i class="material-icons">arrow_drop_down</i></div>
                            <div class="collapsible-body">
                                <span>
                                    <table class="striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Referência' mod='moloni'} </th>
                                                <th>{l s='Stock anterior' mod='moloni'} </th>
                                                <th>{l s='Stock atualizado' mod='moloni'} </th>
                                                <th>{l s='Stock do artigo pai' mod='moloni'} </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {foreach from=$moloni.syncResult.update_error item=prod}
                                                <tr>
                                                    <td>{$prod.reference}</td>
                                                    <td>{$prod.stock_before}</td>
                                                    <td>{$prod.stock_after}</td>
                                                    <td>{$prod.stocl_total}</td>
                                                </tr>            
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </span>
                            </div>
                        </li>
                    {/if}

                    {if isset($moloni.syncResult.simple)} 
                        <li>
                            <div class="collapsible-header">{l s='Artigos simples atualizados' mod='moloni'} <i class="material-icons">arrow_drop_down</i></div>
                            <div class="collapsible-body">
                                <span>
                                    <table class="striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Referência' mod='moloni'} </th>
                                                <th>{l s='Stock anterior' mod='moloni'} </th>
                                                <th>{l s='Stock atualizado' mod='moloni'} </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {foreach from=$moloni.syncResult.simple item=prod}
                                                <tr>
                                                    <td>{$prod.reference}</td>
                                                    <td>{$prod.stock_before}</td>
                                                    <td>{$prod.stock_after}</td>
                                                </tr>            
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </span>
                            </div>
                        </li>
                    {/if}

                    {if isset($moloni.syncResult.insert_success)} 
                        <li>
                            <div class="collapsible-header">{l s='Artigos inseridos' mod='moloni'} <i class="material-icons">arrow_drop_down</i></div>
                            <div class="collapsible-body">
                                <span>
                                    <table class="striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Referência' mod='moloni'} </th>
                                                <th>{l s='Nome' mod='moloni'} </th>
                                                <th>{l s='Preço' mod='moloni'} </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {foreach from=$moloni.syncResult.insert_success item=prod}
                                                <tr>
                                                    <td>{$prod.reference}</td>
                                                    <td>{$prod.name}</td>
                                                    <td>{$prod.price}</td>
                                                </tr>            
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </span>
                            </div>
                        </li>
                    {/if}

                    {if isset($moloni.syncResult.insert_error)} 
                        <li>
                            <div class="collapsible-header">{l s='Artigos não inseridos' mod='moloni'} <i class="material-icons">arrow_drop_down</i></div>
                            <div class="collapsible-body">
                                <span>
                                    <table class="striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Referência' mod='moloni'} </th>
                                                <th>{l s='Nome' mod='moloni'} </th>
                                                <th>{l s='Preço' mod='moloni'} </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {foreach from=$moloni.syncResult.insert_error item=prod}
                                                <tr>
                                                    <td>{$prod.reference}</td>
                                                    <td>{$prod.name}</td>
                                                    <td>{$prod.price}</td>
                                                </tr>            
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </span>
                            </div>
                        </li>
                    {/if}
                </ul>
            {/if}

            <form method='POST' id='moloniOptions' action='{$moloni.configurations.formSave|escape:'html':'UTF-8'}'>

                <div class="col s12 z-depth-1">
                    <h3>{l s='Documents' mod='moloni'}</h3>

                    <!-------------------------- Série de Documentos (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[document_set]'>
                            <option value='' disabled selected>{l s='Select your document set' mod='moloni'}</option>
                            {foreach from=$moloni.configurations.document_set.options item=opt}
                                <option value='{$opt.document_set_id|escape:'html':'UTF-8'}' {if $moloni.configurations.document_set.value == $opt.document_set_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                        <label>{l s='Document set' mod='moloni'}</label>
                    </div>


                    <!-------------------------- Tipo de Documento (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[document_type]'>
                            <option value='' disabled selected>{l s='Document type' mod='moloni'}</option>  
                            <option value='invoices' 		{if $moloni.configurations.document_type.value == 'invoices'} selected {/if}>{l s='Invoice' mod='moloni'}</option>
                            <option value='invoiceReceipts'	{if $moloni.configurations.document_type.value == 'invoiceReceipts'} selected {/if}>{l s='Invoice/Receipt' mod='moloni'}</option>
                            <option value='purchaseOrder'	{if $moloni.configurations.document_type.value == 'purchaseOrder'} selected {/if}>{l s='Order Note' mod='moloni'}</option>
                            <option value='estimates'		{if $moloni.configurations.document_type.value == 'estimates'} selected {/if}>{l s='Estimate' mod='moloni'}</option>
                            <option value='billsOfLading'	{if $moloni.configurations.document_type.value == 'billsOfLading'} selected {/if}>{l s='Bill of Landing' mod='moloni'}</option>
                        </select>
                        <label>{l s='Document type' mod='moloni'}</label>
                    </div>


                    <!-------------------------- Estado do documento (rascunho/fechado) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[document_status]'>
                            <option value='' disabled selected>{l s='Moloni document status' mod='moloni'}</option>
                            <option value='0'	{if $moloni.configurations.document_status.value == '0'} selected {/if}>{l s='Draft' mod='moloni'}</option>	  
                            <option value='1'	{if $moloni.configurations.document_status.value == '1'} selected {/if}>{l s='Closed' mod='moloni'}</option>	  
                        </select>
                        <label>{l s='Document status' mod='moloni'}</label>
                    </div>



                </div>




                <div class="col s12 z-depth-1">
                    <h3>{l s='Orders' mod='moloni'}</h3>

                    <!-------------------------- Encomendas por gerar com o estado X ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[order_status][]' multiple >
                            <option value='' disabled selected>{l s='Status of pending orders' mod='moloni'}</option>
                            {foreach from=$moloni.configurations.order_status.options item=opt}
                                <option value='{$opt.id|escape:'html':'UTF-8'}'{foreach from=$moloni.configurations.order_status.value item=values} {if $values == $opt.id} selected {/if} {/foreach}>{$opt.name|escape:'html':'UTF-8'}</option>	
                            {/foreach}
                        </select>
                        <label>{l s='Status of pending orders' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Encomendas por gerar desde dia X ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <label for='after_date' class='label-input' style='cursor: pointer; left: 20px; top: 15px'>{l s='Show orders since' mod='moloni'}</label>
                        <input type="date" class="datepicker" name='options[after_date]' id='after_date' value='{$moloni.configurations.after_date.value|escape:'html':'UTF-8'}'>
                    </div>

                </div>




                <div class="col s12 z-depth-1">
                    <h3>{l s='Default Values' mod='moloni'}</h3>

                    <!-------------------------- Razão de isenção a ser usada quando o artigo não tem IVA (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[exemption_reason]'>  
                            <option value='' disabled selected>{l s='Used in case the order has no taxes' mod='moloni'}</option>	
                            {foreach from=$moloni.configurations.exemption_reason.options item=opt}
                                <option value='{$opt.code|escape:'html':'UTF-8'}' {if $moloni.configurations.exemption_reason.value == $opt.code} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                        <label>{l s='Exemption reason' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Razão de isenção para os portes (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[exemption_reason_shipping]'>
                            <option value='' disabled selected>{l s='Used in case shipping has no taxes' mod='moloni'}</option>	 
                            {foreach from=$moloni.configurations.exemption_reason.options item=opt}
                                <option value='{$opt.code|escape:'html':'UTF-8'}' {if $moloni.configurations.exemption_reason_shipping.value == $opt.code} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                        <label>{l s='Shipping exemption reason' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Unidade de medida a ser usada por defeito ao inserir artigos (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[measure_unit]'>
                            <option value='' disabled selected>{l s='Default measure unit' mod='moloni'}</option>	  
                            {foreach from=$moloni.configurations.measure_unit.options item=opt}
                                <option value='{$opt.unit_id|escape:'html':'UTF-8'}' {if $moloni.configurations.measure_unit.value == $opt.unit_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                        <label>{l s='measure unit' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Prazo de vencimento (from Moloni) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[maturity_date]'>
                            <option value='' disabled selected>{l s='Maturity date' mod='moloni'}</option>  
                            {foreach from=$moloni.configurations.maturity_date.options item=opt}
                                <option value='{$opt.maturity_date_id|escape:'html':'UTF-8'}' {if $moloni.configurations.maturity_date.value == $opt.maturity_date_id} selected {/if}> {$opt.name|escape:'html':'UTF-8'} </option>
                            {/foreach}
                        </select>
                        <label>{l s='Maturity date' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Tipo de artigo (From AT) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[at_category]'>
                            <option value='' disabled selected>{l s='Product type' mod='moloni'}</option>
                            <option value='SS' {if $moloni.configurations.at_category.value == "SS"} selected {/if}>Serviço (S/ Stock)</option>	  
                            <option value='M' {if $moloni.configurations.at_category.value == "M"} selected {/if}>Mercadorias</option>	  
                            <option value='P' {if $moloni.configurations.at_category.value == "P"} selected {/if}>Matérias-primas, subsidiárias e de consumo</option>	  
                            <option value='A' {if $moloni.configurations.at_category.value == "A"} selected {/if}>Produtos acabados e intermédios</option>	  
                            <option value='S' {if $moloni.configurations.at_category.value == "S"} selected {/if}>Subprodutos, desperdícios e refugos</option>	  
                            <option value='T' {if $moloni.configurations.at_category.value == "T"} selected {/if}>Produtos e trabalhos em curso</option>	  
                        </select>
                        <label>{l s='Product type' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Actualizar dados do cliente caso já exista (Sim/Não) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[update_customer]'>
                            <option value='' disabled selected>{l s='Update client info' mod='moloni'}?</option>
                            <option value='1' {if $moloni.configurations.update_customer.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>	  
                            <option value='0' {if $moloni.configurations.update_customer.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>	  
                        </select>
                        <label>{l s='Update client' mod='moloni'}</label>
                    </div>

                </div>


                <div class="col s12 z-depth-1">
                    <h3>{l s='Automation' mod='moloni'}</h3>

                    <!-------------------------- Gerar documento automaticamente quando é pago (Sim/Não) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[invoice_auto]'>
                            <option value='' disabled selected>{l s='Create document when paid' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.invoice_auto.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>	  
                            <option value='0' {if $moloni.configurations.invoice_auto.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>	  
                        </select>
                        <label>{l s='Create document when paid' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Enviar email ao cliente quando é gerado o documento fechado (Sim/Não) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[email_send]'>
                            <option value='' disabled selected>{l s='Send by email (document must be closed)' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.email_send.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>	  
                            <option value='0' {if $moloni.configurations.email_send.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>	  
                        </select>
                        <label>{l s='Send email' mod='moloni'}</label>
                    </div>

                    <!-------------------------- Adicionar produtos novos automaticamente ao Moloni (Sim/Não) ------------------------------>
                    <div class='input-field col s6' style='margin-top: 50px'>
                        <select name='options[auto_add_product]'>
                            <option value='' disabled selected>{l s='Add new products to Moloni' mod='moloni'}</option>
                            <option value='1' {if $moloni.configurations.auto_add_product.value == "1"} selected {/if}>{l s='Yes' mod='moloni'}</option>	  
                            <option value='0' {if $moloni.configurations.auto_add_product.value == "0"} selected {/if}>{l s='No' mod='moloni'}</option>	  
                        </select>
                        <label>{l s='Add new products' mod='moloni'}</label>
                    </div>

                    <div class='input-field col s6' style='margin-top: 50px; margin-bottom: 50px;'>
                        <a href='{$moloni.configurations.formSave|escape:'html':'UTF-8'}&action=forcestocks&updateSince={$moloni.configurations.updateStocksSince}'>Sincronizar Stocks</a> - Actualizar os stocks do Prestashop com os stocks do Moloni, dos artigos atualizados nos últimos 7 dias
                        <br>
                        <a href='{$moloni.configurations.formSave|escape:'html':'UTF-8'}&action=importProducts&updateSince={$moloni.configurations.updateStocksSince}'>Importar Artigos e categorias</a> - Importar artigos e categorias atualizados nos últimos 7 dias do Moloni para o Prestashop
                    </div>

                </div>

            </form>
            <div class="LogoutButton"><a class="waves-effect waves-light red btn-large right" 
                                         style='color: white' id="formSubmit"
                                         href="{$moloni.configurations.logout|escape:'html':'UTF-8'}"
                                         >{l s='Logout from account' mod='moloni'}</a></div>
        </div>

    </div>






</section>

<script>

    $(document).ready(function() {
        $('select').material_select();

        $('.datepicker').pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            formatSubmit: "yyyy-mm-dd",
            format: 'yyyy-mm-dd'

    });

        $('#toolbar-nav').html('<li><div class="formSave"><a class="waves-effect waves-light red btn-large" id="formSubmit">{l s='Save Changes' mod='moloni'}</a></div></li>');

        $('#formSubmit').click(function () {
            $("#moloniOptions").submit();
    });

        $('.message_success').click(function () {
            $(this).hide("slow");
    });

        $(document).ready(function(){
            $('.collapsible').collapsible();
    });


    });

</script>
