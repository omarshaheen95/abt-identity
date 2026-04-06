{{--
    Proctor Images Viewer - Standalone Partial
    Include this in any page that needs to display proctor images.

    Required variables:
        $proctor_images  - Collection of ProctorImage models
        $student_name    - Student name for the modal title

    Usage:
        @include('general.proctor-viewer', ['proctor_images' => $student_term->proctorImages, 'student_name' => $student->name])

    Button trigger (place wherever you want the button):
        @if($student_term->proctorImages->count() > 0)
            <button type="button" class="btn btn-sm btn-secondary" onclick="openProctorGallery()">
                <i class="fas fa-camera"></i> Student Screenshots (count)
            </button>
        @endif
--}}

@if(isset($proctor_images) && $proctor_images->count() > 0)

<style>
    .proctor-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        padding: 10px 0;
    }
    .proctor-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
        cursor: pointer;
    }
    .proctor-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .proctor-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }
    .proctor-card-info {
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        background: #f8f9fa;
    }
    .proctor-type-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .proctor-type-selfie {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .proctor-type-screenshot {
        background: #e3f2fd;
        color: #1565c0;
    }
    .proctor-minute {
        color: #666;
        font-weight: 500;
    }
    #proctor-filter-buttons .btn {
        border-radius: 20px;
        padding: 7px 18px;
        font-size: 13px;
        margin: 0 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        line-height: 1.2;
        height: 34px;
        border-width: 1px;
        box-sizing: border-box;
    }
    .proctor-empty {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
    .proctor-empty i {
        font-size: 48px;
        margin-bottom: 10px;
        display: block;
        opacity: 0.3;
    }
    /* Lightbox */
    .proctor-lightbox {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .proctor-lightbox.active { display: flex; }
    .proctor-lightbox img {
        max-width: 90%;
        max-height: 80vh;
        border-radius: 8px;
        box-shadow: 0 4px 30px rgba(0,0,0,0.5);
    }
    .proctor-lightbox-info {
        color: #fff;
        margin-top: 15px;
        font-size: 14px;
        text-align: center;
    }
    .proctor-lightbox-close {
        position: absolute;
        top: 20px; right: 30px;
        color: #fff;
        font-size: 32px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
        background: none;
        border: none;
    }
    .proctor-lightbox-close:hover { opacity: 1; }
    .proctor-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        width: 50px; height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .proctor-lightbox-nav:hover {
        opacity: 1;
        background: rgba(255,255,255,0.2);
    }
    .proctor-lightbox-prev { right: 20px; }
    .proctor-lightbox-next { left: 20px; }
</style>

{{-- Gallery Modal --}}
<div class="modal fade" id="modal-proctor-gallery" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera text-primary"></i> Student Screenshots - {{ $student_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Type filter buttons --}}
                <div class="d-flex justify-content-center mb-3" id="proctor-filter-buttons">
                    <button class="btn btn-primary" data-filter="all">All</button>
                    <button class="btn btn-outline-success" data-filter="selfie"><i class="fas fa-camera"></i> Selfies</button>
                    <button class="btn btn-outline-info" data-filter="screenshot"><i class="fas fa-desktop"></i> Screenshots</button>
                </div>

                <div id="proctor-gallery-content"></div>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div class="proctor-lightbox" id="proctor-lightbox">
    <button class="proctor-lightbox-close" onclick="closeProctorLightbox()">&times;</button>
    <button class="proctor-lightbox-nav proctor-lightbox-prev" onclick="navigateProctorLightbox(-1)"><i class="fas fa-chevron-right"></i></button>
    <button class="proctor-lightbox-nav proctor-lightbox-next" onclick="navigateProctorLightbox(1)"><i class="fas fa-chevron-left"></i></button>
    <img id="proctor-lightbox-img" src="" alt="">
    <div class="proctor-lightbox-info" id="proctor-lightbox-info"></div>
</div>

<script>
    @php
        $proctorJsonData = $proctor_images->map(function($img) {
            return [
                'id' => $img->id,
                'type' => $img->type,
                'file_path' => asset($img->file_path),
                'capture_minute' => $img->capture_minute,
            ];
        })->values();
    @endphp
    var proctorAllImages = @json($proctorJsonData);
    var currentLightboxImages = [];
    var currentLightboxIndex = 0;

    function openProctorGallery() {
        // Reset filter to All
        $('#proctor-filter-buttons .btn').removeClass('btn-primary btn-success btn-info')
            .not('[data-filter="all"]').addClass('btn-outline-success').end()
            .not('[data-filter="all"]').end();

        // Reset all to outline
        $('#proctor-filter-buttons .btn[data-filter="all"]').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#proctor-filter-buttons .btn[data-filter="selfie"]').removeClass('btn-success').addClass('btn-outline-success');
        $('#proctor-filter-buttons .btn[data-filter="screenshot"]').removeClass('btn-info').addClass('btn-outline-info');

        renderProctorGallery('all');

        $('#proctor-filter-buttons .btn').off('click').on('click', function() {
            var filter = $(this).data('filter');

            // Reset all buttons
            $('#proctor-filter-buttons .btn[data-filter="all"]').removeClass('btn-primary').addClass('btn-outline-secondary');
            $('#proctor-filter-buttons .btn[data-filter="selfie"]').removeClass('btn-success').addClass('btn-outline-success');
            $('#proctor-filter-buttons .btn[data-filter="screenshot"]').removeClass('btn-info').addClass('btn-outline-info');

            // Activate clicked
            if (filter === 'all') $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
            else if (filter === 'selfie') $(this).removeClass('btn-outline-success').addClass('btn-success');
            else $(this).removeClass('btn-outline-info').addClass('btn-info');

            renderProctorGallery(filter);
        });

        $('#modal-proctor-gallery').modal('show');
    }

    function renderProctorGallery(filter) {
        var filtered = filter === 'all' ? proctorAllImages : proctorAllImages.filter(function(img) { return img.type === filter; });
        currentLightboxImages = filtered;

        if (filtered.length === 0) {
            $('#proctor-gallery-content').html(
                '<div class="proctor-empty"><i class="fas fa-image"></i><p>No images found</p></div>'
            );
            return;
        }

        var html = '<div class="proctor-gallery-grid">';
        filtered.forEach(function(img, index) {
            var typeBadge = img.type === 'selfie'
                ? '<span class="proctor-type-badge proctor-type-selfie"><i class="fas fa-camera"></i> Selfie</span>'
                : '<span class="proctor-type-badge proctor-type-screenshot"><i class="fas fa-desktop"></i> Screenshot</span>';
            var minuteText = img.capture_minute !== null ? 'Min ' + img.capture_minute : '';

            html += '<div class="proctor-card" onclick="openProctorLightbox(' + index + ')">' +
                '<img src="' + img.file_path + '" alt="' + img.type + '" loading="lazy">' +
                '<div class="proctor-card-info">' + typeBadge +
                '<span class="proctor-minute"><i class="fas fa-clock"></i> ' + minuteText + '</span>' +
                '</div></div>';
        });
        html += '</div>';
        $('#proctor-gallery-content').html(html);
    }

    function openProctorLightbox(index) {
        currentLightboxIndex = index;
        updateProctorLightbox();
        $('#proctor-lightbox').addClass('active');
    }

    function closeProctorLightbox() {
        $('#proctor-lightbox').removeClass('active');
    }

    function navigateProctorLightbox(direction) {
        currentLightboxIndex += direction;
        if (currentLightboxIndex < 0) currentLightboxIndex = currentLightboxImages.length - 1;
        if (currentLightboxIndex >= currentLightboxImages.length) currentLightboxIndex = 0;
        updateProctorLightbox();
    }

    function updateProctorLightbox() {
        var img = currentLightboxImages[currentLightboxIndex];
        if (!img) return;
        $('#proctor-lightbox-img').attr('src', img.file_path);
        var typeLabel = img.type === 'selfie' ? 'Selfie' : 'Screenshot';
        var minuteText = img.capture_minute !== null ? ' | Minute: ' + img.capture_minute : '';
        $('#proctor-lightbox-info').html(
            typeLabel + minuteText + ' | ' + (currentLightboxIndex + 1) + ' / ' + currentLightboxImages.length
        );
    }

    $(document).on('keydown', function(e) {
        if ($('#proctor-lightbox').hasClass('active')) {
            if (e.key === 'Escape') closeProctorLightbox();
            if (e.key === 'ArrowLeft') navigateProctorLightbox(-1);
            if (e.key === 'ArrowRight') navigateProctorLightbox(1);
        }
    });

    $('#proctor-lightbox').on('click', function(e) {
        if (e.target === this) closeProctorLightbox();
    });
</script>

@endif
