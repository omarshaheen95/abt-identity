@extends('general.new_reports.layout')
@push('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        @if(app()->getLocale()=='ar')
    .report-date {
            top: 73% !important;
            left: 11% !important;
        }

        @else
    .report-date {
            top: 77% !important;
            right: 26% !important;
        }
    .report-qr {
            top: 38% !important;
            right: 80% !important;
        }

        .student-name {
            top: 50% !important;
            right: 18% !important;
            text-align: center;
            color: #1E4397;
            width: 485px;
            font-weight: bolder;
        }

        .report-title {
            top: 58% !important;
            right: 18% !important;
            text-align: center;
            color: #1E4397;
            width: 485px;
        }

        .report-ranges {
            color: #E37425;
            font-weight: 500;
        }
        @endif
    </style>
@endpush

@section('content')
    <div class="page p-0">
        <div class="subpage-w">
            <div class="position-relative">
                <img src="{{ asset("reports/covers/student_".app()->getLocale().".svg") }}"
                     class="w-100" alt="">


                <h2 class="position-absolute student-name m-3 text-black">
                    {{$student->name}}
                </h2>

                <div class="position-absolute report-qr m-3">
                    @if($student->gender == 1)
                        {!! QrCode::color(0, 166, 255)->size(120)->generate(sysDomain()."/student-report?token=".encryptStudentId($student->id)); !!}
                    @else
                        {!! QrCode::color(0,0,0)->size(120)->generate(sysDomain()."/student-report?token=".encryptStudentId($student->id)); !!}
                    @endif
                </div>
                <h4 class="position-absolute report-date m-3 text-black">
                    {{ re('Report issue date') }} : {{ date('d/m/Y') }}
                </h4>
            </div>
        </div>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row text-center">
                <h2 class="sub-color">
                    {{re('What is A B T assessments')}}!
                </h2>
            </div>
            <div class="row mt-5 justify-content-center">
                <div class="col-10">
                    <ul class="list-unstyled">
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 1 1')}}</span> {{re('p report 1 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 2 1')}}</span> {{re('p report 2 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 3 1')}}</span> {{re('p report 3 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 4 1')}}</span> {{re('p report 4 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 5 1')}}</span> {{re('p report 5 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 6 1')}}</span> {{re('p report 6 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 7 1')}}</span> {{re('p report 7 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 8 1')}}</span> {{re('p report 8 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 9 1')}}</span> {{re('p report 9 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 10 1')}}</span> {{re('p report 10 2')}}</span>
                        </li>
                        <li class="mt-3 fw-500 d-flex align-items-start">
                            <i class="fas fa-check-circle main-color me-3 mt-1"></i>
                            <span><span class="main-color fw-bold">{{re('p report 11 1')}}</span> {{re('p report 11 2')}}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h5 class="fw-bold sub-color">{{re('Understand - Evaluate - Apply your knowledge')}}</h5>
                </div>
            </div>
        </div>
        <span class="number-page">2</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center">
                <div class="col-6 text-center">
                    <div class="image-container">
                        <img src="{{asset($student->school->logo)}}" alt="">
                    </div>
                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-10 text-center">
                    <div class="table-container">
                        <table class="table m-0">
                            <tr>
                                <td class="main-td w- py-2"><i class="fas fa-user-graduate me-2"></i>{{re('Name')}}</td>
                                <td class="">{{$student->name}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-flag me-2"></i>{{re('Nationality')}}</td>
                                <td class="">{{$student->nationality_name}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-school me-2"></i>{{re('School')}}</td>
                                <td class="">{{$student->school->name}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-layer-group me-2"></i>{{re('Level')}}</td>
                                <td class="">{{$student->level->short_name}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-users me-2"></i>{{re('Section')}}</td>
                                <td class="">{{$student->grade_name}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-10 text-center">
                    <div class="table-container">
                        <table class="table m-0">
                            <tr class="text-center">
                                <td class="" colspan="2"><i class="fas fa-trophy me-2"></i>{{re('Ranking & Benchmarking')}}</td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-home me-2"></i>{{re('Internal Ranking')}}</td>
                                <td class="">{{ $student_internal }} / {{$student_internal_terms_count}}</td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-chart-bar me-2"></i>{{re('Average School Benchmark')}}</td>
                                <td class="">{{ $sum_student_internal_terms > 0 && $student_internal_terms_count > 0 ? round($sum_student_internal_terms / $student_internal_terms_count, 1) .' / 100':'-' }}

                                </td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-flag me-2"></i>{{re('Country Ranking')}}</td>
                                <td class="">{{ $student_country }} / {{$student_country_terms_count}}</td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-chart-line me-2"></i>{{re('Average Country Benchmark')}}</td>
                                <td class="">{{ $sum_student_country_terms > 0 && $student_country_terms_count > 0 ? round($sum_student_country_terms / $student_country_terms_count, 1) .' / 100':'-' }}

                                </td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-globe me-2"></i>{{re('Global Ranking')}}</td>
                                <td class="">{{ $student_external }} / {{$student_external_terms_count}}</td>
                            </tr>
                            <tr>
                                <td class="sub-td py-2"><i class="fas fa-chart-area me-2"></i>{{re('Average global Benchmark')}}</td>
                                <td class="">{{ $sum_student_external_terms > 0 && $student_external_terms_count > 0 ? round($sum_student_external_terms / $student_external_terms_count, 1) .' / 100':'-' }}

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12 fw-bold">
                    <ul class="text-danger">
                        <li>{{re('Internal Ranking = in the same Grade, in the same school of student')}}</li>
                        <li>{{re('External Ranking = in the same Grade, in all ABT schools')}}</li>
                    </ul>
                </div>
            </div>

        </div>
        <span class="number-page">3</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row text-center justify-content-center">
                <div class="col-11">
                    <h5 class="section-title"><i class="fas fa-chart-bar me-2"></i>{{re('The Skills Results')}}</h5>
                </div>
            </div>
            <div class="row mb-5 mt-3">
                <div id="container" style="height: 380px"></div>
            </div>
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> #</th>
                                <th class="main-th"><i class="fas fa-layer-group me-2"></i> {{re('Skills')}}</th>
                                @foreach($student_terms as $d_term)
                                    <th class="main-th text-center"><i
                                            class="fas fa-calendar-alt me-1"></i> {{ $d_term->month }}</th>
                                @endforeach

                            </tr>
                            </thead>
                            <tbody>

                            @foreach($subjects as $subject)
                                <tr>
                                    <td class="text-danger">{{$subject->id}}</td>
                                    <td><i class="fas fa-book-open text-success me-2"></i>{{$subject->name}}</td>


                                    @foreach($student_terms as $d_term)
                                        <td class="text-center">
                                            @if($d_term->{"mark_step$subject->id"})
                                                {{ $d_term->{"mark_step$subject->id"} }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    @endforeach

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <span class="number-page">4</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row text-center justify-content-center">
                <div class="col-11">
                    <h5 class="section-title"><i class="fas fa-chart-pie me-2"></i>{{re('Overall Results')}}</h5>
                </div>
            </div>
            <div class="row mb-5 mt-3">
                <div id="container2" style="height: 380px"></div>
            </div>
            <div class="row mt-5 text-center justify-content-center">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th class="main-th text-center"><i class="fas fa-clipboard-check me-2"></i>{{re('The assessment')}}</th>
                                <th class="main-th text-center"><i class="fas fa-percentage me-2"></i> {{re('Mark')}}</th>
                                <th class="main-th text-center"><i class="fas fa-gavel me-2"></i> {{re('Attainment')}}</th>
                                <th class="main-th text-center"><i class="fas fa-chart-line me-2"></i> {{re('Progress')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_terms as $d_term)
                                <tr>
                                    <td class="text-center"> {{ $d_term->month }} {{re('Round')}} </td>
                                    <td class="text-center"> {{ $d_term->total}} / 100</td>
                                    <td class="text-center"><span class="{{$d_term->css_class}}-badge">{{ $d_term->expectation }}</span>
                                    </td>
                                    <td class="text-center"><span
                                            class="{{ strtolower($d_term->progress_class) }}-badge">{{ $d_term->progress }}</span>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <span class="number-page">5</span>
    </div>
    @php
    $pageCounter = 6;
    @endphp
    @foreach($terms as $term)
        @foreach($subjects as $subject)
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-12">
                            <h5 class="section-title"><i class="fas fa-clipboard-list me-2"></i>{{re('The assessment Outcomes & Moderation')}}
                                <br /> {{$subject->name}} - {{$term->term->round}}
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-container">
                                <table class="table table-standards">
                                    <thead>
                                    <tr>

                                        <th class="main-th standard" style="width:40px;"><i class="fas fa-question-circle me-1"></i> {{re('Question Number')}} </th>
                                        <th class="main-th standard" style="width:40px;"><i class="fas fa-calculator me-1"></i> {{re('Question score')}} </th>
                                        <th class="main-th standard" style="width:40px;"><i class="fas fa-user-check me-1"></i> {{re('Student score')}} </th>
                                        <th class="main-th standard" ><i class="fas fa-bullseye me-1"></i> {{re('The assessment Outcomes')}}  </th>
                                        <th class="main-th standard" style="width:40px;"><i class="fas fa-home me-1"></i> {{re('Internal')}}  </th>
                                        <th class="main-th standard" style="width:40px;"><i class="fas fa-globe me-1"></i> {{re('External')}}  </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $qn = 1;
                                    @endphp
                                    @foreach($standards as $standard)
                                        @if($standard->question->term_id == $term->term_id && $standard->question->subject_id == $subject->id)
                                            <tr>
                                                <td class="standard" >{{ $qn++ }}</td>
                                                <td class="standard" >{{ $standard->mark }}</td>
                                                <td class="standard" >{{ optional($standard->studentTermStandards->first())->mark ?? 0 }}</td>
                                                <td class="standard" >{{ $standard->standard }}</td>
                                                <td class="standard" >{{ $standard->student_in_level }}</td>
                                                <td class="standard" >{{$standard->student_in_system}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="number-page">{{$pageCounter++}}</span>
            </div>
        @endforeach
    @endforeach

    @push('script')
        <script>
            $(function () {
                var colors = ['#3A98B9', '#BFBFBF', '#FF953A', '#FFC107'];

                Highcharts.setOptions({
                    colors: ['#3A98B9', '#BFBFBF', '#FF953A', '#FFC107', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
                });
                var chart = new Highcharts.Chart({

                    chart: {
                        renderTo: 'container',
                        type: 'column'
                    },

                    xAxis: {
                        categories: [
                            @foreach($subjects as $subject)
                            '{{$subject->id}}',
                            @endforeach
                        ]@if(app()->getLocale()=='ar').reverse()@endif,
                    },

                    yAxis: {
                        gridLineDashStyle: 'LongDash',
                        title: {
                            text: ' ',
                            useHTML: Highcharts.hasBidiBug
                        },
                        tickInterval: 5,
                        min: 0,
                        max: 30,

                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            events: {
                                legendItemClick: function () {
                                    return false;
                                }
                            },
                        },
                        allowPointSelect: false,
                        series: {
                            animation: false,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}',
                                color: '#000'
                            },
                            borderRadiusTopLeft: '15%',
                            borderRadiusTopRight: '15%'
                        }
                    },

                    title: {
                        text: ' ',
                        useHTML: Highcharts.hasBidiBug
                    },

                    legend: {
                        useHTML: Highcharts.hasBidiBug,
                        itemMarginTop: 8,
                    },

                    tooltip: {
                        useHTML: true
                    },

                    series: [
                            @foreach($student_terms as $term_data)
                        {
                            name: '{{ $term_data->month }}',
                            data: [
                                @foreach($subjects as $subject)
                                        {{ $term_data->{"mark_step$subject->id"} }},
                                @endforeach
                            ]@if(app()->getLocale()=='ar').reverse()@endif,
                        },
                        @endforeach
                    ]@if(app()->getLocale()=='ar').reverse()@endif

                });
                var chart2 = new Highcharts.Chart({

                    chart: {
                        renderTo: 'container2',
                        type: 'column'
                    },

                    xAxis: {
                        categories: ['{{re('Total Exams Results')}}']

                    },

                    yAxis: {
                        title: {
                            text: ' ',
                            useHTML: Highcharts.hasBidiBug
                        },
                        tickInterval: 20,
                        gridLineDashStyle: 'LongDash',
                        min: 0,
                        max: 100,

                    },

                    plotOptions: {
                        column: {
                            pointWidth: 60,
                            borderWidth: 0,
                            events: {
                                legendItemClick: function () {
                                    return false;
                                }
                            }
                        },
                        allowPointSelect: false,
                        series: {
                            animation: false,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}',
                                color: '#000'
                            },
                            borderRadiusTopLeft: '10%',
                            borderRadiusTopRight: '10%'
                        }
                    },
                    title: {
                        text: ' ',
                        useHTML: Highcharts.hasBidiBug
                    },

                    legend: {
                        useHTML: Highcharts.hasBidiBug,
                        itemMarginTop: 8,
                    },

                    tooltip: {
                        useHTML: true
                    },

                    series: [
                            @foreach($student_terms as $term_data)
                        {
                            name: '{{ $term_data->month }}',
                            data: [
                                {{ $term_data->total }},
                            ]
                        },
                        @endforeach
                    ]@if(app()->getLocale()=='ar').reverse()@endif


                });

            });
        </script>
    @endpush
