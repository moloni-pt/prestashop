$.fn.slideFadeToggle = function (easing, callback) {
    return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
};

if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.Movements === undefined) {
    pt.moloni.Movements = {};

}

pt.moloni.Movements = (function ($) {
    var translations;

    function init(_translations) {
        translations = _translations;

        startObservers();
    }

    function startObservers() {
        $('.dataTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "sPaginationType": "full_numbers",
            "lengthMenu": [10, 25, 50, 75, 100, 250],
            "pageLength": 10,
            "columnDefs": [
                {
                    className: "dt-center",
                    targets: [0, 4]
                },
                {
                    className: "dt-right",
                    targets: 3
                },
            ],
            "order": [[ 2, "desc" ]],
            "sDom": '<"dataTable--header panel"<l><"dataTable--search"f>>tr<"dataTable--footer panel"<i><"dataTable--pagination" p>>',
            "language": {
                "sLengthMenu": "_MENU_",
                "sZeroRecords": translations.sZeroRecords,
                "sInfo": translations.sInfo,
                "sInfoEmpty": translations.sInfoEmpty,
                "sInfoFiltered": translations.sInfoFiltered,
                "sSearch": "",
                "sSearchPlaceholder": translations.sSearchPlaceholder,
                "oPaginate": {
                    "sFirst": translations.sFirst,
                    "sPrevious": translations.sPrevious,
                    "sNext": translations.sNext,
                    "sLast": translations.sLast,
                }
            }
        })

        $('.check_error').on('click', function () {
            if ($(this).hasClass('selected')) {
                deselect($(this));
            } else {
                $(this).addClass('selected');
                $('.pop').slideFadeToggle();
            }
        });

        $('.close').on('click', function () {
            deselect($('.check_error'));
        });
    }

    function deselect(e) {
        $('.pop').slideFadeToggle(function () {
            e.removeClass('selected');
        });
    }

    return {
        init: init,
    }
}(jQuery));

