@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <div class="row">
        <form class="form" id="form_data"
              action="{{ route('manager.update-profile')}}"
              method="post">
            @csrf
            @isset($manager)
                <input type="hidden" name="_method" value="PATCH"/>
            @endisset
            <div class="form-group row">
                <div class="col-lg-6">
                    <label>{{t('Name')}} : </label>
                    <input name="name" type="text" placeholder="{{t('Name')}}"
                           class="form-control"
                           value="{{ \Illuminate\Support\Facades\Auth::user()->name }}"
                    />
                </div>
                <div class="col-lg-6">
                    <label>{{t('Email')}} :</label>
                    <input name="email" type="text" placeholder="{{t('Email')}}"
                           class="form-control"
                           value="{{ \Illuminate\Support\Facades\Auth::user()->email }}"/>
                </div>
            </div>

            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                </div>
            </div>
        </form>
    </div>

@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ManagerProfileRequest::class, '#form_data'); !!}
@endsection
