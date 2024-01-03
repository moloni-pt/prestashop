<div class="modal fade"
     id="create_cron_link_modal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">
                    {l s='Create cron URL' mod='moloni'}
                </h5>
            </div>
            <div class="modal-body">
                <b>
                    {l s='Product fields to synchronize' mod='moloni'}
                </b>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="name" name='sync_fields[]' value="name" checked>
                        {l s='Name' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="price" name='sync_fields[]' value="price" checked>
                        {l s='Price' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="description" name='sync_fields[]' value="description" checked>
                        {l s='Description' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="ean" name='sync_fields[]' value="ean" checked>
                        {l s='EAN' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="stock" name='sync_fields[]' value="stock" checked>
                        {l s='Stock' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="tax" name='sync_fields[]' value="tax">
                        {l s='Tax' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="image" name='sync_fields[]' value="image">
                        {l s='Image' mod='moloni'}
                    </label>
                </div>

                <div class="cron-url--holder" id="urlHolder" style="display: none;">
                    <b>
                        {l s='Created URL' mod='moloni'}
                    </b>

                    <div class="cron-url">
                        <input type="text" disabled placeholder='{l s='Please create URL' mod='moloni'}'>

                        <button type="button">
                            <i class="material-icons">content_copy</i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-dismiss="modal">
                    {l s='Close' mod='moloni'}
                </button>

                <button class="btn btn-info" id="get_url_button">
                    {l s='Create URL' mod='moloni'}
                </button>
            </div>
        </div>
    </div>
</div>
