jQuery(function ($) {
    'use strict';

    const settings = window.JSINFO && JSINFO.plugins && JSINFO.plugins.dw2pdf;
    if (!settings || settings.templates.length < 2 || !$.fn.dialog) return;

    $(document).on('click.dw2pdf', 'a[href]', function (event) {
        // Keep modified clicks, explicit templates and other export integrations intact.
        if (event.isDefaultPrevented() || (event.which && event.which !== 1)
            || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
            || (this.target && this.target !== '_self') || this.hasAttribute('download')) return;

        const url = new URL(this.href, window.location.href);
        if (url.origin !== window.location.origin || url.searchParams.has('tpl')
            || !['export_pdf', 'export_pdfns'].includes(url.searchParams.get('do'))) return;

        event.preventDefault();
        if ($('#dw2pdf-template-dialog').length) return;

        const trigger = this;
        const form = $('<form id="dw2pdf-template-dialog"/>');
        $('<label for="dw2pdf-template"/>').text(settings.label).appendTo(form);
        const select = $('<select id="dw2pdf-template" name="tpl" class="form-control"/>').appendTo(form);
        settings.templates.forEach(function (name) {
            $('<option/>').val(name).text(name).appendTo(select);
        });
        select.val(settings.templates.includes(settings.defaultTemplate)
            ? settings.defaultTemplate : settings.templates[0]);

        function exportPdf() {
            // Retain the page, revision, namespace and all other export options.
            url.searchParams.set('tpl', select.val());
            form.dialog('close');
            window.location.assign(url.href);
        }

        form.on('submit', function (event) {
            event.preventDefault();
            exportPdf();
        }).appendTo('body').dialog({
            title: settings.title,
            classes: {'ui-dialog': 'dw2pdf-export-dialog'},
            modal: true,
            width: Math.min(420, window.innerWidth - 32),
            resizable: false,
            closeText: settings.cancel,
            buttons: [
                {text: settings.cancel, class: 'btn btn-default', click: function () { form.dialog('close'); }},
                {text: settings.export, class: 'btn btn-primary', click: exportPdf}
            ],
            close: function () {
                form.dialog('destroy').remove();
                trigger.focus();
            }
        });
    });
});
