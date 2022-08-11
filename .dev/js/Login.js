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
        $(".moloni-login-form .button").on('click', function () {
            $(".moloni-login-form").submit();
        });

        $(".moloni-login--error").on('click', function () {
            $(this).slideUp(500);
        });
    }

    return {
        init: init,
    }
}(jQuery));

