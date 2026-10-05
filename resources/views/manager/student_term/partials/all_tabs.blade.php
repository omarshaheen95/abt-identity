{{-- Status tabs of the all students assessments page. data-type is the value of the
     hidden status input (#status_type): empty for both, then the project's own codes. --}}
@php
    $tabCorrected = $tabCorrected ?? '1';
    $tabUncorrected = $tabUncorrected ?? '2';
    $tabCurrent = (string)($corrected ?? '');
@endphp
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <ul class="nav nav-pills gap-2" id="status_tabs">
        <li class="nav-item">
            <a class="nav-link btn btn-sm btn-color-gray-700 btn-active-light-primary px-4 {{$tabCurrent === '' ? 'active' : ''}}"
               href="#!" data-type="">
                {{t('All Assessments')}}
                <span class="badge badge-light-primary ms-2" data-count="all">-</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-sm btn-color-gray-700 btn-active-light-success px-4 {{$tabCurrent === (string)$tabCorrected ? 'active' : ''}}"
               href="#!" data-type="{{$tabCorrected}}">
                <span class="legend-dot bg-success me-1"></span>
                {{t('Status Corrected')}}
                <span class="badge badge-light-success ms-2" data-count="corrected">-</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-sm btn-color-gray-700 btn-active-light-warning px-4 {{$tabCurrent === (string)$tabUncorrected ? 'active' : ''}}"
               href="#!" data-type="{{$tabUncorrected}}">
                <span class="legend-dot bg-warning me-1"></span>
                {{t('Status Uncorrected')}}
                <span class="badge badge-light-warning ms-2" data-count="uncorrected">-</span>
            </a>
        </li>
    </ul>

    <div class="d-flex flex-wrap align-items-center gap-4 fs-7 text-gray-600">
        <span id="selected_info" class="d-none">
            <span class="badge badge-primary" id="selected_count">0</span> {{t('Selected')}}
            <a href="#!" class="ms-1 text-danger" id="clear_selection">{{t('Clear')}}</a>
        </span>
        <span><span class="legend-dot me-1" style="background:#ffacac"></span>{{t('SEN')}}</span>
        <span><span class="legend-dot me-1" style="background:#fff0b0"></span>{{t('G&T')}}</span>
    </div>
</div>
