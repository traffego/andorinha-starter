jQuery(document).ready(function($) {

    /* =============================================
       SELETOR DE TIPO: PROJETO / EVENTO
    ============================================= */
    function updateTipoUI() {
        var tipo = $('#andorinha_tipo_hidden').val() || 'projeto';
        $('.andorinha-tipo-btn').removeClass('active');
        $('.andorinha-tipo-btn[data-tipo="' + tipo + '"]').addClass('active');
        var labelNome = tipo === 'evento' ? 'Descrição do Evento' : 'Descrição do Projeto';
        $('.andorinha-label-nome').text(labelNome);
    }
    $(document).on('click', '.andorinha-tipo-btn', function() {
        $('#andorinha_tipo_hidden').val($(this).data('tipo'));
        updateTipoUI();
    });
    updateTipoUI();


    /* =============================================
       TABS DE VÍDEO
    ============================================= */
    $(document).on('click', '.andorinha-video-tab', function() {
        var panel = $(this).data('panel');
        $('.andorinha-video-tab').removeClass('active');
        $(this).addClass('active');
        $('.andorinha-video-panel').removeClass('active');
        $('#andorinha_video_panel_' + panel).addClass('active');
    });


    /* =============================================
       UPLOAD PDF
    ============================================= */
    var pdfFrame;
    $('#andorinha_upload_pdf_btn').on('click', function(e) {
        e.preventDefault();
        if (pdfFrame) { pdfFrame.open(); return; }
        pdfFrame = wp.media({
            title: 'Selecionar PDF',
            button: { text: 'Usar este PDF' },
            library: { type: 'application/pdf' },
            multiple: false
        });
        pdfFrame.on('select', function() {
            var a = pdfFrame.state().get('selection').first().toJSON();
            $('#andorinha_projeto_pdf').val(a.url);
            $('#andorinha_pdf_preview').html('<a href="' + a.url + '" target="_blank">PDF: ' + (a.filename || 'arquivo.pdf') + '</a>');
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
       UPLOAD VÍDEO (arquivo mp4)
    ============================================= */
    var videoFrame;
    $('#andorinha_upload_video_btn').on('click', function(e) {
        e.preventDefault();
        if (videoFrame) { videoFrame.open(); return; }
        videoFrame = wp.media({
            title: 'Selecionar Vídeo',
            button: { text: 'Usar este Vídeo' },
            library: { type: 'video' },
            multiple: false
        });
        videoFrame.on('select', function() {
            var a = videoFrame.state().get('selection').first().toJSON();
            $('#andorinha_video_arquivo').val(a.url);
            $('#andorinha_video_preview').html('<video src="' + a.url + '" controls style="max-height:160px;max-width:100%;border-radius:6px;margin-top:8px;"></video>');
            $('#andorinha_remove_video_btn').show();
        });
        videoFrame.open();
    });
    $('#andorinha_remove_video_btn').on('click', function(e) {
        e.preventDefault();
        $('#andorinha_video_arquivo').val('');
        $('#andorinha_video_preview').empty();
        $(this).hide();
    });


    /* =============================================
       UPLOAD GALERIA DE FOTOS
    ============================================= */
    $('#andorinha_upload_galeria_btn').on('click', function(e) {
        e.preventDefault();
        var galeriaFrame = wp.media({
            title: 'Selecionar Fotos',
            button: { text: 'Adicionar à Galeria' },
            library: { type: 'image' },
            multiple: 'add'
        });
        galeriaFrame.on('select', function() {
            var selection = galeriaFrame.state().get('selection');
            var currentIds = $('#andorinha_projeto_galeria').val()
                ? $('#andorinha_projeto_galeria').val().split(',').filter(function(v) { return v !== ''; })
                : [];
            selection.each(function(attachment) {
                attachment = attachment.toJSON();
                var idStr = attachment.id.toString();
                if (currentIds.indexOf(idStr) === -1) {
                    currentIds.push(idStr);
                    var thumb = (attachment.sizes && attachment.sizes.thumbnail)
                        ? attachment.sizes.thumbnail.url : attachment.url;
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
            ? $('#andorinha_projeto_galeria').val().split(',').filter(function(v) { return v !== ''; })
            : [];
        $('#andorinha_projeto_galeria').val(
            currentIds.filter(function(id) { return id !== idToRemove; }).join(',')
        );
    });

});
