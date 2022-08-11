if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.CompanySelect === undefined) {
    pt.moloni.CompanySelect = {};

}

pt.moloni.CompanySelect = (function ($) {

    function init(_translations) {
        startObservers();
    }

    function startObservers() {}

    return {
        init: init,
    }
}(jQuery));
