@extends('general.new_reports.layout')
@push('style')

    <style>
        @if(app()->getLocale()=='ar')
    .report-date {
            top: 70% !important;
            left: 15% !important;
        }

        .report-title {
            top: 42% !important;
            left: 3% !important;
            text-align: center;
            color: #3F1023;
            width: 485px;
            font-size:2.2rem;
        }
        .report-ranges {
            color: #E37425;
            font-weight: 500;
        }


        @else
        .report-date {
            top: 70% !important;
            right: 15% !important;
        }

        .report-title {
            top: 42% !important;
            right: 3% !important;
            text-align: center;
            color: #3F1023;
            width: 485px;
            font-size:2.2rem;
        }
        .report-ranges {
            color: #E37425;
            font-weight: 500;
        }

        @endif
        .std_title {
            font-size: 12px;
        }
    </style>
@endpush
@php
    $lang = app()->getLocale();
@endphp

@section('content')
    <div class="page p-0">
        <div class="subpage-w">
            <div class="position-relative">
                <img src="{{ asset("reports/covers/progress_".$lang.".svg").'?v=2' }}"
                     class="w-100" alt="">


                <h2 class="position-absolute report-title m-3 text-black">
                    تقرير المقارنة <br />
                    Comparison Report
                    <br /><br>
                </h2>
                <h4 class="position-absolute report-date m-3 text-black">
                    {{ re('Report issue date') }} : {{ date('d/m/Y') }}
                </h4>
            </div>
        </div>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center mt-3 mb-5">
                <div class="col-6 text-center">
                    <div class="image-container">
                        <img src="{{asset($school->logo)}}" alt="">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center  mt-5">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('What We Compare !')}}</h4>
                </div>
            </div>
            <div class="row justify-content-center  mt-4">
                <div class="col-11 text-center">
                    <img src="{{asset("reports/analysis_points_$lang.svg")}}?v2" width="100%" alt="">
                </div>
            </div>


        </div>
        <span class="number-page">2</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center  mt-5">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('This Comparison Report for')}}</h4>
                </div>
            </div>
            <div class="row text-center justify-content-center">
                <div class="col-11 ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('The comparison')}}</th>
                                <th class="main-th"> {{re('With')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{--                        <tr class="text-center">--}}
                            {{--                            <td>Countries</td>--}}
                            {{--                            <td>@if(is_array($data['countries']))--}}
                            {{--                                    @foreach($data['countries'] as $country)--}}
                            {{--                                        @if($loop->last)--}}
                            {{--                                            {{$country}}--}}
                            {{--                                        @else--}}
                            {{--                                            {{$country}} ,--}}
                            {{--                                        @endif--}}
                            {{--                                    @endforeach--}}
                            {{--                                @else--}}
                            {{--                                    {{$data['countries']}}--}}
                            {{--                                @endif</td>--}}
                            {{--                        </tr>--}}
                            <tr class="text-center">
                                <td>Curriculum</td>
                                <td>@if(is_array($data['curriculums']))
                                        @foreach($data['curriculums'] as $curriculum)
                                            @if($loop->last)
                                                {{$curriculum}}
                                            @else
                                                {{$curriculum}} ,
                                            @endif
                                        @endforeach
                                    @else
                                        {{$data['curriculums']}}
                                    @endif</td>
                            </tr>
                            <tr class="text-center">
                                <td>Grades</td>
                                <td>@if($data['arab_levels'])
                                        {{$data['arab_levels']}}
                                    @endif
                                    @if($data['non_arab_levels'])
                                        <br/>
                                        {{$data['non_arab_levels']}}
                                    @endif</td>
                            </tr>
                            <tr class="text-center">
                                <td>{{re('Students Type')}}</td>
                                <td>{{$data['section']}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>{{re('Gender')}}</td>
                                <td>{{$data['gender']}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>{{re('Students of determination')}}</td>
                                <td>{{$data['sen_students']}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>{{re('Comparison year')}}</td>
                                <td>{{$data['years']}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>{{re('Comparison round')}}</td>
                                <td>{{$selected_round}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center  mt-5">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('This Comparison Report for')}}</h4>
                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-10 text-center">
                    <div class="table-container">
                        <table class="table m-0">
                            <tr>
                                <td class="main-td w-25 py-2">{{re('School Name')}}</td>
                                <td class="">{{$school->name}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2">{{re('Curriculum Type')}}</td>
                                <td class="">{{$school->curriculum_type}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2">{{re('Total students')}}</td>
                                <td class="">{{$total_school_students}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2">{{re('Total Boys')}}</td>
                                <td class="">{{$boys_school_students}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2">{{re('Total Girls')}}</td>
                                <td class="">{{$girls_school_students}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2">{{re('Local Ranking')}}</td>
                                <td class="">{{$local_rank}} / {{count($array_local_schools)}}</td>
                            </tr>
                            {{--                        <tr>--}}
                            {{--                            <td class="main-td py-2">{{re('Global Ranking')}}</td>--}}
                            {{--                            <td class="">{{$general_rank}} / {{count($array_general_schools)}}</td>--}}
                            {{--                        </tr>--}}
                            {{--                        <tr>--}}
                            {{--                            <td class="main-td py-2">{{re('Curriculum Ranking')}}</td>--}}
                            {{--                            <td class="">{{$type_rank}} / {{count($array_type_schools)}}</td>--}}
                            {{--                        </tr>--}}
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <span class="number-page">3</span>
    </div>
    @if(count($arabs_grade_ranking))
        <div class="page">
            <div class="row justify-content-center  mt-5">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('Arabic for Arabs Benchmarking')}}</h4>
                </div>
                <div class="col-11 text-center mt-5">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th">{{re('Grade')}}</th>
                                <th class="main-th">{{re('Average School Benchmark')}}</th>
                                <th class="main-th">{{re('Average Country Benchmark')}}</th>
                                {{--                            <th class="main-th">{{re('Average global Benchmark')}}</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(range(1,12) as $grade)
                                <tr>
                                    <td >{{re('Grade'.' '.$grade)}}</td>
                                    <td >
                                        @if(isset($arabs_grade_ranking['local'][$grade])){{$arabs_grade_ranking['local'][$grade]}}@endif
                                    </td>
                                    <td >
                                        @if(isset($arabs_grade_ranking['country'][$grade])){{$arabs_grade_ranking['country'][$grade]}}@endif
                                    </td>
                                    {{--                                <td >--}}
                                    {{--                                    @if(isset($arabs_grade_ranking['global'][$grade])){{$arabs_grade_ranking['global'][$grade]}}@endif--}}
                                    {{--                                </td>--}}
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(count($non_arabs_grade_ranking))
        <div class="page">
            <div class="row justify-content-center  mt-5">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('Arabic for Non-Arabs Benchmarking')}}</h4>
                </div>
                <div class="col-11 text-center mt-5">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th">{{re('Grade')}}</th>
                                <th class="main-th">{{re('Average School Benchmark')}}</th>
                                <th class="main-th">{{re('Average Country Benchmark')}}</th>
                                {{--                            <th class="main-th">{{re('Average global Benchmark')}}</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(range(1,12) as $grade)
                                <tr>
                                    <td >{{re('Grade'.' '.$grade)}}</td>
                                    <td >
                                        @if(isset($non_arabs_grade_ranking['local'][$grade])){{$non_arabs_grade_ranking['local'][$grade]}}@endif
                                    </td>
                                    <td >
                                        @if(isset($non_arabs_grade_ranking['country'][$grade])){{$non_arabs_grade_ranking['country'][$grade]}}@endif
                                    </td>
                                    {{--                                <td >--}}
                                    {{--                                    @if(isset($non_arabs_grade_ranking['global'][$grade])){{$non_arabs_grade_ranking['global'][$grade]}}@endif--}}
                                    {{--                                </td>--}}
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--<div class="page">--}}
    {{--    <div class="subpage-w">--}}
    {{--        <div class="row justify-content-center  my-5">--}}
    {{--            <div class="col-12 text-center">--}}
    {{--                <h4 class="main-color">{{re('The comparison with the selected')}} {{re('Country/Countries')}}</h4>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-md-12 text-center">--}}
    {{--                <div id="countries_chart"></div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-md-12 text-center">--}}
    {{--                <div id="school_container"></div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--</div>--}}
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center  my-5">
                <div class="col-12 text-center">
                    <h4 class="main-color">{{re('The comparison with the selected')}} {{re('Curriculum / Curriculums')}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="curriculums_chart"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="school_2_container"></div>
                    <h4 class="text-center main-color">{{$school->name}}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center  mt-5">
                <div class="col-12 text-center">
                    <h4 class="main-color">{{re('The comparison with the selected')}} {{re('Gender')}}</h4>
                    <h4 class="main-color">{{re('The selected')}} {{re('School/Schools')}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-6 text-center">
                    <div id="general_boys_container" style="height: 350px"></div>
                </div>
                <div class="col-6 text-center">
                    <div id="general_girls_container" style="height: 350px"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="genders_container"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center  mt-5">
                <div class="col-12 text-center">
                    <h4 class="main-color">{{re('The comparison with the selected')}} {{re('School/Schools')}} <br/>
                        {{re('for the students of determination')}}</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="general_sen_container" style="height: 350px"></div>
                </div>
            </div>
            <br/>
            <br/>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="school_sen_container" style="height: 350px"></div>
                </div>
            </div>
        </div>
    </div>
    @foreach($general_schools as $key => $general_school)
        @if(count($general_school) >= 15)
            @php
                $height = 800;
            @endphp
        @elseif(count($general_school) >= 10)
            @php
                $height = 600;
            @endphp
        @elseif(count($general_school) >= 5)
            @php
                $height = 400;
            @endphp
        @else
            @php
                $height = 400;
            @endphp
        @endif
        <div class="page">
            <div class="subpage-w">
                <div class="row justify-content-center  mt-5">
                    <div class="col-12 text-center">
                        <h4 class="main-color">{{re('The comparison with the selected')}} {{re('Grade/Grades')}}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="general_schools_chart_{{$key}}" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
@push('script')
    <!-- END THEME LAYOUT SCRIPTS -->
    <script type="text/javascript">
        $(document).ready(function () {
            //window.print();
            // Radialize the colors
            Highcharts.setOptions({
                colors: ["#EA5455", "#F0DE36", "#28C76F", '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
            });
            //---------countries chart----------//
            {{--Highcharts.chart('countries_chart', {--}}
            {{--    chart: {--}}
            {{--        type: 'column'--}}
            {{--    },--}}
            {{--    title: {--}}
            {{--        text: ''--}}
            {{--    },--}}
            {{--    xAxis: {--}}
            {{--        categories: [--}}
            {{--            @php--}}
            {{--                $key = 1;--}}
            {{--            @endphp--}}
            {{--                @foreach($countries_data as $country_data)--}}
            {{--                '{{$key .'-'. strtoupper($country_data->ID)}}',--}}
            {{--            @php--}}
            {{--                $key++;--}}
            {{--            @endphp--}}
            {{--            @endforeach--}}
            {{--        ]--}}
            {{--    },--}}
            {{--    yAxis: {--}}
            {{--        min: 0,--}}
            {{--        title: {--}}
            {{--            text: ''--}}
            {{--        }--}}
            {{--    },--}}
            {{--    tooltip: {--}}
            {{--        enabled: false,--}}
            {{--        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',--}}
            {{--        shared: true--}}
            {{--    },--}}
            {{--    plotOptions: {--}}
            {{--        column: {--}}
            {{--            stacking: 'percent',--}}
            {{--            events: {--}}
            {{--                legendItemClick: function () {--}}
            {{--                    return false;--}}
            {{--                }--}}
            {{--            },--}}
            {{--            dataLabels: {--}}
            {{--                enabled: true,--}}
            {{--                format: '<b>{point.name}</b><br>{point.percentage:.1f} %',--}}
            {{--                filter: {--}}
            {{--                    property: 'percentage',--}}
            {{--                    operator: '>',--}}
            {{--                    value: 4--}}
            {{--                },--}}
            {{--                style: {--}}
            {{--                    font: 'bold 8px "Trebuchet MS", Verdana, sans-serif',--}}
            {{--                    color: "#000",--}}
            {{--                    fontSize: '9px',--}}
            {{--                    textOutline: 0--}}
            {{--                }--}}
            {{--            }--}}

            {{--        },--}}
            {{--        allowPointSelect: false,--}}

            {{--    },--}}
            {{--    series: [{--}}
            {{--        name: '{{re('Below')}}',--}}
            {{--        data: [--}}
            {{--            @foreach($countries_data as $country_data)--}}
            {{--                {{$country_data->below}},--}}
            {{--            @endforeach--}}
            {{--        ],--}}
            {{--        color: "#EA5455"--}}
            {{--    }, {--}}
            {{--        name: '{{re('Inline')}}',--}}
            {{--        data: [--}}
            {{--            @foreach($countries_data as $country_data)--}}
            {{--                {{$country_data->inline}},--}}
            {{--            @endforeach--}}
            {{--        ],--}}
            {{--        color: "#F0DE36"--}}
            {{--    }, {--}}
            {{--        name: '{{re('Above')}}',--}}
            {{--        data: [--}}
            {{--            @foreach($countries_data as $country_data)--}}
            {{--                {{$country_data->above}},--}}
            {{--            @endforeach--}}
            {{--        ],--}}
            {{--        color: "#28C76F"--}}
            {{--    }]--}}
            {{--});--}}
            Highcharts.chart('curriculums_chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        @php
                            $key = 1;
                        @endphp
                            @foreach($curriculums_data as $curriculum_data)
                            @if($curriculum_data->ID === "International Baccalaureate")
                            '{{$key}}-IB Curriculum',
                        @else
                            '{{$key .'-'. strtoupper($curriculum_data->ID)}}',
                        @endif
                        @php
                            $key++;
                        @endphp
                        @endforeach
                    ]
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                    shared: true
                },
                plotOptions: {
                    column: {
                        stacking: 'percent',
                        events: {
                            legendItemClick: function () {
                                return false;
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 4
                            },
                            style: {
                                font: 'bold 8px "Trebuchet MS", Verdana, sans-serif',
                                color: "#000",
                                fontSize: '9px',
                                textOutline: 0
                            }
                        }

                    },
                    allowPointSelect: false,

                },
                series: [{
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($curriculums_data as $curriculum_data)
                            {{$curriculum_data->below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($curriculums_data as $curriculum_data)
                            {{$curriculum_data->inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($curriculums_data as $curriculum_data)
                            {{$curriculum_data->above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }]
            });
            {{--Highcharts.chart('school_container', {--}}
            {{--    chart: {--}}
            {{--        plotBackgroundColor: null,--}}
            {{--        plotBorderWidth: 0,--}}
            {{--        plotShadow: false--}}
            {{--    },--}}
            {{--    title: {--}}
            {{--        text: '{{$school->name}}',--}}
            {{--        align: 'center',--}}
            {{--        verticalAlign: 'middle',--}}
            {{--        y: 120--}}
            {{--    },--}}
            {{--    tooltip: {--}}
            {{--        enabled: false,--}}
            {{--        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'--}}
            {{--    },--}}

            {{--    plotOptions: {--}}
            {{--        pie: {--}}
            {{--            dataLabels: {--}}
            {{--                enabled: true,--}}
            {{--                distance: -25,--}}
            {{--                style: {--}}
            {{--                    fontWeight: 'bold',--}}
            {{--                    color: 'white'--}}
            {{--                }--}}
            {{--            },--}}
            {{--            startAngle: -90,--}}
            {{--            endAngle: 90,--}}
            {{--            center: ['50%', '75%'],--}}
            {{--            size: '100%',--}}
            {{--            showInLegend: true,--}}
            {{--        },--}}

            {{--        series: {--}}
            {{--            borderWidth: 0,--}}
            {{--            dataLabels: {--}}
            {{--                enabled: true,--}}
            {{--                format: '{point.y} %',--}}
            {{--                color: '#000',--}}
            {{--                style: {--}}
            {{--                    fontSize: '10px',--}}

            {{--                }--}}

            {{--            }--}}
            {{--        }--}}
            {{--    },--}}
            {{--    series: [{--}}
            {{--        type: 'pie',--}}
            {{--        name: '{{re('Rate')}}',--}}
            {{--        innerSize: '50%',--}}
            {{--        data: [--}}
            {{--            ['{{re('Below expecting')}}', {{ $school_data->percent_below }}],--}}
            {{--            ['{{re('In line with curriculum expectations')}}', {{ $school_data->percent_inline }}],--}}
            {{--            ['{{re('Above curriculum expectations')}}', {{ $school_data->percent_above }}],--}}
            {{--        ]--}}
            {{--    }]--}}
            {{--});--}}
            Highcharts.chart("school_2_container", {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    color: "#F00",
                    text: " ",
                    style: {
                        font: 'bold 18px "Trebuchet MS", Verdana, sans-serif',
                        color: '#000',
                    },
                    verticalAlign: 'bottom',
                    y: 20,
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f} %',
                            connectorColor: 'silver'
                        },
                        showInLegend: true
                    },

                },
                series: [{
                    name: '{{re('Share')}}',
                    innerSize: '50%',
                    data: [
                        {name: '{{re('Below expecting')}} ', y: {{ $school_data->percent_below }}, 'color': "#EA5455"},
                        {name: '{{re('In line with curriculum expectations')}} ', y: {{ $school_data->percent_inline }}, 'color': "#F0DE36"},
                        {name: '{{re('Above curriculum expectations')}}', y: {{ $school_data->percent_above }}, 'color': "#28C76F"},
                    ]
                }]
            });
            Highcharts.chart("general_boys_container", {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    color: "#F00",
                    text: "{{re('Boys')}}",
                    style: {
                        font: 'bold 15px "Trebuchet MS", Verdana, sans-serif',
                        color: '#000',
                    },
                    verticalAlign: 'bottom',
                    y: -20,
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y:.1f} %',
                            distance: -50,
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 4
                            },
                            style: {
                                font: 'bold 11px "Trebuchet MS", Verdana, sans-serif',
                                color: "#000",
                                textOutline: 0
                            }
                        }
                    },
                    showInLegend: true
                },
                series: [{
                    name: '{{re('Share')}}',
                    data: [
                        {name: '{{re('Below')}} ', y: {{ $general_boy->percent_below }}, 'color': "#EA5455"},
                        {name: '{{re('In line')}} ', y: {{ $general_boy->percent_inline }}, 'color': "#F0DE36"},
                        {name: '{{re('Above')}}', y: {{ $general_boy->percent_above }}, 'color': "#28C76F"},
                    ]
                }]
            });
            Highcharts.chart("general_girls_container", {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    color: "#F00",
                    text: "{{re('Girls')}}",
                    style: {
                        font: 'bold 15px "Trebuchet MS", Verdana, sans-serif',
                        color: '#000',
                    },
                    verticalAlign: 'bottom',
                    y: -20,
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y:.1f} %',
                            distance: -50,
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 4
                            },
                            style: {
                                font: 'bold 11px "Trebuchet MS", Verdana, sans-serif',
                                color: "#000",
                                textOutline: 0
                            }
                        }
                    },
                    showInLegend: true
                },
                series: [{
                    name: '{{re('Share')}}',
                    data: [
                        {name: '{{re('Below')}} ', y: {{ $general_girl->percent_below }}, 'color': "#EA5455"},
                        {name: '{{re('In line')}} ', y: {{ $general_girl->percent_inline }}, 'color': "#F0DE36"},
                        {name: '{{re('Above')}}', y: {{ $general_girl->percent_above }}, 'color': "#28C76F"},
                    ]
                }]
            });
            Highcharts.chart("general_sen_container", {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    color: "#F00",
                    text: "{{re('The school/schools selected')}}",
                    style: {
                        font: 'bold 18px "Trebuchet MS", Verdana, sans-serif',
                        color: '#000',
                    },
                    verticalAlign: 'bottom',
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f} %',
                            connectorColor: 'silver'
                        },
                        showInLegend: true
                    },

                },
                series: [{
                    innerSize: '40%',
                    name: '{{re('Share')}}',
                    data: [
                        {name: '{{re('Below expecting')}} ', y: {{ $general_sen->percent_below }}, 'color': "#EA5455"},
                        {name: '{{re('In line with curriculum expectations')}} ', y: {{ $general_sen->percent_inline }}, 'color': "#F0DE36"},
                        {name: '{{re('Above curriculum expectations')}}', y: {{ $general_sen->percent_above }}, 'color': "#28C76F"},
                    ]
                }]
            });
            Highcharts.chart("school_sen_container", {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    color: "#F00",
                    text: "{{$school->name}}",
                    style: {
                        font: 'bold 18px "Trebuchet MS", Verdana, sans-serif',
                        color: '#000',
                    },
                    verticalAlign: 'bottom',
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '{series.name}: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f} %',
                            connectorColor: 'silver'
                        },
                        showInLegend: true
                    },

                },
                series: [{
                    innerSize: '40%',
                    name: '{{re('Share')}}',
                    data: [
                        {name: '{{re('Below expecting')}} ', y: {{ $school_data->sen->percent_below }}, 'color': "#EA5455"},
                        {name: '{{re('In line with curriculum expectations')}} ', y: {{ $school_data->sen->percent_inline }}, 'color': "#F0DE36"},
                        {name: '{{re('Above curriculum expectations')}}', y: {{ $school_data->sen->percent_above }}, 'color': "#28C76F"},
                    ]
                }]
            });

            Highcharts.chart('genders_container', {
                chart: {
                    type: 'column',
                },
                title: {
                    text: '{{$school->name}}'
                },
                subtitle: {
                    text: ' '
                },
                xAxis: {
                    categories: [
                        '{{re('Boys')}}',
                        '{{re('Girls')}}',
                    ],
                    crosshair: true,
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    },
                    tickInterval: 20,
                    tickPositions: [0, 20, 40, 60, 80, 100, 120],
                },
                tooltip: {
                    enabled: false,
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}  %</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true,

                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        events: {
                            legendItemClick: function () {
                                return false;
                            }
                        }
                    },
                    allowPointSelect: false,
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y} %',
                            rotation: -90,
                            y: -20,
                            color: '#000',
                            style: {
                                fontSize: '10px',

                            }

                        }
                    }
                },
                series: [{
                    name: '{{re('Below expecting')}}',
                    data: [
                        {{ $school_data->boys->percent_below }},
                        {{ $school_data->girls->percent_below }},
                    ],
                    color: "#EA5455"

                }, {
                    name: '{{re('In line with curriculum expectations')}} ',
                    data: [
                        {{ $school_data->boys->percent_inline }},
                        {{ $school_data->girls->percent_inline }},
                    ],
                    color: "#F0DE36"

                }, {
                    name: '{{re('Above curriculum expectations')}}',
                    data: [
                        {{ $school_data->boys->percent_above }},
                        {{ $school_data->girls->percent_above }},
                    ],
                    color: "#28C76F"

                }]
            });


            @php
                $order_key = 1;
            @endphp
            @foreach($general_schools as $key => $general_school)
            Highcharts.chart('general_schools_chart_{{$key}}', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: ' '
                },
                xAxis: {
                    categories: [
                        @foreach($general_school as $general_school_data)
                            '{{$order_key .'-'. strtoupper($general_school_data->school_name)}}',
                        @php
                            $order_key++;
                        @endphp
                        @endforeach
                    ],
                    labels: {
                        formatter() {
                            let text = this.value;
                            let result = text.includes("{{strtoupper($school->name)}}");
                            if (result) {
                                return `<span style="color: red">${this.value}</span>`;
                            } else {

                                return `<span >${this.value}</span>`
                            }
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ' '
                    }
                },
                legend: {
                    reversed: true
                },
                tooltip: {
                    enabled: false,
                },
                plotOptions: {
                    series: {
                        maxPointWidth: 20,
                        stacking: 'percent',
                        events: {
                            legendItemClick: function () {
                                return false;
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 4
                            },
                            style: {
                                font: 'bold 8px "Trebuchet MS", Verdana, sans-serif',
                                color: "#000",
                                fontSize: '9px',
                                textOutline: 0
                            }
                        }
                    },
                    allowPointSelect: false,
                },
                series: [
                    {
                        name: '{{re('Below')}}',
                        data: [
                            @foreach($general_school as $general_school_data)
                                {{$general_school_data->below}},
                            @endforeach
                        ],
                        color: "#EA5455"
                    }, {
                        name: '{{re('Inline')}}',
                        data: [
                            @foreach($general_school as $general_school_data)
                                {{$general_school_data->inline}},
                            @endforeach
                        ],
                        color: "#F0DE36"
                    }, {
                        name: '{{re('Above')}}',
                        data: [
                            @foreach($general_school as $general_school_data)
                                {{$general_school_data->above}},
                            @endforeach
                        ],
                        color: "#28C76F"
                    }
                ]
            });
            @endforeach


        });
    </script>
@endpush
