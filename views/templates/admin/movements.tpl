<section id="moloni">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style.css">
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}datatables/jquery.dataTables.min.css">

    <div class="row">
        <table class='dataTable display dataTable--slimed panel'>
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
                        <td style='width: 150px'>
                            <a class="waves-effect waves-light btn blue order" style='cursor: '>
                                {$document.document_type|escape:"html":"UTF-8"} | {$document.document_set_name|escape:"html":"UTF-8"}-{$document.number|escape:"html":"UTF-8"}
                            </a>
                        </td>
                        <td>
                            <b>
                                {$document.entity_name|escape:'html':'UTF-8'}
                            </b>
                            <br>
                            <span style='font-size: 10px'>
                                {$document.entity_address|escape:'html':'UTF-8'} <br>
                                {$document.entity_vat|escape:'html':'UTF-8'}
                            </span>

                        </td>
                        <td>
                            {$document.date|escape:'html':'UTF-8'}
                        </td>
                        <td>
                            {$document.net_value|string_format:"%.2f"|escape:'html':'UTF-8'}€
                        </td>
                        <td>
                            {if $document.check != ""}
                                <a class="moloni-icon" href="{$document.check|escape:'html':'UTF-8'}" target='_BLANK'>
                                    <i class='moloni-icon__blue material-icons'>search</i>
                                </a>
                            {/if}
                            {if $document.download != ""}
                                <a class="moloni-icon" href='{$document.download|escape:'html':'UTF-8'}' target='_BLANK'>
                                    <i class='moloni-icon__green material-icons'>cloud_download</i>
                                </a>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}Movements.js"></script>

<script>
    $(document).ready(function () {
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
        };

        $(document).ready(function () {
            pt.moloni.Movements.init(translations);
        });
    });
</script>
