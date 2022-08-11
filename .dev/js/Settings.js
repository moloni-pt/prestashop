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

    function init(_translations) {
        translations = _translations;

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

