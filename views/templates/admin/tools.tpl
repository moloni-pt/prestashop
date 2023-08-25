<section id="moloni">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}compiled.min.css">

    <div class="panel panel-default">
        <div class="panel-heading">
            {l s='Tools' mod='moloni'}
        </div>
        <div class="panel-body">
            <p>
                {l s='Use this tool to synchronize the products from your Moloni account in your Prestashop store.' mod='moloni'}
            </p>

            {* Results table *}
            {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/tools/syncProductsResults.tpl"}

            <button type="button"
                    class="btn btn-info btn-lg"
                    data-toggle="modal"
                    data-target="#sync_products_modal">
                {l s='Synchronize' mod='moloni'}
            </button>
        </div>
    </div>

    {* Overlays *}
    {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/tools/syncProductsOverlay.tpl"}
    {include file="`$smarty.const._PS_MODULE_DIR_`moloni/views/templates/admin/tools/actionOverlay.tpl"}
</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}compiled.min.js?v={$moloni.version|escape:'html':'UTF-8'}"></script>
<script>
    var currentAction = "{Context::getContext()->link->getAdminLink('MoloniTools', true)}";

    $(document).ready(function() {
        pt.moloni.Tools.init(currentAction);
    });
</script>
