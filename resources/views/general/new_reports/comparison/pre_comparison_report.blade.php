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
          action="{{route(getGuard().'.report.comparison-report')}}"
          method="get">


        <div class="form-group row">
            @if(guardIs('manager'))
            <div class="col mb-2">
                <label class="form-label mb-1">{{t('School')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select School')}}" id="school_id" name="school_id">
                    <option></option>
                    @foreach($schools as $school)
                        <option
                            value="{{$school->id}}">{{$school->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col mb-2">
                <label class="form-label mb-1">{{t('Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Year')}}" id="year_id" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option
                            value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col mb-2">
                <label class="form-label mb-1">{{t('Round')}} :</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select One')}}" name="round">
                    <option></option>
                    <option value="september">{{t('September')}}</option>
                    <option value="february">{{t('February')}}</option>
                    <option value="may">{{t('May')}}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">

            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Gender')}} :</label>
                <select name="gender" id="gender" class="form-control form-select" data-control="select2"
                        data-placeholder="{{t('Gender')}}">
                    <option value="0">{{t('All')}}</option>
                    <option value="boy">{{t('Boys')}}</option>
                    <option value="girl">{{t('Girls')}}</option>
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Student Type')}} :</label>
                <select name="student_type" id="student_type" class="form-control form-select" data-control="select2"
                        data-placeholder="{{t('Type')}}">
                    <option value=""></option>
                    <option value="1">{{t('Arabs')}}</option>
                    <option value="2">{{t('Non-Arabs')}}</option>
                </select>
            </div>
            <!-- Curriculums Selection - Enhanced -->
            <div class="col-lg-12 mb-6">
                <div class="d-flex align-items-center mb-4">
                    <i class="ki-duotone ki-book fs-1 text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    <div>
                        <h4 class="fw-bold text-gray-800 mb-1">{{t('The Curriculums')}}</h4>
                        <p class="text-muted fs-6 mb-0">{{t('Choose which curriculums to include in your report')}}</p>
                    </div>
                </div>
                <div class="card-header bg-light-secondary py-4">
                    <div class="card-title">
                        <div class="form-check form-check-custom form-check-solid form-check-lg">
                            <input class="form-check-input" type="checkbox" name="all_curriculums"
                                   value="all_curriculums" id="all_curriculums_toggle" checked/>
                            <label class="form-check-label fw-bold fs-5 text-primary" for="all_curriculums_toggle">
                                {{t('Select All Curriculums')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body py-8">
                    <div class="row g-4">
                        @foreach(schoolsType() as $key => $type)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                <div class="curriculum-item border border-gray-300 rounded-3 p-4 h-100 position-relative cursor-pointer" data-curriculum="{{$key}}">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input curriculum-checkbox" name="curriculums[]" type="checkbox" value="{{$key}}" id="curriculum_{{$key}}" checked/>
                                        <label class="form-check-label fw-semibold fs-6 text-gray-800 cursor-pointer w-100" for="curriculum_{{$key}}">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <span class="text-gray-700 fs-7 fw-bolder">{{t($type)}}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Countries Selection - Enhanced -->
{{--            <div class="col-lg-12 mb-6">--}}
{{--                <div class="d-flex align-items-center mb-4">--}}
{{--                    <i class="ki-duotone ki-geolocation fs-1 text-primary me-3">--}}
{{--                        <span class="path1"></span>--}}
{{--                        <span class="path2"></span>--}}
{{--                    </i>--}}
{{--                    <div>--}}
{{--                        <h4 class="fw-bold text-gray-800 mb-1">{{t('The Countries')}}</h4>--}}
{{--                        <p class="text-muted fs-6 mb-0">{{t('Choose which countries to include in your report')}}</p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-header bg-light-secondary py-4">--}}
{{--                    <div class="card-title">--}}
{{--                        <div class="form-check form-check-custom form-check-solid form-check-lg">--}}
{{--                            <input class="form-check-input" type="checkbox" name="all_countries"--}}
{{--                                   value="all_countries" id="all_countries_toggle" checked/>--}}
{{--                            <label class="form-check-label fw-bold fs-5 text-primary" for="all_countries_toggle">--}}
{{--                                {{t('Select All Countries')}}--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body py-8">--}}
{{--                    <div class="row g-4">--}}
{{--                        @foreach(schoolsCountry() as $key => $country)--}}
{{--                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">--}}
{{--                                <div class="country-item border border-gray-300 rounded-3 p-4 h-100 position-relative cursor-pointer" data-country="{{$key}}">--}}
{{--                                    <div class="form-check form-check-custom form-check-solid">--}}
{{--                                        <input class="form-check-input country-checkbox" name="countries[]" type="checkbox" value="{{$key}}" id="country_{{$key}}" checked/>--}}
{{--                                        <label class="form-check-label fw-semibold fs-6 text-gray-800 cursor-pointer w-100" for="country_{{$key}}">--}}
{{--                                            <div class="d-flex flex-column align-items-center text-center">--}}
{{--                                                <span class="text-gray-700 fs-7 fw-bolder">{{t($country)}}</span>--}}
{{--                                            </div>--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <!-- Grades Selection - Enhanced -->
            <div class="col-lg-12 mb-10">
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
                    <div class="row g-4">
                        @foreach($grades as $grade)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                <div class="grade-item border border-gray-300 rounded-3 p-4 h-100 position-relative cursor-pointer" data-grade="{{$grade}}">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input grade-checkbox" name="grades[]" type="checkbox" value="{{$grade}}" id="grade_{{$grade}}" checked/>
                                        <label class="form-check-label fw-semibold fs-6 text-gray-800 cursor-pointer w-100" for="grade_{{$grade}}">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <span class="text-gray-700 fs-7 fw-bolder">{{t('Grade')}} {{$grade}} / {{t('Year')}} {{$grade + 1}}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="get-report btn btn-primary me-5"
                        data-route="{{route(getGuard().'.report.comparison-report')}}">{{t('Get Report')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>
        var ERROR_TITLE = '{{t('Error')}}';
        var ERROR_MESSAGE = '{{t('Please fill in all required fields correctly.')}}';
    </script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    <script type="text/javascript" src="{{ asset('assets_v1/js/report.js')}}?v=3"></script>
    <script>
        $(document).ready(function() {
            /**
             * Generic function to handle selection logic for any category
             * @param {string} itemClass - Class for the item container (e.g., '.curriculum-item')
             * @param {string} checkboxClass - Class for checkboxes (e.g., '.curriculum-checkbox')
             * @param {string} toggleId - ID for "Select All" toggle (e.g., '#all_curriculums_toggle')
             */
            function initializeSelectionHandler(itemClass, checkboxClass, toggleId) {
                const $allToggle = $(toggleId);
                const $checkboxes = $(checkboxClass);

                // Function to update item visual state
                function updateItemVisual($checkbox) {
                    const $item = $checkbox.closest(itemClass);

                    if ($checkbox.is(':checked')) {
                        $item.addClass('border-info bg-light-info');
                        $item.removeClass('border-gray-300');
                    } else {
                        $item.removeClass('border-info bg-light-info');
                        $item.addClass('border-gray-300');
                    }
                }

                // Make items clickable anywhere
                $(itemClass).on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $checkbox = $(this).find(checkboxClass);
                    const currentState = $checkbox.is(':checked');

                    // Toggle the checkbox state
                    $checkbox.prop('checked', !currentState);

                    // Trigger change event to update visuals
                    $checkbox.trigger('change');
                });

                // Handle direct checkbox clicks
                $(checkboxClass).on('click', function (e) {
                    e.stopPropagation();
                    // Let the checkbox handle its own state change
                    $(this).trigger('change');
                });

                // Handle checkbox changes
                $(checkboxClass).on('change', function () {
                    const totalItems = $checkboxes.length;
                    const checkedItems = $checkboxes.filter(':checked').length;

                    $allToggle.prop('checked', checkedItems === totalItems);
                    $allToggle.prop('indeterminate', checkedItems > 0 && checkedItems < totalItems);

                    // Update visual state for this specific item
                    updateItemVisual($(this));
                });

                // Initialize visual states
                $checkboxes.each(function () {
                    updateItemVisual($(this));
                });

                // When "Select All" is clicked
                $allToggle.on('change', function () {
                    $checkboxes.prop('checked', this.checked);
                    $checkboxes.each(function () {
                        updateItemVisual($(this));
                    });
                });

                // Add hover effects
                $(itemClass).on('mouseenter', function () {
                    if (!$(this).find(checkboxClass).is(':checked')) {
                        $(this).addClass('border-primary bg-light-primary');
                    }
                }).on('mouseleave', function () {
                    if (!$(this).find(checkboxClass).is(':checked')) {
                        $(this).removeClass('border-primary bg-light-primary');
                    }
                });
            }

            // Initialize handlers for each category
            initializeSelectionHandler('.curriculum-item', '.curriculum-checkbox', '#all_curriculums_toggle');
            // initializeSelectionHandler('.country-item', '.country-checkbox', '#all_countries_toggle');
        });
    </script>
    {!! JsValidator::formRequest(\App\Http\Requests\General\Report\ComparisonReportRequest::class, '#form_data'); !!}
@endsection
