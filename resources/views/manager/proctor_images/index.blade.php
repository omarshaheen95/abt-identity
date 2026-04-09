@extends('manager.layout.container')

@section('title')
    {{ $title }}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{ $title }}</li>
@endpush

@section('pre-content')

{{-- ===== Top Bar ===== --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center">
        <i class="fas fa-camera-retro text-primary me-2"></i>
        @if($firstDate && $lastDate)
            <small class="text-muted">
                {{ t('First Image') }}: <strong>{{ $firstDate }}</strong>
                &bull;
                {{ t('Last Image') }}: <strong>{{ $lastDate }}</strong>
            </small>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge bg-light text-dark border fs-7 px-3 py-2">
            <i class="fas fa-images text-primary me-1"></i>
            <span id="total-day-count">{{ $totalCount }}</span>
            /
            <span id="visible-count" class="px-2">{{ $images->count() }}</span>
            {{ t('loaded') }}
        </span>
        @can('delete proctor images')
        <button id="btn-select-mode" class="btn btn-sm btn-light-warning fw-semibold" onclick="toggleSelectMode()">
            <i class="fas fa-check-square me-1"></i>{{ t('Select Mode') }}
        </button>
        @endcan
    </div>
</div>

{{-- ===== Bulk Actions Bar ===== --}}
@can('delete proctor images')
<div id="bulk-bar" class="card mb-3 border-warning d-none">
    <div class="card-body py-2 px-4 d-flex align-items-center gap-3 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary" onclick="selectAll()">
            <i class="fas fa-check-double me-1"></i>{{ t('Select All Loaded') }}
        </button>
        <button class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
            <i class="fas fa-times me-1"></i>{{ t('Deselect All') }}
        </button>
        <span class="text-muted small">
            <span id="selected-count">0</span> {{ t('selected') }}
        </span>
        <div class="ms-auto">
            <button id="btn-bulk-delete" class="btn btn-sm btn-danger fw-semibold" onclick="bulkDelete()" disabled>
                <i class="fas fa-trash-alt me-1"></i>
                {{ t('Delete Selected') }} (<span id="selected-count-2">0</span>)
            </button>
        </div>
    </div>
</div>
@endcan

{{-- ===== Day Navigation ===== --}}
<div class="card mb-3 shadow-xs">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between gap-3">
            @if($prevDate)
                <a href="{{ route('manager.proctor-images.index', ['date' => $prevDate]) }}"
                   class="btn btn-sm btn-light-primary fw-semibold">
                    <i class="fas fa-angle-double-left pi-nav-icon me-1"></i>{{ t('Previous Day') }}
                </a>
            @else
                <button class="btn btn-sm btn-light fw-semibold" disabled>
                    <i class="fas fa-angle-double-left pi-nav-icon me-1"></i>{{ t('Previous Day') }}
                </button>
            @endif

            <div class="text-center">
                <div class="fw-bold fs-4 text-primary">{{ $date }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</small>
            </div>

            @if($nextDate)
                <a href="{{ route('manager.proctor-images.index', ['date' => $nextDate]) }}"
                   class="btn btn-sm btn-light-primary fw-semibold">
                    {{ t('Next Day') }}<i class="fas fa-angle-double-right pi-nav-icon ms-1"></i>
                </a>
            @else
                <button class="btn btn-sm btn-light fw-semibold" disabled>
                    {{ t('Next Day') }}<i class="fas fa-angle-double-right pi-nav-icon ms-1"></i>
                </button>
            @endif
        </div>
    </div>
</div>

@if($totalCount > 0)

    {{-- ===== Type Filters ===== --}}
    <div class="card mb-3 shadow-xs">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted fw-semibold fs-8 text-uppercase">{{ t('Type') }}</span>
                <button class="btn btn-sm btn-primary pi-type-btn active" data-type="all">
                    {{ t('All') }}<span class="badge bg-secondary ms-1">{{ $totalCount }}</span>
                </button>
                <button class="btn btn-sm btn-outline-success pi-type-btn" data-type="selfie">
                    <i class="fas fa-camera me-1"></i>{{ t('Selfies') }}
                    <span class="badge bg-success text-white ms-1">{{ $selfieCount }}</span>
                </button>
                <button class="btn btn-sm btn-outline-info pi-type-btn" data-type="screenshot">
                    <i class="fas fa-desktop me-1"></i>{{ t('Screenshots') }}
                    <span class="badge bg-info text-white ms-1">{{ $screenshotCount }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== Image Grid ===== --}}
    <div class="row g-3" id="pi-grid">
        @php $prevGroupKey = null; @endphp
        @foreach($images as $image)
            @php
                $student    = optional(optional($image->studentTerm)->student);
                $groupKey   = $image->student_term_id;
                
                $isNewGroup = $groupKey !== $prevGroupKey;
                $prevGroupKey = $groupKey;
            @endphp

            @if($isNewGroup)
                <div class="col-12 pi-group-header" data-group-key="{{ $groupKey }}">
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div class="flex-grow-1 border-bottom"></div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white border text-dark fw-semibold px-3 py-2 shadow-xs">
                                <i class="fas fa-user-circle text-primary me-1"></i>
                                {{ $student->name ?? t('Unknown') }}
                                <span class="text-muted fw-normal ms-1">(#{{ $image->student_term_id }})</span>
                            </span>
                            <button class="btn btn-xs btn-outline-secondary py-1 px-2 fs-9 pi-select-group-btn d-none"
                                    onclick="selectGroup('{{ $groupKey }}')"
                                    title="{{ t('Select all images for this student') }}">
                                <i class="fas fa-check-square me-1"></i>{{ t('Select All') }}
                            </button>
                        </div>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>
                </div>
            @endif

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 pi-card-wrapper"
                 data-type="{{ $image->type }}"
                 data-id="{{ $image->id }}"
                 data-group-key="{{ $groupKey }}"
                 data-student-name="{{ $student->name ?? '' }}">
                <div class="card pi-card h-100 border">
                    <div class="pi-checkbox-wrap d-none">
                        <input type="checkbox" class="form-check-input pi-checkbox" value="{{ $image->id }}"
                               onchange="onCheckChange()">
                    </div>
                    @can('delete proctor images')
                    <button class="pi-delete-btn pi-single-delete" title="{{ t('Delete') }}"
                            onclick="confirmDeleteSingle({{ $image->id }}, this)">
                        <i class="fas fa-trash-alt text-white"></i>
                    </button>
                    @endcan
                    <div class="pi-img-wrapper" onclick="handleCardClick(this, {{ $image->id }})">
                        <img src="{{ asset($image->file_path) }}" alt="{{ $image->type }}" loading="lazy"
                             onerror="this.parentElement.classList.add('pi-img-error');this.remove()">
                    </div>
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge {{ $image->type === 'selfie' ? 'badge-light-success' : 'badge-light-info' }} fs-9">
                                <i class="fas {{ $image->type === 'selfie' ? 'fa-camera' : 'fa-desktop' }} me-1"></i>
                                {{ $image->type === 'selfie' ? t('Selfie') : t('Screenshot') }}
                            </span>
                        </div>
                        @if($image->capture_minute !== null)
                            <div class="text-muted fs-9">
                                <i class="fas fa-clock me-1 opacity-50"></i>{{ t('Min') }} {{ $image->capture_minute }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="pi-empty-filter" class="text-center py-10 d-none">
        <i class="fas fa-filter fs-2x text-muted opacity-30 mb-3 d-block"></i>
        <p class="text-muted">{{ t('No images match the selected filters') }}</p>
    </div>

    @if($hasMore)
        <div class="text-center mt-4 mb-2" id="load-more-area">
            <div id="load-spinner" class="d-none">
                <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                {{ t('Loading') }}...
            </div>
            <button id="btn-load-more" class="btn btn-light-primary btn-sm fw-semibold px-5" onclick="loadMoreImages()">
                <i class="fas fa-arrow-down me-1"></i>
                {{ t('Load More') }}
                <span class="text-muted ms-1">({{ $totalCount - $images->count() }} {{ t('remaining') }})</span>
            </button>
        </div>
        <div id="pi-sentinel" style="height:1px;"></div>
    @endif

@else
    <div class="card">
        <div class="card-body text-center py-10">
            <i class="fas fa-image fs-2x text-muted opacity-30 mb-3 d-block"></i>
            <p class="text-muted mb-1">{{ t('No proctor images found for this day') }}</p>
            <small class="text-muted">
                {{ $firstDate ? t('Try navigating to another day') : t('No proctor images have been recorded yet') }}
            </small>
        </div>
    </div>
@endif

{{-- ===== Lightbox ===== --}}
<div class="pi-lightbox" id="pi-lightbox">
    <button class="pi-lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="pi-lightbox-nav pi-lightbox-prev" onclick="navigateLightbox(-1)">
        <i class="fas fa-angle-left"></i>
    </button>
    <button class="pi-lightbox-nav pi-lightbox-next" onclick="navigateLightbox(1)">
        <i class="fas fa-angle-right"></i>
    </button>
    <img id="pi-lightbox-img" src="" alt="">
    <div class="pi-lightbox-info" id="pi-lightbox-info"></div>
    <div class="pi-lightbox-student" id="pi-lightbox-student"></div>
</div>

@endsection

@section('style')
<style>
    .pi-card {
        position: relative; overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .pi-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
        transform: translateY(-2px);
    }
    .pi-card.pi-selected { outline: 3px solid #009ef7; outline-offset: -3px; }
    .pi-checkbox-wrap { position: absolute; top: 7px; left: 7px; z-index: 10; }
    .pi-checkbox-wrap .form-check-input { width: 18px; height: 18px; cursor: pointer; }
    .pi-img-wrapper {
        width: 100%; height: 115px; overflow: hidden;
        background: #f0f0f0; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
    }
    .pi-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s; }
    .pi-img-wrapper:hover img { transform: scale(1.04); }
    .pi-img-wrapper.pi-img-error::before {
        content: '\f1c5'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
        font-size: 28px; color: #ccc;
    }
    .pi-delete-btn {
        position: absolute; top: 6px; right: 6px;
        background: rgba(220,53,69,0.85); color: #fff; border: none;
        border-radius: 50%; width: 26px; height: 26px; font-size: 11px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; opacity: 0; transition: opacity 0.2s, background 0.2s; z-index: 6;
    }
    .pi-card:hover .pi-delete-btn { opacity: 1; }
    .pi-delete-btn:hover { background: #b91c1c; }
    .pi-type-btn { border-radius: 20px; }
    .pi-lightbox {
        position: fixed; inset: 0; background: rgba(0,0,0,0.93);
        z-index: 99999; display: none;
        align-items: center; justify-content: center; flex-direction: column;
    }
    .pi-lightbox.active { display: flex; }
    .pi-lightbox > img { max-width: 88%; max-height: 76vh; border-radius: 6px; box-shadow: 0 4px 40px rgba(0,0,0,0.6); }
    .pi-lightbox-info { color: rgba(255,255,255,0.85); margin-top: 14px; font-size: 13px; text-align: center; }
    .pi-lightbox-close {
        position: absolute; top: 18px; right: 24px;
        color: #fff; font-size: 32px; line-height: 1;
        cursor: pointer; background: none; border: none;
        opacity: 0.7; transition: opacity 0.2s;
    }
    .pi-lightbox-close:hover { opacity: 1; }
    .pi-lightbox-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        color: #fff; font-size: 16px; cursor: pointer;
        background: rgba(255,255,255,0.1); border: none; border-radius: 50%;
        width: 46px; height: 46px;
        display: flex; align-items: center; justify-content: center;
        opacity: 0.75; transition: opacity 0.2s, background 0.2s;
    }
    .pi-lightbox-nav:hover { opacity: 1; background: rgba(255,255,255,0.2); }
    .pi-lightbox-prev { left: 18px; }
    .pi-lightbox-next { right: 18px; }
    .pi-lightbox-student { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 4px; text-align: center; }
    .pi-group-header { transition: opacity 0.2s; }
    [dir=rtl] .pi-nav-icon,
    [dir=rtl] .pi-lightbox-nav i { transform: scaleX(-1); }
    .badge-light-success  { background: #e8f5e9; color: #2e7d32; }
    .badge-light-info     { background: #e3f2fd; color: #1565c0; }
    .badge-light-secondary{ background: #e9ecef; color: #6c757d; }
    .fs-9 { font-size: 0.7rem !important; }
    .btn-xs { font-size: 0.7rem; }
</style>
@endsection

@section('script')
<script>
(function () {
    var CSRF          = '{{ csrf_token() }}';
    var LOAD_MORE_URL = '{{ route('manager.proctor-images.load-more') }}';
    var BULK_URL      = '{{ route('manager.proctor-images.bulk-destroy') }}';
    var DESTROY_BASE  = '{{ route('manager.proctor-images.destroy', ':id') }}';
    var CURRENT_DATE  = '{{ $date }}';
    var typeNames     = {selfie:'{{ t("Selfie") }}',screenshot:'{{ t("Screenshot") }}'};

    var currentType  = 'all';
    var selectMode   = false;
    var loadingMore  = false;
    var nextPage     = {{ $nextPage ?? 2 }};
    var hasMore      = {{ $hasMore ? 'true' : 'false' }};
    var totalLoaded  = {{ $images->count() }};
    var lastAppendedGroupKey = null;

    var lbImages = [];
    var lbIndex  = 0;

    /* ===== Helpers ===== */
    function visibleWrappers() {
        return Array.from(document.querySelectorAll('#pi-grid .pi-card-wrapper'))
            .filter(function (w) { return w.style.display !== 'none'; });
    }

    function updateCounts() {
        document.getElementById('visible-count').textContent = visibleWrappers().length;
    }

    function buildLightbox() {
        lbImages = [];
        visibleWrappers().forEach(function (w) {
            var img = w.querySelector('.pi-img-wrapper img');
            if (img) {
                lbImages.push({
                    id:           parseInt(w.dataset.id),
                    src:          img.src,
                    type:         w.dataset.type,
                    groupKey:     w.dataset.groupKey,
                    studentName:  w.dataset.studentName || '',
                });
            }
        });
    }

    function applyFilters() {
        var anyVisible = false;
        document.querySelectorAll('#pi-grid .pi-card-wrapper').forEach(function (w) {
            var ok = (currentType === 'all' || w.dataset.type === currentType);
            w.style.display = ok ? '' : 'none';
            if (ok) anyVisible = true;
        });

        document.querySelectorAll('#pi-grid .pi-group-header').forEach(function (hdr) {
            var gk = hdr.dataset.groupKey;
            var hasVis = Array.from(document.querySelectorAll(
                '#pi-grid .pi-card-wrapper[data-group-key="' + gk + '"]'
            )).some(function (w) { return w.style.display !== 'none'; });
            hdr.style.display = hasVis ? '' : 'none';
        });

        var emptyEl = document.getElementById('pi-empty-filter');
        if (emptyEl) emptyEl.classList.toggle('d-none', anyVisible);
        updateCounts();
        buildLightbox();
    }

    /* ===== Type filter buttons ===== */
    document.querySelectorAll('.pi-type-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.pi-type-btn').forEach(function (b) {
                b.classList.remove('active','btn-primary','btn-success','btn-info');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary'); this.classList.add('active');
            var t = this.dataset.type;
            if (t === 'all') this.classList.add('btn-primary');
            else if (t === 'selfie') this.classList.add('btn-success');
            else this.classList.add('btn-info');
            currentType = t;
            applyFilters();
        });
    });

    /* ===== Select mode ===== */
    window.toggleSelectMode = function () {
        selectMode = !selectMode;
        var bulkBar = document.getElementById('bulk-bar');
        var btn     = document.getElementById('btn-select-mode');

        document.querySelectorAll('.pi-checkbox-wrap').forEach(function (el) { el.classList.toggle('d-none', !selectMode); });
        document.querySelectorAll('.pi-single-delete').forEach(function (el) { el.classList.toggle('d-none', selectMode); });
        document.querySelectorAll('.pi-select-group-btn').forEach(function (el) { el.classList.toggle('d-none', !selectMode); });

        if (selectMode) {
            bulkBar.classList.remove('d-none');
            btn.classList.remove('btn-light-warning'); btn.classList.add('btn-warning');
            btn.innerHTML = '<i class="fas fa-times me-1"></i>{{ t("Exit Select Mode") }}';
        } else {
            bulkBar.classList.add('d-none');
            btn.classList.remove('btn-warning'); btn.classList.add('btn-light-warning');
            btn.innerHTML = '<i class="fas fa-check-square me-1"></i>{{ t("Select Mode") }}';
            deselectAll();
        }
    };

    window.selectAll = function () {
        visibleWrappers().forEach(function (w) {
            var cb = w.querySelector('.pi-checkbox');
            if (cb) cb.checked = true;
        });
        onCheckChange();
    };

    window.selectGroup = function (groupKey) {
        visibleWrappers().forEach(function (w) {
            if (w.dataset.groupKey === groupKey) {
                var cb = w.querySelector('.pi-checkbox');
                if (cb) cb.checked = true;
            }
        });
        onCheckChange();
    };

    window.deselectAll = function () {
        document.querySelectorAll('.pi-checkbox').forEach(function (cb) { cb.checked = false; });
        onCheckChange();
    };

    window.onCheckChange = function () {
        var checked = document.querySelectorAll('.pi-checkbox:checked').length;
        document.getElementById('selected-count').textContent   = checked;
        document.getElementById('selected-count-2').textContent = checked;
        document.getElementById('btn-bulk-delete').disabled     = (checked === 0);
        document.querySelectorAll('.pi-checkbox').forEach(function (cb) {
            cb.closest('.pi-card-wrapper').querySelector('.pi-card').classList.toggle('pi-selected', cb.checked);
        });
    };

    /* ===== Card click ===== */
    window.handleCardClick = function (imgWrapper, id) {
        if (selectMode) {
            var cb = imgWrapper.closest('.pi-card-wrapper').querySelector('.pi-checkbox');
            if (cb) { cb.checked = !cb.checked; onCheckChange(); }
        } else {
            buildLightbox();
            var idx = lbImages.findIndex(function (img) { return img.id === id; });
            lbIndex = idx >= 0 ? idx : 0;
            renderLightbox();
            document.getElementById('pi-lightbox').classList.add('active');
        }
    };

    /* ===== Lightbox ===== */
    function renderLightbox() {
        var img = lbImages[lbIndex];
        if (!img) return;
        document.getElementById('pi-lightbox-img').src = img.src;
        document.getElementById('pi-lightbox-info').innerHTML =
            (typeNames[img.type] || img.type) +
            ' &bull; ' + (lbIndex + 1) + ' / ' + lbImages.length;
        document.getElementById('pi-lightbox-student').innerHTML =
            '<i class="fas fa-user-circle me-1"></i>' + (img.studentName || '{{ t("Unknown") }}');
    }

    window.closeLightbox = function () {
        document.getElementById('pi-lightbox').classList.remove('active');
    };

    window.navigateLightbox = function (dir) {
        lbIndex = (lbIndex + dir + lbImages.length) % lbImages.length;
        renderLightbox();
    };

    document.getElementById('pi-lightbox').addEventListener('click', function (e) {
        if (e.target === this) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (!document.getElementById('pi-lightbox').classList.contains('active')) return;
        if (e.key === 'Escape')     closeLightbox();
        if (e.key === 'ArrowLeft')  navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    /* ===== Single delete ===== */
    window.confirmDeleteSingle = function (id, btn) {
        if (!confirm('{{ t("Delete this image permanently?") }}')) return;
        var wrapper = btn.closest('.pi-card-wrapper');
        fetch(DESTROY_BASE.replace(':id', id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                wrapper.style.transition = 'opacity 0.25s';
                wrapper.style.opacity    = '0';
                setTimeout(function () {
                    var gk = wrapper.dataset.groupKey;
                    wrapper.remove();
                    if (gk) {
                        var remaining = document.querySelectorAll('.pi-card-wrapper[data-group-key="' + gk + '"]');
                        if (remaining.length === 0) {
                            var hdr = document.querySelector('.pi-group-header[data-group-key="' + gk + '"]');
                            if (hdr) hdr.remove();
                        }
                    }
                    document.getElementById('total-day-count').textContent =
                        parseInt(document.getElementById('total-day-count').textContent) - 1;
                    updateCounts(); buildLightbox();
                }, 260);
                toastr.success(res.message);
            }
        })
        .catch(function () { toastr.error('{{ t("An error occurred") }}'); });
    };

    /* ===== Bulk delete ===== */
    window.bulkDelete = function () {
        var checked = Array.from(document.querySelectorAll('.pi-checkbox:checked'));
        if (checked.length === 0) return;
        var ids = checked.map(function (cb) { return parseInt(cb.value); });

        if (!confirm('{{ t("Delete") }} ' + ids.length + ' {{ t("images permanently?") }}')) return;

        fetch(BULK_URL, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: ids }),
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                var affectedGroups = {};
                ids.forEach(function (id) {
                    var w = document.querySelector('.pi-card-wrapper[data-id="' + id + '"]');
                    if (w) { affectedGroups[w.dataset.groupKey] = true; w.remove(); }
                });
                Object.keys(affectedGroups).forEach(function (gk) {
                    var remaining = document.querySelectorAll('.pi-card-wrapper[data-group-key="' + gk + '"]');
                    if (remaining.length === 0) {
                        var hdr = document.querySelector('.pi-group-header[data-group-key="' + gk + '"]');
                        if (hdr) hdr.remove();
                    }
                });
                document.getElementById('total-day-count').textContent =
                    parseInt(document.getElementById('total-day-count').textContent) - res.deleted;
                updateCounts(); buildLightbox(); deselectAll();
                toastr.success(res.deleted + ' {{ t("images deleted successfully") }}');
            } else {
                toastr.error(res.message || '{{ t("An error occurred") }}');
            }
        })
        .catch(function () { toastr.error('{{ t("An error occurred") }}'); });
    };

    /* ===== Load More ===== */
    window.loadMoreImages = function () {
        if (loadingMore || !hasMore) return;
        loadingMore = true;
        var btnEl     = document.getElementById('btn-load-more');
        var spinnerEl = document.getElementById('load-spinner');
        if (btnEl)     btnEl.classList.add('d-none');
        if (spinnerEl) spinnerEl.classList.remove('d-none');

        fetch(LOAD_MORE_URL + '?date=' + CURRENT_DATE + '&page=' + nextPage, { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            res.images.forEach(function (img) { appendImageCard(img); });
            totalLoaded += res.images.length;
            hasMore  = res.has_more;
            nextPage = res.next_page;
            applyFilters();
            if (!hasMore) {
                var area     = document.getElementById('load-more-area');
                var sentinel = document.getElementById('pi-sentinel');
                if (area)     area.remove();
                if (sentinel) sentinel.remove();
                if (observer) observer.disconnect();
            } else {
                if (btnEl)     btnEl.classList.remove('d-none');
                if (spinnerEl) spinnerEl.classList.add('d-none');
                var remaining = parseInt(document.getElementById('total-day-count').textContent) - totalLoaded;
                if (btnEl) btnEl.innerHTML = '<i class="fas fa-arrow-down me-1"></i>{{ t("Load More") }} <span class="text-muted ms-1">(' + remaining + ' {{ t("remaining") }})</span>';
            }
            loadingMore = false;
        })
        .catch(function () {
            loadingMore = false;
            if (btnEl)     btnEl.classList.remove('d-none');
            if (spinnerEl) spinnerEl.classList.add('d-none');
            toastr.error('{{ t("Failed to load more images") }}');
        });
    };

    function appendImageCard(img) {
        var gk          = img.student_term_id;
        var typeBadge   = img.type === 'selfie'
            ? '<span class="badge badge-light-success fs-9"><i class="fas fa-camera me-1"></i>{{ t("Selfie") }}</span>'
            : '<span class="badge badge-light-info fs-9"><i class="fas fa-desktop me-1"></i>{{ t("Screenshot") }}</span>';
        var minuteHtml  = img.capture_minute !== null
            ? '<div class="text-muted fs-9"><i class="fas fa-clock me-1 opacity-50"></i>{{ t("Min") }} ' + img.capture_minute + '</div>'
            : '';
        var studentName = img.student_name || '{{ t("Unknown") }}';
        var grid        = document.getElementById('pi-grid');

        if (gk !== lastAppendedGroupKey) {
            var existingHdr = grid.querySelector('.pi-group-header[data-group-key="' + gk + '"]');
            if (!existingHdr) {
                var hdrHtml = '<div class="col-12 pi-group-header" data-group-key="' + gk + '">' +
                    '<div class="d-flex align-items-center gap-2 mt-2">' +
                    '<div class="flex-grow-1 border-bottom"></div>' +
                    '<div class="d-flex align-items-center gap-2">' +
                    '<span class="badge bg-white border text-dark fw-semibold px-3 py-2 shadow-xs">' +
                    '<i class="fas fa-user-circle text-primary me-1"></i>' + studentName +
                    '<span class="text-muted fw-normal ms-1">(' + '#' + img.student_term_id + ')</span>' +
                    '</span>' +
                    '<button class="btn btn-xs btn-outline-secondary py-1 px-2 fs-9 pi-select-group-btn' + (selectMode ? '' : ' d-none') + '"' +
                    ' onclick="selectGroup(\'' + gk + '\')" title="{{ t("Select all images for this student") }}">' +
                    '<i class="fas fa-check-square me-1"></i>{{ t("Select All") }}</button>' +
                    '</div><div class="flex-grow-1 border-bottom"></div></div></div>';
                grid.insertAdjacentHTML('beforeend', hdrHtml);
            }
            lastAppendedGroupKey = gk;
        }

        var cardHtml = '<div class="col-6 col-sm-4 col-md-3 col-lg-2 pi-card-wrapper"' +
            ' data-type="' + img.type + '" data-id="' + img.id + '"' +
            ' data-group-key="' + gk + '" data-student-name="' + studentName + '">' +
            '<div class="card pi-card h-100 border">' +
            '<div class="pi-checkbox-wrap' + (selectMode ? '' : ' d-none') + '">' +
            '<input type="checkbox" class="form-check-input pi-checkbox" value="' + img.id + '" onchange="onCheckChange()"></div>' +
            '<button class="pi-delete-btn pi-single-delete' + (selectMode ? ' d-none' : '') + '"' +
            ' title="{{ t("Delete") }}" onclick="confirmDeleteSingle(' + img.id + ',this)"><i class="fas fa-trash-alt"></i></button>' +
            '<div class="pi-img-wrapper" onclick="handleCardClick(this,' + img.id + ')">' +
            '<img src="' + img.file_path + '" alt="' + img.type + '" loading="lazy"' +
            ' onerror="this.parentElement.classList.add(\'pi-img-error\');this.remove()"></div>' +
            '<div class="card-body p-2">' +
            '<div class="d-flex justify-content-between align-items-center mb-1">' + typeBadge + '</div>' +
            minuteHtml + '</div></div></div>';
        grid.insertAdjacentHTML('beforeend', cardHtml);
    }

    /* ===== Infinite Scroll ===== */
    var observer = null;
    var sentinel = document.getElementById('pi-sentinel');
    if (sentinel && hasMore && 'IntersectionObserver' in window) {
        observer = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting && !loadingMore && hasMore) loadMoreImages();
        }, { rootMargin: '200px' });
        observer.observe(sentinel);
    }

    /* ===== Init ===== */
    var allCards = document.querySelectorAll('#pi-grid .pi-card-wrapper');
    if (allCards.length) lastAppendedGroupKey = allCards[allCards.length - 1].dataset.groupKey;
    buildLightbox();
    updateCounts();
}());
</script>
@endsection
