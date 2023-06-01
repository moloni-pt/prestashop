if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.Tools === undefined) {
    pt.moloni.Tools = {};
}

pt.moloni.Tools = (function ($) {
    var currentPageAction;

    function init(_currentPageAction) {
        currentPageAction = _currentPageAction;

        startObservers();
    }

    function startObservers() {
        // Init "fancy" datepicker
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            dateFormat: 'yy-mm-dd',
        });

        $('#sync_products_modal').find('#sync_products_button').on('click', function () {
            pt.moloni.Tools.Overlays.SyncProducts(currentPageAction);
        });
    }

    return {
        init: init,
    }
}(jQuery));