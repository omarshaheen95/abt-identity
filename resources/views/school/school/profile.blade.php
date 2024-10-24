@extends('school.layout.container')

@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item b text-muted">
        {{$title}}
    </li>
@endpush

@section('content')
    <form action="{{route('school.update-profile')}}" method="post" class="form" id="form-profile-save" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!--begin::Image input-->
            <div class="col-12 d-flex flex-column align-items-center mb-5">
                <div>Logo</div>
                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(/manager_assets/media/svg/avatars/blank.svg)">

                    @if(isset($school) && $school->logo )
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{asset($school->logo)}})"></div>

                    @else
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url(/new_assets/media/svg/avatars/blank.svg)"></div>
                    @endif

                    <!--begin::Edit button-->
                    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                           data-kt-image-input-action="change"
                           data-bs-toggle="tooltip"
                           data-bs-dismiss="click"
                           title="Change avatar">
                        <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i>

                        <!--begin::Inputs-->
                        <input type="file" name="logo" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                        <!--end::Inputs-->
                    </label>
                    <!--end::Edit button-->

                    <!--begin::Cancel button-->
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                          data-kt-image-input-action="cancel"
                          data-bs-toggle="tooltip"
                          data-bs-dismiss="click"
                          title="Cancel avatar">
                <i class="ki-outline ki-cross fs-3"></i>
            </span>
                    <!--end::Cancel button-->

                    <!--begin::Remove button-->
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                          data-kt-image-input-action="remove"
                          data-bs-toggle="tooltip"
                          data-bs-dismiss="click"
                          title="Remove avatar">
                <i class="ki-outline ki-cross fs-3"></i>
            </span>
                    <!--end::Remove button-->
                </div>
            </div>
            <!--end::Image input-->

            <div class="row">
                <div class="col-6 mb-2">
                    <div class="form-group">
                        <label for="School_Name" class="form-label">{{t('School Name')}}</label>
                        <input type="url" id="School_Name" name="name" class="form-control" placeholder="{{t('School Name')}}" value="{{$school->name}}" required>
                    </div>
                </div>

                <div class="col-6 mb-2">
                    <div class="form-group">
                        <label for="Email" class="form-label">{{t('Email')}}</label>
                        <input type="email" id="Email" name="email" class="form-control" placeholder="{{t('Email')}}" value="{{$school->email}}" required>
                    </div>
                </div>

                <div class="col-6 mb-2">
                    <div class="form-group">
                        <label for="School_Url" class="form-label">{{t('School Url')}}</label>
                        <input type="url" id="School_Url" name="url" class="form-control" placeholder="{{t('School Url')}}" value="{{$school->url}}" required>
                    </div>
                </div>
                <div class="col-6 mb-2">
                    <div class="form-group">
                        <label for="School_Phone" class="form-label">{{t('School Phone')}}</label>
                        <input type="tel" id="School_Phone" name="mobile" class="form-control" placeholder="{{t('School Phone')}}" value="{{$school->mobile}}" required>
                    </div>
                </div>

            </div>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                </div>
            </div>
        </div>

    </form>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\School\SchoolProfileRequest::class, '#form-profile-save'); !!}
@endsection
