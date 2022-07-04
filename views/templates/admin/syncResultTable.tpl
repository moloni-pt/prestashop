{if $moloni.message_alert != null}
    <div class="col s12 z-depth-1 message_success green">
        {l s='Your options were saved :)' mod='moloni'}
    </div>
{/if}
{if $moloni.syncResult}
    <ul class="col s12 z-depth-1 collapsible" style="padding-bottom: 15px">

        {if isset($moloni.syncResult.header) && is_array($moloni.syncResult.header)}
            <li>
                <div class="collapsible-header">
                    <b>{l s='Products updated since: ' mod='moloni'}{$moloni.syncResult.header.updated_since}
                        ({l s='Found' mod='moloni'}: {$moloni.syncResult.header.products_total})</b></div>
            </li>
        {/if}

        {if isset($moloni.syncResult.with_attributes) && is_array($moloni.syncResult.with_attributes)}
            <li>
                <div class="collapsible-header">{l s='Products with updated attributes' mod='moloni'} <i
                            class="material-icons">arrow_drop_down</i></div>
                <div class="collapsible-body">
                    <span>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>{l s='Reference' mod='moloni'} </th>
                                    <th>{l s='Before' mod='moloni'} </th>
                                    <th>{l s='Updated' mod='moloni'} </th>
                                    {if isset($moloni.syncResult.shouldSyncStock)}
                                        <th>{l s='Parent product stock' mod='moloni'} </th>
                                    {/if}
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$moloni.syncResult.with_attributes item=prod}
                                    <tr>
                                        <td>{$prod.reference}</td>
                                        <td>
                                            {if isset($prod.name_before)}
                                                <b>{l s='Name:' mod='moloni'}</b> {$prod.name_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.description_before)}
                                                <b>{l s='Description:' mod='moloni'}</b> {$prod.description_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.price_before)}
                                                <b>{l s='Price:' mod='moloni'}</b> {$prod.price_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.stock_before)}
                                                <b>{l s='Stock:' mod='moloni'}:</b> {$prod.stock_before}
                                            {/if}
                                            {if isset($prod.ean_before)}
                                                <b>{l s='EAN:' mod='moloni'}:</b> {$prod.ean_before}
                                            {/if}
                                        </td>
                                        <td>
                                            {if isset($prod.name_after)}
                                                <b>{l s='Name:' mod='moloni'}</b> {$prod.name_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.description_after)}
                                                <b>{l s='Description:' mod='moloni'}</b> {$prod.description_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.price_after)}
                                                <b>{l s='Price:' mod='moloni'}</b> {$prod.price_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.stock_after)}
                                                <b>{l s='Stock:' mod='moloni'}</b> {$prod.stock_after}
                                            {/if}
                                            {if isset($prod.ean_after)}
                                                <b>{l s='EAN:' mod='moloni'}</b> {$prod.ean_after}
                                            {/if}
                                        </td>
                                        <td>
                                            {if isset($prod.stock_total) && $prod.shouldSyncStock}
                                                {$prod.stock_total}
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </span>
                </div>
            </li>
        {/if}

        {if isset($moloni.syncResult.update_error)}
            <li>
                <div class="collapsible-header">{l s='Products with attributes - error updating' mod='moloni'} <i
                            class="material-icons">arrow_drop_down</i></div>
                <div class="collapsible-body">
                    <span>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>{l s='Reference' mod='moloni'} </th>
                                    <th>{l s='Old stock' mod='moloni'} </th>
                                    <th>{l s='Updated stock' mod='moloni'} </th>
                                    <th>{l s='Parent product stock' mod='moloni'} </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$moloni.syncResult.update_error item=prod}
                                    <tr>
                                        <td>{$prod.reference}</td>
                                        <td>{$prod.stock_before}</td>
                                        <td>{$prod.stock_after}</td>
                                        <td>{$prod.stock_total}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </span>
                </div>
            </li>
        {/if}

        {if isset($moloni.syncResult.simple)}
            <li>
                <div class="collapsible-header">{l s='Updated simple products' mod='moloni'} <i class="material-icons">arrow_drop_down</i>
                </div>
                <div class="collapsible-body">
                    <span>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>{l s='Reference' mod='moloni'} </th>
                                    <th>{l s='Before' mod='moloni'} </th>
                                    <th>{l s='Updated' mod='moloni'} </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$moloni.syncResult.simple item=prod}
                                    <tr>
                                        <td>{$prod.reference}</td>
                                        <td>
                                            {if isset($prod.name_before)}
                                                <b>{l s='Name:' mod='moloni'}</b> {$prod.name_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.description_before)}
                                                <b>{l s='Description:' mod='moloni'}</b> {$prod.description_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.price_before)}
                                                <b>{l s='Price:' mod='moloni'}</b> {$prod.price_before}
                                                <br>
                                            {/if}
                                            {if isset($prod.stock_before)}
                                                <b>{l s='Stock:' mod='moloni'}:</b> {$prod.stock_before}
                                            {/if}
                                            {if isset($prod.ean_before)}
                                                <b>{l s='EAN:' mod='moloni'}:</b> {$prod.ean_before}
                                            {/if}
                                        </td>
                                        <td>
                                            {if isset($prod.name_after)}
                                                <b>{l s='Name:' mod='moloni'}</b> {$prod.name_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.description_after)}
                                                <b>{l s='Description:' mod='moloni'}</b> {$prod.description_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.price_after)}
                                                <b>{l s='Price:' mod='moloni'}</b> {$prod.price_after}
                                                <br>
                                            {/if}
                                            {if isset($prod.stock_after)}
                                                <b>{l s='Stock:' mod='moloni'}</b> {$prod.stock_after}
                                            {/if}
                                            {if isset($prod.ean_after)}
                                                <b>{l s='EAN:' mod='moloni'}</b> {$prod.ean_after}
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </span>
                </div>
            </li>
        {/if}

        {if isset($moloni.syncResult.insert_success)}
            <li>
                <div class="collapsible-header">{l s='Inserted products' mod='moloni'} <i class="material-icons">arrow_drop_down</i>
                </div>
                <div class="collapsible-body">
                    <span>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>{l s='Reference' mod='moloni'} </th>
                                    <th>{l s='Name' mod='moloni'} </th>
                                    <th>{l s='Price' mod='moloni'} </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$moloni.syncResult.insert_success item=prod}
                                    <tr>
                                        <td>{$prod.reference}</td>
                                        <td>{$prod.name}</td>
                                        <td>{$prod.price}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </span>
                </div>
            </li>
        {/if}

        {if isset($moloni.syncResult.insert_error)}
            <li>
                <div class="collapsible-header">{l s='Products not inserted' mod='moloni'} <i class="material-icons">arrow_drop_down</i>
                </div>
                <div class="collapsible-body">
                    <span>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>{l s='Reference' mod='moloni'} </th>
                                    <th>{l s='Name' mod='moloni'} </th>
                                    <th>{l s='Price' mod='moloni'} </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$moloni.syncResult.insert_error item=prod}
                                    <tr>
                                        <td>{$prod.reference}</td>
                                        <td>{$prod.name}</td>
                                        <td>{$prod.price}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </span>
                </div>
            </li>
        {/if}
    </ul>
{/if}