<button style="display: none;" type="button" data-toggle="modal" data-target="#action_overlay_modal" id="action_overlay_button"></button>

<div class="modal fade"
     id="action_overlay_modal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {l s='Action in progress.' mod='moloni'}
                </h5>
            </div>
            <div class="modal-body">
                <div id="action_overlay_content" style="display: none;"></div>
                <div id="action_overlay_error" style="display: none;">
                    <div class="alert alert-danger" role="alert">
                        <p class="alert-text">
                            {l s='Something went wrong!' mod='moloni'}
                        </p>
                    </div>
                    <p>
                        {l s='Please check logs for more information.' mod='moloni'}
                    </p>
                </div>
                <div id="action_overlay_spinner">
                    {l s='We are processing your request.' mod='moloni'}
                    <br>
                    {l s='Please wait until the process finishes!' mod='moloni'}
                    <br>
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="action_overlay_button"
                        type="button"
                        class="btn btn-outline-secondary"
                        data-dismiss="modal"
                        style="display: none;">
                    {l s='Close' mod='moloni'}
                </button>
            </div>
        </div>
    </div>
</div>
