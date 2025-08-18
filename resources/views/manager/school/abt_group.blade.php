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
    <form class="form" id="form_data"
          action="{{route('manager.abt-school-group-store')}}"
          method="post">
        {{csrf_field()}}
        <div class="form-group row">
            <div class="col-lg-6 mb-2">
                <label class="form-label mb-1">{{t('School')}} :</label>
                <select name="school_id"  class="form-control form-select" data-control="select2"
                        data-placeholder="{{t('Select School')}}" data-allow-clear="true">
                    <option></option>
                    @foreach($schools as $school)
                        <option value="{{$school->id}}">{{$school->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 mb-2">
                <label class="form-label mb-1">{{t('Primary Year')}}:</label>
                <select name="primary_year" data-placeholder="{{t('Select Year')}}"
                        class="form-select" data-control="select2" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($years as $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 mb-2">
                <label class="form-label mb-1">{{t('Secondary Years')}}:</label>
                <select name="secondary_years[]" multiple data-placeholder="{{t('Select Year')}}"
                        class="form-select" data-control="select2" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($years as $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex gap-2 mt-10">
                    <div class="form-check form-check-custom form-check-solid">
                        <input name="link_by_number" class="form-check-input" type="radio" checked value="1" id="flexRadioDefault1"/>
                        <label class="form-check-label text-dark fs-6" for="flexRadioDefault1">
                            {{t('Link by full id number')}}
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid">
                        <input name="link_by_number" class="form-check-input" type="radio" value="2" id="flexRadioDefault2"/>
                        <label class="form-check-label text-dark fs-6" for="flexRadioDefault2">
                            {{t('Link by a part form id number')}}
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid">
                        <input name="link_by_number" class="form-check-input" type="radio" value="3" id="flexRadioDefault3"/>
                        <label class="form-check-label text-dark fs-6" for="flexRadioDefault3">
                            {{t('Link by Full Name')}}
                        </label>
                    </div>

                </div>
            </div>

            <div class="col-lg-6 mb-2 d-none" id="dialer">
                <label class="mb-2">{{t('The count of numbers')}}:</label>
                <!--begin::Dialer-->
                <div class="input-group "
                     data-kt-dialer="true"
                     data-kt-dialer-currency="true"
                     data-kt-dialer-min="1"
                     data-kt-dialer-max="10"
                     data-kt-dialer-step="1"
                     data-kt-dialer-prefix="">

                    <!--begin::Decrease control-->
                    <button class="btn btn-icon btn-outline btn-active-color-primary" type="button" data-kt-dialer-control="decrease">
                        <i class="ki-duotone ki-minus fs-2"></i>
                    </button>
                    <!--end::Decrease control-->

                    <!--begin::Input control-->
                    <input name="link_number" type="text" class="form-control" readonly placeholder="Amount" value="1" data-kt-dialer-control="input"/>
                    <!--end::Input control-->

                    <!--begin::Increase control-->
                    <button class="btn btn-icon btn-outline btn-active-color-primary" type="button" data-kt-dialer-control="increase">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </button>
                    <!--end::Increase control-->
                </div>
                <!--end::Dialer-->
            </div>





        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button  type="submit" class="btn btn-primary me-5">{{t('Group Selected Data')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    <script>
        $(document).ready(function () {
            //disable default submit form
            $('input[name="link_by_number"]').change(function () {
                if ($(this).is(':checked') && $(this).val()==='2'){
                    $('#dialer').removeClass('d-none')
                }else if(!$('#dialer').hasClass('d-none')){
                    $('#dialer').addClass('d-none')
                }
            })
        });

    </script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\SchoolAbtGroupRequest::class, '#form_data'); !!}
@endsection
