if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.Settings === undefined) {
    pt.moloni.Settings = {};

}

pt.moloni.Settings = (function ($) {
    var translations;
    var currentPageAction;
    var doingAjax = false;

    function init(_translations, _currentPageAction) {
        translations = _translations;
        currentPageAction = _currentPageAction;

        drawSaveButton();
        startObservers();
    }

    function startObservers() {
        // Init "fancy" datepicker
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            dateFormat: 'yy-mm-dd',
        });

        // Save settings
        $('#formSubmit').on('click', function () {
            $("#moloniOptions").submit();
        });

        // Start product sync
        $('#formToolsSubmit').on('click', function () {
            $("#moloniTools").submit();
        });

        // Close success message
        $('.moloni-message__success').on('click', function () {
            $(this).hide("slow");
        });

        // Toggle tools results
        $('.collapsible-header').on('click', function () {
            var body = $(this).next('.collapsible-body');
            var icon = $(this).find('.collapsible-icon');

            if (body.length && icon.length) {
                if (body.height() > 0) {
                    icon.addClass('collapsible-icon--open');
                } else {
                    icon.removeClass('collapsible-icon--open');
                }
            }
        });

        // Show Product sync webservice URl
        var showProductSyncWebserviceBtn = $('#showProductSyncWebservice');
        var productSyncWebserviceInput = $('#productSyncWebserviceUrl');

        showProductSyncWebserviceBtn.on('click', function () {
            var url = currentPageAction + '&operation=getWebserviceProductSyncUrl&ajax=true';

            if (doingAjax) {
                return;
            }

            doingAjax = true;

            showProductSyncWebserviceBtn.prop('disabled', true);

            fetch(url, { method: "GET" }).then(
                response => response.json()
            ).then((data) => {
                if (data && data.valid) {
                    showProductSyncWebserviceBtn.hide();
                    productSyncWebserviceInput.val(data.url);
                    productSyncWebserviceInput.show();
                } else {
                    alert('Something went wrong');
                }
            });
        });
    }

    function drawSaveButton() {
        var html = '';

        html += '<button class="btn btn-primary btn-lg" id="formSubmit">';
        html += '   ' + translations.save_changes;
        html += '</button>';

        $('#toolbar-nav').html(html);
    }

    return {
        init: init,
    }
}(jQuery));

