if (!pt.moloni.Tools.Overlays) {
    pt.moloni.Tools.Overlays = {}
}

pt.moloni.Tools.Overlays.SyncProducts = (async function (currentPageAction) {
    var syncProductsModal = $('#sync_products_modal');

    var actionButton = $('#action_overlay_button');
    var actionModal = $('#action_overlay_modal');
    var closeButton = actionModal.find('#action_overlay_button');
    var spinner = actionModal.find('#action_overlay_spinner');
    var content = actionModal.find('#action_overlay_content');
    var error = actionModal.find('#action_overlay_error');

    var page = 1;
    var fields = [];
    var results = {
        'header': {
            'updated_since': '',
            'products_total': 0,
        },
        'insert_success': [],
        'insert_error': [],
        'simple': [],
        'with_attributes': [],
        'update_error': [],
        'not_found': [],
    };
    var since = syncProductsModal.find('[name=sync_since]').val() || '';
    var url = currentPageAction + '&operation=syncProducts&ajax=true';

    var resetActionModel = () => {
        content.html('').hide();
        closeButton.hide();
        error.hide();
        spinner.show();
        actionButton.trigger('click');
    }
    var toogleContent = () => {
        spinner.fadeOut(100, function () {
            content.fadeIn(200);
        });
    }
    var updateContent = (overlayContent) => {
        content.html(overlayContent);
    }
    var appendResults = (requestResults) => {
        if (requestResults.header) {
            results.header.products_total += requestResults.header.products_total;
            results.header.updated_since = requestResults.header.updated_since;
        }

        if (requestResults.with_attributes) {
            results.with_attributes = results.with_attributes.concat(Object.values(requestResults.with_attributes));
        }

        if (requestResults.update_error) {
            results.update_error = results.update_error.concat(Object.values(requestResults.update_error));
        }

        if (requestResults.simple) {
            results.simple = results.simple.concat(Object.values(requestResults.simple));
        }

        if (requestResults.insert_success) {
            results.insert_success = results.insert_success.concat(Object.values(requestResults.insert_success));
        }

        if (requestResults.insert_error) {
            results.insert_error = results.insert_error.concat(Object.values(requestResults.insert_error));
        }
    }
    var showResults = () => {
        console.log(results);

        var resultsHolder = $('#sync_products_results');

        var syncProductsHeader = resultsHolder.find('#sync_products_header');
        var syncProductsWith_attributes = resultsHolder.find('#sync_products_with_attributes');
        var syncProductsUpdateError = resultsHolder.find('#sync_products_update_error');
        var syncProductsSimple = resultsHolder.find('#sync_products_simple');
        var syncProductsInsertSuccess = resultsHolder.find('#sync_products_insert_success');
        var syncProductsInsertError = resultsHolder.find('#sync_products_insert_error');

        syncProductsHeader.hide();
        syncProductsWith_attributes.hide();
        syncProductsUpdateError.hide();
        syncProductsSimple.hide();
        syncProductsInsertSuccess.hide();
        syncProductsInsertError.hide();

        if (results.header) {
            var html = '';

            html += '<b>Products updated since: ' + results.header.updated_since + '</b>';
            html += '<b>(Found: ' + results.header.products_total + ')</b>';

            syncProductsHeader.html(html);
            syncProductsHeader.show();
        }

        if (results.with_attributes.length) {
            var tableBody = syncProductsWith_attributes.find('tbody');

            tableBody = $(tableBody);
            tableBody.html('');

            $.each(results.with_attributes, function (index, entry) {
                var html = '';

                html += '<tr>';
                html += '   <td>' + entry.reference + '</td>';
                html += '   <td>';

                if (entry.name_before) {
                    html += '<b>Name:</b>';
                    html += entry.name_before;
                    html += '<br>';
                }

                if (entry.description_before) {
                    html += '<b>Description:</b>';
                    html += entry.description_before;
                    html += '<br>';
                }

                if (entry.price_before) {
                    html += '<b>Price:</b>';
                    html += entry.price_before;
                    html += '<br>';
                }

                if (entry.stock_before) {
                    html += '<b>Stock:</b>';
                    html += entry.stock_before;
                    html += '<br>';
                }

                if (entry.ean_before) {
                    html += '<b>EAN:</b>';
                    html += entry.ean_before;
                    html += '<br>';
                }

                if (entry.tax_before) {
                    html += '<b>Tax group:</b>';
                    html += entry.tax_before;
                    html += '<br>';
                }

                if (entry.image_before) {
                    html += '<b>Image:</b>';
                    html += entry.image_before;
                    html += '<br>';
                }

                html += '   </td>';
                html += '   <td>';

                if (entry.name_after) {
                    html += '<b>Name:</b>';
                    html += entry.name_after;
                    html += '<br>';
                }

                if (entry.description_after) {
                    html += '<b>Description:</b>';
                    html += entry.description_after;
                    html += '<br>';
                }

                if (entry.price_after) {
                    html += '<b>Price:</b>';
                    html += entry.price_after;
                    html += '<br>';
                }

                if (entry.stock_after) {
                    html += '<b>Stock:</b>';
                    html += entry.stock_after;
                    html += '<br>';
                }

                if (entry.ean_after) {
                    html += '<b>EAN:</b>';
                    html += entry.ean_after;
                    html += '<br>';
                }

                if (entry.tax_after) {
                    html += '<b>Tax group:</b>';
                    html += entry.tax_after;
                    html += '<br>';
                }

                if (entry.image_after) {
                    html += '<b>Image:</b>';
                    html += entry.image_after;
                    html += '<br>';
                }

                html += '   </td>';
                html += '   <td>';

                if (entry.stock_total) {
                    html += entry.stock_total;
                }

                html += '   </td>';
                html += '</tr>';

                tableBody.append($(html));
            });

            syncProductsWith_attributes.show();
        }

        if (results.update_error.length) {
            var tableBody = syncProductsUpdateError.find('tbody');

            tableBody = $(tableBody);
            tableBody.html('');

            $.each(results.update_error, function (index, entry) {
                var html = '';

                html += '<tr>';
                html += '   <td>' + entry.reference + '</td>';
                html += '   <td>' + entry.stock_before + '</td>';
                html += '   <td>' + entry.stock_after + '</td>';
                html += '   <td>' + entry.stock_total + '</td>';
                html += '</tr>';

                tableBody.append($(html));
            });

            syncProductsUpdateError.show();
        }

        if (results.simple.length) {
            var tableBody = syncProductsSimple.find('tbody');

            tableBody = $(tableBody);
            tableBody.html('');

            $.each(results.simple, function (index, entry) {
                var html = '';

                html += '<tr>';
                html += '   <td>' + entry.reference + '</td>';
                html += '   <td>';

                if (entry.name_before) {
                    html += '<b>Name:</b>';
                    html += entry.name_before;
                    html += '<br>';
                }

                if (entry.description_before) {
                    html += '<b>Description:</b>';
                    html += entry.description_before;
                    html += '<br>';
                }

                if (entry.price_before) {
                    html += '<b>Price:</b>';
                    html += entry.price_before;
                    html += '<br>';
                }

                if (entry.stock_before) {
                    html += '<b>Stock:</b>';
                    html += entry.stock_before;
                    html += '<br>';
                }

                if (entry.ean_before) {
                    html += '<b>EAN:</b>';
                    html += entry.ean_before;
                    html += '<br>';
                }

                if (entry.tax_before) {
                    html += '<b>Tax group:</b>';
                    html += entry.tax_before;
                    html += '<br>';
                }

                if (entry.image_before) {
                    html += '<b>Image:</b>';
                    html += entry.image_before;
                    html += '<br>';
                }

                html += '   </td>';
                html += '   <td>';

                if (entry.name_after) {
                    html += '<b>Name:</b>';
                    html += entry.name_after;
                    html += '<br>';
                }

                if (entry.description_after) {
                    html += '<b>Description:</b>';
                    html += entry.description_after;
                    html += '<br>';
                }

                if (entry.price_after) {
                    html += '<b>Price:</b>';
                    html += entry.price_after;
                    html += '<br>';
                }

                if (entry.stock_after) {
                    html += '<b>Stock:</b>';
                    html += entry.stock_after;
                    html += '<br>';
                }

                if (entry.ean_after) {
                    html += '<b>EAN:</b>';
                    html += entry.ean_after;
                    html += '<br>';
                }

                if (entry.tax_after) {
                    html += '<b>Tax group:</b>';
                    html += entry.tax_after;
                    html += '<br>';
                }

                if (entry.image_after) {
                    html += '<b>Image:</b>';
                    html += entry.image_after;
                    html += '<br>';
                }

                html += '   </td>';
                html += '</tr>';

                tableBody.append($(html));
            });

            syncProductsSimple.show();
        }

        if (results.insert_success.length) {
            var tableBody = syncProductsInsertSuccess.find('tbody');

            tableBody = $(tableBody);
            tableBody.html('');

            $.each(results.insert_success, function (index, entry) {
                var html = '';

                html += '<tr>';
                html += '   <td>' + entry.reference + '</td>';
                html += '   <td>' + entry.name + '</td>';
                html += '   <td>' + entry.price + '</td>';
                html += '</tr>';

                tableBody.append($(html));
            });

            syncProductsInsertSuccess.show();
        }

        if (results.insert_error.length) {
            var tableBody = syncProductsInsertError.find('tbody');

            tableBody = $(tableBody);
            tableBody.html('');

            $.each(results.insert_error, function (index, entry) {
                var html = '';

                html += '<tr>';
                html += '   <td>' + entry.reference + '</td>';
                html += '   <td>' + entry.name + '</td>';
                html += '   <td>' + entry.price + '</td>';
                html += '</tr>';

                tableBody.append($(html));
            });

            syncProductsInsertError.show();
        }

        resultsHolder.show(200);
    }
    var sync = async () => {
        var body = {
            page,
            sync_fields: fields,
            since
        };

        var response = await fetch(url + '&' + (new URLSearchParams(body)).toString());
        var jsonData = await response.json();

        if (page === 1) {
            toogleContent();
        }

        updateContent(jsonData.overlayContent || '');
        appendResults(jsonData.results || {});

        if (jsonData.hasMore && actionModal.is(':visible')) {
            page = page + 1;

            return await sync();
        }
    }

    syncProductsModal.find('input:checked').each(function (index, elem) {
        fields.push(elem.id);
    });

    try {
        resetActionModel();

        await sync();
    } catch (ex) {
        spinner.fadeOut(50);
        content.fadeOut(50);
        error.fadeIn(200);
    }

    showResults();
    closeButton.show(200);
});
