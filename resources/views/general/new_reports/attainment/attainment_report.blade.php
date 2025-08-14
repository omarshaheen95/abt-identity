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
            top: 73% !important;
            right: 11% !important;
        }

        .report-title {
            top: 42% !important;
            right: 2% !important;
            text-align: center;
            color: #3F1023;
            width: 485px;
            font-size: 2.2rem;
        }
        .report-ranges {
            color: #3F1023;
            font-weight: 500;
        }
        @endif
    </style>
@endpush
@php
    $lang = app()->getLocale()=='ar'?'ar':'en';
@endphp
@section('content')
    <div class="page p-0">
        <div class="subpage-w">
            <div class="position-relative">
                <img src="{{ asset("reports/covers/attainment_".$lang.".svg").'?v=1' }}"
                     class="w-100" alt="">


                <h2 class="position-absolute report-title m-3 text-black">
                    {!! $reportTitleGroup['ar'] !!}
                    <br><br><br>
                    {!! $reportTitleGroup['en'] !!}

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
            <div class="row justify-content-center text-center">
                <div class="col-md-11 ">
                    @if($student_type == 1)
                        <img src="{{ asset("report/pages/attainment_ranges_arabs_".$lang.".svg") }}?v=2" width="100%"/>
                    @else
                        <img src="{{ asset("report/pages/attainment_ranges_non_arabs_".$lang.".svg") }}?v=2" width="100%"/>
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
                        <h4 class="main-color text-center"><i class="fas fa-chart-bar me-2"></i> {{re('Students Statistics')}}</h4>
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
                            <h5 class="section-title"> <i class="fas fa-chart-bar section-title-icon"></i>  {{re('General Statistics')}} @if(!$isCombined)  - {{ re('Grade')}} {{$grade}} / {{re('Year')}} {{$grade+1}} @endif</h5>
                        </div>
                        <div class="col-12 text-center @if($isCombined) mt-4  @endif">
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
                    @if(!$isCombined)
                        <div class="row justify-content-center mt-3">
                            <div class="col-12 text-center">
                                <h5 class="section-title"> <i class="fas fa-trophy section-title-icon"></i> {{re('Students Achievement')}}</h5>
                            </div>
                            <div class="col-12 text-center">
                                <div class="table-container">
                                    <table class="table m-0">
                                        <tr>
                                            <td class="main-td">{{re('Round')}}</td>
                                            <td class="main-td">{{re('Highest Student Achievement')}}</td>
                                            <td class="main-td">{{re('Lowest Student Achievement')}}</td>
                                        </tr>
                                        @foreach($page->rounds as $round)
                                            <tr>
                                                <td class="">{{$round->id}}</td>
                                                <td class="">{{$round->highest_student_mark}}</td>
                                                <td class="">{{$round->lowest_student_mark}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <div class="col-12 text-center">
                                <h5 class="section-title"> <i class="fas fa-calculator section-title-icon"></i> {{re('Average Benchmark of Grade')}} {{$grade}}</h5>
                            </div>
                            <div class="col-12 text-center">
                                <div class="table-container">
                                    <table class="table m-0">
                                        @foreach($page->rounds as $round)
                                            <tr>
                                                <td class="main-td w-25">{{$round->id}}</td>
                                                <td class="">{{$round->average}} / 100</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-11">
                            <h5 class="section-title"> <i class="fas fa-chart-line section-title-icon"></i> {{ $page->title }}</h5>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> <i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}}
                                        </th>
                                        <th class="below-td"> <i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}
                                        </th>
                                        <th class="inline-td"> <i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}
                                        </th>
                                        <th class="above-td"> <i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}
                                        </th>
                                        <th class="main-th"> <i class="fas fa-users me-1"></i>
                                            {{re('Total')}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->rounds as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                            <td>{{ $round->total }} {{re('Students')}}
                                                / {{$page->grade_data->students}} {{re('Students')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11  mb-5">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> <i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}}</th>
                                        <th class="below-td"> <i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}</th>
                                        <th class="inline-td"> <i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}</th>
                                        <th class="above-td"> <i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}</th>
                                        <th class="main-th"> <i class="fas fa-gavel me-1"></i>
                                            {{re('The Judgement')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->rounds as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">
                                                {{ $round->below }} {{re('Student')}}<br/>
                                                <span class="text-danger">{{ $round->per_below }} %</span>
                                            </td>
                                            <td class="inline-t-td">
                                                {{ $round->inline }} {{re('Student')}}<br/>
                                                <span class="text-danger">{{ $round->per_inline }} %</span>
                                            </td>
                                            <td class="above-t-td">
                                                {{ $round->above }} {{re('Student')}}<br/>
                                                <span class="text-danger">{{ $round->per_above }} %</span>
                                            </td>
                                            @php
                                                $rowData = judgement($round->per_below,$round->per_inline, $round->per_above);
                                            @endphp
                                            <td>
                                                {{ $round->total }} {{re('Students')}}
                                                / {{$page->grade_data->students}} {{re('Students')}}<br/>
                                                <span
                                                        class="{{str_replace(' ', '-', strtolower($rowData['level']))}}-badge">{{ $rowData['level'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11">
                            <div id="{{$page->student_type}}_{{$grade}}_rounds"></div>
                        </div>
                    </div>
                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>


            @foreach($subjects->chunk(2) as $subjects_chunk)
                <div class="page">
                    <div class="subpage-w">
                        @foreach($subjects_chunk as $subject)
                            <div class="row text-center justify-content-center">
                                <div class="col-11 ">
                                    <div class="table-container">
                                        <table class="table small m-0">
                                            <thead>
                                            <tr>
                                                <th class="main-th"> <i class="fas fa-calendar-alt me-1"></i>
                                                    {{re('Assessment')}} ({{$subject->name}})
                                                </th>
                                                <th class="below-td"> <i class="fas fa-arrow-down me-1"></i>
                                                    {{re('Below')}}</th>
                                                <th class="inline-td"> <i class="fas fa-minus me-1"></i>
                                                    {{re('Inline')}}</th>
                                                <th class="above-td"> <i class="fas fa-arrow-up me-1"></i>
                                                    {{re('Above')}}</th>
                                                <th class="main-th"> <i class="fas fa-users me-1"></i>
                                                    {{re('Total')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($page->{'step_'.$subject->id} as $round)
                                                <tr class="text-center">
                                                    <td>{{ $round->id }}</td>
                                                    <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                                    <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                                    <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                                    <td>{{ $round->total }}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center justify-content-center mt-2">
                                <div class="col-11">
                                    <div id="{{$page->student_type}}_{{$grade}}_step_{{$subject->id}}_rounds"></div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                    <span class="number-page">{{$pageNum++}}</span>
                </div>
            @endforeach

            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> <i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}} ({{re('Boys')}})</th>
                                        <th class="below-td"> <i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}</th>
                                        <th class="inline-td"> <i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}</th>
                                        <th class="above-td"> <i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}</th>
                                        <th class="main-th"> <i class="fas fa-users me-1"></i>
                                            {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->boys as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                            <td>{{ $round->total }} {{re('Student')}}
                                                / {{$page->grade_data->boys_students}} {{re('Students')}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        @foreach($page->boys as $round)
                            <div class="col-4">
                                <div id="{{$page->student_type}}_{{$grade}}_boys_{{$round->id}}"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}} ({{re('Girls')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i>
                                            {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->girls as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                            <td>{{ $round->total }} {{re('Student')}}
                                                / {{$page->grade_data->girls_students}} {{re('Students')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center justify-content-center mt-2">
                        @foreach($page->boys as $round)
                            <div class="col-4">
                                <div id="{{$page->student_type}}_{{$grade}}_girls_{{$round->id}}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}} ({{re('SEN')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i>
                                            {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->sen as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                            <td>{{ $round->total }} {{re('Student')}}
                                                / {{$page->grade_data->sen_students}} {{re('Students')}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        @foreach($page->sen as $round)
                            <div class="col-4">
                                <div id="{{$page->student_type}}_{{$grade}}_sen_{{$round->id}}"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row text-center justify-content-center mt-5">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                            {{re('Assessment')}} ({{re('G&T')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                            {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                            {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                            {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i>
                                            {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($page->g_t as $round)
                                        <tr class="text-center">
                                            <td>{{ $round->id }}</td>
                                            <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                            <td>{{ $round->total }} {{re('Student')}}
                                                / {{$page->grade_data->g_t_students}} {{re('Students')}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        @foreach($page->g_t as $round)
                            <div class="col-4">
                                <div id="{{$page->student_type}}_{{$grade}}_g_t_{{$round->id}}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>

            @if($page->grade_data->student_type != 'non_arabs')
                <div class="page">
                    <div class="subpage-w">
                        <div class="row text-center justify-content-center mt-5">
                            <div class="col-11">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                                {{re('Assessment')}} ({{sysNationality()}})</th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                                {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                                {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                                {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i>
                                                {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($page->citizen as $round)
                                            <tr class="text-center">
                                                <td>{{ $round->id }}</td>
                                                <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                                <td>{{ $round->total }} {{re('Student')}}
                                                    / {{$page->grade_data->citizen_students}} {{re('Students')}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div id="{{$page->student_type}}_{{$grade}}_citizen_rounds"></div>
                            </div>
                        </div>
                    </div>
                    <span class="number-page">{{$pageNum++}}</span>
                </div>
                <div class="page">
                    <div class="subpage-w">
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11 ">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                                {{re('Assessment')}}
                                                ({{sysNationality()}} {{re('Boys')}}
                                                )
                                            </th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                                {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                                {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                                {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i>
                                                {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($page->boys_citizen as $round)
                                            <tr class="text-center">
                                                <td>{{ $round->id }}</td>
                                                <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                                <td>{{ $round->total }} {{re('Student')}}
                                                    / {{$page->grade_data->boys_citizen_students}} {{re('Students')}}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div id="{{$page->student_type}}_{{$grade}}_boys_citizen_rounds"></div>
                            </div>
                        </div>

                        <div class="row text-center justify-content-center mt-4">
                            <div class="col-11">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                                {{re('Assessment')}}
                                                ({{sysNationality()}} {{re('Girls')}})
                                            </th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i>
                                                {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i>
                                                {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i>
                                                {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i>
                                                {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($page->girls_citizen as $round)
                                            <tr class="text-center">
                                                <td>{{ $round->id }}</td>
                                                <td class="below-t-td">{{ $round->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $round->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $round->above }} {{re('Student')}}</td>
                                                <td>{{ $round->total }} {{re('Student')}}
                                                    / {{$page->grade_data->girls_citizen_students}} {{re('Students')}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div id="{{$page->student_type}}_{{$grade}}_girls_citizen_rounds"></div>
                            </div>
                        </div>
                    </div>
                    <span class="number-page">{{$pageNum++}}</span>
                </div>
            @endif

        @endforeach
@endsection

@push('script')
    <script type="text/javascript">
        const chartColors = {
            below: "#ef4444",
            inline: "#f59e0b",
            above: "#10b981"
        };
        @foreach($pages as $grade => $page)
        var chart = Highcharts.chart("{{$page->student_type}}_{{$grade}}_rounds", {
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
                    @foreach($page->rounds as $round)
                        "{{$round->id}}",
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
                data: [@foreach($page->rounds as $round)
                        {{ $round->per_below}},
                    @endforeach ],
                color: chartColors.below

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($page->rounds as $round)
                        {{ $round->per_inline }},
                    @endforeach],
                color: chartColors.inline

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($page->rounds as $round)
                        {{ $round->per_above}},
                    @endforeach ],
                color: chartColors.above

            }]
        });


        @foreach($subjects->pluck('id') as $step)
        var chart = Highcharts.chart("{{$page->student_type}}_{{$grade}}_step_{{$step}}_rounds", {
            chart: {
                type: 'bar',
            },
            title: {
                text: ' '
            },
            subtitle: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($page->{'step_'.$step} as $round)
                        "{{$round->id}}",
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
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [{
                name: '{{re('Below expecting')}}',
                data: [@foreach($page->{'step_'.$step} as $round)
                        {{ $round->per_below }},
                    @endforeach],
                color: chartColors.below

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($page->{'step_'.$step} as $round)
                        {{ $round->per_inline }},
                    @endforeach],
                color: chartColors.inline

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($page->{'step_'.$step} as $round)
                        {{ $round->per_above }},
                    @endforeach],
                color: chartColors.above

            }]
        });
        chart.setSize(null, 350);
        @endforeach


        @foreach(['boys','girls','sen','g_t'] as $category)
        @foreach($page->{$category} as $round)
        var chart = Highcharts.chart('{{$page->student_type}}_{{$grade}}_{{$category}}_{{$round->id}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{$round->id}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85
            },
            tooltip: {
                enabled: false // This disables tooltips
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: -50,
                        style: {
                            fontWeight: 'bold',
                            color: 'white'
                        },
                        format: '{point.percentage:.1f}%'

                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%',
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '75%'],
                    size: '110%'
                }
            },
            series: [{
                type: 'pie',
                name: '{{re('Browser share')}}',
                innerSize: '65%',
                data: [
                    {name: '{{re('below')}}', y: {{$round->per_below}}, color: chartColors.below},
                    {name: '{{re('inline')}}', y: {{$round->per_inline}}, color: chartColors.inline},
                    {name: '{{re('above')}}', y: {{$round->per_above}}, color: chartColors.above},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach
        @endforeach
        @if($page->student_type != 'non_arabs')
        @foreach(['citizen', 'boys_citizen','girls_citizen'] as $category)
        console.log("{{$page->student_type}}_{{$grade}}_{{$category}}_rounds")
        @foreach($page->{$category} as $round)
        var chart = Highcharts.chart('{{$page->student_type}}_{{$grade}}_{{$category}}_rounds', {
            chart: {
                type: 'column',
                inverted: true,
                polar: true,
                rotation: -90,
            },
            title: {
                text: ''
            },
            tooltip: {
                outside: true
            },
            pane: {
                size: '85%',
                innerSize: '20%',
                endAngle: 180
            },
            xAxis: {
                tickInterval: 1,
                rotation: -90,
                labels: {
                    align: 'right',
                    useHTML: true,
                    allowOverlap: false,
                    step: 1,
                    y: 3,
                    style: {
                        fontSize: '13px'
                    }
                },
                lineWidth: 0,
                categories: [
                    @foreach($page->{$category} as $round)
                        "{{$round->id}}",
                    @endforeach
                ]
            },
            yAxis: {
                max: 100,
                crosshair: {
                    enabled: true,
                    color: '#333'
                },
                lineWidth: 0,
                tickInterval: 25,
                reversedStacks: false,
                endOnTick: true,
                showLastLabel: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    borderWidth: 0,
                    pointPadding: 0,
                    groupPadding: 0.15
                },
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y} %',
                        // rotation: -90,
                        x: 10,
                        color: '#000',
                        style: {
                            fontSize: '8px',

                        }

                    }
                }
            },
            series: [{
                name: '{{re('Below expecting')}}',
                data: [@foreach($page->{$category} as $round)
                        {{ $round->per_below }},
                    @endforeach],
                color: chartColors.below
            }, {
                name: '{{re('In line with curriculum expectations')}}',
                data: [@foreach($page->{$category} as $round)
                        {{ $round->per_inline }},
                    @endforeach],
                color: chartColors.inline
            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($page->{$category} as $round)
                        {{ $round->per_above }},
                    @endforeach],
                color: chartColors.above
            }]
        });
        chart.setSize(null, 300);
        @endforeach
        @endforeach
        @endif
        @endforeach
    </script>
@endpush
