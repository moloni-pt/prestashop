<div>
    {if $hasMore eq true}
        <div class="d-flex align-items-center">
            {l s='%s products processed!' sprintf=[$processedProducts] mod='moloni'}
        </div>

        <br>

        <div class="alert alert-info" role="alert">
            <p class="alert-text">
                {l s='Please wait, synchronization in progress' mod='moloni'}
            </p>
        </div>
    {else}
        <p>
            {l s='%s products processed!' sprintf=[$processedProducts] mod='moloni'}
        </p>

        <br>

        <div class="alert alert-success" role="alert">
            <p class="alert-text">
                {l s='Process complete' mod='moloni'}
            </p>
        </div>
    {/if}
</div>
