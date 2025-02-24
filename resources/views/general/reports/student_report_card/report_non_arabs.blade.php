<!doctype html>
<html lang="{{app()->getlocale()}}" dir="{{app()->getLocale()=='ar'?'rtl':'ltr'}}">
@php
    $lang = app()->getLocale()=='ar'?'ar':'en';
@endphp
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Social Studies Benchmark Test"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="{{ asset('print/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('print/css/blue.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('print/css/custom-rtl.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('print/css/print.css') }}" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('social-small-logo.svg'):asset(settingCache('logo_min'))}}"/>

    <script src="{{ asset('print/js/highcharts.js') }}"></script>
    <style>
        .subpage-w {
            padding: 1cm;
            border: 4px #00cb3b solid;
            /* border-left: 5px #00B0F0 solid; */
            height: 265mm;
            outline: none;
            background: transparent;
        }
        .page {
            padding: 14mm;
        }
        tspan{
            font-weight: bold;
        }
        .highcharts-plot-line-label-s {
            font-weight: bold !important;
        }
        .grades th, .grades td{
            font-size: 14px !important;
        }
        @media print {
            .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
                float: left!important;
            }
            .col-sm-12 {
                width: 100%!important;
            }
            .col-sm-11 {
                width: 91.66666667%!important;
            }
            .col-sm-10 {
                width: 83.33333333%!important;
            }
            .col-sm-9 {
                width: 75%!important;
            }
            .col-sm-8 {
                width: 66.66666667%!important;
            }
            .col-sm-7 {
                width: 58.33333333%!important;
            }
            .col-sm-6 {
                width: 50% !important;
            }
            .col-sm-5 {
                width: 41.66666667%!important;
            }
            .col-sm-4 {
                width: 33.33333333%!important;
            }
            .col-sm-3 {
                width: 25%!important;
            }
            .col-sm-2 {
                width: 16.66666667%!important;
            }
            .col-sm-1 {
                width: 8.33333333%!important;
            }
        }
    </style>
    <title>{{re('The Student Report')}} - {{ $student->name }}</title>

</head>
<body>
<div class="page">
    <div class="subpage-w">
        <div class="row" style="">
            <div class="col-xs-5" style="margin-top: 30px">
                <img width="100%" src="{{!settingCache('logo')? asset('social-logo.svg'):asset(settingCache('logo'))}}"/>
            </div>
            <div class="col-xs-3">
            </div>
            <div class="col-xs-4 text-right">
                <img width="65%" src="{{ asset('report/student_pattern.png') }}"/>
            </div>

        </div>
        <br/>
        <div class="row">
            <div class="col-xs-12">
                <h3 class="text-center text-green" style="font-weight: bold; font-family:system-ui !important">Social Studies Benchmark Test  – اختبار الدراسات الاجتماعية</h3>
                <h1 class="text-center text-red" style="font-weight: bold; font-family:system-ui !important">تقرير الطالب</h1>
                <h1 class="text-center text-green" style="font-weight: bold;">The Student Report</h1>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-xs-12 text-center">
                <img src="{{ asset('report/uae_flag.png') }}" width="65%"/>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h4 class="text-center" style="font-weight: bold;">{{re('Report issue date')}} : {{ date("d/m/Y") }}</h4>
            </div>
            <div class="col-xs-12">
                <h4 class="text-center" style="font-weight: bold;">ABT assessments</h4>
            </div>
            <div class="col-xs-12">
                <h4 class="text-center">www.ABT-assessments.com</h4>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="col-xs-12">
                <img src="{{asset('assets_v1/media/report/footer-logos.svg')}}?v=1" width="100%"/>
            </div>
        </div>
    </div>
    <span class="numder-page"></span>
</div>
<div class="page">
    <div class="subpage-w">
        <h2 class="text-center text-green">{{re('What is A.B.T assessments!')}}</h2>
        <br/>
        <div class="row">
            <div class="col-md-12">
                <ul style="line-height: 2; font-size:16px;">
                    <li><span class="">{{re('p report 1 1')}}</span> {{re('p report 1 2')}}
                    </li>
                    <li><span class="">{{re('p report 2 1')}}</span> {{re('p report 2 2')}}</li>
                    <li><span class="">{{re('p report 3 1')}}</span> {{re('p report 3 2')}}
                    </li>
                    <li><span class="">{{re('p report 4 1')}}</span> {{re('p report 4 2')}}
                    </li>
                    <li><span class="">{{re('p report 5 1')}}</span> {{re('p report 5 2')}}
                    </li>
                    <li><span class="">{{re('p report 6 1')}}</span> {{re('p report 6 2')}}
                    </li>
                    <li><span class="">{{re('p report 7 1')}}</span> {{re('p report 7 2')}}
                    </li>
                    <li><span class="">{{re('p report 8 1')}}</span> {{re('p report 8 2')}}
                    </li>
                    <li><span class="">{{re('p report 9 1')}}</span> {{re('p report 9 2')}}
                    </li>
                    <li><span class="">{{re('p report 10 1')}}</span> {{re('p report 10 2')}}
                    </li>
                    <li><span class="">{{re('p report 11 1')}}</span> {{re('p report 11 2')}}
                    </li>

                </ul>
            </div>
        </div>

    </div>
    <span class="numder-page">2</span>
</div>
<div class="page">
    <div class="subpage-w">
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3 text-center">
                <img style="max-height:200px" src="{{ $student->school->logo }}" />
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="cell-orange text-center"> Student Name  </th>
                        <th class="text-center"> {{ $student->name }} </th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center"> Username  </th>
                        <th class="text-center"> {{ $student->email }} </th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center">Nationality</th>
                        <th class="text-center">{{ $student->nationality_name }}</th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center">Date of Birth</th>
                        <th class="text-center">{{ $student->dob }}</th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center">School</th>
                        <th class="text-center">{{ $student->school->name }}</th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center">Grade</th>
                        <th class="text-center">{{ $student->level->name }}</th>
                    </tr>
                    <tr>
                        <th class="cell-orange text-center">Gender</th>
                        <th class="text-center text-capitalize">{{ $student->gender }}</th>
                    </tr>
                    {{--                    <tr>--}}
                    {{--                        <th class="cell-orange text-center">  Internal Ranking  </th>--}}
                    {{--                        <th class="text-center"> {{ $student_internal }} </th>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <th class="cell-orange text-center"> External Ranking</th>--}}
                    {{--                        <th class="text-center">{{ $student_external }}</th>--}}
                    {{--                    </tr>--}}
                    </thead>
                    <tbody>


                    </tbody>
                </table>
                {{--                <p class="text-red">**Internal Ranking = in the same Grade, in the same school of student</p>--}}
                {{--                <p class="text-red">**External Ranking = in the same Grade, in all ABT schools</p>--}}
            </div>

        </div>
    </div>
    <span class="numder-page">3</span>
</div>
<div class="page">
    <div class="subpage-w">
        <br/>
        <br/>
        <br/>
        <div class="row">
            <div class="col-xs-12 text-center">
                <img src="{{ asset("report/student_report_ranges_$lang.png") }}?v=2" width="100%"/>
            </div>
        </div>
    </div>
    <span class="numder-page">4</span>
</div>
@php
    $counter = 5;
@endphp
@foreach($levels_array as $level)
    @if(count($level['terms']) > 0)
        <div class="page">
            <div class="subpage-w">
                <h3 class="text-center h3">
                    {{ $level['name'] }}
                </h3>
                <div class="row">
                    <div id="container_level_{{ $level['id'] }}" style="height: 400px"></div>
                    {{--                <table class="table table-bordered table-hover grades">--}}
                    {{--                    <thead>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">The Holy Qur’an and Hadeeth  القرآن الكريم والحديث الشريف</th>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">Islamic values and Principles  قيم الإسلام وآدابه </th>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">Islamic law and Etiquettes  أحكام الإسلام ومقاصدها</th>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">Seerah and Islamic figures   السيرة والشخصيات الإسلامية</th>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">Faith - العقيدة</th>--}}
                    {{--                    <th class="cell-orange text-center" style="font-size: 12px !important; width: 16.6% !important; vertical-align: middle">Identity and Belonging  الهوية والانتماء</th>--}}
                    {{--                    </thead>--}}
                    {{--                </table>--}}
                </div>
                <br/>
                <div class="row">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover grades">
                            <thead>
                            <tr>
                                <th class="cell-orange text-center"> {{re('The Assessment')}}</th>
                                @foreach($level['terms'] as $term)
                                    <th class="cell-orange text-center">  {{ $term->term->name }}</th>
                            @endforeach
                            </thead>
                            <tbody>

                            <tr>
                                <td class="text-center" style="font-weight: bold;">{{re('Moral Education')}}</td>
                                @foreach($level['terms'] as $term)
                                    <td class="text-center" style="font-weight: bold;"> {{ $term->mark_step1 }} / 33 </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-center" style="font-weight: bold;">{{re('Social Studies')}}</td>
                                @foreach($level['terms'] as $term)
                                    <td class="text-center" style="font-weight: bold;"> {{ $term->mark_step2 }} / 33 </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-center" style="font-weight: bold;">{{re('Cultural Education')}}</td>
                                @foreach($level['terms'] as $term)
                                    <td class="text-center" style="font-weight: bold;"> {{ $term->mark_step3 }} / 34 </td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <span class="numder-page">{{ $counter }}</span>
            @php
                $counter ++;
            @endphp
        </div>
        <div class="page">
            <div class="subpage-w">
                <h3 class="text-center h3">
                    {{ $level['name'] }}
                </h3>
                <div class="row">
                    <div id="total_container_level_{{ $level['id'] }}" style="height: 400px"></div>
                </div>
                <br/>
                <br/>
                <div class="row">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="cell-orange text-center">{{re('The Assessment')}}</th>
                                <th class="cell-orange text-center text-center" > {{re('Total')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($level['terms'] as $term)
                                <tr>
                                    <td class="text-center" style="font-weight: bold;"> {{ $term->term->name }} </td>
                                    <td class="text-center" style="font-weight: bold;"> {{ $term->total }} / 100</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <span class="numder-page">{{ $counter }}</span>
            @php
                $counter ++;
            @endphp
        </div>
        @foreach($level['terms'] as $term)
            <div class="page">
                <div class="subpage-w" style="padding-top: 0;">

                    <div class="row">
                        <h3 class="text-center h3" > <small> {{re('Objectives')}} {{re('of')}} {{ $term->term->name }}<br />
                                <label style="padding-top:8px">{{re('Moral Education')}}</label>  </small></h3>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question Number')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question score')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Student score')}} </th>
                            <th class="cell-orange text-center" style="font-size:10px"> {{re('The Objectives')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Internal')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('External')}}  </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $qn = 1;
                        @endphp
                        @foreach($standards->where('question.subject', 1)->where('question.term_id', $term->term->id) as $standard)
                            <tr>
                                <td class="text-center" style="font-size:10px">{{ $qn }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->mark }}</td>
                                <td class="text-center" style="font-size:10px">{{ optional(optional($standard->student_standards)->first())->mark ?? 0 }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->standard }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_level($student,$standard) }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_system($standard) }}</td>
                            </tr>
                            @php
                                $qn ++;
                            @endphp
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <span class="numder-page">{{ $counter }}</span>
                @php
                    $counter ++;
                @endphp
            </div>
            <div class="page">
                <div class="subpage-w" style="padding-top: 0;">

                    <div class="row">
                        <h3 class="text-center h3" > <small> {{re('Objectives')}} {{re('of')}} {{ $term->term->name }}<br />
                                <label style="padding-top:8px">{{re('Social Studies')}}</label>  </small></h3>
                        </small></h3>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question Number')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question score')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Student score')}} </th>
                            <th class="cell-orange text-center" style="font-size:10px"> {{re('The Objectives')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Internal')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('External')}}  </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $qn = 1;
                        @endphp
                        @foreach($standards->where('question.subject', 2)->where('question.term_id', $term->term->id) as $standard)
                            <tr>
                                <td class="text-center" style="font-size:10px">{{ $qn }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->mark }}</td>
                                <td class="text-center" style="font-size:10px">{{ optional(optional($standard->student_standards)->first())->mark ?? 0 }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->standard }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_level($student,$standard) }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_system($standard) }}</td>
                            </tr>
                            @php
                                $qn ++;
                            @endphp
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <span class="numder-page">{{ $counter }}</span>
                @php
                    $counter ++;
                @endphp
            </div>
            <div class="page">
                <div class="subpage-w" style="padding-top: 0;">

                    <div class="row">
                        <h3 class="text-center h3" > <small> {{re('Objectives')}} {{re('of')}} {{ $term->term->name }}<br />
                                <label style="padding-top:8px">{{re('Cultural Education')}} </label></small></h3>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question Number')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Question score')}} </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Student score')}} </th>
                            <th class="cell-orange text-center" style="font-size:10px"> {{re('The Objectives')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('Internal')}}  </th>
                            <th class="cell-orange text-center" style="width:40px;font-size:10px"> {{re('External')}}  </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $qn = 1;
                        @endphp
                        @foreach($standards->where('question.subject', 3)->where('question.term_id', $term->term->id) as $standard)
                            <tr>
                                <td class="text-center" style="font-size:10px">{{ $qn }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->mark }}</td>
                                <td class="text-center" style="font-size:10px">{{ optional(optional($standard->student_standards)->first())->mark ?? 0 }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->standard }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_level($student,$standard) }}</td>
                                <td class="text-center" style="font-size:10px">{{ $standard->student_in_system($standard) }}</td>
                            </tr>
                            @php
                                $qn ++;
                            @endphp
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <span class="numder-page">{{ $counter }}</span>
                @php
                    $counter ++;
                @endphp
            </div>
        @endforeach
    @endif
@endforeach

<script src="{{ asset('assets/vendors/general/jquery/dist/jquery.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/general/popper.js/dist/umd/popper.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/general/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/general/js-cookie/src/js.cookie.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/general/moment/min/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset("assets/vendors/general/sticky-js/dist/sticky.min.js") }}" type="text/javascript"></script>

<script src="{{ asset('assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js') }}" type="text/javascript"></script>


<!--begin:: Global Optional Vendors -->
<script src="{{ asset('assets/vendors/general/toastr/build/toastr.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('assets/vendors/general/sweetalert2/dist/sweetalert2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/custom/js/vendors/sweetalert2.init.js') }}" type="text/javascript"></script>


<!--begin::Global Theme Bundle(used by all pages) -->
<script src="{{ asset('assets/js/demo2/scripts.bundle.js') }}" type="text/javascript"></script>
<!-- begin::Global Config(global config for global JS sciprts) -->


<script type="text/javascript">
    $(document).ready (function(){
        //window.print();
    });
</script>
<!-- End -->
<script type='text/javascript'>//<![CDATA[
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#374afb",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
    var ticks = [];
    for(var i=0;i<25;i++){
        ticks.push(i+1);
    }
    @foreach($levels_array as $level)
    @if(count($level['terms']) > 0)

    var colors = [ '#2525ef', '#0fee00', '#f16e31','#c8018b',];
    var data_array = new Array();
    var data_array_all = new Array();

    @foreach($level['terms'] as $term)
    data_array.push({{ $term->mark_step1 }});
    data_array.push({{ $term->mark_step2 }});
    data_array.push({{ $term->mark_step3 }});
    $collection = {data:data_array, name:"{{ $term->term->name }}", color:colors[0]};
    data_array_all.push($collection);
    data_array = [];
    colors.splice(0, 1)
    @endforeach
    console.log(data_array_all);
    new Highcharts.Chart({

        chart: {
            renderTo: 'container_level_{{ $level['id'] }}',
            type: 'column'
        },

        xAxis: {
            categories:  [
                '{{re('Moral Education')}}',
                '{{re('Social Studies')}}',
                '{{re('Cultural Education')}}',
            ],
            labels:{
                style:{
                    color: "#F00",
                    fontSize: "12px",
                }
            },

        },

        yAxis: {
            title: {
                text: ' ',
                useHTML: Highcharts.hasBidiBug
            },
            tickInterval: 2,
            min: 0,
            max: 35,


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
                animation: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y}',
                    color: '#000'
                }
            }
        },

        title: {
            text: ' ',
            useHTML: Highcharts.hasBidiBug
        },

        legend: {
            useHTML: Highcharts.hasBidiBug
        },

        tooltip: {
            useHTML: true
        },

        series:  data_array_all


    });


    var colors = [ '#2525ef', '#0fee00', '#f16e31','#c8018b',];
    var total_data_array_all = new Array();
    @foreach($level['terms'] as $term)
        $collection = {name:"{{ $term->term->name }}", y:{{ $term->total }}, color:colors[0]};
    total_data_array_all.push($collection);
    colors.splice(0, 1)
    @endforeach
    console.log(total_data_array_all);
    Highcharts.chart('total_container_level_{{ $level['id'] }}', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        accessibility: {
            announceNewData: {
                enabled: true
            }
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: ''
            },
            tickInterval: 5,
            min: 0,
            max: 100,

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                animation: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.1f}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
        },

        series: [
            {
                name: "{{re('Assessments')}}",
                colorByPoint: true,
                data: total_data_array_all
            }
        ],

    });
    @endif
    @endforeach
</script>
</body>
</html>
