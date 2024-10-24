@extends('manager.layout.container')
@section('title')
    {{t('Show Activity Log')}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('manager.activity-log.index') }}">
            {{t('Activity Log')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{t('Show Activity Log')}}
    </li>
@endpush

@section('content')
    <div class="row">
        <div class="col-6 fs-5">
            <h6>{{t('Causer')}} : <label class="text-primary">{{ optional($activity->causer)->name}}</label></h6>
        </div>
        <div class="col-6 fs-5">
            <h6>{{t('Action Date')}} : <label class="text-primary">{{$activity->created_at}}</label></h6>
        </div>
        <div class="col-12 fs-5">
            <h6>{{t('New')}}</h6>
        </div>
        <div class="col-12 fs-5">
            <pre class="p-3">{{$new}}</pre>
        </div>
        <div class="col-12 fs-5">
            <h6>{{t('Old')}}</h6>
        </div>
        <div class="col-12 fs-5">
            <pre class="p-3">{{$old}}</pre>
        </div>

    </div>
@endsection

