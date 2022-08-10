<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style-company.css">

<section id="moloni">
    <div class="moloni-company--wrapper">
        {if $moloni.companies AND $moloni.companies|@count > 1}
            {foreach from=$moloni.companies item=company}
                {if $company.company_id != 5}
                    <div class="panel moloni-company--card">
                        <div class="panel-heading">
                            {$company.name|escape:'html':'UTF-8'}
                        </div>
                        <div class="panel-body" style="flex: 1;">
                                <div class="moloni-company--image">
                                    {if $company.image}
                                        <img height="auto"
                                             alt="Company logo"
                                             src="https://www.moloni.pt/_imagens/?macro=imgAC_iconeEmpresa_s3&img={$company.image}">
                                    {else}
                                        <img height="auto"
                                             alt="Company logo"
                                             src="{$moloni.path.img|escape:'html':'UTF-8'}companyDefault.png">
                                    {/if}
                                </div>
                                <div class="moloni-company--information">
                                    {if $company.address}
                                        <p style="white-space: normal;">
                                            {l s='Address' mod='moloni'}
                                            : {$company.address|escape:'html':'UTF-8'}
                                        </p>
                                    {/if}

                                    {if $company.city}
                                        <p>
                                            {l s='City' mod='moloni'}
                                            : {$company.city|escape:'html':'UTF-8'}
                                        </p>
                                    {/if}

                                    {if $company.zip_code}
                                        <p>
                                            {l s='Zip-Code' mod='moloni'}
                                            : {$company.zip_code|escape:'html':'UTF-8'}
                                        </p>
                                    {/if}

                                    {if $company.vat}
                                        <p>
                                            {l s='VAT' mod='moloni'}
                                            : {$company.vat|escape:'html':'UTF-8'}
                                        </p>
                                    {/if}
                                </div>
                            </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary"
                                    onclick='window.location = "{$company.form_url|escape:'html':'UTF-8'}"'>
                                {l s='Select this company' mod='moloni'}
                            </button>
                        </div>
                    </div>
                {/if}
            {/foreach}
        {else}
            <div>
                {l s='Your account does not have access to any eligible company.' mod='moloni'}
            </div>
        {/if}
    </div>
</section>
