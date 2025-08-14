@extends('general.new_reports.layout')
@push('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        @if(app()->getLocale()=='ar')
    .report-date {
            top: 70% !important;
            left: 15% !important;
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
        .std_title{
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
                <img src="{{ asset("report/pages/progress_".$lang.".svg").'?v=1' }}"
                     class="w-100" alt="">


                <h2 class="position-absolute report-title m-3 text-black">
                    {!! $reportTitleGroup['ar'] !!}
                    <br /><br /><br />
                    {!! $reportTitleGroup['en'] !!}
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
            <div class="row text-center">
                <h2 class="sub-color">{{re('What is A B T assessments')}}!
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
            {{--        <div class="row justify-content-center mt-3 mb-5">--}}
            {{--            <div class="col-6 text-center">--}}
            {{--                <div class="image-container">--}}
            {{--                    <img src="{{asset($school->logo)}}" alt="">--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--        </div>--}}
            <div class="row justify-content-center  mt-2">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('In this report, we analyse the below points')}}</h4>
                </div>
            </div>
            <div class="row justify-content-center  mt-4">
                <div class="col-11 text-center">
                    <img src="{{asset("report/parts/analysis_points_$lang.svg")}}" width="100%" alt="">
                </div>
            </div>


        </div>
        <span class="number-page">3</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center">

                <div class="col-12 text-center mt-2">
                    @if($type==1)
                        <img src="{{asset("report/Arabs-Ranges_$lang.png")}}" width="90%"
                             alt="">
                    @else
                        <img src="{{asset("report/Non-Arabs-Ranges_$lang.png")}}" width="90%"
                             alt="">
                    @endif
                </div>
            </div>


        </div>
        <span class="number-page">4</span>
    </div>
    @php
        $pageNum = 5;
    @endphp
    {{--start check guard and show the report--}}
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center mt-3 mb-5">
                <div class="col-6 text-center">
                    <div class="image-container">
                        @if(guardIs('inspection'))
                            <img src="{{asset($auth()->guard('inspection')->user()->image)}}" alt="">
                        @elseif(guardIs('school') || $schools->count() == 1)
                            <img src="{{asset($schools->first()->logo)}}" alt="">
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-4 justify-content-center">
                <div class="col-12 text-center">
                    <div class="table-container">
                        <table class="table m-0">
                            @if(guardIs('inspection'))
                                <tr>
                                    <td class="main-td w-25 py-2"><i class="fas fa-user me-2"></i>
                                        {{re('Inspection Name')}}</td>
                                    <td class="">{{$auth()->guard('inspection')->user()->name}}</td>
                                </tr>
                            @elseif(guardIs('school') || $schools->count() == 1)
                                <tr>
                                    <td class="main-td w-25 py-2"><i class="fas fa-school me-2"></i>
                                        {{re('School Name')}}</td>
                                    <td class="">{{$report_info['school']}}</td>
                                </tr>
                            @endif
                            @if($schools->count() > 1)
                                <tr>
                                    <td class="main-td w-25 py-2"><i class="fas fa-school me-2"></i>
                                        {{re('Schools')}}</td>
                                    <td class="">
                                        @foreach($schools as $school)
                                            <span class="badge bg-secondary">{{$school->name}}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="main-td w-25 py-2"><i class="fas fa-calendar me-2"></i> {{re('Academic Year')}}</td>
                                <td class="">{{$report_info['year']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-users me-2"></i> {{re('Students Type')}}</td>
                                <td class="">{{$report_info['student_type']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-layer-group me-2"></i> {{re('Grades')}}</td>
                                <td class="">{{$report_info['grades']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-list me-2"></i> {{re('Sections')}}</td>
                                <td class="">{{$report_info['sections']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-hands-helping me-2"></i> {{re('SEN')}}</td>
                                <td class="">{{$report_info['sen']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"> <i class="fas fa-star me-2"></i> {{re('G&T')}}</td>
                                <td class="">{{$report_info['g_t']}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-calendar-day me-2"></i> {{re('Issue Date')}}</td>
                                <td class="">{{date('Y-m-d')}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @isset($data['student_statistics'])
                <div class="row mt-5 justify-content-center">
                    <div class="col-12">
                        <h4 class="main-color text-center">{{re('Students Statistics')}}</h4>
                    </div>
                    <div class="col-12 mt-2 text-center">
                        <div class="table-container">
                            <table class="table m-0">
                                <tr>
                                    <td class="main-td"><i class="fas fa-users me-1"></i> {{re('Total Students')}}</td>
                                    <td class="main-td"><i class="fas fa-male me-1"></i> {{re('Boys')}}</td>
                                    <td class="main-td"><i class="fas fa-female me-1"></i> {{re('Girls')}}</td>
                                    <td class="main-td"><i class="fas fa-hands-helping me-1"></i> {{re('SEN')}}</td>
                                    <td class="main-td"><i class="fas fa-star me-1"></i> {{re('G&T')}}</td>
                                    @if($type == 1 || $type == 2)
                                    <td class="main-td"><i class="fas fa-flag me-1"></i> {{re(sysNationality())}}</td>
                                    <td class="main-td"><i class="fas fa-male me-1"></i> {{re(sysNationality().' Boys')}}</td>
                                    <td class="main-td"><i class="fas fa-female me-1"></i> {{re(sysNationality().' Girls')}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="">{{$data['student_statistics']->students}}</td>
                                    <td class="">{{$data['student_statistics']->boys_students}}</td>
                                    <td class="">{{$data['student_statistics']->girls_students}}</td>
                                    <td class="">{{$data['student_statistics']->sen_students}}</td>
                                    <td class="">{{$data['student_statistics']->g_t_students}}</td>
                                    @if($type == 1 || $type == 2)
                                    <td class="">{{$data['student_statistics']->citizen_students}}</td>
                                    <td class="">{{$data['student_statistics']->boys_citizen_students}}</td>
                                    <td class="">{{$data['student_statistics']->girls_citizen_students}}</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
        <span class="number-page">{{$pageNum}}</span>
    </div>
    {{--end check guard and show the report--}}
    {{--Start Pages--}}
    @foreach($pages as $grade => $page)
        <div class="page">
            <div class="subpage-w">
                <div class="row justify-content-center mt-2">
                    <div class="col-12 text-center">
                        <h5 class="section-title"> <i class="fas fa-chart-bar section-title-icon"></i>  {{re('General Statistics')}}</h5>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-12 text-center">
                        <div class="table-container">
                            <table class="table m-0">
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-users me-2"></i> {{re('Total Students')}}</td>
                                    <td class="">{{$page->grade_data->students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-male me-2"></i> {{re('Boys')}}</td>
                                    <td class="">{{$page->grade_data->boys_students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-female me-2"></i> {{re('Girls')}}</td>
                                    <td class="">{{$page->grade_data->girls_students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-hands-helping me-2"></i> {{re('SEN')}}</td>
                                    <td class="">{{$page->grade_data->sen_students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-star me-2"></i> {{re('G&T')}}</td>
                                    <td class="">{{$page->grade_data->g_t_students}}</td>
                                </tr>
                                @if($type == 1 || $type == 2)
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-flag me-2"></i> {{re(sysNationality())}}</td>
                                    <td class="">{{$page->grade_data->citizen_students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-male me-2"></i> {{re(sysNationality().' Boys')}}</td>
                                    <td class="">{{$page->grade_data->boys_citizen_students}}</td>
                                </tr>
                                <tr>
                                    <td class="main-td w-25"><i class="fas fa-female me-2"></i> {{re(sysNationality().' Girls')}}</td>
                                    <td class="">{{$page->grade_data->girls_citizen_students}}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page">
            <div class="subpage-w">
                <div class="row text-center justify-content-center">
                    <div class="col-12">
                        <h5 class="section-title"><i class="fas fa-chart-line section-title-icon"></i> {{$page->title}}</h5>
                    </div>
                </div>
                <div class="row text-center justify-content-center my-3">
                    <div class="col-12  mb-5">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Rounds')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                    <th class="below-td"> {{re('Below expected')}} <br /><i class="fas fa-arrow-down me-1"></i></th>
                                    <th class="inline-td"> {{re('Expected progress')}} <br /><i class="fas fa-minus me-1"></i></th>
                                    <th class="above-td"> {{re('Better than expected')}} <br /><i class="fas fa-arrow-up me-1"></i></th>
                                    <th class="main-th"> {{re('Total')}} <br /><i class="fas fa-users me-1"></i></th>
                                    <th class="main-th"> {{re('Judgement')}} <br /><i class="fas fa-gavel me-1"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-center">
                                    <td class="" > {{ $page->septProgressData['name'] }}</td>
                                    <td class="below-t-td">{{ $page->septProgressData['below'] }}
                                        {{re('Student')}} = {{ $page->septProgressData['below_ratio'] }}%
                                    </td>
                                    <td class="inline-t-td">{{ $page->septProgressData['inline'] }}
                                        {{re('Student')}} = {{ $page->septProgressData['inline_ratio'] }}%
                                    </td>
                                    <td class="above-t-td">{{ $page->septProgressData['above'] }}
                                        {{re('Student')}} = {{ $page->septProgressData['above_ratio'] }}%
                                    </td>
                                    <td class="">{{ $page->septProgressData['total'] }}
                                    </td>
                                    @php
                                        $data = judgement($page->septProgressData['below_ratio'],$page->septProgressData['inline_ratio'],$page->septProgressData['above_ratio']);
                                    @endphp
                                    <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                </tr>
                                <tr class="text-center">
                                    <td class="" > {{ $page->febProgressData['name'] }}</td>
                                    <td class="below-t-td">{{ $page->febProgressData['below'] }}
                                        {{re('Student')}} = {{ $page->febProgressData['below_ratio'] }}%
                                    </td>
                                    <td class="inline-t-td">{{ $page->febProgressData['inline'] }}
                                        {{re('Student')}} = {{ $page->febProgressData['inline_ratio'] }}%
                                    </td>
                                    <td class="above-t-td">{{ $page->febProgressData['above'] }}
                                        {{re('Student')}} = {{ $page->febProgressData['above_ratio'] }}%
                                    </td>
                                    <td class="">{{ $page->febProgressData['total'] }}
                                    </td>
                                    @php
                                        $data = judgement($page->febProgressData['below_ratio'],$page->febProgressData['inline_ratio'],$page->febProgressData['above_ratio']);
                                    @endphp
                                    <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                </tr>
                                <tr class="text-center">
                                    <td class="" > {{ $page->mayProgressData['name'] }}</td>
                                    <td class="below-t-td">{{ $page->mayProgressData['below'] }}
                                        {{re('Student')}} = {{ $page->mayProgressData['below_ratio'] }}%
                                    </td>
                                    <td class="inline-t-td">{{ $page->mayProgressData['inline'] }}
                                        {{re('Student')}} = {{ $page->mayProgressData['inline_ratio'] }}%
                                    </td>
                                    <td class="above-t-td">{{ $page->mayProgressData['above'] }}
                                        {{re('Student')}} = {{ $page->mayProgressData['above_ratio'] }}%
                                    </td>
                                    <td class="">{{ $page->mayProgressData['total'] }}
                                    </td>
                                    @php
                                        $data = judgement($page->mayProgressData['below_ratio'],$page->mayProgressData['inline_ratio'],$page->mayProgressData['above_ratio']);
                                    @endphp
                                    <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11">
                        <div id="{{$page->student_type}}_{{$grade}}_progress"></div>
                    </div>
                </div>
            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
        <div class="page">
            <div class="subpage-w">
                <div class="row text-center justify-content-center mt-2">
                    <div class="col-12 ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"><i class="fas fa-male me-1"></i> {{re('Boys')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                    <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                    <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                    <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                    <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                    <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($page->boys as $key => $round)
                                    <tr class="text-center">
                                        <td>{{ $key }}</td>
                                        <td class="below-t-td">{{ $round['below'] }} <span class="std_title">{{re('Student')}}</span> = {{ $round['below_ratio'] }}%</td>
                                        <td class="inline-t-td">{{ $round['inline'] }} <span class="std_title">{{re('Student')}}</span> = {{ $round['inline_ratio'] }}%</td>
                                        <td class="above-t-td">{{ $round['above'] }} <span class="std_title">{{re('Student')}}</span> = {{ $round['above_ratio'] }}%</td>
                                        <td>{{ $round['total'] }}</td>
                                        @php
                                            $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                        @endphp
                                        <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-2">
                    <div class="col-12  ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"><i class="fas fa-female me-1"></i> {{re('Girls')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                    <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                    <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                    <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                    <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                    <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($page->girls as $key => $round)
                                    <tr class="text-center">
                                        <td>{{ $key }}</td>
                                        <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                        <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                        <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                        <td>{{ $round['total'] }}</td>
                                        @php
                                            $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                        @endphp
                                        <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-2">
                    <div class="col-12  ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"><i class="fas fa-hands-helping me-1"></i> {{re('SEN')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                    <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                    <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                    <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                    <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                    <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($page->sen as $key => $round)
                                    <tr class="text-center">
                                        <td>{{ $key }}</td>
                                        <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                        <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                        <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                        <td>{{ $round['total'] }}</td>
                                        @php
                                            $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                        @endphp
                                        <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-2">
                    <div class="col-12  ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"><i class="fas fa-star me-1"></i> {{re('G&T')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                    <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                    <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                    <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                    <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                    <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($page->g_t as $key => $round)
                                    <tr class="text-center">
                                        <td>{{ $key }}</td>
                                        <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                        <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                        <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                        <td>{{ $round['total'] }}</td>
                                        @php
                                            $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                        @endphp
                                        <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if($type == 1 || $type == 2)
                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-12 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-flag me-1"></i> {{sysNationality()}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                        <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                        <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                        <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                        <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->citizen as $key => $round)
                                        <tr class="text-center">
                                            <td>{{ $key }}</td>
                                            <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                            <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                            <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                            <td>{{ $round['total'] }}</td>
                                            @php
                                                $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                            @endphp
                                            <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-12 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-male me-1"></i> {{sysNationality()}} {{re('Boys')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                        <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                        <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                        <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                        <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->citizen_boys as $key => $round)
                                        <tr class="text-center">
                                            <td>{{ $key }}</td>
                                            <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                            <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                            <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                            <td>{{ $round['total'] }}</td>
                                            @php
                                                $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                            @endphp
                                            <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-12 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-female me-1"></i> {{sysNationality()}} {{re('Girls')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below expected')}}<br /><i class="fas fa-arrow-down me-1"></i></th>
                                        <th class="inline-td"> {{re('Expected progress')}}<br /><i class="fas fa-minus me-1"></i></th>
                                        <th class="above-td"> {{re('Better than expected')}}<br /><i class="fas fa-arrow-up me-1"></i></th>
                                        <th class="main-th"> {{re('Total')}}<br /><i class="fas fa-users me-1"></i></th>
                                        <th class="main-th"> {{re('Judgement')}}<br /><i class="fas fa-gavel me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->citizen_girls as $key => $round)
                                        <tr class="text-center">
                                            <td>{{ $key }}</td>
                                            <td class="below-t-td">{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                            <td class="inline-t-td">{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                            <td class="above-t-td">{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
                                            <td>{{ $round['total'] }}</td>
                                            @php
                                                $data = judgement($round['below_ratio'],$round['inline_ratio'],$round['above_ratio']);
                                            @endphp
                                            <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
    @endforeach
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function () {
            const chartColors = {
                below: "#ef4444",
                inline: "#f59e0b",
                above: "#10b981"
            };
            @foreach($pages as $grade => $page)
            var chart = Highcharts.chart("{{$page->student_type}}_{{$grade}}_progress", {
                chart: {
                    type: 'column',
                },
                title: {
                    text: ' '
                },
                subtitle: {
                    text: ' '
                },
                xAxis: {
                    categories: [
                        @foreach($rounds as $round)
                            "{{re($round)}}",
                        @endforeach
                    ],
                    crosshair: true,


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
                            style: {
                                fontSize: '9px',
                            }
                        },
                        borderRadiusTopLeft: '15%',
                        borderRadiusTopRight: '15%'
                    }
                },
                series: [{
                    name: '{{re('Below expecting')}}',
                    data: [
                        {{$page->septProgressData['below_ratio']}},
                        {{$page->febProgressData['below_ratio']}},
                        {{$page->mayProgressData['below_ratio']}},
                    ],
                    color: chartColors.below

                }, {
                    name: '{{re('In line with curriculum expectations')}} ',
                    data: [
                        {{$page->septProgressData['inline_ratio']}},
                        {{$page->febProgressData['inline_ratio']}},
                        {{$page->mayProgressData['inline_ratio']}},
                    ],
                    color: chartColors.inline

                }, {
                    name: '{{re('Above curriculum expectations')}}',
                    data: [
                        {{$page->septProgressData['above_ratio']}},
                        {{$page->febProgressData['above_ratio']}},
                        {{$page->mayProgressData['above_ratio']}},
                    ],
                    color: chartColors.above

                }]
            });
            @endforeach
        });
    </script>
@endpush
