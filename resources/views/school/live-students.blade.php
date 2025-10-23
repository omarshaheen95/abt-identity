<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{t('Live Users Dashboard') }}</title>
    <!-- Arabic Font Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    @if(app()->getLocale() == 'ar')
        <link href="{{asset('assets_v1/plugins/global/plugins.bundle.rtl.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('assets_v1/css/style.bundle.rtl.css')}}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{asset('assets_v1/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('assets_v1/css/style.bundle.css')}}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @endif
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="school-id" content="{{ $school->id }}">
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <link href="{{asset('web-socket/css/live_users.css')}}?v=2.1" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="control-buttons">
    <button class="control-btn" id="fullscreenBtn" title="{{t('Fullscreen') }}">
        <i class="fas fa-expand"></i>
    </button>
    <button class="control-btn" id="refreshBtn" title="{{t('Refresh Page') }}">
        <i class="fas fa-sync-alt"></i>
    </button>
    <a href="{{route('school.switch-language', app()->getLocale() == 'ar' ? 'en' : 'ar')}}" class="control-btn" id="langBtn" title="{{app()->getLocale() == 'ar' ? 'English' : 'العربية'}}">
        <i class="fas fa-language"></i>
    </a>
    <a href="{{route('school.home')}}" class="control-btn" id="homeBtn" title="{{t('Home Page') }}">
        <i class="fas fa-home"></i>
    </a>
</div>

<div class="container">
    <div class="header">
        <h1>{{t('Active Students Dashboard') }}</h1>
        <p>{{t('Real-time monitoring for your school') }}</p>
    </div>

    <!-- Overall Statistics -->
    <div class="overall-stats">
        <div class="overall-card">
            <div class="card-icon total">
                <i class="fas fa-users text-white fs-2"></i>
            </div>
            <h3>{{t('Total Active Students') }}</h3>
            <div class="overall-number">
                <span id="total-users">0</span>
                <span class="status-indicator status-online pulse"></span>
            </div>
            <div>{{t('students online now') }}</div>
        </div>

        <div class="overall-card">
            <div class="card-icon platforms">
                <i class="fas fa-layer-group text-white fs-2"></i>
            </div>
            <h3>{{t('Active Grades') }}</h3>
            <div class="overall-number" id="total-grades">0</div>
            <div>{{t('grades with students') }}</div>
        </div>

        <div class="overall-card">
            <div class="card-icon schools">
                <i class="fas fa-door-open text-white fs-2"></i>
            </div>
            <h3>{{t('Active Sections') }}</h3>
            <div class="overall-number" id="total-sections">0</div>
            <div>{{t('sections with students') }}</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">
        <!-- Grades & Sections Section -->
        <div class="platforms-section">
            <div class="section-title">
                <div class="section-icon">📚</div>
                {{t('Grades & Sections') }}
            </div>
            <div id="grades-list">
                <div class="no-platforms">
                    <i class="fas fa-hourglass-half" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">{{t('Loading grades...') }}</p>
                </div>
            </div>
        </div>

        <!-- Activity Log Section -->
        <div class="activity-section">
            <div class="section-title">
                <div class="section-icon">📋</div>
                {{t('Activity Log') }}
            </div>
            <div class="activity-log" id="activity-log">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clock" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">{{t('Waiting for activity...') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="last-update">
        {{t('Last update') }}: <span id="lastUpdate">--:--:--</span>
    </div>
</div>

<!-- Grade Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalTitle">{{t('Grade Students') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="search-box p-3 border-bottom">
                    <input type="text" class="search-input" id="gradeSearch" placeholder="{{t('Search students...') }}">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="modal-scroll-container" style="max-height: 60vh; overflow-y: auto; padding: 1rem;">
                    <div id="gradeUsers"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionModalTitle">{{t('Section Students') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="search-box p-3 border-bottom">
                    <input type="text" class="search-input" id="sectionSearch" placeholder="{{t('Search students...') }}">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="modal-scroll-container" style="max-height: 60vh; overflow-y: auto; padding: 1rem;">
                    <div id="sectionUsers"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets_v1/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('assets_v1/js/scripts.bundle.js')}}"></script>
<script src="{{asset('web-socket/js/live_users_handler.js')}}?v={{time()}}"></script>
</body>
</html>
