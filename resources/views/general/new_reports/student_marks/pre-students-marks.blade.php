@extends(getGuard().'.layout.container')

@section('title', $title)
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('style')
    <link href="{{asset('assets_v1/css/additional.css')}}?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <form class="w-100" method="get" id="form_data" action="{{route(getGuard().'.report.student-mark-report')}}"
          autocomplete="off">
        <!-- Report Configuration Header -->
        <div class="d-flex align-items-center mb-4">
            <i class="ki-duotone ki-setting-2 fs-1 text-primary me-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div>
                <h4 class="fw-bold text-gray-800 mb-1">{{t('Report Configuration')}}</h4>
                <p class="text-muted fs-6 mb-0">{{t('Select required fields starting with a red asterisk *')}}</p>
            </div>
        </div>

        <div class="card-body py-2">
            <!-- Required Fields Section -->
            <div class="row g-4 mb-4">
                @if(guardIs('admin') || guardIs('inspection'))
                    <div class="col-md-9">
                        <label class="form-label required">{{t('School')}}</label>
                        <select class="form-control form-select" data-control="select2" data-allow-clear="true" multiple required
                                data-placeholder="{{t('Select School')}}" name="school_id[]" id="school_id">
                            @foreach($schools as $school)
                                <option value="{{$school->id}}">{{$school->name}}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="school_id" value="{{auth()->user()->id}}">
                @endif
                <div class="col-md-3">
                    <label class="form-label required">{{t('Year')}}</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                            data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
                        <option></option>
                        @foreach($years as $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <label>{{t('Levels')}} :</label>
                    <select name="level_id[]" id="levels_id" class="form-select direct-value" data-control="select2"
                            data-placeholder="{{t('Select Level')}}" multiple data-allow-clear="true">
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>{{t('Class Name')}} :</label>
                    <select name="grades_names[]" id="grades_names" class="form-select direct-value" data-control="select2"
                            data-placeholder="{{t('Select Class Name')}}" multiple data-allow-clear="true">
                    </select>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <label>{{t('SID Num')}}:</label>
                    <input type="text" name="id_number" class="form-control direct-search"
                           placeholder="{{t('Student Id Number')}}">
                </div>

                <div class="col-lg-3 ">
                    <label>{{t('Student Name')}}:</label>
                    <input type="text" name="name" class="form-control direct-search"
                           placeholder="{{t('Student Name')}}">
                </div>
                <div class="col-lg-3">
                    <label>{{t('Username')}}:</label>
                    <input type="text" name="email" class="form-control direct-search" placeholder="{{t('Username')}}">
                </div>
                <div class="col-lg-3">
                    <label>{{t('Gender')}} :</label>
                    <select name="gender" id="gender" class="form-select" data-control="select2"
                            data-placeholder="{{t('Select Gender')}}" data-allow-clear="true">
                        <option></option>
                        <option value="boy">{{t('Boy')}}</option>
                        <option value="girl">{{t('Girl')}}</option>
                    </select>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-3">
                    <label class="mb-1">{{t('Type Student')}}:</label>
                    <select class="form-control form-select " data-hide-search="true" data-control="select2"
                            data-placeholder="{{t('Select Student Type')}}" name="student_type" data-allow-clear="true">
                        <option></option>
                        <option value="2">{{t("All")}}</option>
                        <option value="1">{{t("Arabs")}}</option>
                        <option value="0">{{t("Non-Arabs")}}</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="mb-1">{{t('Sen Student')}}:</label>
                    <select class="form-control form-select" data-hide-search="true" data-control="select2"
                            data-placeholder="{{t('Select Student Status')}}" name="sen"
                            data-allow-clear="true">
                        <option></option>
                        <option value="1">{{t('SEN Student')}}</option>
                        <option value="2">{{t('Normal Student')}}</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="mb-1">{{t('Citizen Student')}}:</label>
                    <select class="form-control form-select " data-hide-search="true" data-control="select2"
                            data-placeholder="{{t('Select Student Status')}}" name="citizen"
                            data-allow-clear="true">
                        <option></option>
                        <option value="1">{{t('Citizen')}}</option>
                        <option value="2">{{t('NonCitizen')}}</option>
                    </select>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <label>{{t('Registration Date')}} :</label>
                    <input autocomplete="disabled" class="form-control form-control-solid" name="registration_date"
                           value="" placeholder="{{t('Pick date range')}}" id="registration_date"/>
                    <input type="hidden" name="start_date" id="start_registration_date"/>
                    <input type="hidden" name="end_date" id="end_registration_date"/>
                </div>


                <div class="col-lg-3">
                    <label>{{t('Order By')}} :</label>
                    <select name="orderBy" id="orderBy" class="form-select" data-control="select2"
                            data-placeholder="{{t('Select Type')}}">
                        <option value="latest" selected>{{t('Latest')}}</option>
                        <option value="name">{{t('Name')}}</option>
                        <option value="level">{{t('Level')}}</option>
                        <option value="section">{{t('Section')}}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="d-flex justify-content-end gap-3">
            <button type="submit" class="btn btn-lg get-report gradient-btn gradient-btn-primary"
                    data-route="{{route(getGuard().'.report.student-mark-report')}}">
                <i class="fas fa-file-alt btn-icon"></i>
                {{t('Export Report')}}
            </button>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{asset('assets_v1/js/report.js')}}?v=2"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\General\Report\StudentMarkRequest::class, '#form_data'); !!}
    <script>
        initializeDateRangePicker('registration_date');

        $(document).ready(function () {
            //onsubmit the form
            $('#form_data').on('submit', function (e) {
                e.preventDefault();
                // Validate the form
                if (!$('#form_data').validate().form()) {
                    // If the form is invalid, show an error message
                    Swal.fire({
                        icon: 'error',
                        title: '{{t('Error')}}',
                        text: '{{t('Please fill in all required fields correctly.')}}'
                    });
                    return;
                }
                this.submit();
            });
        });
    </script>

@endsection
