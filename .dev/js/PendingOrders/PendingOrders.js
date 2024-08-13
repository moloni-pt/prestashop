if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.PendingOrders === undefined) {
    pt.moloni.PendingOrders = {};
}

pt.moloni.PendingOrders = (function($) {
    var translations;
    var currentPageAction;

    var checkMaster;
    var actionButton;
    var datatable;

    function init(_translations, _currentPageAction) {
        translations = _translations;
        currentPageAction = _currentPageAction;

        startObservers();
    }

    function startObservers() {
        var checkError = $('.check_error');
        var close = $('.close');

        datatable = $('.dataTable');
        checkMaster = $('.select-all')

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
                        orderable: false,
                        render: renderCheckbox,
                    },
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
                        targets: [1, 7]
                    },
                    {
                        className: "dt-right",
                        targets: 6
                    },
                ],
                "fnDrawCallback": function() {
                    onTableRender();
                }, // https://datatables.net/reference/option/drawCallback
                "lengthMenu": [10, 25, 50, 75, 100, 250],
                "pageLength": 10,
                "sDom": '<"dataTable--header panel"' +
                    'l' +
                    '<"dataTable--options"' +
                    '<"dataTable--search"<f>>' +
                    '<"dataTable--button">' +
                    '>' +
                    '>' +
                    'tr' +
                    '<"dataTable--footer panel"' +
                    '<i>' +
                    '<"dataTable--pagination" p>' +
                    '>',
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
                },
            })

        addCreateAndDiscardOptions();

        actionButton = $('.execute');

        checkError.on('click', function() {
            if (checkError.hasClass('selected')) {
                deselect($(this));
            } else {
                checkError.addClass('selected');
                $('.pop').slideFadeToggle();
            }

            return false;
        });

        close.on('click', function() {
            deselect(checkError);
            return false;
        });


        checkMaster.on('change', function() {
            $('.order_doc').prop('checked', $(this).prop('checked'));

            if ($(this).prop('checked')) {
                $('.execute').prop('disabled', false);
            } else {
                $('.execute').prop('disabled', true);
            }
        });

        // Duck tape to fix, multiple ajax requests while searching.
        // Only searches when pressing "enter"
        $('.dataTables_filter input')
            .off('')
            .bind('keyup', function(e) {
                if (e.keyCode != 13) {
                    return;
                }

                datatable.fnFilter($(this).val());
            });

       actionButton.on('click', function() {
            var action = $('.select-action').val();

            pt.moloni.PendingOrders.Overlays.ProcessOrder(currentPageAction, action, datatable, translations);
        });
    }

    //       PRIVATES       //

    function deselect(e) {
        $('.pop').slideFadeToggle(function() {
            e.removeClass('selected');
        });
    }

    function disableTable() {
        datatable.addClass('dataTable--disabled');
    }

    function enableTable() {
        datatable.removeClass('dataTable--disabled');
    }

    function onTableRender() {
        enableTable();

        checkMaster.prop('checked', false);
        actionButton.prop('disabled', true);

        $('.select-action').prop('disabled', false);

        $('.order_doc').each(function() {
            $(this).on('change', function() {
                if ($('.order_doc').length === $("input[class='order_doc']:checked").length) {
                    checkMaster.prop('checked', true);
                } else {
                    checkMaster.prop('checked', false);
                }

                if (!$("input[class='order_doc']:checked").length) {
                    actionButton.prop('disabled', true);
                } else {
                    actionButton.prop('disabled', false);
                }
            });
        });
    }

    function addCreateAndDiscardOptions() {
        $('.dataTable--button').html(
            '<select class="select-action" disabled>  ' +
            '<option value="generate_document">' + translations.sCreateInvoice + '</option>' +
            '<option value="delete_document">' + translations.sDiscardOrder + '</option> ' +
            '</select>' +
            '<input type="button" class="execute" value="'+ translations.sAction +'" data-target="#sync_products_modal" disabled>');
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

    function renderCheckbox(data, type, row, meta) {
        var html = '<input ' +
            'type="checkbox" ' +
            'name="checkbox" ' +
            'class="order_doc" ' +
            'id="order_doc_' + row.info.id_order + '" ' +
            'value="' + row.info.id_order + '"' +
            '>';
        return html;
    }

    return {
        init: init,
    }
}(jQuery));

