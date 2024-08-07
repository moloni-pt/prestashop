<section id="moloni">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}compiled.min.css">

    <div class="row">
        {if isset($moloni.message.error.message)}
            <div class="moloni-message moloni-message__error">
                <strong>
                    {l s='Ups, there was an error :(' mod='moloni'}
                </strong>

                <button class="btn btn-default check_error">
                    {l s='Error' mod='moloni'}
                </button>
            </div>

            <div class="messagepop--wrapper">
                <div class="messagepop pop">
                    {l s='Error' mod='moloni'}: {$moloni.message.error.where|escape:'html':'UTF-8'}
                    <br>
                    {$moloni.message.error.message|escape:'html':'UTF-8'}
                    <br>
                    {l s='Sent' mod='moloni'}:
                    <pre>
                    {$moloni.message.error.values_sent|@print_r|escape:'html':'UTF-8'}}
                </pre>
                    {l s='Received' mod='moloni'}:
                    <pre>
                    {$moloni.message.error.values_receive|@print_r|escape:'html':'UTF-8'}}
                </pre>
                    <br>
                    <a class="close" href="/">{l s='Close' mod='moloni'}</a>
                </div>
            </div>
        {/if}

        {if isset($moloni.message.success.success)}
            <div class="moloni-message moloni-message__success">
                <strong>
                    {$moloni.message.success.message|escape:'html':'UTF-8'}
                </strong>

                {if !empty($moloni.message.success.button)}
                    <a class="btn btn-default"
                       href='{$moloni.message.success.url|escape:"html":"UTF-8"}'
                       target='{if !empty($moloni.message.success.tab)}{$moloni.message.success.tab|escape:"html":"UTF-8"}{/if}'>
                        {$moloni.message.success.button|escape:'html':'UTF-8'}
                    </a>
                {/if}
            </div>
        {/if}

        <table class='dataTable display dataTable--slimed panel'>
            <thead>
            <tr>
                <th class="no-sort" style='width: 30px'><input type="checkbox" class='select-all'></th>
                <th style='width: 50px'>{l s='Number' mod='moloni'}</th>
                <th>{l s='Client' mod='moloni'}</th>
                <th style='width: 200px'>{l s='Email' mod='moloni'}</th>
                <th style='width: 200px'>{l s='Order date' mod='moloni'}</th>
                <th style='width: 120px'>{l s='Status' mod='moloni'}</th>
                <th style='width: 120px'>{l s='Total' mod='moloni'}</th>
                <th style='width: 150px'>{l s='Actions' mod='moloni'}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="100%" class="dataTables_empty">
                    {l s='Please wait, fetching data' mod='moloni'}
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    {* Overlays *}
    {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/index/actionOverlay.tpl"}

</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}compiled.min.js?v={$moloni.version|escape:'html':'UTF-8'}"></script>

<script>
    var translations = {
        "sZeroRecords": "{l s='No results found' mod='moloni'}",
        "sInfo": "{l s='Showing' mod='moloni'} <b>_START_</b> - <b>_END_</b> {l s='of' mod='moloni'} <b>_TOTAL_</b> {l s='orders' mod='moloni'}",
        "sInfoEmpty": "{l s='Nothing to show' mod='moloni'}",
        "sInfoFiltered": "({l s='Filtered from' mod='moloni'} _MAX_)",
        "sSearchPlaceholder": "{l s='Search' mod='moloni'}...",
        "sFirst": "{l s='Start' mod='moloni'}",
        "sPrevious": "{l s='Back' mod='moloni'}",
        "sNext": "{l s='Next' mod='moloni'}",
        "sLast": "{l s='Last' mod='moloni'}",
        "sAction": "{l s='Execute' mod='moloni'}",
        "sCreateInvoice": "{l s='Create Invoice' mod='moloni'}",
        "sDiscardOrder": "{l s='Discard Order' mod='moloni'}",
        "sOrder": "{l s='Order' mod='moloni'}",
    };
    var currentAction = "{Context::getContext()->link->getAdminLink('MoloniStart', true)}";

    $(document).ready(function () {
        pt.moloni.PendingOrders.init(translations, currentAction);
    });
</script>
