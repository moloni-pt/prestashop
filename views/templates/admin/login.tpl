<link rel="stylesheet" type="text/css" href="{$moloni.path.css|escape:'html':'UTF-8'}compiled.min.css">

<section id="moloni">
    <div class="moloni-login--wrapper">
        <form class='moloni-login-form' action='' method='POST' autocomplete="false">

            <div class="panel moloni-login--card">
                <div class="moloni-login--block">
                    <div class="moloni-login--image">
                        <a href="https://www.moloni.es/" target="_blank">
                            <img src="{$moloni.path.img|escape:'html':'UTF-8'}logo.svg"
                                 alt="Logo" width="240px" height="60px">
                        </a>
                    </div>
                </div>

                {if isset($moloni_error.login) }
                    <div class="moloni-login--block moloni-login--error">
                        <center>
                            {l s='Email and password don\'t match' mod='moloni'}
                        </center>
                    </div>
                {/if}

                <div class="moloni-login--block">
                    <div class="form-group">
                        <label for="mol-username">
                            {l s='Email' mod='moloni'}
                        </label>
                        <input type="email" class="form-control" name='mol-username' autocomplete="false" id="mol-username">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">
                            {l s='Password' mod='moloni'}
                        </label>
                        <input type="password" class="form-control" name="mol-password" id="mol-password">
                    </div>
                </div>
                <div class="moloni-login--block">
                    <button type="submit" class="btn btn-lg btn-primary moloni-login--button">
                        {l s='Login' mod='moloni'}
                    </button>
                </div>
        </form>
    </div>
</section>

<script type="text/javascript" src="{$moloni.path.js|escape:'html':'UTF-8'}compiled.min.js?v={$moloni.version|escape:'html':'UTF-8'}"></script>
<script>
    $(window, document, undefined).ready(function () {
        pt.moloni.Login.init();
    });
</script>
