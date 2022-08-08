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
        $('select').material_select();

        $('.datepicker').pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            formatSubmit: "yyyy-mm-dd",
            format: 'yyyy-mm-dd'
        });

        $('#formSubmit').on('click', function () {
            $("#moloniOptions").submit();
        });

        $('#formToolsSubmit').on('click', function () {
            $("#moloniTools").submit();
        });

        $('.message_success').on('click', function () {
            $(this).hide("slow");
        });

        $('.collapsible').collapsible();
    }

    function drawSaveButton() {
        var html = '';

        html += '<li>';
        html += '   <div class="formSave">';
        html += '       <a class="waves-effect waves-light red btn-large" id="formSubmit">';
        html += '           ' + translations.save_changes;
        html += '       </a>';
        html += '   </div>';
        html += '</li>';

        $('#toolbar-nav').html(html);
    }

    return {
        init: init,
    }
}(jQuery));

