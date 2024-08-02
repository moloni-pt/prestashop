if (!pt.moloni.PendingOrders.Overlays) {
    pt.moloni.PendingOrders.Overlays = {}
}

pt.moloni.PendingOrders.Overlays.ProcessOrder = (async function (currentPageAction, actionBulk) {
    var pendingDocs = $('.pending_doc:checked')
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
            'generated_documents': 0,
            'cancel_documents': 0,
        },
    };
    var url = currentPageAction + '&operation=' + actionBulk + '&ajax=true';

    var resetActionModel = () => {
        content.html('Julho').hide();
        closeButton.hide();
        error.hide();
        spinner.show();
        actionButton.trigger('click');
    }
    var updateContent = (overlayContent) => {
        content.html(overlayContent);
    }

    var appendResults = (requestResults) => {
        if (requestResults.header && actionBulk === 'generate_document') {
            results.header.generated_documents += requestResults.header.generated_documents;
        }

        if (requestResults.header && actionBulk === 'delete_document') {
            results.header.generated_documents += requestResults.header.cancel_documents;
        }
    }

    var showResults = () => {
        console.log(results);
    }


    var processOrder = async () => {

        var body = {
            field_to_process: fields.pop(),
            has_more : fields.length,
        };

        var response = await fetch(url + '&' + (new URLSearchParams(body)).toString());
        var jsonData = await response.json();

        //updateContent(jsonData.overlayContent || '');
        appendResults(jsonData.results || {});

        if (jsonData.has_more && actionModal.is(':visible')) {
            return await processOrder();
        } else {
            actionBulk = null;
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
