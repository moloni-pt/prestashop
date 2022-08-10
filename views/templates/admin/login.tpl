<link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style-login.css">

<form class='moloni-login-form' action = '' method='POST' autocomplete="false">
    <a href='https://moloni.pt' target='_BLANK'><img src='{$moloni.path.img|escape:'html':'UTF-8'}logo.png' class='moloni-logo'></a>

    {if isset($moloni_error.login) }
        <div class="group login-error">
          <center> {l s='Email and password don\'t match' mod='moloni'}</center>
        </div>
    {/if}

    <div class="group">
        <input type="email" name='mol-username' autocomplete="false" onfocus="this.removeAttribute('readonly');" readonly><span class="bar"></span>
        <label>{l s='Email' mod='moloni'}</label>
    </div>

    <div class="group">
        <input id='mol-pwd' type="password" name='mol-password' autocomplete="false" onfocus="this.removeAttribute('readonly');" readonly><span class="bar"></span>
        <label class='pwd-fix'>{l s='Password' mod='moloni'}</label>
    </div>

    <button type="button" class="button buttonBlue">{l s='Login' mod='moloni'}
        <div class="ripples buttonRipples"><span class="ripplesCircle"></span></div>
    </button>
</form>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}Login.js"></script>
<script>
    $(window, document, undefined).ready(function () {
        pt.moloni.Login.init();
    });
</script>
