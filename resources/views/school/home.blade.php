@extends('school.layout.container')
@section('title')
    {{$title}}
@endsection
@if(Auth::guard('school')->user()->active)
    @section('charts')
        <div class="row gy-5 g-xl-10 justify-content-center">
            <div class="col-sm-6 col-xl-2 mb-xl-10">
                <div class="card h-lg-100" style="background-color: #F1416C;background-image:url('/new_assets/media/svg/shapes/wave-bg-red.svg')">
                    <!--begin::Body-->
                    <div class="card-body d-flex justify-content-between align-items-start flex-column px-2 py-8">
                        <!--begin::Icon-->
                        <div class="d-flex justify-content-center w-100 m-0">
                            <div class="d-flex flex-center rounded-circle h-80px w-80px" style="border: 1px dashed rgba(255, 255, 255, 0.4);">

                                <i class="ki-duotone ki-profile-user fs-2hx text-white">
                                    <i class="path1"></i>
                                    <i class="path2"></i>
                                    <i class="path3"></i>
                                    <i class="path4"></i>
                                </i>
                            </div>

                        </div>
                        <!--end::Icon-->
                        <!--begin::Section-->
                        <div class="d-flex flex-column mt-5 align-items-center w-100">
                            <!--begin::Number-->
                            <span class="fw-semibold fs-2x text-white lh-1 ls-n2">{{$data['students_count']}} </span>
                            <!--end::Number-->

                            <!--begin::Follower-->
                            <div class="m-0">
                                <span class="fw-semibold fs-6 text-white text-center">{{t('Students')}}</span>
                            </div>
                            <!--end::Follower-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <div class="col-sm-6 col-xl-2 mb-xl-10">
                <div class="card h-lg-100" style="background-color: #f141bf;background-image:url('/new_assets/media/svg/shapes/wave-bg-red.svg')">
                    <!--begin::Body-->
                    <div class="card-body d-flex justify-content-between align-items-start flex-column px-2 py-8">
                        <!--begin::Icon-->
                        <div class="d-flex justify-content-center w-100 m-0">
                            <div class="d-flex flex-center rounded-circle h-80px w-80px" style="border: 1px dashed rgba(255, 255, 255, 0.4);">

                                <i class="ki-duotone ki-note-2 fs-2hx text-white">
                                    <i class="path1"></i>
                                    <i class="path2"></i>
                                    <i class="path3"></i>
                                    <i class="path4"></i>
                                </i>
                            </div>

                        </div>
                        <!--end::Icon-->
                        <!--begin::Section-->
                        <div class="d-flex flex-column mt-5 align-items-center w-100">
                            <!--begin::Number-->
                            <span class="fw-semibold fs-2x text-white lh-1 ls-n2">{{$data['student_assessments']}} </span>
                            <!--end::Number-->

                            <!--begin::Follower-->
                            <div class="d-flex justify-content-center m-0">
                                <span class="fw-semibold fs-6 text-white text-center">{{t('Students Assessments')}}</span>
                            </div>
                            <!--end::Follower-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <div class="col-sm-6 col-xl-2 mb-xl-10">
                <div class="card h-lg-100" style="background-color: #1da2b7;background-image:url('/new_assets/media/svg/shapes/wave-bg-red.svg')">
                    <!--begin::Body-->
                    <div class="card-body d-flex justify-content-between align-items-start flex-column px-2 py-8">
                        <!--begin::Icon-->
                        <div class="d-flex justify-content-center w-100 m-0">
                            <div class="d-flex flex-center rounded-circle h-80px w-80px" style="border: 1px dashed rgba(255, 255, 255, 0.4);">
                                <i class="ki-duotone ki-book-open fs-2hx text-white">
                                    <i class="path1"></i>
                                    <i class="path2"></i>
                                    <i class="path3"></i>
                                    <i class="path4"></i>
                                </i>
                            </div>

                        </div>
                        <!--end::Icon-->
                        <!--begin::Section-->
                        <div class="d-flex flex-column mt-5 align-items-center w-100">
                            <!--begin::Number-->
                            <span class="fw-semibold fs-2x text-white lh-1 ls-n2">{{$data['corrected_assessments']}} </span>
                            <!--end::Number-->

                            <!--begin::Follower-->
                            <div class="d-flex justify-content-center m-0">
                                <span class="fw-semibold fs-6 text-white text-center">{{t('Corrected Assessments')}}</span>
                            </div>
                            <!--end::Follower-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <div class="col-sm-6 col-xl-2 mb-xl-10">
                <div class="card h-lg-100" style="background-color: #ff8640;background-image:url('/new_assets/media/svg/shapes/wave-bg-red.svg')">
                    <!--begin::Body-->
                    <div class="card-body d-flex justify-content-between align-items-start flex-column px-2 py-8">
                        <!--begin::Icon-->
                        <div class="d-flex justify-content-center w-100 m-0">
                            <div class="d-flex flex-center rounded-circle h-80px w-80px" style="border: 1px dashed rgba(255, 255, 255, 0.4);">
                                <i class="ki-duotone ki-document fs-2hx text-white">
                                    <i class="path1"></i>
                                    <i class="path2"></i>
                                </i>
                            </div>

                        </div>
                        <!--end::Icon-->
                        <!--begin::Section-->
                        <div class="d-flex flex-column mt-5  align-items-center w-100">
                            <!--begin::Number-->
                            <span class="fw-semibold fs-2x text-white lh-1 ls-n2">{{$data['uncorrected_assessments']}}</span>
                            <!--end::Number-->

                            <!--begin::Follower-->
                            <div class="d-flex justify-content-center m-0">
                                <span class="fw-semibold fs-6 text-white text-center">{{t('Uncorrected Assessments')}}</span>
                            </div>
                            <!--end::Follower-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <div class="col-sm-6 col-xl-2 mb-xl-10">
                <div class="card h-lg-100" style="background-color: #2db77a;background-image:url('/new_assets/media/svg/shapes/wave-bg-red.svg')">
                    <!--begin::Body-->
                    <div class="card-body d-flex justify-content-between align-items-start flex-column px-2 py-8">
                        <!--begin::Icon-->
                        <div class="d-flex justify-content-center w-100 m-0">
                            <div class="d-flex flex-center rounded-circle h-80px w-80px" style="border: 1px dashed rgba(255, 255, 255, 0.4);">
                                <i class="ki-duotone ki-questionnaire-tablet fs-2hx text-white">
                                    <i class="path1"></i>
                                    <i class="path2"></i>
                                </i>
                            </div>

                        </div>
                        <!--end::Icon-->
                        <!--begin::Section-->
                        <div class="d-flex flex-column mt-5 align-items-center w-100">
                            <!--begin::Number-->
                            <span class="fw-semibold fs-2x text-white lh-1 ls-n2">{{$data['marking_requests']}} </span>
                            <span class="text-white fs-6">({{$data['new_marking_requests']}} {{t('Pending')}})</span>
                            <!--end::Number-->

                            <!--begin::Follower-->
                            <div class="d-flex justify-content-center m-0">
                                <span class="fw-semibold fs-6 text-white text-center">{{t('Marking Request')}}</span>
                            </div>
                            <!--end::Follower-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

        </div>
        <div class="row gy-5 g-xl-10">
            <div class="col-12">
                <!--begin::Chart widget 38-->
                <div class="card card-flush  mb-xl-10">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <!--begin::Title-->
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">{{t('Students Login Statistics')}} <span id="student_login_data_total" class="fs-6 text-danger">{{$login_data['total']}}</span></span>
                        </h3>
                        <!--end::Title-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                            <div class=" mb-5 input-group input-group-solid">
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Datepicker-->
                                    <input id="StudentLoginDataPicker" class="form-control form-control-solid ps-12 fs-8" placeholder="Select a date" name="due_date" type="text" readonly="readonly">
                                    <input type="hidden" name="start_StudentLoginDataPicker" id="start_StudentLoginDataPicker" value="{{date('Y-m-d')}}" />
                                    <input type="hidden" name="end_StudentLoginDataPicker" id="end_StudentLoginDataPicker" value="{{date('Y-m-d')}}" />
                                    <!--end::Datepicker-->
                                    <!--begin::Icon-->
                                    <i class="ki-duotone ki-calendar-8 fs-2 position-absolute mx-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                    <!--end::Icon-->
                                </div>
                            </div>
                            <!--end::Daterangepicker-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end px-0 pt-3 pb-5">
                        <!--begin::Chart-->
                        <div id="StudentLoginData_chart" class="h-325px w-100 min-h-auto ps-4 pe-6"></div>
                        <!--end::Chart-->
                    </div>
                    <!--end: Card Body-->
                </div>
                <!--end::Chart widget 38-->
            </div>

            <div class="col-12">
                <!--begin::Chart widget 38-->
                <div class="card card-flush  mb-xl-10">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <!--begin::Title-->
                        <h4 class="card-title  align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">{{t('Students Assessments Statistics')}}</span>
                        </h4>
                        <!--end::Title-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                            <div class=" mb-5 input-group input-group-solid">
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Datepicker-->
                                    <input id="TermDataPicker" class="form-control form-control-solid ps-12 flatpickr-input fs-8" placeholder="Select a date" name="due_date" type="text" readonly="readonly">
                                    <input type="hidden" name="start_TermDataPicker" id="start_TermDataPicker" value="{{date('Y-m-d')}}" />
                                    <input type="hidden" name="end_TermDataPicker" id="end_TermDataPicker" value="{{date('Y-m-d')}}" />
                                    <!--end::Datepicker-->
                                    <!--begin::Icon-->
                                    <i class="ki-duotone ki-calendar-8 fs-2 position-absolute mx-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                    <!--end::Icon-->
                                </div>
                            </div>
                            <!--end::Daterangepicker-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end px-0 pt-3 pb-5">
                        <!--begin::Chart-->
                        <div id="TermData_chart" class="h-325px w-100 min-h-auto ps-4 pe-6"></div>
                        <!--end::Chart-->
                    </div>
                    <!--end: Card Body-->
                </div>
                <!--end::Chart widget 38-->
            </div>

        </div>
    @endsection
    @section('script')
        <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>

        <script>
            initializeDateRangePicker('StudentLoginDataPicker', [$('#start_StudentLoginDataPicker').val(), $('#end_StudentLoginDataPicker').val()]);
            initializeDateRangePicker('TermDataPicker', [$('#start_TermDataPicker').val(), $('#end_TermDataPicker').val()]);
            var StudentLoginData = {
                'categories': {!! json_encode($login_data['categories']) !!},
                'data': {!! json_encode($login_data['data']) !!},
            };
            var TermData = {
                'categories': {!! json_encode($term_data['categories']) !!},
                'data': {!! json_encode($term_data['data']) !!},
            };

            var StudentLoginDataChart = function () {
                var e = {self: null, rendered: !1}, t = function () {
                    var t = document.getElementById("StudentLoginData_chart");
                    if (t) {
                        var a = parseInt(KTUtil.css(t, "height")), l = KTUtil.getCssVariableValue("--bs-gray-900"),
                            r = KTUtil.getCssVariableValue("--bs-border-dashed-color"), o = {
                                series: [{name: "LOI Issued", data: StudentLoginData.data}],
                                chart: {fontFamily: "inherit", type: "bar", height: a, toolbar: {show: !1}},
                                plotOptions: {
                                    bar: {
                                        horizontal: !1,
                                        columnWidth: ["28%"],
                                        borderRadius: 5,
                                        dataLabels: {position: "top"},
                                        startingShape: "flat"
                                    }
                                },
                                legend: {show: !1},
                                dataLabels: {
                                    enabled: !0,
                                    offsetY: -28,
                                    style: {fontSize: "13px", colors: [l]},
                                    formatter: function (e) {
                                        return e
                                    }
                                },
                                stroke: {show: !0, width: 2, colors: ["transparent"]},
                                xaxis: {
                                    categories: StudentLoginData.categories,
                                    axisBorder: {show: !1},
                                    axisTicks: {show: !1},
                                    labels: {style: {colors: KTUtil.getCssVariableValue("--bs-gray-500"), fontSize: "13px"}},
                                    crosshairs: {fill: {gradient: {opacityFrom: 0, opacityTo: 0}}}
                                },
                                yaxis: {
                                    labels: {
                                        style: {colors: KTUtil.getCssVariableValue("--bs-gray-500"), fontSize: "13px"},

                                    }
                                },
                                fill: {opacity: 1},
                                states: {
                                    normal: {filter: {type: "none", value: 0}},
                                    hover: {filter: {type: "none", value: 0}},
                                    active: {allowMultipleDataPointsSelection: !1, filter: {type: "none", value: 0}}
                                },
                                tooltip: {
                                    style: {fontSize: "12px"}, y: {
                                        formatter: function (e) {
                                            return +e + "M"
                                        }
                                    }
                                },
                                colors: [KTUtil.getCssVariableValue("--bs-primary"), KTUtil.getCssVariableValue("--bs-primary-light")],
                                grid: {borderColor: r, strokeDashArray: 4, yaxis: {lines: {show: !0}}}
                            };
                        e.self = new ApexCharts(t, o), setTimeout((function () {
                            e.self.render(), e.rendered = !0
                        }), 200)
                    }
                };
                return {
                    init: function () {
                        t(), KTThemeMode.on("kt.thememode.change", (function () {
                            e.rendered && e.self.destroy(), t()
                        }))
                    },
                    refetch: function () {
                        e.self.destroy();
                        t();
                    }
                }
            }();
            StudentLoginDataChart.init();
            var TermDataChart = function () {
                var e = {self: null, rendered: !1}, t = function () {
                    var t = document.getElementById("TermData_chart");
                    if (t) {
                        var a = parseInt(KTUtil.css(t, "height")), l = KTUtil.getCssVariableValue("--bs-gray-900"),
                            r = KTUtil.getCssVariableValue("--bs-border-dashed-color"), o = {
                                series: [{name: "LOI Issued", data: TermData.data}],
                                chart: {fontFamily: "inherit", type: "bar", height: a, toolbar: {show: !1}},
                                plotOptions: {
                                    bar: {
                                        horizontal: !1,
                                        columnWidth: ["28%"],
                                        borderRadius: 5,
                                        dataLabels: {position: "top"},
                                        startingShape: "flat"
                                    }
                                },
                                legend: {show: !1},
                                dataLabels: {
                                    enabled: !0,
                                    offsetY: -28,
                                    style: {fontSize: "13px", colors: [l]},
                                    formatter: function (e) {
                                        return e
                                    }
                                },
                                stroke: {show: !0, width: 2, colors: ["transparent"]},
                                xaxis: {
                                    categories: TermData.categories,
                                    axisBorder: {show: !1},
                                    axisTicks: {show: !1},
                                    labels: {style: {colors: KTUtil.getCssVariableValue("--bs-gray-500"), fontSize: "13px"}},
                                    crosshairs: {fill: {gradient: {opacityFrom: 0, opacityTo: 0}}}
                                },
                                yaxis: {

                                    labels: {
                                        style: {colors: KTUtil.getCssVariableValue("--bs-gray-500"), fontSize: "13px"},

                                    }
                                },
                                fill: {opacity: 1},
                                states: {
                                    normal: {filter: {type: "none", value: 0}},
                                    hover: {filter: {type: "none", value: 0}},
                                    active: {allowMultipleDataPointsSelection: !1, filter: {type: "none", value: 0}}
                                },
                                tooltip: {
                                    style: {fontSize: "12px"}, y: {
                                        formatter: function (e) {
                                            return +e + "M"
                                        }
                                    }
                                },
                                colors: [KTUtil.getCssVariableValue("--bs-primary"), KTUtil.getCssVariableValue("--bs-primary-light")],
                                grid: {borderColor: r, strokeDashArray: 4, yaxis: {lines: {show: !0}}}
                            };
                        e.self = new ApexCharts(t, o), setTimeout((function () {
                            e.self.render(), e.rendered = !0
                        }), 200)
                    }
                };
                return {
                    init: function () {
                        t(), KTThemeMode.on("kt.thememode.change", (function () {
                            e.rendered && e.self.destroy(), t()
                        }))
                    },
                    refetch: function () {
                        e.self.destroy();
                        t();
                    }
                }
            }();
            TermDataChart.init();


            $('#StudentLoginDataPicker').on('apply.daterangepicker', function (ev, picker) {
                getStudentLoginData();
            });
            $('#TermDataPicker').on('apply.daterangepicker', function (ev, picker) {
                getTermData();
            });

            //send start and end date to controller to get data through ajax
            function getStudentLoginData() {
                var start_date = $('#start_StudentLoginDataPicker').val();
                var end_date = $('#end_StudentLoginDataPicker').val();
                $.ajax({
                    url: "{{ route('school.statistics.student_login_data') }}",
                    type: "POST",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        $('#student_login_data_total').text(data.data.total);
                        StudentLoginData.data = data.data.data;
                        StudentLoginData.categories = data.data.categories;
                        StudentLoginDataChart.refetch();
                    }
                });
            }
            function getTermData() {
                var start_date = $('#start_TermDataPicker').val();
                var end_date = $('#end_TermDataPicker').val();
                $.ajax({
                    url: "{{ route('school.statistics.assessments_data') }}",
                    type: "POST",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        TermData.data = data.data.data;
                        TermData.categories = data.data.categories;
                        TermDataChart.refetch();
                    }
                });
            }
        </script>
    @endsection
@endif

