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

<link rel="stylesheet" href="{$moloni.path.css|escape:'html':'UTF-8'}style-login.css">

<form class='moloni-login-form' action = '' method='POST' autocomplete="false">
    <a href='https://moloni.com' target='_BLANK'><img src='{$moloni.path.img|escape:'html':'UTF-8'}logo.png' class='moloni-logo'></a>
    
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

{literal}
    <script>
        $(window, document, undefined).ready(function () {

            $('input').blur(function () {
                var $this = $(this);
                if ($this.val())
                    $this.addClass('used');
                else
                    $this.removeClass('used');
            });

            var $ripples = $('.ripples');

            $ripples.on('click.Ripples', function (e) {

                var $this = $(this);
                var $offset = $this.parent().offset();
                var $circle = $this.find('.ripplesCircle');

                var x = e.pageX - $offset.left;
                var y = e.pageY - $offset.top;

                $circle.css({
                    top: y + 'px',
                    left: x + 'px'
                });

                $this.addClass('is-active');

            });

            $ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function (e) {
                $(this).removeClass('is-active');
            });



        });

        $(".moloni-login-form .button").click(function () {
            $(".moloni-login-form").submit();
        });

        $(".login-error").click(function () {
            $(this).slideUp(500);
        });
    </script>
{/literal}