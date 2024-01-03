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

        // Get webservice URL
        $('#get_url_button').on('click', getWebserviceURL);
    }

    function drawSaveButton() {
        var html = '';

        html += '<button class="btn btn-primary btn-lg" id="formSubmit">';
        html += '   ' + translations.save_changes;
        html += '</button>';

        $('#toolbar-nav').html(html);
    }

    function getWebserviceURL() {
        var getButton = $(this);

        if (getButton.is(':disabled')) {
            return;
        }

        var inputHolder = $('#urlHolder');

        if (!inputHolder.length) {
            return;
        }

        var input = inputHolder.find('input');
        var copyButton = inputHolder.find('button');
        var allSelectedFields = jQuery("input[name='sync_fields[]']:checkbox:checked");

        var params = {
            ajax: true,
            operation: 'getWebserviceProductSyncUrl',
            sync_fields: [],
        };

        allSelectedFields.each(function () {
            params.sync_fields.push($(this).val());
        });

        var url = currentPageAction + '&' + new URLSearchParams(params).toString();

        inputHolder.hide();
        getButton.attr("disabled", true);

        fetch(url, { method: "GET" }).then(
            response => response.json()
        ).then((data) => {
            getButton.removeAttr("disabled");

            if (data && data.valid) {
                inputHolder.show();
                input.val(data.url);

                copyButton.off('click').on('click', function () {
                    navigator.clipboard.writeText(data.url);
                });
            } else {
                alert('Something went wrong');
            }
        });
    }

    return {
        init: init,
    }
}(jQuery));

