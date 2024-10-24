@extends('inspection.layout.container')
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
        <form enctype="multipart/form-data" class="form" id="form_data"
              action="{{ route('inspection.update-profile')}}"
              method="post">
            @csrf
            <div class="d-flex flex-column align-items-center mb-10">
                <label class="form-label mb-2">{{t('Image')}}</label>
                <div class="">

                    <!--begin::Image input-->
                    <div class="image-input image-input-outline" data-kt-image-input="true"
                         style="background-image: url({{asset('assets_v1/media/svg/avatars/blank.svg')}})">

                        @if($inspection->image )
                            <div class="image-input-wrapper w-125px h-125px"
                                 style="background-image: url({{asset($inspection->image)}})"></div>
                        @else
                            <div class="image-input-wrapper w-125px h-125px"
                                 style="background-image: url({{asset('assets_v1/media/svg/avatars/blank.svg')}})"></div>
                        @endif

                        <!--begin::Edit button-->
                        <label
                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change"
                            data-bs-toggle="tooltip"
                            data-bs-dismiss="click"
                            title="Change avatar">
                            <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i>

                            <!--begin::Inputs-->
                            <input type="file" name="image" accept=".png, .jpg, .jpeg"/>
                            <input type="hidden" name="avatar_remove"/>
                            <!--end::Inputs-->
                        </label>
                        <!--end::Edit button-->

                        <!--begin::Cancel button-->
                        <span
                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel"
                            data-bs-toggle="tooltip"
                            data-bs-dismiss="click"
                            title="Cancel avatar">
                                                <i class="ki-outline ki-cross fs-3"></i>
                                            </span>
                        <!--end::Cancel button-->

                        <!--begin::Remove button-->
                        <span
                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="remove"
                            data-bs-toggle="tooltip"
                            data-bs-dismiss="click"
                            title="Remove avatar">
                                                <i class="ki-outline ki-cross fs-3"></i>
                                            </span>
                        <!--end::Remove button-->
                    </div>
                    <!--end::Image input-->


                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>{{t('Name')}} : </label>
                    <input name="name" type="text" placeholder="{{t('Name')}}"
                           class="form-control"
                           value="{{ $inspection->name }}"
                    />
                </div>
                <div class="col-lg-4">
                    <label>{{t('Email')}} :</label>
                    <input name="email" type="text" placeholder="{{t('Email')}}"
                           class="form-control"
                           value="{{ $inspection->email  }}"/>
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
    {!! JsValidator::formRequest(\App\Http\Requests\Inspection\InspectionProfileRequest::class, '#form_data'); !!}
@endsection
