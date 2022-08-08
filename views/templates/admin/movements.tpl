{**
* 2016 - moloni.pt
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
    <link rel="stylesheet" href='{$moloni.path.css|escape:'html':'UTF-8'}materialize/materialize.css'>
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}/dataTables/jquery.dataTables.min.css">

    <div class="row" style='margin-left: -5px;   margin-right: -5px;'>

        <div class="col s12" style='margin-top: 25px;'>

            <table class='dataTable highlight panel'>
                <thead>
                    <tr>
                        <th data-field="id" style='width: 70px'>{l s='Document' mod='moloni'}</th>
                        <th data-field="name">{l s='Client' mod='moloni'}</th>
                        <th data-field="email" style='width: 200px'>{l s='Date' mod='moloni'}</th>
                        <th data-field="date" style='width: 200px'>{l s='Total' mod='moloni'}</th>
                        <th data-field="status" style='width: 120px'>{l s='Actions' mod='moloni'}</th>
                </tr>
                </thead>

                <tbody>

                    {foreach from=$moloni.documents item=document}
                        <tr>
                            <td style='width: 150px'><a class="waves-effect waves-light btn blue order" style='cursor: '>{$document.document_type|escape:"html":"UTF-8"} | {$document.document_set_name|escape:"html":"UTF-8"}-{$document.number|escape:"html":"UTF-8"}</a></td>
                            <td>
                                <b>{$document.entity_name|escape:'html':'UTF-8'}</b><br>
                                <span style='font-size: 10px'>
                                    {$document.entity_address|escape:'html':'UTF-8'} <br>
                                    {$document.entity_vat|escape:'html':'UTF-8'}
                                </span>

                            </td>
                            <td>{$document.date|escape:'html':'UTF-8'}</td>
                            <td>{$document.net_value|string_format:"%.2f"|escape:'html':'UTF-8'}€</td>
                            <td>
                                {if $document.check != ""}<a href="{$document.check|escape:'html':'UTF-8'}" target='_BLANK' class='waves-effect waves-light btn blue generate'><i class='material-icons'>search</i></a>{/if}
                                {if $document.download != ""}<a href='{$document.download|escape:'html':'UTF-8'}' target='_BLANK' class='waves-effect waves-light btn green generate'><i class='material-icons'>cloud_download</i></a>{/if}

                            </td>
                        </tr>
                    {/foreach}

                </tbody>
            </table>
        </div>

    </div>


</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}datatables/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('.dataTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "sPaginationType": "full_numbers",
            "order": [[ 2, "desc" ]],
            "sDom": '<"DTtop panel"<"MolShowing"l><"MolSearch"f>>rt<"DTbottom panel"<"MolInfo"i><"MolPagination"p>><"clear">',
            "oLanguage": {
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "{l s='No results found' mod='moloni'}",
                "sInfo": "{l s='Showing' mod='moloni'} <b>_START_</b> - <b>_END_</b> {l s='of' mod='moloni'} <b>_TOTAL_</b> {l s='documents' mod='moloni'}",
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
