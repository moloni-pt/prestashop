{**
* 2016 - Moloni.com
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
* DISCLAIMER 
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Nuno Almeida
*  @copyright Nuno Almeida
*  @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
*}

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style-company.css">
<link rel="stylesheet" href='{$moloni.path.css|escape:'html':'UTF-8'}materialize.css'>

<section id="moloni">

    {foreach from=$moloni.companies item=company}

        <div class="card">
            <div class="card-image waves-effect waves-block waves-light">

                {html_entity_decode($company.image|escape:'htmlall':'UTF-8')}

            </div>
            <div class="card-content">
                <span class="card-title activator grey-text text-darken-4">{$company.name|escape:'html':'UTF-8'}</span>
                <span><i class="material-icons right " onclick='window.location = "{$company.form_url|escape:'html':'UTF-8'}"'>input</i></span>
                <span class="activator"><i class="material-icons right ">more_vert</i><span>
                        <p><a>{$company.vat|escape:'html':'UTF-8'}</a></p>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4">{$company.name|escape:'html':'UTF-8'}<i class="material-icons right">close</i></span>
                            <br>
                            <p>{$company.address|escape:'html':'UTF-8'}<br>
                                {$company.city|escape:'html':'UTF-8'}<br>
                                {$company.zip_code|escape:'html':'UTF-8'}</p>
                        </div>
                        </div>

                    {/foreach}

                    </section>

                    <script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}materialize/js/materialize.js"></script>
