jQuery(document).ready(function($) {

    /* =============================================
       UPLOAD PDF
    ============================================= */
    var pdfFrame;

    $('#andorinha_upload_pdf_btn').on('click', function(e) {
        e.preventDefault();

        if (pdfFrame) {
            pdfFrame.open();
            return;
        }

        pdfFrame = wp.media({
            title: 'Selecionar PDF do Projeto',
            button: { text: 'Usar este PDF' },
            library: { type: 'application/pdf' },
            multiple: false
        });

        pdfFrame.on('select', function() {
            var attachment = pdfFrame.state().get('selection').first().toJSON();
            $('#andorinha_projeto_pdf').val(attachment.url);
            $('#andorinha_pdf_preview').html('<a href="' + attachment.url + '" target="_blank">📄 ' + (attachment.filename || 'arquivo.pdf') + '</a>');
            $('#andorinha_remove_pdf_btn').show();
        });

        pdfFrame.open();
    });

    $('#andorinha_remove_pdf_btn').on('click', function(e) {
        e.preventDefault();
        $('#andorinha_projeto_pdf').val('');
        $('#andorinha_pdf_preview').empty();
        $(this).hide();
    });

    /* =============================================
       UPLOAD GALERIA DE FOTOS
    ============================================= */
    var galeriaFrame;

    $('#andorinha_upload_galeria_btn').on('click', function(e) {
        e.preventDefault();

        // Sempre reabrir para permitir novas seleções
        galeriaFrame = wp.media({
            title: 'Selecionar Fotos do Projeto',
            button: { text: 'Adicionar à Galeria' },
            library: { type: 'image' },
            multiple: 'add'
        });

        galeriaFrame.on('select', function() {
            var selection = galeriaFrame.state().get('selection');
            var currentIds = $('#andorinha_projeto_galeria').val()
                ? $('#andorinha_projeto_galeria').val().split(',').filter(function(v){ return v !== ''; })
                : [];

            selection.each(function(attachment) {
                attachment = attachment.toJSON();
                var idStr = attachment.id.toString();

                if (currentIds.indexOf(idStr) === -1) {
                    currentIds.push(idStr);
                    var thumb = (attachment.sizes && attachment.sizes.thumbnail)
                        ? attachment.sizes.thumbnail.url
                        : attachment.url;

                    $('#andorinha_galeria_container').append(
                        '<div class="andorinha-galeria-item" data-id="' + idStr + '">' +
                        '<img src="' + thumb + '" />' +
                        '<button type="button" class="remove-img">&times;</button>' +
                        '</div>'
                    );
                }
            });

            $('#andorinha_projeto_galeria').val(currentIds.join(','));
        });

        galeriaFrame.open();
    });

    /* =============================================
       REMOVER FOTO DA GALERIA
    ============================================= */
    $(document).on('click', '.andorinha-galeria-item .remove-img', function(e) {
        e.preventDefault();
        var item = $(this).closest('.andorinha-galeria-item');
        var idToRemove = item.data('id').toString();
        item.remove();

        var currentIds = $('#andorinha_projeto_galeria').val()
            ? $('#andorinha_projeto_galeria').val().split(',').filter(function(v){ return v !== ''; })
            : [];
        var newIds = currentIds.filter(function(id) { return id !== idToRemove; });
        $('#andorinha_projeto_galeria').val(newIds.join(','));
    });

});
