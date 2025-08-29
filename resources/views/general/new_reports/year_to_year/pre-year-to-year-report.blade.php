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
    <form class="form" id="form_data"
          action="{{route(getGuard().'.report.pre-year-to-year-report')}}"
          method="get">
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
                <div class="row g-6 mb-8">
                    <div class="col-md-4">
                        <label class="form-label required">{{t('Report Type')}}</label>
                        <select name="generated_report_type" id="generated_report_type" class="form-select"
                                data-control="select2"
                                data-placeholder="{{t('Select Type')}}" required>
                            <option></option>
                            <option value="attainment" selected>{{t('Progress Report')}}</option>
                            <option value="combined">{{t('Combined Progress Report')}}</option>
                        </select>
                    </div>
                    @if(guardIs('manager') || guardIs('inspection'))
                        <div class="col-md-4">
                            <label class="form-label required">{{t('School')}}</label>
                            <select class="form-select" data-control="select2" data-allow-clear="true"
                                    data-placeholder="{{t('Select School')}}" id="school_id" name="school_id[]" multiple required>
                                @foreach($schools as $school)
                                    <option value="{{$school->id}}">{{$school->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="school_id" name="school_id" value="{{Auth::guard('school')->id()}}">
                    @endif
                    <div class="col-md-4">
                        <label class="form-label required">{{t('Round')}}</label>
                        <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                                data-placeholder="{{t('Select One')}}" name="round">
                            <option></option>
                            <option value="september">{{t('September')}}</option>
                            <option value="february">{{t('February')}}</option>
                            <option value="may">{{t('May')}}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">{{t('Student Type')}}</label>
                        <select name="student_type" id="student_type" class="form-control form-select"
                                data-control="select2" data-placeholder="{{t('Student Type')}}">
                            <option value="2" selected>{{t('All')}}</option>
                            <option value="1">{{t('Arabs')}}</option>
                            <option value="0">{{t('Non-Arabs')}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Years Selection - Enhanced -->
        <div class="">
            <div class="d-flex align-items-center mb-4">
                <i class="ki-duotone ki-calendar fs-1 text-primary me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h4 class="fw-bold text-gray-800 mb-1">{{t('Year Selection')}}</h4>
                    <p class="text-muted fs-6 mb-0">{{t('Choose up to '.$yearsCount.' years for comparison (maximum '.$yearsCount.' years allowed)')}}</p>
                </div>
            </div>

                <div class="card-body py-2">
                    <div class="row g-4 mb-8">
                        @foreach($years as $year)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                <div
                                    class="year-item border border-gray-300 rounded-3 p-4 h-100 position-relative cursor-pointer"
                                    data-year="{{$year->name}}">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input year-checkbox" name="years[]"
                                               type="checkbox" value="{{$year->id}}"
                                               id="year_{{$year->name}}"/>
                                        <label
                                            class="form-check-label fw-semibold fs-6 text-gray-800 cursor-pointer w-100"
                                            for="year_{{$year->name}}">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <span class="text-gray-700 fs-7 fw-bolder">{{$year->name}}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>

        <!-- Grades Selection - Enhanced -->
        <div class="">
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

                <div class="card-header bg-light-primary py-4">
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
                </div>
        </div>

        <!-- Submit Section -->
        <div class="d-flex justify-content-end gap-3 flex-wrap">
            <button type="button" class="get-report btn btn-lg gradient-btn gradient-btn-primary"
                    data-route="{{route(getGuard().'.report.year-to-year-report')}}">
                <i class="fas fa-chart-line btn-icon"></i>
                {{t('The progress report')}}
            </button>
            <button type="button" class="get-report btn btn-lg gradient-btn gradient-btn-info"
                    data-route="{{route(getGuard().'.report.excel-year-to-year-report')}}">
                <i class="fas fa-file-download btn-icon"></i>
                {{t('Get Marksheet')}}
            </button>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\General\Report\YearToYearProgressReportRequest::class, '#form_data'); !!}

    {{--    <script type="text/javascript" src="{{ asset('assets_v1/js/report.js')}}?v=1"></script>--}}
    <script>
        $(document).ready(function () {
            // Year selection logic with limit of 2
            const yearLimit = {{$yearsCount}};
            const $yearCheckboxes = $('.year-checkbox');
            // Handle report generation
            $('.get-report').on('click', function (e) {
                e.preventDefault();
                const route = $(this).data('route');
                let formData = $("#form_data").serialize();

                // Check if at least one year is selected
                if ($('.year-checkbox:checked').length !== yearLimit) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{t('Error')}}',
                        text: '{{t('Please select exactly '.$yearsCount.' years for comparison.')}}'
                    });
                    return;
                }

                // Check if at least one grade is selected
                if ($('.grade-checkbox:checked').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{t('Error')}}',
                        text: '{{t('Please select at least one grade.')}}'
                    });
                    return;
                }
                if (!$('#form_data').valid()) {
                    // If the form is invalid, show an error message
                    Swal.fire({
                        icon: 'error',
                        title: '{{t('Error')}}',
                        text: '{{t('Please fill in all required fields correctly.')}}'
                    });
                    return;
                }

                window.open(route + '?' + formData, "_blank");
            });

            // Function to update year item visual state
            function updateYearItemVisual($checkbox) {
                const $yearItem = $checkbox.closest('.year-item');

                if ($checkbox.is(':checked')) {
                    $yearItem.addClass('border-success bg-light-success');
                    $yearItem.removeClass('border-gray-300');
                } else {
                    $yearItem.removeClass('border-success bg-light-success');
                    $yearItem.addClass('border-gray-300');
                }
            }

            // Make year items clickable anywhere
            $('.year-item').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $checkbox = $(this).find('.year-checkbox');
                const currentState = $checkbox.is(':checked');
                const checkedCount = $yearCheckboxes.filter(':checked').length;

                // If trying to check and already at limit, don't allow
                if (!currentState && checkedCount >= yearLimit) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{t('Limit Reached')}}',
                        text: '{{t('You can select maximum '.$yearsCount.' years for comparison.')}}'
                    });
                    return;
                }

                // Toggle the checkbox state
                $checkbox.prop('checked', !currentState);
                $checkbox.trigger('change');
            });

            // Handle direct checkbox clicks
            $('.year-checkbox').on('click', function (e) {
                e.stopPropagation();
                const checkedCount = $yearCheckboxes.filter(':checked').length;

                if ($(this).is(':checked') && checkedCount > yearLimit) {
                    $(this).prop('checked', false);
                    Swal.fire({
                        icon: 'warning',
                        title: '{{t('Limit Reached')}}',
                        text: '{{t('You can select maximum '.$yearsCount.' years for comparison.')}}'
                    });
                    return;
                }

                $(this).trigger('change');
            });

            // Handle year checkbox changes
            $yearCheckboxes.on('change', function () {
                updateYearItemVisual($(this));
            });

            // Add hover effects for year items
            $('.year-item').on('mouseenter', function () {
                if (!$(this).find('.year-checkbox').is(':checked')) {
                    $(this).addClass('border-primary bg-light-primary');
                }
            }).on('mouseleave', function () {
                if (!$(this).find('.year-checkbox').is(':checked')) {
                    $(this).removeClass('border-primary bg-light-primary');
                }
            });

        });
    </script>
@endsection
