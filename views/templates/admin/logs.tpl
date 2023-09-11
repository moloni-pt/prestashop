<section id="moloni">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}compiled.min.css">

    <div class="row">
        <table class='dataTable display dataTable--slimed panel'>
            <thead>
            <tr>
                <th width="150px">{l s='Date' mod='moloni'}</th>
                <th width="150px">{l s='Level' mod='moloni'}</th>
                <th>{l s='Message' mod='moloni'}</th>
                <th width="150px">{l s='Context' mod='moloni'}</th>
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

    <div class="row">
        <a type="button" class="btn btn-danger" href="{$link->getAdminLink('MoloniLogs')}&delete=true">
            {l s='Download logs older than 1 week' mod='moloni'}
        </a>
    </div>

    {* Overlays *}
    {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/logs/logsOverlay.tpl"}
</section>

<script type="text/javascript"
        src="{$moloni.path.js|escape:'html':'UTF-8'}compiled.min.js?v={$moloni.version|escape:'html':'UTF-8'}"></script>

<script>
    var translations = {
        "sZeroRecords": "{l s='No results found' mod='moloni'}",
        "sInfo": "{l s='Showing' mod='moloni'} <b>_START_</b> - <b>_END_</b> {l s='of' mod='moloni'} <b>_TOTAL_</b> {l s='logs' mod='moloni'}",
        "sInfoEmpty": "{l s='Nothing to show' mod='moloni'}",
        "sInfoFiltered": "({l s='Filtered from' mod='moloni'} _MAX_)",
        "sSearchPlaceholder": "{l s='Search' mod='moloni'}...",
        "sFirst": "{l s='Start' mod='moloni'}",
        "sPrevious": "{l s='Back' mod='moloni'}",
        "sNext": "{l s='Next' mod='moloni'}",
        "sLast": "{l s='Last' mod='moloni'}",
        "see": "{l s='See' mod='moloni'}",
        "debug": "{l s='Debug' mod='moloni'}",
        "info": "{l s='Info' mod='moloni'}",
        "warning": "{l s='Warning' mod='moloni'}",
        "critical": "{l s='Critical' mod='moloni'}",
        "error": "{l s='Error' mod='moloni'}",
    };
    var currentAction = "{$link->getAdminLink('MoloniLogs')}";

    $(document).ready(function () {
        pt.moloni.Logs.init(translations, currentAction);
    });
</script>
