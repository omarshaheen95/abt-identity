@extends(getGuard().'.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush

@section('style')
    <link href="{{asset('assets_v1/css/additional.css')}}?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <form class="form" id="form_data" action="{{route(getGuard().'.report.attainment-report')}}" method="get">
        <input type="hidden" name="summary" id="summary" value="0">

        <!-- Report Configuration - Enhanced -->
        <div class="">
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
            <!-- Basic Filters -->
            <div class="card-body py-2">
                <!-- Basic Filters -->
                <div class="row g-6 mb-8">
                    <div class="col-md-4">
                        <label class="form-label required">{{t('Report Type')}}</label>
                        <select name="generated_report_type" id="generated_report_type" class="form-select"
                                data-control="select2"
                                data-placeholder="{{t('Select Type')}}" required>
                            <option></option>
                            <option value="attainment" selected>{{t('Attainment Report')}}</option>
                            <option value="combined">{{t('Combined Attainment Report')}}</option>
                        </select>
                    </div>
                    @if(guardIs('manager') || guardIs('inspection'))
                        <div class="col-md-4">
                            <label class="form-label required">{{t('School')}}</label>
                            <select class="form-select" data-control="select2" data-allow-clear="true"
                                    data-placeholder="{{t('Select School')}}" id="school_id" name="school_id[]" multiple required>
                                @foreach($schools as $school)
                                    <option value="{{$school->id}}" data-expected-report="{{$school->national_report}}"
                                            data-advanced-report="{{$school->international_report}}">{{$school->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="school_id" value="{{auth()->user()->id}}">
                    @endif
                    <div class="col-md-4">
                        <label class="form-label required">{{t('Year')}}</label>
                        <select class="form-select" data-control="select2" data-allow-clear="true"
                                data-placeholder="{{t('Select Year')}}" id="year_id" name="year_id" required>
                            <option></option>
                            @foreach($years as $year)
                                <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{t('Sections')}}</label>
                        <select name="grades_names[]" multiple id="grades_names" class="form-select" data-control="select2"
                                data-placeholder="{{t('Select Sections')}}" data-allow-clear="true">
                        </select>
                    </div>

                    <div id="" class="col-md-4">
                        <label class="form-label required">{{t("Student Type")}}</label>
                        <select name="student_type" id="student_type_attainment" class="form-select"
                                data-control="select2"
                                data-placeholder="{{t("Student Type")}}">
                            <option value="2" selected>{{t("All")}}</option>
                            <option value="1">{{t("Arabs")}}</option>
                            <option value="0">{{t("Non-Arabs")}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grades Selection - Enhanced -->
        <div class="mb-10">
            <div class="d-flex align-items-center mb-4">
                <i class="ki-duotone ki-grid fs-1 text-primary me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h4 class="fw-bold text-gray-800 mb-1">{{t('Grade Selection')}}</h4>
                    <p class="text-muted fs-6 mb-0">{{t('Choose which grades to include in your report')}}</p>
                </div>
            </div>
                <div class="card-header bg-light-secondary py-4">
                    <div class="card-title">
                        <div class="form-check form-check-custom form-check-solid form-check-lg">
                            <input class="form-check-input" type="checkbox" name="all_grades"
                                   value="all_grades" id="all_grades_toggle" checked/>
                            <label class="form-check-label fw-bold fs-5 text-primary" for="all_grades_toggle">
                                {{t('Select All Grades')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body py-8">
                    <!-- Individual Grades with Enhanced Styling -->
                    <div class="row g-4 mb-8">
                        @foreach(\App\Helpers\Constant::GRADES as $grade)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                <div
                                    class="grade-item border border-gray-300 rounded-3 p-4 h-100 position-relative cursor-pointer"
                                    data-grade="{{$grade}}">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input grade-checkbox" name="grades[]"
                                               type="checkbox" value="{{$grade}}"
                                               id="grade_{{$grade}}" checked/>
                                        <label
                                            class="form-check-label fw-semibold fs-6 text-gray-800 cursor-pointer w-100"
                                            for="grade_{{$grade}}">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <span
                                                    class="text-gray-700 fs-7 fw-bolder">{{t('Grade')}} {{$grade}} / {{t('Year')}} {{$grade + 1}}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Special Options with Better Styling -->
                    <div class="separator separator-dashed my-8"></div>
                    <div class="row g-6">
                        <div class="col-12 mb-4">
                            <h6 class="fw-bold text-gray-800 mb-3">
                                <i class="ki-duotone ki-gear fs-3 text-info me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{t('Special Student Categories')}}
                            </h6>
                        </div>
                        <div class="col-sm-6">
                            <div
                                class="special-category-item p-4 border border-2 rounded-3 cursor-pointer position-relative transition-all"
                                data-category="sen" style="transition: all 0.3s ease;">
                                <input class="form-check-input special-category-toggle d-none" type="checkbox" value="1"
                                       id="include_sen" name="include_sen" checked/>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="symbol symbol-40px me-3">
                                                <div class="symbol-label bg-light-warning text-info fw-bold">
                                                    <i class="ki-duotone ki-profile-user fs-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-gray-800 fs-6 mb-1">{{t('SEN Students')}}</div>
                                                <div class="text-muted fs-7">{{t('Special Educational Needs')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="category-toggle-btn">
                                        <div
                                            class="btn btn-sm btn-outline btn-outline-warning btn-active-warning fw-bold category-btn-included">
                                            <i class="ki-duotone ki-check fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{t('INCLUDED')}}
                                        </div>
                                        <div
                                            class="btn btn-sm btn-outline btn-outline-secondary btn-active-secondary fw-bold category-btn-excluded d-none">
                                            <i class="ki-duotone ki-minus fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{t('EXCLUDED')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div
                                class="special-category-item p-4 border border-2 rounded-3 cursor-pointer position-relative transition-all"
                                data-category="gt" style="transition: all 0.3s ease;">
                                <input class="form-check-input special-category-toggle d-none" type="checkbox" value="1"
                                       id="include_g_t" name="include_g_t" checked/>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="symbol symbol-40px me-3">
                                                <div class="symbol-label bg-light-warning text-warning fw-bold">
                                                    <i class="ki-duotone ki-award fs-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-gray-800 fs-6 mb-1">{{t('G&T Students')}}</div>
                                                <div class="text-muted fs-7">{{t('Gifted & Talented')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="category-toggle-btn">
                                        <div
                                            class="btn btn-sm btn-outline btn-outline-warning btn-active-warning fw-bold category-btn-included">
                                            <i class="ki-duotone ki-check fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{t('INCLUDED')}}
                                        </div>
                                        <div
                                            class="btn btn-sm btn-outline btn-outline-secondary btn-active-secondary fw-bold category-btn-excluded d-none">
                                            <i class="ki-duotone ki-minus fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{t('EXCLUDED')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Submit Section -->
        <div class="d-flex justify-content-end gap-3">
            <button type="button" class="btn btn-lg get-report gradient-btn gradient-btn-primary"
                    data-route="{{route(getGuard().'.report.attainment-report')}}">
                <i class="fas fa-file-alt btn-icon"></i>
                {{t('Generate PDF Report')}}
            </button>
            <button type="button" class="btn btn-lg get-report gradient-btn gradient-btn-info"
                    data-route="{{route(getGuard().'.report.student-mark-report')}}">
                <i class="fas fa-file-excel btn-icon"></i>
                {{t('Generate Excel Report')}}
            </button>
        </div>
    </form>
@endsection

@section('script')
    <script>
        var ERROR_TITLE = '{{t('Error')}}';
        var ERROR_MESSAGE = '{{t('Please fill in all required fields correctly.')}}';
    </script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets_v1/js/report.js')}}"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\General\Report\AttainmentReportRequest::class, '#form_data'); !!}
@endsection
