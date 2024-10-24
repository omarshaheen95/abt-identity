@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.inspection.index')}}" class="text-muted">
            {{t('Inspectors')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{isset($inspection) ? route('manager.inspection.update', $inspection->id):route('manager.inspection.store')}}"
           method="post"
          enctype="multipart/form-data">
         @csrf
        @isset($inspection)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="d-flex flex-column align-items-center mb-10">
            <label class="form-label mb-2">{{t('Image')}}</label>
            <div class="">

                <!--begin::Image input-->
                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url({{asset('assets_v1/media/svg/avatars/blank.svg')}})">

                    @if(isset($inspection) && $inspection->image )
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{asset($inspection->image)}})"></div>

                    @else
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{asset('assets_v1/media/svg/avatars/blank.svg')}})"></div>
                    @endif

                    <!--begin::Edit button-->
                    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                           data-kt-image-input-action="change"
                           data-bs-toggle="tooltip"
                           data-bs-dismiss="click"
                           title="Change avatar">
                        <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i>

                        <!--begin::Inputs-->
                        <input type="file" name="image" accept=".png, .jpg, .jpeg" />
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
                <!--end::Image input-->


            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Name')}} : </label>
                <input name="name" type="text" placeholder="{{t('Name')}}"
                       class="form-control"
                       value="{{ isset($inspection) ? $inspection->name : old("name") }}"
                />
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Email')}} :</label>
                <input name="email" type="text" placeholder="{{t('Email')}}"
                       class="form-control"
                       value="{{ isset($inspection) ? $inspection->email : old("email") }}"/>
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Password')}} :</label>
                <input name="password" type="password" placeholder="{{t('Password')}}"
                       class="form-control"/>
            </div>

            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('School')}}:</label>
                <select class="form-control form-select" data-control="select2" multiple data-placeholder="{{t('Select School')}}" name="schools_ids[]">
                    <option></option>
                    @foreach($schools as $school)
                        <option
                            {{isset($inspection_schools_ids) && in_array($school->id,$inspection_schools_ids)  ? 'selected':'' }} value="{{$school->id}}">{{$school->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-4 mt-8">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                           {{ isset($inspection) && $inspection->active == 1 ? 'checked' :'' }} name="active"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('Activation')}}
                    </label>
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


@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\InspectionRequest::class, '#form_data'); !!}
@endsection
