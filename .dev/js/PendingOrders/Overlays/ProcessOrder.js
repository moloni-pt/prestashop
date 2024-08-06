if (!pt.moloni.PendingOrders.Overlays) {
    pt.moloni.PendingOrders.Overlays = {}
}

pt.moloni.PendingOrders.Overlays.ProcessOrder = (async function (currentPageAction, actionBulk, table) {
    var pendingDocs = $('.pending_doc:checked')
    var actionButton = $('#action_overlay_button');
    var actionModal = $('#action_overlay_modal');
    var closeButton = actionModal.find('#action_overlay_button');
    var spinner = actionModal.find('#action_overlay_spinner');
    var content = actionModal.find('#action_overlay_content');
    var error = actionModal.find('#action_overlay_error');
    var orderPosition = 0;
    var processedDocuments = 1;

    var fields = [];
    var results = {
        'header': {
            'generated_documents': 0,
            'cancel_documents': 0,
        },
        'message':{
            'success':{}
        }
    };
    var url = currentPageAction + '&operation=' + actionBulk + '&ajax=true';

    var resetActionModel = () => {
        content.html('').hide();
        closeButton.on('click', function(){
           table._fnAjaxUpdate();
        }).hide();
        error.hide();
        spinner.show();
        actionButton.trigger('click');
    }
    var updateContent = (overlayContent) => {
        content.html(overlayContent);
    }

    var appendResults = (requestResults) => {
        results.header.generated_documents += requestResults.results.generated_documents;
        results.header.cancel_documents += requestResults.results.cancel_documents;

        results.message.success[orderPosition] = requestResults.success;
        orderPosition++;
    }

    var drawResults = () => {
        $.each(results.message.success
            , function(key, value) {
                var html = '<div> Order Id: ' + value.orderId + ' - '
                    + value.message + ' - ' +
                    ' <a class="" ' +
                    ' href="' + value.url + '" ' +
                    ' target="' + value.tab + '">' + value.button +'</a>' +
                    '</div>'

                console.log('Key: ' + key + ', Value: ' + value);
                return content.find('.order_processed').append(html);
            });
    }

    var showResults = () => {
        drawResults();
    }

    var processOrder = async () => {
        var body = {
            field_to_process: fields.pop(),
            has_more: fields.length,
            processed_documents: processedDocuments
        };

        var response = await fetch(url + '&' + (new URLSearchParams(body)).toString());
        var jsonData = await response.json();

        spinner.fadeOut(100, function() {
            content.fadeIn(200);
        });

        updateContent(jsonData.overlayContent || '');
        appendResults(jsonData || {});

        if (parseInt(jsonData.has_more) && actionModal.is(':visible')) {
            processedDocuments++;
            return await processOrder();
        }
    }

    pendingDocs.each(function (index, elem) {
        fields.push(elem.value);
    });

    try {
        resetActionModel();

        await processOrder();
    } catch (ex) {
        spinner.fadeOut(50);
        content.fadeOut(50);
        error.fadeIn(200);
    }

    showResults();
    closeButton.show(200);
});
