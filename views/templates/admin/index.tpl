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
    <link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}materialize.css">
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}/jquery.dataTables.css">

    <div class="row" style='margin-left: -5px; margin-right: -5px;'>

        <div class="col s12" style='margin-top: 25px;'>

            {if isset($moloni.message.error.message)}
                <div class="col s12 z-depth-1 message_error red">
                    {l s='Ups, there was an error :(' mod='moloni'}
                    <a class="waves-effect waves-light btn white check_error">{l s='Error' mod='moloni'}</a>
                </div>

                <div class="messagepop pop">
                    {l s='Error' mod='moloni'}: {$moloni.message.error.where|escape:'html':'UTF-8'}<br> 
                    {$moloni.message.error.message|escape:'html':'UTF-8'}<br>
                    {l s='Sent' mod='moloni'}:
                    <pre>{$moloni.message.error.values_sent|@print_r|escape:'html':'UTF-8'}}</pre>
                    {l s='Received' mod='moloni'}:
                    <pre>{$moloni.message.error.values_receive|@print_r|escape:'html':'UTF-8'}}</pre><br>
                    <a class="close" href="/">{l s='Close' mod='moloni'}</a>
                </div>
            {/if}

            {if isset($moloni.message.success.success)}
                <div class="col s12 z-depth-1 message_error green">
                    {$moloni.message.success.message|escape:'html':'UTF-8'}
                    {if !empty($moloni.message.success.button)}
                        <a class="waves-effect waves-light btn white" target='{if !empty($moloni.message.success.tab)}{$moloni.message.success.tab|escape:"html":"UTF-8"}{/if}' href='{$moloni.message.success.url|escape:"html":"UTF-8"}' style='color: #363a46;float: right;margin-top: -2px;'>{$moloni.message.success.button|escape:'html':'UTF-8'}</a>
                    {/if}
                </div>
            {/if}

            <table class='dataTable highlight panel'>
                <thead>
                    <tr>
                        <th data-field="id" style='width: 70px'>{l s='Number' mod='moloni'}</th>
                        <th data-field="name">{l s='Client' mod='moloni'}</th>
                        <th data-field="email" style='width: 200px'>{l s='Email' mod='moloni'}</th>
                        <th data-field="date" style='width: 200px'>{l s='Order date' mod='moloni'}</th>
                        <th data-field="status" style='width: 120px'>{l s='Status' mod='moloni'}</th>
                        <th data-field="total" style='width: 120px'>{l s='Total' mod='moloni'}</th>
                        <th data-field="acts" style='width: 150px'><center>{l s='Actions' mod='moloni'}</center></th>
                </tr>
                </thead>

                <tbody>

                    {foreach from=$moloni.orders item=order}

                        <tr>
                            <td><a class='waves-effect waves-light btn blue order' href='{$order.url.order|escape:"html":"UTF-8"}'>#{$order.info.id_order|escape:'html':'UTF-8'}</a></td>
                            <td>
                                <b>{$order.address.firstname|escape:'html':'UTF-8'} {$order.address.lastname|escape:'html':'UTF-8'} {if $order.address.company != ""} - {$order.address.company|escape:'html':'UTF-8'} {/if}</b><br>
                                <span style='font-size: 10px'>
                                    {if $order.address.address1 != ""} {$order.address.address1|escape:'html':'UTF-8'} <br> {/if}
                                    {if $order.address.vat_number != ""} {$order.address.vat_number|escape:'html':'UTF-8'} <br> {/if}
                                </span>

                            </td>
                            <td>{$order.customer.email|escape:'html':'UTF-8'}</td>
                            <td>{$order.info.date_add|escape:'html':'UTF-8'}</td>
                            <td>{$order.state.name|escape:'html':'UTF-8'}</td>
                            <td>{$order.info.total_paid|string_format:"%.2f"|escape:'html':'UTF-8'}€</td>
                            <td><center>
                        <a class='waves-effect waves-light btn green generate' href='{$order.url.create|escape:"html":"UTF-8"}'><i class='material-icons'>note_add</i></a>
                        <a class='waves-effect waves-light btn red discard' href='{$order.url.clean|escape:"html":"UTF-8"}'><i class='material-icons'>delete</i></a>
                    </center>	
                    </td></tr>
                {/foreach}							

                </tbody>
            </table>
        </div>

    </div>


</section>

<script type="text/javascript" src="https://cdn.datatables.net/t/dt/dt-1.10.11/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $('.dataTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "sPaginationType": "full_numbers",
            "sDom": '<"DTtop panel"<"MolShowing"l><"MolSearch"f>>rt<"DTbottom panel"<"MolInfo"i><"MolPagination"p>><"clear">',
            "oLanguage": {
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "{l s='No results found' mod='moloni'}",
                "sInfo": "{l s='Showing' mod='moloni'} <b>_START_</b> - <b>_END_</b> {l s='of' mod='moloni'} <b>_TOTAL_</b> {l s='orders' mod='moloni'}",
                "sInfoEmpty": "{l s='Nothing to show' mod='moloni'}",
                "sInfoFiltered": "({l s='Filtered from' mod='moloni'} _MAX_)",
                "sSearch": "",
                "sSearchPlaceholder": "{l s='Search' mod='moloni'}...",
                "oPaginate": {
                    "sFirst": "{l s='Start' mod='moloni'}",
                    "sPrevious": "{l s='Back' mod='moloni'}",
                    "sNext": "{l s='Next' mod='moloni'}",
                    "sLast": "{l s='Last' mod='moloni'}"
                }
            }
        })

        function deselect(e) {
            $('.pop').slideFadeToggle(function () {
                e.removeClass('selected');
            });
        }

        $(function () {
            $('.check_error').on('click', function () {
                if ($(this).hasClass('selected')) {
                    deselect($(this));
                } else {
                    $(this).addClass('selected');
                    $('.pop').slideFadeToggle();
                }
                return false;
            });

            $('.close').on('click', function () {
                deselect($('.check_error'));
                return false;
            });
        });

        $.fn.slideFadeToggle = function (easing, callback) {
            return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
        };

    });
</script>