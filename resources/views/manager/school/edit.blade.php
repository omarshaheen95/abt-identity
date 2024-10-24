@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.school.index')}}" class="text-muted">
            {{t('Schools')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
        {{ isset($school) ? '/'.$school->getTranslation('name', app()->getLocale()) : '' }}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data" enctype="multipart/form-data"
          action="{{isset($school) ? route('manager.school.update', $school->id):route('manager.school.store')}}"
          method="post">
        @csrf
        @isset($school)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset

        <div class="d-flex flex-column align-items-center mb-10">
            <label class="form-label mb-2">{{t('Logo')}}</label>
            <div class="">

                <!--begin::Image input-->
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
                <!--end::Image input-->


            </div>
        </div>

        <div class="form-group row">
            @foreach(\Config::get('app.languages') as $locale)
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('School Name')}} : ({{$locale}})</label>
                    <input name="name[{{$locale}}]" type="text" placeholder="{{t('School Name')}}"
                           class="form-control"
                           value="{{ isset($school) ? $school->getTranslation('name', $locale) : old("name[$locale]") }}"
                    />
                </div>
            @endforeach
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Email')}} :</label>
                <input name="email" type="text" placeholder="{{t('Email')}}"
                       class="form-control"
                       value="{{ isset($school) ? $school->email : old("email") }}"/>
            </div>

                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Password')}} :</label>
                    <input name="password" type="password" placeholder="{{t('Password')}}"
                           class="form-control"/>
                </div>
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('URL')}} :</label>
                    <input name="url" type="text" placeholder="{{t('URL')}}"
                           class="form-control" value="{{ isset($school) ? $school->url : old("url") }}"/>
                </div>
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Mobile')}} :</label>
                    <input name="mobile" type="text" placeholder="{{t('Mobile')}}"
                           class="form-control"
                           value="{{ isset($school) ? $school->mobile : old("mobile") }}"/>
                </div>

                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Available Year')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Available Year')}}"
                            name="available_year_id">
                        <option></option>
                        @foreach($years as $year)
                            <option
                                {{isset($school) && $school->available_year_id == $year->id ? 'selected':'' }} value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Country')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Country')}}"
                            name="country">
                        <option></option>
                        @foreach(schoolsCountry() as $key => $type)
                            <option
                                {{isset($school) && $school->country == $key ? 'selected':'' }} value="{{$key}}">{{$type}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Curriculum Type')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Curriculum Type')}}"
                            name="curriculum_type">
                        <option></option>
                        @foreach(schoolsType() as $key => $type)
                            <option
                                {{isset($school) && $school->curriculum_type == $key ? 'selected':'' }} value="{{$key}}">{{$type}}</option>
                        @endforeach
                    </select>
                </div>

        </div>

        <div class="row mt-5">
            <div class="col-lg-2">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                           {{ isset($school) && $school->active == 1 ? 'checked' :'' }} name="active"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('Activation')}}
                    </label>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                           {{ isset($school) && $school->student_login == 1 ? 'checked' :'' }} name="student_login"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('Student Login')}}
                    </label>
                </div>
            </div>
        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-6 d-flex justify-content-start">
                @isset($school)
                    <a href="{{route('manager.school-login', $school->id)}}" class="btn btn-info">{{ t('Login') }}</a>
                @endisset
            </div>
            <div class="col-6 d-flex justify-content-end">
                <button type="submit"
                        class="btn btn-primary">{{ isset($school) ? t('Update'):t('Create') }}</button>&nbsp;
            </div>

        </div>

    </form>


@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\SchoolRequest::class, '#form_data'); !!}
    <script>
        var avatar5 = new KTImageInput('kt_image_5');
    </script>
@endsection
