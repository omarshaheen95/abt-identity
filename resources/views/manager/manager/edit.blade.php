@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.manager.index')}}" class="text-muted">
            {{t('Managers')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush

@section('content')
    <div class="row">
        <!--begin::Form-->
        <form class="form" id="form_data"
              action="{{isset($manager) ? route('manager.manager.update', $manager->id):route('manager.manager.store')}}"
              method="post">
            @csrf
            @isset($manager)
                <input type="hidden" name="_method" value="PATCH"/>
            @endisset
            <div class="form-group row">
                <div class="col-lg-3 mb-2">
                    <label class="form-label mb-1">{{t('Name')}} : </label>
                    <input name="name" type="text" placeholder="{{t('Name')}}"
                           class="form-control"
                           value="{{ isset($manager) ? $manager->name : old("name") }}"
                    />
                </div>
                <div class="col-lg-3 mb-2">
                    <label class="form-label mb-1">{{t('Email')}} :</label>
                    <input name="email" type="text" placeholder="{{t('Email')}}"
                           class="form-control"
                           value="{{ isset($manager) ? $manager->email : old("email") }}"/>
                </div>
                <div class="col-lg-3 mb-2">
                    <label class="form-label mb-1">{{t('Password')}} :</label>
                    <input name="password" type="password" placeholder="{{t('Password')}}"
                           class="form-control"/>
                </div>
                <div class="col-lg-3">
                    <label>{{t('Approved Status')}} :</label>

                    <div class="form-check form-switch form-check-custom form-check-solid mt-1">
                        <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                               {{ isset($manager) && $manager->approved == 1 ? 'checked' :'' }} name="approved"
                        />
                    </div>
                </div>

            </div>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>

@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ManagerRequest::class, '#form_data'); !!}
@endsection
