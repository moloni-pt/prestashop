if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.Login === undefined) {
    pt.moloni.Login = {};

}

pt.moloni.Login = (function ($) {

    function init(_translations) {
        startObservers();
    }

    function startObservers() {
        $('input').blur(function () {
            var $this = $(this);

            if ($this.val()){
                $this.addClass('used');
            }
            else {
                $this.removeClass('used');
            }
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

        $(".moloni-login-form .button").click(function () {
            $(".moloni-login-form").submit();
        });

        $(".login-error").click(function () {
            $(this).slideUp(500);
        });
    }

    return {
        init: init,
    }
}(jQuery));

