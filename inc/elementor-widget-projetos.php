<?php
/**
 * Elementor Widget & Shortcode: Lista de Projetos Realizados
 * com Modal de Detalhes
 *
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =============================================
// 1. HELPER: obter imagem do projeto
// =============================================
function andorinha_get_projeto_image( $post_id, $size = 'medium_large' ) {
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, $size );
    }
    $galeria_ids = get_post_meta( $post_id, '_andorinha_projeto_galeria', true );
    if ( ! empty( $galeria_ids ) ) {
        $ids = array_filter( explode( ',', $galeria_ids ) );
        if ( ! empty( $ids ) ) {
            $url = wp_get_attachment_image_url( trim( reset( $ids ) ), $size );
            if ( $url ) return $url;
        }
    }
    return '';
}

// =============================================
// 1b. HELPER: converter URL de vídeo em embed
// =============================================
function andorinha_get_video_embed( $url ) {
    if ( empty( $url ) ) return '';

    // YouTube
    if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/', $url, $m ) ) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&controls=1';
    }
    // Vimeo
    if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    // Arquivo direto
    return $url;
}

// =============================================
// 2. CSS: GRID + CARDS + MODAL
// =============================================
function andorinha_projetos_widget_css() {
    static $printed = false;
    if ( $printed ) return;
    $printed = true;
    ?>
    <style>
    /* ---- Grid ---- */
    .andorinha-projetos-grid { display:flex; flex-wrap:wrap; margin:-12px; }
    .andorinha-col { padding:12px; box-sizing:border-box; }
    .andorinha-col-2 { width:50%; }
    .andorinha-col-3 { width:33.333%; }
    .andorinha-col-4 { width:25%; }
    @media (max-width:900px){ .andorinha-col-3,.andorinha-col-4{ width:50%; } }
    @media (max-width:600px){ .andorinha-col-2,.andorinha-col-3,.andorinha-col-4{ width:100%; } }

    /* ---- Card ---- */
    .andorinha-projeto-card {
        border-radius:12px; overflow:hidden;
        box-shadow:0 3px 16px rgba(0,0,0,.10);
        background:#fff; display:flex; flex-direction:column; height:100%;
        transition:transform .25s, box-shadow .25s;
        font-family:'Epilogue',sans-serif;
    }
    .andorinha-projeto-card:hover {
        transform:translateY(-5px);
        box-shadow:0 12px 32px rgba(0,0,0,.14);
    }
    .andorinha-card-img-wrap {
        position:relative; height:210px; overflow:hidden; background:#f4f4f4;
    }
    .andorinha-card-img-wrap img {
        width:100%; height:100%; object-fit:cover; display:block;
        transition:transform .35s;
    }
    .andorinha-projeto-card:hover .andorinha-card-img-wrap img { transform:scale(1.05); }
    .andorinha-card-no-img {
        height:100%; display:flex; align-items:center; justify-content:center;
        background:linear-gradient(135deg,#f0f3f8,#e8ecf4); color:#b0b8c9; font-size:52px;
    }
    .andorinha-tipo-badge {
        position:absolute; top:12px; left:12px;
        display:inline-flex; align-items:center; gap:5px;
        color:#fff; font-size:11px; font-weight:700; letter-spacing:.4px;
        padding:4px 12px; border-radius:20px;
        font-family:'Epilogue',sans-serif;
    }
    .andorinha-tipo-badge i { font-size:10px; }
    .andorinha-card-body { padding:20px; display:flex; flex-direction:column; flex:1; }
    .andorinha-card-title {
        font-size:1.05rem; font-weight:700; margin:0 0 10px; line-height:1.35;
        font-family:'Epilogue',sans-serif; color:#1a1a2e;
    }
    .andorinha-card-desc {
        font-size:.92rem; color:#555; line-height:1.65; margin:0 0 14px; flex-grow:1;
    }
    .andorinha-card-meta {
        list-style:none; margin:0 0 16px; padding:0;
        border-top:1px solid #f0f0f0; padding-top:13px;
    }
    .andorinha-card-meta li {
        display:flex; align-items:flex-start; gap:7px;
        font-size:.82rem; margin-bottom:6px; line-height:1.4; color:#444;
    }
    .andorinha-card-meta li i { font-size:12px; color:#020873; margin-top:2px; flex-shrink:0; width:14px; text-align:center; }
    .andorinha-meta-label { font-weight:700; color:#666; white-space:nowrap; min-width:90px; }
    .andorinha-meta-value { color:#232323; }
    .andorinha-card-actions {
        display:flex; gap:8px; align-items:center;
        margin-top:auto; padding-top:14px; border-top:1px solid #f0f0f0;
    }
    .andorinha-btn-modal {
        display:inline-flex; align-items:center; gap:6px;
        padding:9px 20px; background:#020873; color:#fff !important;
        border-radius:6px; font-size:.84rem; font-weight:700;
        text-decoration:none; cursor:pointer; border:none;
        transition:background .2s, transform .15s;
        font-family:'Epilogue',sans-serif; letter-spacing:.2px;
    }
    .andorinha-btn-modal:hover { background:#030a8c; transform:scale(1.03); }
    .andorinha-btn-pdf-card {
        display:inline-flex; align-items:center; gap:6px;
        padding:9px 14px; background:#fef2f2; color:#c0392b !important;
        border-radius:6px; font-size:.84rem; font-weight:700;
        text-decoration:none; border:1px solid #fecaca;
        transition:background .2s;
        font-family:'Epilogue',sans-serif;
    }
    .andorinha-btn-pdf-card:hover { background:#fee2e2; }

    /* ---- Carrossel ---- */
    .andorinha-carousel-outer { position:relative; display:flex; align-items:center; gap:10px; }
    .andorinha-carousel-wrap { overflow:hidden; flex:1; min-width:0; }
    .andorinha-carousel-track { display:flex; transition:transform .45s cubic-bezier(.4,0,.2,1); will-change:transform; }
    .andorinha-carousel-track .andorinha-col { flex-shrink:0; }
    .andorinha-carousel-btn {
        flex-shrink:0; width:46px; height:46px; border-radius:50%;
        background:#020873; color:#fff; border:none; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        font-size:15px; transition:background .2s, transform .15s;
        box-shadow:0 4px 14px rgba(2,8,115,.28);
    }
    .andorinha-carousel-btn:hover { background:#030a8c; transform:scale(1.08); }
    .andorinha-carousel-btn:disabled { background:#d0d4e8; box-shadow:none; cursor:default; transform:none; }
    .andorinha-carousel-dots { display:flex; justify-content:center; gap:7px; margin-top:16px; }
    .andorinha-carousel-dot {
        width:8px; height:8px; border-radius:4px; background:#d0d4e8;
        border:none; cursor:pointer; padding:0;
        transition:background .25s, width .25s;
    }
    .andorinha-carousel-dot.and-dot-active { background:#020873; width:22px; }

    /* ---- Navegação entre projetos na modal ---- */
    .and-modal-nav {
        position:absolute; top:50%; transform:translateY(-50%);
        width:50px; height:50px; border-radius:50%; border:none; cursor:pointer;
        background:#fff; color:#020873;
        display:none; align-items:center; justify-content:center;
        font-size:18px; box-shadow:0 4px 20px rgba(0,0,0,.22);
        transition:background .2s, box-shadow .2s; z-index:2;
    }
    .and-modal-nav.and-nav-show { display:flex; }
    .and-modal-nav:hover { background:#eef0ff; box-shadow:0 6px 24px rgba(0,0,0,.28); }
    .and-modal-nav-prev { left:-66px; }
    .and-modal-nav-next { right:-66px; }
    @media (max-width:1100px){
        .and-modal-nav-prev { left:-46px; }
        .and-modal-nav-next { right:-46px; }
    }
    @media (max-width:900px){ .and-modal-nav { display:none !important; } }

    /* ============================================================
       MODAL
    ============================================================ */
    .and-modal-overlay {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(5,8,50,.72);
        align-items:center; justify-content:center;
        padding:20px; box-sizing:border-box;
    }
    /* Blur via pseudo-element para NÃO criar stacking context no overlay */
    .and-modal-overlay::before {
        content:''; position:fixed; inset:0; z-index:-1;
        pointer-events:none;
    }
    .and-modal-overlay.and-open { display:flex; }

    .and-modal {
        background:#fff; border-radius:18px;
        width:100%; max-width:820px; max-height:90vh;
        overflow:hidden; display:flex; flex-direction:column;
        box-shadow:0 40px 100px rgba(0,0,0,.45);
        animation:andModalIn .35s cubic-bezier(.22,.68,0,1.2) forwards;
        position:relative; font-family:'Epilogue',sans-serif;
    }
    @keyframes andModalIn {
        from { opacity:0; transform:scale(.88) translateY(30px); }
        to   { opacity:1; transform:none; }
    }
    .and-modal-close {
        position:absolute; top:16px; right:18px; z-index:10;
        background:rgba(255,255,255,.9); border:none; border-radius:50%;
        width:36px; height:36px; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; color:#555; box-shadow:0 2px 8px rgba(0,0,0,.18);
        transition:background .2s, transform .15s;
    }
    .and-modal-close:hover { background:#fff; color:#020873; transform:scale(1.1); }

    /* Hero image do modal */
    .and-modal-hero {
        position:relative; height:260px; overflow:hidden; flex-shrink:0;
        background:linear-gradient(135deg,#020873,#149dcc);
    }
    .and-modal-hero img {
        width:100%; height:100%; object-fit:cover; display:block; opacity:.92;
    }
    .and-modal-hero-overlay {
        position:absolute; inset:0;
        background:linear-gradient(to top, rgba(2,8,115,.85) 0%, transparent 60%);
    }
    .and-modal-hero-content {
        position:absolute; bottom:0; left:0; right:0; padding:24px 28px;
    }
    .and-modal-badge {
        display:inline-flex; align-items:center; gap:5px;
        color:#fff; font-size:11px; font-weight:700; padding:4px 12px;
        border-radius:20px; margin-bottom:10px; letter-spacing:.4px;
    }
    .and-modal-badge i { font-size:10px; }
    .and-modal-title {
        font-size:1.55rem; font-weight:800; color:#fff;
        line-height:1.25; margin:0; text-shadow:0 2px 6px rgba(0,0,0,.25);
    }

    /* Corpo do modal */
    .and-modal-body { padding:28px; overflow-y:auto; flex:1; }

    .and-modal-desc {
        font-size:.97rem; color:#444; line-height:1.75;
        margin:0 0 22px; border-bottom:1px solid #f0f0f0; padding-bottom:20px;
    }
    .and-modal-desc:empty,
    .and-modal-desc[style*="display:none"] { margin:0; padding:0; border:none; }

    /* Fields grid */
    .and-modal-fields { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:24px; }
    @media (max-width:560px){ .and-modal-fields { grid-template-columns:1fr; } }
    .and-modal-field {
        background:#f8f9fc; border-radius:8px; padding:12px 14px;
        border-left:3px solid #020873;
    }
    .and-modal-field-label {
        display:flex; align-items:center; gap:6px;
        font-size:.75rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#020873; margin-bottom:5px;
    }
    .and-modal-field-label i { font-size:11px; }
    .and-modal-field-value { font-size:.93rem; color:#232323; font-weight:600; }

    /* Galeria modal */
    .and-modal-gallery-title {
        font-size:.78rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.6px; color:#888; margin:0 0 12px;
        display:flex; align-items:center; gap:7px;
    }
    .and-modal-gallery-title::after {
        content:''; flex:1; height:1px; background:#eee;
    }
    .and-modal-gallery { display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:8px; margin-bottom:24px; }
    .and-modal-gallery-item {
        display:block; border-radius:6px; overflow:hidden; aspect-ratio:1;
        cursor:pointer; position:relative;
    }
    .and-modal-gallery-item img {
        width:100%; height:100%; object-fit:cover; display:block;
        transition:transform .3s, opacity .3s;
    }
    .and-modal-gallery-item:hover img { transform:scale(1.08); opacity:.88; }
    .and-modal-gallery-item::after {
        content:'\f00e'; font-family:'Font Awesome 6 Free'; font-weight:900;
        position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:20px; background:rgba(0,0,0,.35);
        opacity:0; transition:opacity .2s;
    }
    .and-modal-gallery-item:hover::after { opacity:1; }

    /* Viewer de imagem interno */
    .and-img-viewer {
        display:none; position:fixed; inset:0; z-index:999999;
        background:rgba(0,0,0,.92);
        align-items:center; justify-content:center;
        flex-direction:column;
    }
    .and-img-viewer.and-iv-open { display:flex; }
    .and-img-viewer img {
        max-width:90vw; max-height:82vh;
        border-radius:8px; object-fit:contain;
        box-shadow:0 20px 60px rgba(0,0,0,.6);
        animation:andIvIn .25s ease both;
    }
    @keyframes andIvIn {
        from { opacity:0; transform:scale(.92); }
        to   { opacity:1; transform:scale(1); }
    }
    .and-iv-close {
        position:absolute; top:18px; right:22px;
        background:rgba(255,255,255,.15); border:none; color:#fff;
        width:40px; height:40px; border-radius:50%; font-size:18px;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        transition:background .2s;
    }
    .and-iv-close:hover { background:rgba(255,255,255,.28); }
    .and-iv-nav {
        position:absolute; top:50%; transform:translateY(-50%);
        background:rgba(255,255,255,.15); border:none; color:#fff;
        width:44px; height:44px; border-radius:50%; font-size:18px;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        transition:background .2s;
    }
    .and-iv-nav:hover { background:rgba(255,255,255,.28); }
    .and-iv-prev { left:20px; }
    .and-iv-next { right:20px; }
    .and-iv-counter {
        margin-top:14px; color:rgba(255,255,255,.6);
        font-size:13px; font-family:'Epilogue',sans-serif;
    }

    /* Footer do modal */
    .and-modal-footer {
        padding:16px 28px; border-top:1px solid #f0f0f0;
        display:flex; align-items:center; gap:10px; flex-shrink:0;
        background:#fafafa;
    }
    .and-modal-btn-pdf {
        display:inline-flex; align-items:center; gap:7px;
        padding:11px 22px; background:#c0392b; color:#fff !important;
        border-radius:7px; font-size:.88rem; font-weight:700;
        text-decoration:none; transition:background .2s;
        font-family:'Epilogue',sans-serif;
    }
    .and-modal-btn-pdf:hover { background:#a93226; }
    .and-modal-btn-close {
        display:inline-flex; align-items:center; gap:7px;
        padding:11px 22px; background:#f0f0f0; color:#555 !important;
        border-radius:7px; font-size:.88rem; font-weight:700;
        cursor:pointer; border:none; transition:background .2s;
        font-family:'Epilogue',sans-serif;
    }
    .and-modal-btn-close:hover { background:#e0e0e0; }

    /* Vídeo na modal */
    .and-modal-video-wrap { margin-bottom:24px; }
    .and-modal-video-title {
        font-size:.78rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.6px; color:#888; margin:0 0 12px;
        display:flex; align-items:center; gap:7px;
    }
    .and-modal-video-title::after { content:''; flex:1; height:1px; background:#eee; }
    .and-modal-video-responsive {
        position:relative; padding-bottom:56.25%; height:0; overflow:hidden;
        border-radius:10px; background:#000;
    }
    .and-modal-video-responsive iframe,
    .and-modal-video-responsive video {
        position:absolute; top:0; left:0; width:100%; height:100%; border:none; border-radius:10px;
    }
    </style>
    <?php
}

// =============================================
// 3. HTML DA MODAL GLOBAL (renderizada 1x)
// =============================================
function andorinha_render_modal_html() {
    static $done = false;
    if ( $done ) return;
    $done = true;
    ?>
    <div class="and-modal-overlay" id="andModalOverlay" role="dialog" aria-modal="true">

        <!-- Navegação entre projetos -->
        <button class="and-modal-nav and-modal-nav-prev" id="andModalNavPrev" aria-label="Projeto anterior">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="and-modal" id="andModal">
            <button class="and-modal-close" id="andModalClose" aria-label="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Hero -->
            <div class="and-modal-hero" id="andModalHero"></div>

            <!-- Corpo -->
            <div class="and-modal-body" id="andModalBody">
                <p class="and-modal-desc" id="andModalDesc" style="display:none;"></p>
                <div class="and-modal-fields" id="andModalFields"></div>
                <div id="andModalGalleryWrap"></div>
            </div>

            <!-- Footer -->
            <div class="and-modal-footer" id="andModalFooter">
                <button class="and-modal-btn-close" id="andModalBtnClose">
                    <i class="fa-solid fa-xmark"></i> Fechar
                </button>
            </div>
        </div>

        <!-- Navegação entre projetos -->
        <button class="and-modal-nav and-modal-nav-next" id="andModalNavNext" aria-label="Próximo projeto">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

    </div>

    <!-- Viewer de imagem interno -->
    <div class="and-img-viewer" id="andImgViewer">
        <button class="and-iv-close" id="andIvClose"><i class="fa-solid fa-xmark"></i></button>
        <button class="and-iv-nav and-iv-prev" id="andIvPrev"><i class="fa-solid fa-chevron-left"></i></button>
        <img src="" id="andIvImg" alt="" />
        <button class="and-iv-nav and-iv-next" id="andIvNext"><i class="fa-solid fa-chevron-right"></i></button>
        <span class="and-iv-counter" id="andIvCounter"></span>
    </div>
    <?php
}

// =============================================
// 4. JS DA MODAL (renderizado 1x)
// =============================================
function andorinha_render_modal_js() {
    static $done = false;
    if ( $done ) return;
    $done = true;
    ?>
    <script>
    (function(){
        /* ---- ELEMENTOS ---- */
        var overlay   = document.getElementById('andModalOverlay');
        var modal     = document.getElementById('andModal');
        var hero      = document.getElementById('andModalHero');
        var desc      = document.getElementById('andModalDesc');
        var fields    = document.getElementById('andModalFields');
        var galWrap   = document.getElementById('andModalGalleryWrap');
        var footer    = document.getElementById('andModalFooter');
        var viewer    = document.getElementById('andImgViewer');
        var ivImg     = document.getElementById('andIvImg');
        var ivCounter = document.getElementById('andIvCounter');
        var ivPrev    = document.getElementById('andIvPrev');
        var ivNext    = document.getElementById('andIvNext');
        var navPrev   = document.getElementById('andModalNavPrev');
        var navNext   = document.getElementById('andModalNavNext');
        var ivItems   = [];
        var ivIndex   = 0;
        var allProjetos      = [];
        var currentProjetoIdx = -1;

        if (!overlay || !modal) return; // segurança

        /* ================================================================
           MODAL
        ================================================================ */
        function openModal(data) {
            // HERO
            var heroContent = '<div class="and-modal-hero-overlay"></div>';
            if (data.img) {
                hero.innerHTML = '<img src="'+data.img+'" alt="'+esc(data.title)+'" />'
                    + heroContent
                    + '<div class="and-modal-hero-content">'
                    + '<div class="and-modal-badge" style="background:'+data.tipo_bg+';">'
                    + '<i class="'+data.tipo_icon+'"></i> '+esc(data.tipo_label)
                    + '</div>'
                    + '<h2 class="and-modal-title">'+esc(data.title)+'</h2>'
                    + '</div>';
            } else {
                hero.innerHTML = '<div style="background:linear-gradient(135deg,#020873,#149dcc);height:100%;position:relative;">'
                    + '<div class="and-modal-hero-no-img"><i class="'+data.tipo_icon+'"></i></div>'
                    + heroContent
                    + '<div class="and-modal-hero-content">'
                    + '<div class="and-modal-badge" style="background:'+data.tipo_bg+';">'
                    + '<i class="'+data.tipo_icon+'"></i> '+esc(data.tipo_label)
                    + '</div>'
                    + '<h2 class="and-modal-title">'+esc(data.title)+'</h2>'
                    + '</div></div>';
            }

            // DESCRIÇÃO
            if (data.desc && data.desc.trim()) {
                desc.textContent = data.desc.trim();
                desc.style.display = '';
            } else {
                desc.textContent = '';
                desc.style.display = 'none';
            }

            // CAMPOS
            var fieldDefs = [
                { icon:'fa-solid fa-calendar-days',    label:'Data',             key:'data_label' },
                { icon:'fa-solid fa-file-contract',    label:'Termo de Fomento', key:'termo' },
                { icon:'fa-solid fa-barcode',          label:'Cód. Objeto',      key:'objeto' },
                { icon:'fa-solid fa-hashtag',          label:'Cód. Programa',    key:'programa' },
                { icon:'fa-solid fa-building-columns', label:'Programa',          key:'nome_programa' }
            ];
            var fieldsHtml = '';
            fieldDefs.forEach(function(f){
                if (data[f.key]) {
                    fieldsHtml += '<div class="and-modal-field">'
                        + '<div class="and-modal-field-label"><i class="'+f.icon+'"></i>'+f.label+'</div>'
                        + '<div class="and-modal-field-value">'+esc(data[f.key])+'</div>'
                        + '</div>';
                }
            });
            fields.innerHTML = fieldsHtml;
            fields.style.display = fieldsHtml ? '' : 'none';

            // VÍDEO + GALERIA
            galWrap.innerHTML = '';
            if (data.video) {
                var videoHtml = '<div class="and-modal-video-wrap">'
                    + '<p class="and-modal-video-title"><i class="fa-solid fa-circle-play" style="margin-right:6px;color:#020873;"></i>Vídeo</p>'
                    + '<div class="and-modal-video-responsive">';
                videoHtml += data.video_is_file
                    ? '<video src="'+data.video+'" controls></video>'
                    : '<iframe src="'+data.video+'" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
                videoHtml += '</div></div>';
                galWrap.innerHTML = videoHtml;
            }
            if (data.gallery && data.gallery.length) {
                var galHtml = '<p class="and-modal-gallery-title"><i class="fa-solid fa-images" style="margin-right:6px;color:#020873;"></i>Galeria de Fotos</p>'
                    + '<div class="and-modal-gallery">';
                data.gallery.forEach(function(g, idx){
                    galHtml += '<div class="and-modal-gallery-item" data-src="'+g.full+'" data-idx="'+idx+'">'
                        + '<img src="'+g.thumb+'" alt="" loading="lazy" />'
                        + '</div>';
                });
                galHtml += '</div>';
                galWrap.innerHTML += galHtml;
            }

            // FOOTER
            var footerHtml = '<button class="and-modal-btn-close" id="andModalBtnClose"><i class="fa-solid fa-xmark"></i> Fechar</button>';
            if (data.pdf) {
                footerHtml = '<a href="'+data.pdf+'" target="_blank" class="and-modal-btn-pdf">'
                    + '<i class="fa-solid fa-file-pdf"></i> Download PDF'
                    + '</a>' + footerHtml;
            }
            footer.innerHTML = footerHtml;
            document.getElementById('andModalBtnClose').addEventListener('click', closeModal);

            overlay.classList.add('and-open');
            document.body.style.overflow = 'hidden';
            modal.querySelector('.and-modal-body').scrollTop = 0;
            updateModalNav();
        }

        function closeModal() {
            overlay.classList.remove('and-open');
            document.body.style.overflow = '';
        }

        function esc(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        // Eventos da modal
        overlay.addEventListener('click', function(e){ if (e.target === overlay) closeModal(); });
        document.getElementById('andModalClose').addEventListener('click', closeModal);

        // Abrir modal ao clicar no botão
        document.addEventListener('click', function(e){
            var btn = e.target.closest('.andorinha-btn-modal');
            if (!btn) return;
            e.preventDefault();
            var raw = btn.getAttribute('data-projeto');
            if (!raw) return;
            try {
                var data = JSON.parse(raw);
                // Capturar lista de projetos do wrapper pai
                var wrapper = btn.closest('[data-all-projetos]');
                if (wrapper) {
                    try { allProjetos = JSON.parse(wrapper.getAttribute('data-all-projetos')) || []; }
                    catch(ex) { allProjetos = []; }
                }
                // Encontrar índice atual pelo título
                currentProjetoIdx = allProjetos.findIndex(function(p){ return p.title === data.title; });
                openModal(data);
            } catch(err){ console.error('Modal data error', err); }
        });

        /* ================================================================
           NAVEGAÇÃO ENTRE PROJETOS
        ================================================================ */
        function updateModalNav() {
            var show = allProjetos.length > 1;
            if (navPrev) navPrev.classList.toggle('and-nav-show', show && currentProjetoIdx > 0);
            if (navNext) navNext.classList.toggle('and-nav-show', show && currentProjetoIdx < allProjetos.length - 1);
        }
        if (navPrev) {
            navPrev.addEventListener('click', function(e){
                e.stopPropagation();
                if (currentProjetoIdx > 0) {
                    currentProjetoIdx--;
                    openModal(allProjetos[currentProjetoIdx]);
                }
            });
        }
        if (navNext) {
            navNext.addEventListener('click', function(e){
                e.stopPropagation();
                if (currentProjetoIdx < allProjetos.length - 1) {
                    currentProjetoIdx++;
                    openModal(allProjetos[currentProjetoIdx]);
                }
            });
        }

        /* ================================================================
           IMAGE VIEWER
        ================================================================ */
        function openViewer(items, startIdx) {
            if (!viewer) return;
            ivItems = items;
            showIvImage(startIdx);
            viewer.classList.add('and-iv-open');
        }
        function closeViewer() {
            if (!viewer) return;
            viewer.classList.remove('and-iv-open');
            ivImg.src = '';
        }
        function showIvImage(idx) {
            ivIndex = (idx + ivItems.length) % ivItems.length;
            ivImg.src = ivItems[ivIndex];
            ivCounter.textContent = (ivIndex + 1) + ' / ' + ivItems.length;
            if (ivPrev) ivPrev.style.display = ivItems.length > 1 ? '' : 'none';
            if (ivNext) ivNext.style.display = ivItems.length > 1 ? '' : 'none';
        }
        function navigate(dir) { showIvImage(ivIndex + dir); }

        if (viewer) {
            // Mover viewer para body root — evita qualquer stacking context do overlay
            document.body.appendChild(viewer);
            document.getElementById('andIvClose').addEventListener('click', closeViewer);
            ivPrev.addEventListener('click', function(e){ e.stopPropagation(); navigate(-1); });
            ivNext.addEventListener('click', function(e){ e.stopPropagation(); navigate(1); });
            viewer.addEventListener('click', function(e){ if (e.target === viewer) closeViewer(); });
        }

        // Clicar em thumbnail da galeria
        document.addEventListener('click', function(e){
            var item = e.target.closest('.and-modal-gallery-item');
            if (!item) return;
            e.preventDefault();
            e.stopPropagation();
            var gallery = item.closest('.and-modal-gallery');
            if (!gallery) return;
            var all  = Array.from(gallery.querySelectorAll('.and-modal-gallery-item'));
            var srcs = all.map(function(el){ return el.getAttribute('data-src'); });
            openViewer(srcs, all.indexOf(item));
        });

        // Teclado: Esc fecha viewer ou modal; setas navegam viewer ou projetos
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') {
                if (viewer && viewer.classList.contains('and-iv-open')) closeViewer();
                else closeModal();
            }
            if (viewer && viewer.classList.contains('and-iv-open')) {
                if (e.key === 'ArrowRight') navigate(1);
                if (e.key === 'ArrowLeft')  navigate(-1);
            } else if (overlay.classList.contains('and-open')) {
                if (e.key === 'ArrowRight' && navNext && navNext.classList.contains('and-nav-show')) navNext.click();
                if (e.key === 'ArrowLeft'  && navPrev && navPrev.classList.contains('and-nav-show')) navPrev.click();
            }
        });

    })();
    </script>
    <?php
}



// =============================================
// JS DO CARROSSEL (inline, escopo isolado)
// =============================================
function andorinha_render_carousel_js() {
    static $done = false;
    if ( $done ) return;
    $done = true;
    ?>
    <script>
    (function(){
        function initCarousel(wrapper) {
            var track   = wrapper.querySelector('.andorinha-carousel-track');
            var items   = wrapper.querySelectorAll('.andorinha-carousel-item');
            var prevBtn = wrapper.querySelector('.andorinha-carousel-prev');
            var nextBtn = wrapper.querySelector('.andorinha-carousel-next');
            var dotsWrap= wrapper.querySelector('.andorinha-carousel-dots');
            var visible = parseInt(wrapper.getAttribute('data-visible')) || 3;
            var total   = items.length;
            var maxIdx  = Math.max(0, total - visible);
            var cur     = 0;

            if (!track || total === 0) return;

            // Definir largura de cada item via JS
            Array.from(items).forEach(function(item){
                item.style.width = 'calc(100% / ' + visible + ')';
            });

            // Criar dots
            var dots = [];
            if (dotsWrap) {
                for (var i = 0; i <= maxIdx; i++) {
                    var dot = document.createElement('button');
                    dot.className = 'andorinha-carousel-dot';
                    dot.setAttribute('aria-label', 'Ir para ' + (i+1));
                    (function(idx){ dot.addEventListener('click', function(){ goTo(idx); }); })(i);
                    dotsWrap.appendChild(dot);
                    dots.push(dot);
                }
            }

            function goTo(idx) {
                cur = Math.max(0, Math.min(idx, maxIdx));
                track.style.transform = 'translateX(-' + (cur * (100 / visible)) + '%)';
                if (prevBtn) prevBtn.disabled = cur === 0;
                if (nextBtn) nextBtn.disabled = cur >= maxIdx;
                dots.forEach(function(d, i){
                    d.classList.toggle('and-dot-active', i === cur);
                });
            }

            if (prevBtn) prevBtn.addEventListener('click', function(e){ e.stopPropagation(); goTo(cur - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function(e){ e.stopPropagation(); goTo(cur + 1); });

            // Touch/drag support
            var touchStartX = 0;
            track.addEventListener('touchstart', function(e){ touchStartX = e.touches[0].clientX; }, {passive:true});
            track.addEventListener('touchend',   function(e){
                var diff = touchStartX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
            });

            goTo(0);
        }

        // Inicializar todos os carrosseis da página
        document.querySelectorAll('[data-layout="carousel"]').forEach(initCarousel);
    })();
    </script>
    <?php
}



// =============================================
// 5. RENDERIZAR CARD
// =============================================
function andorinha_render_projeto_card( $post_id, $col_class ) {
    $tipo          = get_post_meta( $post_id, '_andorinha_tipo',           true ) ?: 'projeto';
    $descricao     = get_post_meta( $post_id, '_andorinha_descricao',      true );
    $termo         = get_post_meta( $post_id, '_andorinha_termo_fomento',  true );
    $cod_objeto    = get_post_meta( $post_id, '_andorinha_cod_objeto',     true );
    $cod_programa  = get_post_meta( $post_id, '_andorinha_cod_programa',   true );
    $nome_programa = get_post_meta( $post_id, '_andorinha_nome_programa',  true );
    $pdf_url       = get_post_meta( $post_id, '_andorinha_projeto_pdf',    true );
    $galeria_ids   = get_post_meta( $post_id, '_andorinha_projeto_galeria', true );
    $video_url_raw = get_post_meta( $post_id, '_andorinha_video_url',      true );
    $video_arquivo = get_post_meta( $post_id, '_andorinha_video_arquivo',  true );
    $data_tipo     = get_post_meta( $post_id, '_andorinha_data_tipo',      true ) ?: 'unica';
    $data_unica    = get_post_meta( $post_id, '_andorinha_data_unica',     true );
    $data_inicio   = get_post_meta( $post_id, '_andorinha_data_inicio',    true );
    $data_fim      = get_post_meta( $post_id, '_andorinha_data_fim',       true );
    $img_card      = andorinha_get_projeto_image( $post_id, 'medium_large' );
    $img_modal     = andorinha_get_projeto_image( $post_id, 'large' );

    // Resolver embed de vídeo
    $video_embed  = '';
    $video_is_file = false;
    if ( ! empty( $video_arquivo ) ) {
        $video_embed   = $video_arquivo;
        $video_is_file = true;
    } elseif ( ! empty( $video_url_raw ) ) {
        $video_embed = andorinha_get_video_embed( $video_url_raw );
    }

    // Formatar data para exibição
    $data_label = '';
    if ( $tipo === 'evento' ) {
        $fmt = function( $d ) {
            if ( empty( $d ) ) return '';
            $ts = strtotime( $d );
            return $ts ? date_i18n( 'd/m/Y', $ts ) : $d;
        };
        if ( $data_tipo === 'periodo' && ( $data_inicio || $data_fim ) ) {
            $partes = array_filter( array( $fmt( $data_inicio ), $fmt( $data_fim ) ) );
            $data_label = implode( ' a ', $partes );
        } elseif ( $data_unica ) {
            $data_label = $fmt( $data_unica );
        }
    }

    $tipo_label = $tipo === 'evento' ? 'Evento' : 'Projeto';
    $tipo_icon  = $tipo === 'evento' ? 'fa-solid fa-calendar-days' : 'fa-solid fa-diagram-project';
    $tipo_bg    = $tipo === 'evento' ? '#0073aa' : '#2e7d32';

    // Montar galeria para a modal
    $gallery_data = array();
    if ( ! empty( $galeria_ids ) ) {
        $ids = array_filter( explode( ',', $galeria_ids ) );
        foreach ( $ids as $gid ) {
            $gid   = trim( $gid );
            $thumb = wp_get_attachment_image_url( $gid, 'thumbnail' );
            $full  = wp_get_attachment_image_url( $gid, 'large' );
            if ( $thumb && $full ) {
                $gallery_data[] = array( 'thumb' => $thumb, 'full' => $full );
            }
        }
    }

    // Dados para a modal (JSON no data attribute)
    $modal_data = array(
        'title'         => get_the_title( $post_id ),
        'tipo_label'    => $tipo_label,
        'tipo_icon'     => $tipo_icon,
        'tipo_bg'       => $tipo_bg,
        'img'           => $img_modal,
        'desc'          => $descricao,
        'termo'         => $termo,
        'objeto'        => $cod_objeto,
        'programa'      => $cod_programa,
        'nome_programa' => $nome_programa,
        'pdf'           => $pdf_url,
        'gallery'       => $gallery_data,
        'video'         => $video_embed,
        'video_is_file' => $video_is_file,
        'data_label'    => $data_label,
    );

    $resumo_fields = array();
    if ( $data_label )    $resumo_fields[] = array( 'icon' => 'fa-solid fa-calendar-days',    'label' => 'Data',            'value' => $data_label );
    if ( $termo )         $resumo_fields[] = array( 'icon' => 'fa-solid fa-file-contract',   'label' => 'Termo de Fomento', 'value' => $termo );
    if ( $cod_objeto )    $resumo_fields[] = array( 'icon' => 'fa-solid fa-barcode',          'label' => 'Cód. Objeto',     'value' => $cod_objeto );
    if ( $cod_programa )  $resumo_fields[] = array( 'icon' => 'fa-solid fa-hashtag',          'label' => 'Cód. Programa',   'value' => $cod_programa );
    if ( $nome_programa ) $resumo_fields[] = array( 'icon' => 'fa-solid fa-building-columns', 'label' => 'Programa',        'value' => $nome_programa );

    ob_start();
    ?>
    <div class="<?php echo esc_attr( $col_class ); ?>">
        <div class="andorinha-projeto-card">

            <div class="andorinha-card-img-wrap">
                <?php if ( $img_card ) : ?>
                    <img src="<?php echo esc_url( $img_card ); ?>"
                         alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" />
                <?php else : ?>
                    <div class="andorinha-card-no-img">
                        <i class="<?php echo esc_attr( $tipo_icon ); ?>"></i>
                    </div>
                <?php endif; ?>
                <div class="andorinha-tipo-badge" style="background:<?php echo esc_attr( $tipo_bg ); ?>;">
                    <i class="<?php echo esc_attr( $tipo_icon ); ?>"></i>
                    <?php echo esc_html( $tipo_label ); ?>
                </div>
            </div>

            <div class="andorinha-card-body">
                <h4 class="andorinha-card-title">
                    <?php echo esc_html( get_the_title( $post_id ) ); ?>
                </h4>

                <?php if ( $descricao ) : ?>
                    <p class="andorinha-card-desc">
                        <?php echo esc_html( wp_trim_words( $descricao, 18, '...' ) ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( ! empty( $resumo_fields ) ) : ?>
                    <ul class="andorinha-card-meta">
                        <?php foreach ( $resumo_fields as $f ) : ?>
                            <li>
                                <i class="<?php echo esc_attr( $f['icon'] ); ?>"></i>
                                <span class="andorinha-meta-label"><?php echo esc_html( $f['label'] ); ?>:</span>
                                <span class="andorinha-meta-value"><?php echo esc_html( $f['value'] ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="andorinha-card-actions">
                    <!-- Botão abre modal -->
                    <button
                        class="andorinha-btn-modal"
                        data-projeto="<?php echo esc_attr( wp_json_encode( $modal_data ) ); ?>"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Ver detalhes
                    </button>

                    <?php if ( ! empty( $pdf_url ) ) : ?>
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" class="andorinha-btn-pdf-card">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================
// 6. FUNÇÃO PRINCIPAL DA GRADE / CARROSSEL
// =============================================
function andorinha_render_projetos_grid( $posts_per_page = 6, $columns = 3, $tipo_filtro = 'todos', $layout_type = 'grid' ) {
    $query_args = array(
        'post_type'      => 'projetos',
        'posts_per_page' => intval( $posts_per_page ),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( in_array( $tipo_filtro, array( 'projeto', 'evento' ), true ) ) {
        $query_args['meta_query'] = array( array(
            'key'     => '_andorinha_tipo',
            'value'   => $tipo_filtro,
            'compare' => '=',
        ) );
    }

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;padding:30px 0;font-family:\'Epilogue\',sans-serif;">'
             . '<i class="fa-solid fa-folder-open" style="margin-right:6px;"></i>Nenhum item encontrado.</p>';
    }

    $col_class = 'andorinha-col andorinha-col-' . intval( $columns );
    $is_carousel = ( $layout_type === 'carousel' );

    // Coletar dados de todos os projetos para navegação na modal
    $all_modal_data = array();
    $cards_html     = array();
    while ( $query->have_posts() ) {
        $query->the_post();
        $pid = get_the_ID();
        $card_html = andorinha_render_projeto_card( $pid, $col_class );

        // Extrair modal_data do data-projeto do card para o índice global
        if ( preg_match( '/data-projeto="([^"]+)"/', $card_html, $m ) ) {
            $decoded = html_entity_decode( $m[1], ENT_QUOTES );
            $data    = json_decode( $decoded, true );
            if ( $data ) $all_modal_data[] = $data;
        }
        $cards_html[] = $card_html;
    }
    wp_reset_postdata();

    $all_projetos_json = esc_attr( wp_json_encode( $all_modal_data ) );
    $uid = 'andorinha_carousel_' . uniqid();

    ob_start();
    andorinha_projetos_widget_css();
    andorinha_render_modal_html();

    if ( $is_carousel ) {
        $visible = intval( $columns ); // colunas = itens visíveis
        echo '<div class="andorinha-projetos-widget-wrapper" data-all-projetos="' . $all_projetos_json . '" data-layout="carousel" data-visible="' . $visible . '" id="' . esc_attr( $uid ) . '">';
        echo '<div class="andorinha-carousel-outer">';
        echo '<button class="andorinha-carousel-btn andorinha-carousel-prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>';
        echo '<div class="andorinha-carousel-wrap">';
        echo '<div class="andorinha-carousel-track">';
        foreach ( $cards_html as $ch ) {
            // Ajustar largura do card para o carrossel
            echo str_replace(
                'class="andorinha-col ',
                'class="andorinha-carousel-item andorinha-col ',
                $ch
            );
        }
        echo '</div>'; // track
        echo '</div>'; // wrap
        echo '<button class="andorinha-carousel-btn andorinha-carousel-next" aria-label="Próximo"><i class="fa-solid fa-chevron-right"></i></button>';
        echo '</div>'; // outer
        echo '<div class="andorinha-carousel-dots"></div>';
        echo '</div>'; // wrapper
    } else {
        echo '<div class="andorinha-projetos-widget-wrapper" data-all-projetos="' . $all_projetos_json . '">';
        echo '<div class="andorinha-projetos-grid">';
        foreach ( $cards_html as $ch ) echo $ch;
        echo '</div>';
        echo '</div>';
    }

    andorinha_render_modal_js();
    if ( $is_carousel ) andorinha_render_carousel_js();
    return ob_get_clean();
}

// =============================================
// 7. SHORTCODE
// =============================================
function andorinha_projetos_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts'   => 6,
        'columns' => 3,
        'tipo'    => 'todos',
    ), $atts, 'andorinha_projetos' );
    return andorinha_render_projetos_grid( $atts['posts'], $atts['columns'], $atts['tipo'] );
}
add_shortcode( 'andorinha_projetos', 'andorinha_projetos_shortcode' );

// =============================================
// 8. CATEGORIA ELEMENTOR
// =============================================
add_action( 'elementor/elements/categories_registered', function( $em ) {
    $em->add_category( 'andorinha-category', array(
        'title' => __( 'Andorinha', 'andorinha-starter' ),
        'icon'  => 'fa fa-plug',
    ) );
} );

// =============================================
// 9. WIDGET ELEMENTOR
// =============================================
add_action( 'elementor/init', function() {
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) return;

    if ( ! class_exists( 'Andorinha_Projetos_Elementor_Widget' ) ) {
        class Andorinha_Projetos_Elementor_Widget extends \Elementor\Widget_Base {
            public function get_name()       { return 'andorinha_projetos_grid'; }
            public function get_title()      { return __( 'Lista de Projetos (Andorinha)', 'andorinha-starter' ); }
            public function get_icon()       { return 'eicon-posts-grid'; }
            public function get_categories() { return array( 'andorinha-category', 'general' ); }
            public function get_keywords()   { return array( 'projetos', 'eventos', 'andorinha', 'lista', 'grid', 'portfolio', 'modal' ); }

            protected function register_controls() {
                $this->start_controls_section( 'content_section', array(
                    'label' => __( 'Configurações', 'andorinha-starter' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                ) );
                $this->add_control( 'layout_type', array(
                    'label'   => __( 'Layout', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'grid',
                    'options' => array(
                        'grid'     => __( 'Grade', 'andorinha-starter' ),
                        'carousel' => __( 'Carrossel', 'andorinha-starter' ),
                    ),
                ) );
                $this->add_control( 'tipo_filtro', array(
                    'label'   => __( 'Exibir', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'todos',
                    'options' => array(
                        'todos'   => __( 'Todos (Projetos + Eventos)', 'andorinha-starter' ),
                        'projeto' => __( 'Somente Projetos', 'andorinha-starter' ),
                        'evento'  => __( 'Somente Eventos', 'andorinha-starter' ),
                    ),
                ) );
                $this->add_control( 'posts_per_page', array(
                    'label'   => __( 'Quantidade', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1, 'max' => 24, 'step' => 1, 'default' => 6,
                ) );
                $this->add_control( 'columns', array(
                    'label'   => __( 'Colunas / Visíveis no carrossel', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => '3',
                    'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
                ) );
                $this->end_controls_section();
            }

            protected function render() {
                $s = $this->get_settings_for_display();
                echo andorinha_render_projetos_grid(
                    $s['posts_per_page'] ?? 6,
                    $s['columns']        ?? 3,
                    $s['tipo_filtro']    ?? 'todos',
                    $s['layout_type']    ?? 'grid'
                );
            }
        }
    }

    $reg = function( $wm ) {
        $w = new Andorinha_Projetos_Elementor_Widget();
        if ( method_exists( $wm, 'register' ) )                 $wm->register( $w );
        elseif ( method_exists( $wm, 'register_widget_type' ) ) $wm->register_widget_type( $w );
    };
    add_action( 'elementor/widgets/register',           $reg );
    add_action( 'elementor/widgets/widgets_registered', $reg );
} );
