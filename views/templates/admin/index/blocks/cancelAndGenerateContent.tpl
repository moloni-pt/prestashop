<div>
    {if $hasMore}
        <div class="alert alert-info" role="alert">
            <p class="alert-text">
                {l s='Please wait, process in progress' mod='moloni'}
            </p>
        </div>
        <div class="d-flex align-items-center">
            {if $action == 'generate_document'}
                {l s='%s order(s) processed, the invoice(s) will be available!' sprintf=[$documentsProcessed] mod='moloni'}
            {else}
                {l s='%s order(s) processed, the orders(s) will be discarded!' sprintf=[$documentsProcessed] mod='moloni'}
            {/if}
        </div>
    {else}
        <div class="alert alert-success" role="alert">
            <p class="alert-text">
                {l s='Process complete' mod='moloni'}
            </p>
        </div>
        <div class="order_processed order_overlay"></div>
        <p style="margin-top: 8px">
            {if $action == 'generate_document'}
                {l s='%s order(s) processed!' sprintf=[$documentsProcessed] mod='moloni'}
                {l s='Check the invoices!' mod='moloni'}
            {else}
                {l s='%s order(s) processed!' sprintf=[$documentsProcessed] mod='moloni'}
                {l s='Check the discarded order(s)!' mod='moloni'}
            {/if}
        </p>
    {/if}
</div>
