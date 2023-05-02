<div class="collapsible-wrapper panel" id="sync_products_results" style="display: none;">

    <div class="collapsible-title" id="sync_products_header" style="display: none;">
        <b>
            {l s='Products updated since: ' mod='moloni'}
        </b>
        <b>
            ({l s='Found' mod='moloni'}: <text></text>)
        </b>
    </div>

    <div class="collapsible" id="sync_products_with_attributes" style="display: none;">
        <div class="collapsible-header"
             data-toggle="collapse"
             href="#sync_result_0"
             aria-expanded="false"
             aria-controls="sync_result_0">
            {l s='Products with updated attributes' mod='moloni'}
            <i class="material-icons collapsible-icon">arrow_drop_down</i>
        </div>
        <div class="collapse collapsible-body" id="sync_result_0">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='Reference' mod='moloni'} </th>
                    <th>{l s='Before' mod='moloni'} </th>
                    <th>{l s='Updated' mod='moloni'} </th>
                    <th>{l s='Parent product stock' mod='moloni'} </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="collapsible" id="sync_products_update_error" style="display: none;">
        <div class="collapsible-header"
             data-toggle="collapse"
             href="#sync_result_1"
             aria-expanded="false"
             aria-controls="sync_result_1">
            {l s='Products with attributes - error updating' mod='moloni'}
            <i class="material-icons collapsible-icon">arrow_drop_down</i>
        </div>
        <div class="collapse collapsible-body" id="sync_result_1">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='Reference' mod='moloni'} </th>
                    <th>{l s='Old stock' mod='moloni'} </th>
                    <th>{l s='Updated stock' mod='moloni'} </th>
                    <th>{l s='Parent product stock' mod='moloni'} </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="collapsible" id="sync_products_simple" style="display: none;">
        <div class="collapsible-header"
             data-toggle="collapse"
             href="#sync_result_2"
             aria-expanded="false"
             aria-controls="sync_result_2">
            {l s='Updated simple products' mod='moloni'}
            <i class="material-icons collapsible-icon">arrow_drop_down</i>
        </div>
        <div class="collapse collapsible-body" id="sync_result_2">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='Reference' mod='moloni'} </th>
                    <th>{l s='Before' mod='moloni'} </th>
                    <th>{l s='Updated' mod='moloni'} </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="collapsible" id="sync_products_insert_success" style="display: none;">
        <div class="collapsible-header"
             data-toggle="collapse"
             href="#sync_result_3"
             aria-expanded="false"
             aria-controls="sync_result_3">
            {l s='Inserted products' mod='moloni'}
            <i class="material-icons collapsible-icon">arrow_drop_down</i>
        </div>
        <div class="collapse collapsible-body" id="sync_result_3">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='Reference' mod='moloni'} </th>
                    <th>{l s='Name' mod='moloni'} </th>
                    <th>{l s='Price' mod='moloni'} </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="collapsible" id="sync_products_insert_error" style="display: none;">
        <div class="collapsible-header"
             data-toggle="collapse"
             href="#sync_result_4"
             aria-expanded="false"
             aria-controls="sync_result_4">
            {l s='Products not inserted' mod='moloni'}
            <i class="material-icons collapsible-icon">arrow_drop_down</i>
        </div>
        <div class="collapse collapsible-body" id="sync_result_4">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>{l s='Reference' mod='moloni'} </th>
                    <th>{l s='Name' mod='moloni'} </th>
                    <th>{l s='Price' mod='moloni'} </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
