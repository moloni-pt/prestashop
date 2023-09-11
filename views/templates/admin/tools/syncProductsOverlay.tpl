<div class="modal fade"
     id="sync_products_modal"
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
                    {l s='Synchronize products to Prestashop' mod='moloni'}
                </h5>
            </div>
            <div class="modal-body">
                <b>
                    {l s='Product fields to synchronize' mod='moloni'}
                </b>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="name" name='sync_fields[]' value="name">
                        {l s='Name' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="price" name='sync_fields[]' value="price">
                        {l s='Price' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="description" name='sync_fields[]' value="description">
                        {l s='Description' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="ean" name='sync_fields[]' value="ean">
                        {l s='EAN' mod='moloni'}
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="stock" name='sync_fields[]' value="stock">
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

                <div>
                    <label>
                        {l s='Since' mod='moloni'}
                        <input type="text"
                               class="datepicker"
                               name='sync_since'
                               placeholder="yyyy-mm-dd"
                               value=''>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-dismiss="modal">
                    {l s='Close' mod='moloni'}
                </button>
                <button class="btn btn-info"
                        data-dismiss="modal"
                        id="sync_products_button">
                    {l s='Synchronize products' mod='moloni'}
                </button>
            </div>
        </div>
    </div>
</div>
