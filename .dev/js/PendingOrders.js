if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.PendingOrders === undefined) {
    pt.moloni.PendingOrders = {};
}

pt.moloni.PendingOrders = (function ($) {
    var translations;
    var currentPageAction;

    function init(_translations, _currentPageAction) {
        translations = _translations;
        currentPageAction = _currentPageAction;

        startObservers();
    }

    function startObservers() {
        var datatable = $('.dataTable');
        var checkError = $('.check_error');
        var close = $('.close');

        datatable
            .on('preXhr.dt', disableTable) // https://datatables.net/reference/event/preXhr
            .dataTable({
                "processing": true,
                "serverSide": true,
                "bStateSave": true,
                "ajax": {
                    "url": currentPageAction,
                    "data": {
                        "ajax": true,
                    }
                },
                "columns": [
                    {
                        data: 'info.id_order',
                        orderable: true,
                        render: renderOrderCol,
                    },
                    {
                        data: 'address',
                        orderable: false,
                        render: renderClientCol
                    },
                    {
                        data: 'customer.email',
                        defaultContent: '',
                        orderable: false,
                    },
                    {
                        data: 'info.date_add',
                        orderable: true,
                    },
                    {
                        data: 'state.name',
                        orderable: false,
                    },
                    {
                        data: 'info.total_paid',
                        orderable: true,
                        render: renderPriceCol
                    },
                    {
                        data: 'acts',
                        orderable: false,
                        render: renderActionsCol
                    }
                ],
                "columnDefs": [
                    {
                        className: "dt-center",
                        targets: [0, 6]
                    },
                    {
                        className: "dt-right",
                        targets: 5
                    },
                ],
                "fnDrawCallback": enableTable, // https://datatables.net/reference/option/drawCallback
                "searchDelay": 2000,
                "lengthMenu": [10, 25, 50, 75, 100, 250],
                "pageLength": 10,
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

        checkError.on('click', function () {
            if (checkError.hasClass('selected')) {
                deselect($(this));
            } else {
                checkError.addClass('selected');
                $('.pop').slideFadeToggle();
            }

            return false;
        });

        close.on('click', function () {
            deselect(checkError);
            return false;
        });

        // Duck tape to fix, multiple ajax requests while searching.
        // Only searches when pressing "enter"
        $('.dataTables_filter input')
            .off('')
            .bind('keyup', function (e) {
                if (e.keyCode != 13) {
                    return;
                }

                datatable.fnFilter($(this).val());
            });
    }

    //       PRIVATES       //

    function deselect(e) {
        $('.pop').slideFadeToggle(function () {
            e.removeClass('selected');
        });
    }

    function disableTable() {
        $('.dataTable').addClass('dataTable--disabled');
    }

    function enableTable() {
        $('.dataTable').removeClass('dataTable--disabled');
    }

    //       RENDERS       //

    function renderActionsCol(data, type, row, meta) {
        var html = "";

        html += "<a class='moloni-icon' href='" + row.url.create + "'>";
        html += "   <i class='moloni-icon__blue material-icons'>note_add</i>";
        html += "</a>";
        html += "<a class='moloni-icon' href='" + row.url.clean + "'>";
        html += "   <i class='moloni-icon__red material-icons'>delete</i>";
        html += "</a>";

        return html;
    }

    function renderClientCol(data, type, row, meta) {
        var html = "";

        html += "<b>" + data.firstname + " " + data.lastname + "</b>";
        html += "<br>";
        html += "<span style='font-size: 10px'>";

        if (data.address1) {
            html += data.address1 + "<br>";
        }

        if (data.vat_number) {
            html += data.vat_number + "<br>";
        }

        html += "</span> ";

        return html;
    }

    function renderOrderCol(data, type, row, meta) {
        var html = "";

        html += "<a target='_blank' href='" + row.url.order + "'>";
        html += "    #" + data;
        html += "</a>";

        return html;
    }

    function renderPriceCol(data, type, row, meta) {
        var html = "";
        var symbol = "€";

        html += "<div>";
        html += parseFloat(data).toFixed(2);

        if (row && row.currency && row.currency.symbol) {
            symbol = row.currency.symbol;
        }

        html += symbol;
        html += "</div>";

        return html;
    }

    return {
        init: init,
    }
}(jQuery));

