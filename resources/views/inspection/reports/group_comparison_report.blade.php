<!DOCTYPE html>
<html lang="{{app()->getlocale()}}" dir="{{app()->getLocale()=='ar'?'rtl':'ltr'}}">
@php
    $lang = app()->getLocale()=='ar'?'ar':'en';
@endphp
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="نظام اختبارات وتقييم الطلاب للهوية Identity Benchmark Test" name="description"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets_v1/plugins/bootstrap-5.0.2/css/bootstrap.rtl.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/print.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.rtl.css') }}" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{ asset('assets_v1/plugins/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/print.css') }}?v=1" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.css') }}" rel="stylesheet" type="text/css"/>
    @endif
    <link rel="shortcut icon"
          href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}"/>
    @hasSection('title')
        <title>@yield('title')</title>
    @else
        <title>{{$title??'A.B.T Identity Report'}}</title>
    @endif
    <script src="{{ asset('assets_v1/plugins/print/js/new_highcharts.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/rounded-corners.js') }}"></script>

    @stack('style')
</head>
<body>
<div class="page">
    <div class="subpage-w">
        <div class="row align-items-center">
            <div class="col-6 justify-content-center text-center">
                <img src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" width="75%" style="max-height: 100px"
                     alt="">

            </div>
            <div class="col-6  justify-content-center text-center">
{{--                @if($report_type == 2)--}}
{{--                    <img src="{{asset('assets_v1/media/AdvancedRangeLogo.svg')}}" width="75%"--}}
{{--                         style="max-height: 80px" alt="">--}}
{{--                @else--}}
{{--                    <img src="{{asset('assets_v1/media/ExpectedRangesLogo.svg')}}" width="75%"--}}
{{--                         style="max-height: 80px" alt="">--}}
{{--                @endif--}}

            </div>
        </div>
        <div class="row text-center justify-content-center mt-3">
            <div class="col-6">
                <h3 class="main-color">Identity Benchmark Test</h3>
            </div>
            <div class="col-6">
                <h3 class="main-color">اختبار الهوية المعياري</h3>
            </div>
            <div class="col-11 mt-3">
                <h3 class="sub-color">The Combined Report Based
                    on {{$report_type == 1 ? 'Expected Benchmark':'Advanced Benchmark'}} Ranges</h3>
            </div>
            <div class="col-12">
                <h3 class="sub-color">التقرير المجمع بناء على
                    {{$report_type == 1 ? 'نطاقات قياس الأداء المتوقع':'نطاقات قياس الأداء المتقدم'}}</h3>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <img src="{{asset('assets_v1/media/reports/attainment_report_1_page.svg')}}"
                     style="max-height: 300px; width: 50%" alt="">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                @if(isset($sub_title) && !is_null($sub_title))
                    <h4 class="sub-color my-1" style="font-weight: bold;">
                        {{$sub_title}}
                    </h4>
                @endif

                <h5 class="mt-3">{{t('Release Date')}} : {{now()->format('Y-m-d')}}</h5>
                <h5>www.abt-assessments.com</h5>
                <h5>support@abt-assessments.com</h5>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12 text-center">
                <img src="{{asset('assets_v1/media/reports/footer-logos.svg')}}?v=1" width="100%" alt="">
            </div>
        </div>
    </div>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center mt-3 mb-5">
            <div class="col-6 text-center">
                <div class="image-container">
                    <img src="{{$inspection->image}}" alt="">
                </div>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-10 text-center">
               <h5 class="section-title">{{re('Overall Information')}}</h5>
            </div>
            <div class="col-10 text-center">

                <div class="table-container mt-5">
                    <table class="table m-0">
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Group Name')}}</td>
                            <td>
                                {{$inspection->name}}
                            </td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Country')}}</td>
                            <td>
                                {{ucfirst($schools->first()->country)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{re('Schools')}}</td>
                            <td>
                                {{$data['total_schools']}}
                            </td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{re('students')}}</td>
                            <td>
                                {{$data['total_students']}}
                            </td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{re('Boys')}}</td>
                            <td> {{$data['total_boys']}}</td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{re('Girls')}}</td>
                            <td> {{$data['total_girls']}}</td>
                        </tr>
                         @if($arab)
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{sysNationality()}} {{re('students')}}</td>
                            <td> {{$data['total_'.sysNationality()]}}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Total')}} {{re('SEN')}}</td>
                            <td> {{$data['total_sen']}}</td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Round')}}</td>
                            <td> {{$data['round_name']}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">2</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-10 text-center">
               <h5 class="section-title">{{re('Schools’ information')}}</h5>
            </div>
            <div class="col-12 mt-5">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{re('School Name')}}</th>
                            <th class="main-th">{{re('Total')}} {{re('Students')}}</th>
                            <th class="main-th">{{re('Total')}} {{re('Boys')}}</th>
                            <th class="main-th">{{re('Total')}} {{re('Girls')}}</th>
                            @if($arab)
                            <th class="main-th">{{re('Total')}} {{sysNationality()}}</th>
                            @endif
                            <th class="main-th">{{re('Total')}} {{re('SEN')}}</th>
                            <th class="main-th">{{re('Curriculum Type')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($schools_information as $name => $school)
                            <tr>
                                <td>{{$school['school_name'] ?? $name}}</td>
                                <td>{{$school['total_students']}}</td>
                                <td>{{$school['total_boys']}}</td>
                                <td>{{$school['total_girls']}}</td>
                                @if($arab)
                                <td>{{$school['total_' . sysNationality()]}}</td>
                                @endif
                                <td>{{$school['total_sen']}}</td>
                                <td>{{$school['curriculum']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">3</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison by selection Grades')}} <br> {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}}
                    <span class="sub-color">({{re('Overall data')}})</span></h5>
                <h3 class="text-center">
                    (
                    @foreach($grades as $grade)
                        {{re('Grade')}} {{$grade}}
                        @if(!$loop->last) , @endif
                    @endforeach

                    <span>)</span>
                </h3>
            </div>
            <div class="col-12 mt-5">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{re('School Name')}}</th>
                            <th class="below-td">{{re('Below')}}</th>
                            <th class="inline-td">{{re('Inline')}}</th>
                            <th class="above-td">{{re('Above')}}</th>
                            <th class="main-th">{{re('Total')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($schools_information as $name => $school)
                            <tr>
                                <td>{{$school['school_name'] ?? $name}}</td>
                                <td>{{$school['below']}}</td>
                                <td>{{$school['inline']}}</td>
                                <td>{{$school['above']}}</td>
                                <td>{{$school['total_terms']}}</td>
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
@if($data['total_schools'] >= 8)
    @php
        $height = 700;
    @endphp
@elseif($data['total_schools'] >= 6)
    @php
        $height = 500;
    @endphp
@elseif($data['total_schools'] >= 3)
    @php
        $height = 400;
    @endphp
@else
    @php
        $height = 400;
    @endphp
@endif
@php
$pageNum = 5;
@endphp
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison by selection Grades')}} <br> {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}}
                    <span class="sub-color">({{re('Overall data')}})</span></h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="general_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Reading')}}</span> {{re('by selection Grades')}} {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="reading_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Listening')}}</span> {{re('by selection Grades')}} {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="listening_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Writing')}}</span> {{re('by selection Grades')}} {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="writing_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Speaking')}}</span> {{re('by selection Grades')}} {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="speaking_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Boys')}} {{re('Students')}}</span>{{re(' by selection Grades')}}  {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="male_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('Girls')}} {{re('Students')}}</span> {{re('by selection Grades')}}  {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="female_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
               <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{re('SEN')}} {{re('Students')}}</span> {{re('by selection Grades')}}  {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="sen_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
@if($arab)
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
                <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{sysNationality()}} {{re('Boys')}} {{re('Students')}}</span> {{re('by selection Grades')}}  {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="uae_male_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-12 text-center">
                <h5 class="section-title">{{re('The comparison')}} <span class="text-warning">{{sysNationality()}} {{re('Girls')}} {{re('Students')}}</span> {{re('by selection Grades')}}  {{re('for')}} {{$arab == 1 ? re('Arabs'):re('Non-Arabs')}} <br/> ({{re('Overall data')}})</h5>
            </div>
        </div>
        <div class="row justify-content-center  mt-5">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="uae_female_schools_chart" style="height: {{$height}}px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span class="number-page">{{$pageNum++}}</span>
</div>
@endif
<div class="page">
    <div class="subpage-w">
        <div class="row justify-content-center  mt-2">
            <div class="col-10 text-center">
                <h5 class="section-title">{{re('Local and Global Ranking')}}</h5>
            </div>
            <div class="col-12 mt-5">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{re('School Name')}}</th>
                            <th class="main-th">{{re('Local Ranking')}}</th>
                            <th class="main-th">{{re('Global Ranking')}}</th>
                            <th class="main-th">{{re('Curriculum Ranking')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($schools_information as $name => $school)
                            <tr>
                                <td >{{$school['school_name'] ?? $name}}</td>
                                <td >{{$school['local_rank']}}</td>
                                <td >{{$school['global_rank']}}</td>
                                <td >{{$school['curriculum_rank']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <span class="number-page">{{$pageNum++}}</span>
</div>


<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
<script type="text/javascript">
    $(document).ready(function () {
        //window.print();
        // Radialize the colors
        Highcharts.setOptions({
            colors: ['#d50000', '#ffc107', '#00c853', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
        });
        //---------countries chart----------//

        Highcharts.chart('general_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['percent_above']}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['percent_inline']}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['percent_below']}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });
        Highcharts.chart('reading_schools_chart', {
            chart: { type: 'bar' },
            title: { text: ' ' },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: { gridLineDashStyle: 'LongDash', min: 0, title: { text: '' }, tickInterval: 20, max: 100 },
            tooltip: { enabled: false },
            plotOptions: {
                column: { pointPadding: 0.2, borderWidth: 0, events: { legendItemClick: function() { return false; } } },
                allowPointSelect: false,
                series: { animation: false, borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}%', color: '#000' }, borderRadiusTopLeft: '20%', borderRadiusTopRight: '20%' }
            },
            series: [
                { name: '{{re('Above')}}', data: [ @foreach($schools_information as $d) {{$d['reading']->per_above}}, @endforeach ], color: "#28C76F" },
                { name: '{{re('Inline')}}', data: [ @foreach($schools_information as $d) {{$d['reading']->per_inline}}, @endforeach ], color: "#F0DE36" },
                { name: '{{re('Below')}}', data: [ @foreach($schools_information as $d) {{$d['reading']->per_below}}, @endforeach ], color: "#EA5455" }
            ]
        });
        Highcharts.chart('listening_schools_chart', {
            chart: { type: 'bar' },
            title: { text: ' ' },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: { gridLineDashStyle: 'LongDash', min: 0, title: { text: '' }, tickInterval: 20, max: 100 },
            tooltip: { enabled: false },
            plotOptions: {
                column: { pointPadding: 0.2, borderWidth: 0, events: { legendItemClick: function() { return false; } } },
                allowPointSelect: false,
                series: { animation: false, borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}%', color: '#000' }, borderRadiusTopLeft: '20%', borderRadiusTopRight: '20%' }
            },
            series: [
                { name: '{{re('Above')}}', data: [ @foreach($schools_information as $d) {{$d['listening']->per_above}}, @endforeach ], color: "#28C76F" },
                { name: '{{re('Inline')}}', data: [ @foreach($schools_information as $d) {{$d['listening']->per_inline}}, @endforeach ], color: "#F0DE36" },
                { name: '{{re('Below')}}', data: [ @foreach($schools_information as $d) {{$d['listening']->per_below}}, @endforeach ], color: "#EA5455" }
            ]
        });
        Highcharts.chart('writing_schools_chart', {
            chart: { type: 'bar' },
            title: { text: ' ' },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: { gridLineDashStyle: 'LongDash', min: 0, title: { text: '' }, tickInterval: 20, max: 100 },
            tooltip: { enabled: false },
            plotOptions: {
                column: { pointPadding: 0.2, borderWidth: 0, events: { legendItemClick: function() { return false; } } },
                allowPointSelect: false,
                series: { animation: false, borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}%', color: '#000' }, borderRadiusTopLeft: '20%', borderRadiusTopRight: '20%' }
            },
            series: [
                { name: '{{re('Above')}}', data: [ @foreach($schools_information as $d) {{$d['writing']->per_above}}, @endforeach ], color: "#28C76F" },
                { name: '{{re('Inline')}}', data: [ @foreach($schools_information as $d) {{$d['writing']->per_inline}}, @endforeach ], color: "#F0DE36" },
                { name: '{{re('Below')}}', data: [ @foreach($schools_information as $d) {{$d['writing']->per_below}}, @endforeach ], color: "#EA5455" }
            ]
        });
        Highcharts.chart('speaking_schools_chart', {
            chart: { type: 'bar' },
            title: { text: ' ' },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: { gridLineDashStyle: 'LongDash', min: 0, title: { text: '' }, tickInterval: 20, max: 100 },
            tooltip: { enabled: false },
            plotOptions: {
                column: { pointPadding: 0.2, borderWidth: 0, events: { legendItemClick: function() { return false; } } },
                allowPointSelect: false,
                series: { animation: false, borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}%', color: '#000' }, borderRadiusTopLeft: '20%', borderRadiusTopRight: '20%' }
            },
            series: [
                { name: '{{re('Above')}}', data: [ @foreach($schools_information as $d) {{$d['speaking']->per_above}}, @endforeach ], color: "#28C76F" },
                { name: '{{re('Inline')}}', data: [ @foreach($schools_information as $d) {{$d['speaking']->per_inline}}, @endforeach ], color: "#F0DE36" },
                { name: '{{re('Below')}}', data: [ @foreach($schools_information as $d) {{$d['speaking']->per_below}}, @endforeach ], color: "#EA5455" }
            ]
        });
        Highcharts.chart('male_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male']->per_above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male']->per_inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male']->per_below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });
        Highcharts.chart('female_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '28C76F',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female']->per_above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female']->per_inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female']->per_below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });
        Highcharts.chart('sen_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['sen']->per_above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['sen']->per_inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['sen']->per_below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });
        @if($arab)
        Highcharts.chart('uae_male_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male_uae']->per_above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male_uae']->per_inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['male_uae']->per_below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });

        Highcharts.chart('uae_female_schools_chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($schools_information as $general_school_data)
                        '{{strtoupper($general_school_data['school_name'])}}',
                    @endforeach
                ],
            },
            yAxis: {
                gridLineDashStyle: 'LongDash',
                min: 0,
                title: {
                    text: ''
                },
                tickInterval: 20,
                max: 100,
            },
            tooltip: {
                enabled: false // This disables tooltips
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
                        format: '{point.y}%',
                        color: '#000',
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [
                {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female_uae']->per_above}},
                        @endforeach
                    ],
                    color: "#28C76F"
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female_uae']->per_inline}},
                        @endforeach
                    ],
                    color: "#F0DE36"
                }, {
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($schools_information as $general_school_data)
                            {{$general_school_data['female_uae']->per_below}},
                        @endforeach
                    ],
                    color: "#EA5455"
                }
            ]
        });
        @endif
    });
</script>


</body>
</html>
