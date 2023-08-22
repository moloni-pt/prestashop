if (pt === undefined) {
    var pt = {};
}

if (pt.moloni === undefined) {
    pt.moloni = {};
}

if (pt.moloni.Logs === undefined) {
    pt.moloni.Logs = {};
}

pt.moloni.Logs = (function ($) {
    var translations;
    var currentPageAction;
    var logs = {};

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
                        data: 'created_at',
                        orderable: true,
                    },
                    {
                        data: 'log_level',
                        orderable: false,
                        render: renderLevelCol
                    },
                    {
                        data: 'message',
                        defaultContent: '',
                        orderable: false,
                    },
                    {
                        data: 'context',
                        orderable: false,
                        render: renderContextCol
                    },
                ],
                "columnDefs": [
                    {
                        className: "dt-center",
                        targets: [1, 3]
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

    function disableTable() {
        $('.dataTable').addClass('dataTable--disabled');

        logs = [];
    }

    function enableTable() {
        $('.dataTable').removeClass('dataTable--disabled');
    }

    function openLogOverlay(logId) {
        var overlay = $("#logs_overlay_modal");
        var overlayBtn = $("[data-target=#logs_overlay_modal]");
        var content = '';

        content += '<pre class="logs-content">';
        content += logs[logId];
        content += '</pre>';

        overlay.find('.modal-body').html(content);
        overlay.find('.modal-footer').find('#download_log_btn').on('click', downloadLogOverlay.bind(this, logId))

        overlayBtn.trigger('click');
    }

    function downloadLogOverlay(logId) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(logs[logId]));
        element.setAttribute('download', 'log.txt');
        element.style.display = 'none';

        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }

    //       RENDERS       //

    function renderLevelCol(data, type, row, meta) {
        var cssClass = '';
        var labelText = '';

        switch (data) {
            case 'debug':
                cssClass = 'badge-info';
                labelText = translations.debug;

                break;
            case 'warning':
                cssClass = 'badge-warning';
                labelText = translations.warning;

                break;
            case 'critical':
                cssClass = 'badge-danger';
                labelText = translations.critical;

                break;
            case 'error':
                cssClass = 'badge-danger';
                labelText = translations.error;

                break;
            default:
            case 'info':
                cssClass = 'badge-primary';
                labelText = translations.info;

                break;
        }

        var html = "";

        html += "<span class='badge " + cssClass + "'>";
        html += labelText;
        html += "</span>";

        return html;
    }

    function renderContextCol(data, type, row, meta) {
        data = data || '{}';
        data = JSON.parse(data);

        var logId =  row.id;
        var html = "";

        html += "<button type='button' class='btn btn-info' name='log_open_btn' onclick='pt.moloni.Logs.openLogOverlay(" + logId + ");'>";
        html += translations.see;
        html += "</button>";

        logs[logId] = JSON.stringify(data || {}, null, 2);

        return html;
    }

    return {
        init: init,
        openLogOverlay: openLogOverlay,
    }
}(jQuery));

