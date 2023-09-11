<button
        type='button'
        class='btn btn-primary'
        data-toggle='modal'
        data-target='#logs_overlay_modal'
        style="display: none;"
>
    {l s='See' mod='moloni'}
</button>

<div class="modal fade"
     id="logs_overlay_modal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {l s='Log information' mod='moloni'}
                </h5>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info"  id="download_log_btn">
                    {l s='Download' mod='moloni'}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {l s='Close' mod='moloni'}
                </button>
            </div>
        </div>
    </div>
</div>
