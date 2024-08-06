<div>
    {if $hasMore}
        <div class="d-flex align-items-center">
            {l s='%s order(s) processed, the invoice(s) will be available!' sprintf=[$documentsProcessed] mod='moloni'}
        </div>

        <br>

        <div class="alert alert-info" role="alert">
            <p class="alert-text">
                {l s='Please wait, synchronization in progress' mod='moloni'}
            </p>
        </div>
    {else}
        <p>
            {l s='%s order(s) processed!' sprintf=[$documentsProcessed] mod='moloni'}
            {l s='Check the invoices!' mod='moloni'}
        </p>
        <div class="order_processed" style="overflow: auto; max-height:150px"></div>
        <br>
        <div class="alert alert-success" role="alert">
            <p class="alert-text">
                {l s='Process complete' mod='moloni'}
            </p>
        </div>
    {/if}
</div>
