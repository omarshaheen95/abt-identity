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
                <img src="{{ asset("report/pages/progress_".$lang.".svg").'?v=1' }}"
                     class="w-100" alt="">


                <h2 class="position-absolute report-title m-3 text-black">
                    {!! $reportTitleGroup['ar'] !!}
                    <br />
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
                    @if($student_type==1)
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
                                    <td class="">{{$school->name}}</td>
                                </tr>
                            @endif
                            @if($schools->count() > 1)
                                <tr>
                                    <td class="main-td w-25 py-2"><i class="fas fa-school me-2"></i>
                                        {{re('Schools')}}</td>
                                    <td class="">
                                        @foreach($schools as $schoolData)
                                            <span class="badge bg-secondary">{{$schoolData->name}}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="main-td w-25 py-2"><i
                                        class="fas fa-calendar me-2"></i> {{re('Academic Year')}}</td>
                                <td class="">
                                    @foreach($years as $year)
                                        <span class="badge bg-secondary">{{$year->name}}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td class="main-td w-25 py-2"><i
                                        class="fas fa-calendar me-2"></i> {{re('Round')}}</td>
                                <td class="">
                                    <span class="badge bg-secondary">{{$round}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-users me-2"></i> {{re('Students Type')}}</td>
                                <td class="">{{$student_type_title}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-layer-group me-2"></i> {{re('Grades')}}</td>
                                <td class="">{{implode(', ', $grades)}}</td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-layer-group me-2"></i> {{re('Report Type')}}
                                </td>
                                <td class="">
                                    @if(count($years) == 2)
                                        @if($isCombined)
                                            {{re('Combined Year to Year Progress Report')}}
                                        @else
                                            {{re('Year to Year Progress Report')}}
                                        @endif
                                    @else
                                        @if($isCombined)
                                            {{re('Combined Trend Over Time Report')}}
                                        @else
                                            {{re('Trend Over Time Report')}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="main-td py-2"><i class="fas fa-calendar-day me-2"></i> {{re('Issue Date')}}
                                </td>
                                <td class="">{{date('Y-m-d')}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @if(isset($arab_pages['statistics']) && $arab_pages['statistics']->total > 0)
                <div class="row mt-3 justify-content-center">
                    <div class="col-12">
                        <h4 class="main-color text-center">{{re('Arabs Students Statistics')}}</h4>
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
                                    @if($student_type == 1 || $student_type == 2)
                                        <td class="main-td"><i class="fas fa-flag me-1"></i> {{re(sysNationality())}}
                                        </td>
                                        <td class="main-td"><i
                                                class="fas fa-male me-1"></i> {{re(sysNationality().' Boys')}}</td>
                                        <td class="main-td"><i
                                                class="fas fa-female me-1"></i> {{re(sysNationality().' Girls')}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="">{{$arab_pages['statistics']->total}}</td>
                                    <td class="">{{$arab_pages['statistics']->boys}}</td>
                                    <td class="">{{$arab_pages['statistics']->girls}}</td>
                                    <td class="">{{$arab_pages['statistics']->sen_students}}</td>
                                    <td class="">{{$arab_pages['statistics']->g_t_students}}</td>
                                    @if($student_type == 1 || $student_type == 2)
                                        <td class="">{{$arab_pages['statistics']->citizen_students}}</td>
                                        <td class="">{{$arab_pages['statistics']->citizen_boys_students}}</td>
                                        <td class="">{{$arab_pages['statistics']->citizen_girls_students}}</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($non_arab_pages['statistics']) && $non_arab_pages['statistics']->total > 0)
                <div class="row mt-3 justify-content-center">
                    <div class="col-12">
                        <h4 class="main-color text-center">{{re('Non-Arabs Students Statistics')}}</h4>
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
                                    {{--                                    @if($student_type == 1 || $student_type == 2)--}}
                                    {{--                                        <td class="main-td"><i class="fas fa-flag me-1"></i> {{re(sysNationality())}}--}}
                                    {{--                                        </td>--}}
                                    {{--                                        <td class="main-td"><i--}}
                                    {{--                                                class="fas fa-male me-1"></i> {{re(sysNationality().' Boys')}}</td>--}}
                                    {{--                                        <td class="main-td"><i--}}
                                    {{--                                                class="fas fa-female me-1"></i> {{re(sysNationality().' Girls')}}</td>--}}
                                    {{--                                    @endif--}}
                                </tr>
                                <tr>
                                    <td class="">{{$non_arab_pages['statistics']->total}}</td>
                                    <td class="">{{$non_arab_pages['statistics']->boys}}</td>
                                    <td class="">{{$non_arab_pages['statistics']->girls}}</td>
                                    <td class="">{{$non_arab_pages['statistics']->sen_students}}</td>
                                    <td class="">{{$non_arab_pages['statistics']->g_t_students}}</td>
                                    {{--                                    @if($student_type == 1 || $student_type == 2)--}}
                                    {{--                                        <td class="">{{$non_arab_pages['statistics']->citizen_students}}</td>--}}
                                    {{--                                        <td class="">{{$non_arab_pages['statistics']->citizen_boys_students}}</td>--}}
                                    {{--                                        <td class="">{{$non_arab_pages['statistics']->citizen_girls_students}}</td>--}}
                                    {{--                                    @endif--}}
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <span class="number-page">{{$pageNum}}</span>
    </div>

    @foreach([$arab_pages, $non_arab_pages] as $pagesType)
        @foreach($pagesType['attainment'] as $grade => $gradeYears)
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-11">
                            <h5 class="section-title"><i class="fas fa-chart-line section-title-icon"></i> {{re($pagesType['title'], ['grade' => $grade])}}</h5>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> {{re('Assessment')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below')}} <br /><i class="fas fa-arrow-down me-1"></i></th>
                                        <th class="inline-td"> {{re('Inline')}} <br /><i class="fas fa-minus me-1"></i></th>
                                        <th class="above-td"> {{re('Above')}} <br /><i class="fas fa-arrow-up me-1"></i></th>
                                        <th class="main-th"> {{re('Total')}} <br /><i class="fas fa-users me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $year_info->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $year_info->above }} {{re('Student')}}</td>
                                            <td>{{ $year_info->total }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        <div class="col-11">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> {{re('Assessment')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below')}} <br /><i class="fas fa-arrow-down me-1"></i></th>
                                        <th class="inline-td"> {{re('Inline')}} <br /><i class="fas fa-minus me-1"></i></th>
                                        <th class="above-td"> {{re('Above')}} <br /><i class="fas fa-arrow-up me-1"></i></th>
                                        <th class="main-th"> {{re('The Judgement')}} <br /><i class="fas fa-gavel me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->per_below }} %</td>
                                            <td class="inline-t-td">{{ $year_info->per_inline }} %</td>
                                            <td class="above-t-td">{{ $year_info->per_above }} %</td>
                                            @php
                                                $rowData = judgement($year_info->per_below, $year_info->per_inline, $year_info->per_above);
                                            @endphp
                                            <td><span
                                                    class="{{str_replace(' ', '-', strtolower($rowData['level']))}}-badge">{{ $rowData['level'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        <div class="col-11">
                            <h4 class="main-color">{{re('The progress trends over time')}}</h4>
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"> {{re('Rounds')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
                                        <th class="below-td"> {{re('Below expected progress')}}</th>
                                        <th class="inline-td"> {{re('Expected progress')}}</th>
                                        <th class="above-td"> {{re('Better than expected progress')}}</th>
                                        <th class="main-th"> {{re('Total')}} <br /><i class="fas fa-users me-1"></i></th>
                                        <th class="main-th"> {{re('Judgement')}} <br /><i class="fas fa-gavel me-1"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pagesType['progress'][$grade] as $key => $data)
                                        <tr class="text-center">
                                            <td class="" rowspan="2"> {{ $data->id }} </td>
                                            <td class="below-t-td">{{ $data->below }}  {{re('Student')}}
                                            </td>
                                            <td class="inline-t-td">{{ $data->inline }}  {{re('Student')}}
                                            </td>
                                            <td class="above-t-td">{{ $data->above }}  {{re('Student')}}
                                            </td>
                                            <td class="" rowspan="2">{{ $data->total }}
                                                Student
                                            </td>
                                            @php
                                                $judgement = judgementProgress($data->per_below,$data->per_inline,$data->per_above);
                                            @endphp
                                            <td class="" rowspan="2"> <span
                                                    class="{{str_replace(' ', '-', strtolower($judgement['level']))}}-badge">{{$judgement['level']}}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="font-size: 12px !important; padding: 7px" class="below-t-td">
                                                {{ $data->per_below }}%
                                            </td>
                                            <td
                                                style="font-size: 12px !important; padding: 7px" class="inline-t-td">
                                                {{ $data->per_inline }}%
                                            </td>
                                            <td
                                                style="font-size: 12px !important; padding: 7px" class="above-t-td">
                                                {{ $data->per_above }}%
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
                            <div id="{{$pagesType['pages_type_text']}}_grade_container_{{ $grade }}"></div>
                        </div>
                    </div>
                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>
           @php
                $skills_count = 1;
            @endphp
            @foreach(collect($pagesType['skills'])->chunk(2) as $skills_array)
               <div class="page">
                   <div class="subpage-w">
                       @foreach($skills_array as $skill)
                           <div class="row text-center justify-content-center">
                               <div class="col-11 ">
                                   <div class="table-container">
                                       <table class="table small m-0">
                                           <thead>
                                           <tr>
                                               <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                                   {{re('Assessment')}} ({{re($skill)}})
                                               </th>
                                               <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                               <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                               <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                               <th class="main-th"><i class="fas fa-users me-1"></i> {{re('Total')}}</th>
                                           </tr>
                                           </thead>
                                           <tbody>
                                           @foreach($gradeYears as $year_key => $year_info)
                                               <tr class="text-center">
                                                   <td>{{ $year_key }}</td>
                                                   <td class="below-t-td">{{ $year_info->{"mark_step$skills_count"}->below }} {{re('Student')}}</td>
                                                   <td class="inline-t-td">{{ $year_info->{"mark_step$skills_count"}->inline }} {{re('Student')}}</td>
                                                   <td class="above-t-td">{{ $year_info->{"mark_step$skills_count"}->above }} {{re('Student')}}</td>
                                                   <td>{{ $year_info->{"mark_step$skills_count"}->total }}</td>
                                               </tr>
                                           @endforeach

                                           </tbody>
                                       </table>
                                   </div>
                               </div>
                           </div>
                           <div class="row text-center justify-content-center mt-2">
                               <div class="col-11">
                                   <div id="{{$pagesType['pages_type_text']}}_step_{{$skills_count}}_container_{{ $grade }}"></div>
                               </div>
                           </div>
                           @php
                               $skills_count++;
                           @endphp
                       @endforeach
                   </div>
                   <span class="number-page">{{$pageNum++}}</span>
               </div>

           @endforeach
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} ({{re('Boys')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i> {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->boys->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $year_info->boys->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $year_info->boys->above }} {{re('Student')}}</td>
                                            <td>{{ $year_info->boys->total }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-11">
                            <div id="{{$pagesType['pages_type_text']}}_boys_container_{{ $grade }}"></div>
                        </div>
                    </div>

                    <div class="row text-center justify-content-center mt-4">
                        <div class="col-11">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} ({{re('Girls')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i> {{re('The Judgement')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->girls->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $year_info->girls->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $year_info->girls->above }} {{re('Student')}}</td>
                                            <td>{{ $year_info->girls->total }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-11">
                            <div id="{{$pagesType['pages_type_text']}}_girls_container_{{ $grade }}"></div>
                        </div>
                    </div>


                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>
            <div class="page">
                <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-11 ">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} ({{re('SEN')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i> {{re('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->sen_students->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $year_info->sen_students->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $year_info->sen_students->above }} {{re('Student')}}</td>
                                            <td>{{ $year_info->sen_students->total }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-11">
                            <div id="{{$pagesType['pages_type_text']}}_sen_students_container_{{ $grade }}"></div>
                        </div>
                    </div>

                    <div class="row text-center justify-content-center mt-4">
                        <div class="col-11">
                            <div class="table-container">
                                <table class="table small m-0">
                                    <thead>
                                    <tr>
                                        <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} ({{re('G&T')}})</th>
                                        <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                        <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                        <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                        <th class="main-th"><i class="fas fa-users me-1"></i> {{re('The Judgement')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gradeYears as $year_key => $year_info)
                                        <tr class="text-center">
                                            <td>{{ $year_key }}</td>
                                            <td class="below-t-td">{{ $year_info->g_t_students->below }} {{re('Student')}}</td>
                                            <td class="inline-t-td">{{ $year_info->g_t_students->inline }} {{re('Student')}}</td>
                                            <td class="above-t-td">{{ $year_info->g_t_students->above }} {{re('Student')}}</td>
                                            <td>{{ $year_info->g_t_students->total }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center justify-content-center mt-2">
                        <div class="col-11">
                            <div id="{{$pagesType['pages_type_text']}}_g_t_students_container_{{ $grade }}"></div>
                        </div>
                    </div>


                </div>
                <span class="number-page">{{$pageNum++}}</span>
            </div>
            @if($pagesType['pages_type'] != 0)
                <div class="page">
                    <div class="subpage-w">
                        <div class="row text-center justify-content-center">
                            <div class="col-11 ">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} ({{re(sysNationality())}})</th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i> {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($gradeYears as $year_key => $year_info)
                                            <tr class="text-center">
                                                <td>{{ $year_key }}</td>
                                                <td class="below-t-td">{{ $year_info->citizen_students->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $year_info->citizen_students->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $year_info->citizen_students->above }} {{re('Student')}}</td>
                                                <td>{{ $year_info->citizen_students->total }}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div
                                    id="{{$pagesType['pages_type_text']}}_citizen_students_container_{{ $grade }}"></div>
                            </div>
                        </div>

                        <div class="row text-center justify-content-center mt-4">
                            <div class="col-11">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} {{re(sysNationality().' Boys')}}</th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i> {{re('The Judgement')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($gradeYears as $year_key => $year_info)
                                            <tr class="text-center">
                                                <td>{{ $year_key }}</td>
                                                <td class="below-t-td">{{ $year_info->citizen_boys_students->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $year_info->citizen_boys_students->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $year_info->citizen_boys_students->above }} {{re('Student')}}</td>
                                                <td>{{ $year_info->citizen_boys_students->total }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div
                                    id="{{$pagesType['pages_type_text']}}_citizen_boys_students_container_{{ $grade }}"></div>
                            </div>
                        </div>
                    </div>
                    <span class="number-page">{{$pageNum++}}</span>
                </div>
                <div class="page">
                    <div class="subpage-w">
                        <div class="row text-center justify-content-center">
                            <div class="col-11">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"><i class="fas fa-calendar-alt me-1"></i> {{re('Assessment')}} {{re(sysNationality().' Girls')}}</th>
                                            <th class="below-td"><i class="fas fa-arrow-down me-1"></i> {{re('Below')}}</th>
                                            <th class="inline-td"><i class="fas fa-minus me-1"></i> {{re('Inline')}}</th>
                                            <th class="above-td"><i class="fas fa-arrow-up me-1"></i> {{re('Above')}}</th>
                                            <th class="main-th"><i class="fas fa-users me-1"></i> {{re('The Judgement')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($gradeYears as $year_key => $year_info)
                                            <tr class="text-center">
                                                <td>{{ $year_key }}</td>
                                                <td class="below-t-td">{{ $year_info->citizen_girls_students->below }} {{re('Student')}}</td>
                                                <td class="inline-t-td">{{ $year_info->citizen_girls_students->inline }} {{re('Student')}}</td>
                                                <td class="above-t-td">{{ $year_info->citizen_girls_students->above }} {{re('Student')}}</td>
                                                <td>{{ $year_info->citizen_girls_students->total }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div
                                    id="{{$pagesType['pages_type_text']}}_citizen_girls_students_container_{{ $grade }}"></div>
                            </div>
                        </div>
                    </div>
                    <span class="number-page">{{$pageNum++}}</span>
                </div>
            @endif
        @endforeach
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

            @foreach([$arab_pages, $non_arab_pages] as $pagesType)
            @foreach($pagesType['attainment'] as $grade => $gradeYears)
            Highcharts.chart('{{$pagesType['pages_type_text']}}_grade_container_{{ $grade }}', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: '{{re('The progress trends over time')}}'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        @foreach($gradeYears as $year_key => $year_info)
                            '{{$year_key}}',
                        @endforeach
                    ]
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
                plotOptions: {
                    line: {
                        events: {
                            legendItemClick: function () {
                                return false;
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            format: '{y} %',
                        },
                        enableMouseTracking: false
                    },
                    series: {
                        lineWidth: 3
                    }
                },
                series: [{
                    name: '{{re('Below')}}',
                    data: [
                        @foreach($gradeYears as $year_key => $year_info)
                            {{$year_info->per_below}},
                        @endforeach
                    ],
                    marker: {
                        symbol: 'square'
                    },
                    color: chartColors.below,
                }, {
                    name: '{{re('Inline')}}',
                    data: [
                        @foreach($gradeYears as $year_key => $year_info)
                            {{$year_info->per_inline}},
                        @endforeach
                    ],
                    color: chartColors.inline,
                    marker: {
                        symbol: 'square'
                    },
                }, {
                    name: '{{re('Above')}}',
                    data: [
                        @foreach($gradeYears as $year_key => $year_info)
                            {{$year_info->per_above}},
                        @endforeach
                    ],
                    marker: {
                        symbol: 'square'
                    },
                    color: chartColors.above,
                }]
            });

            @foreach(range(1, $steps_count) as $step)
            @if($step)
            var chart = Highcharts.chart("{{$pagesType['pages_type_text']}}_step_{{$step}}_container_{{ $grade }}", {
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
                        @foreach($gradeYears as $year_key => $year_info)
                            '{{$year_key}}',
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
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{'mark_step'.$step}->per_below}},
                        @endforeach],
                    color: chartColors.below,

                }, {
                    name: '{{re('In line with curriculum expectations')}} ',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{'mark_step'.$step}->per_inline}},
                        @endforeach],
                    color: chartColors.inline,

                }, {
                    name: '{{re('Above curriculum expectations')}}',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{'mark_step'.$step}->per_above}},
                        @endforeach],
                    color: chartColors.above,

                }]
            });
            chart.setSize(null, 350);
            @endif
            @endforeach
            @foreach(['boys', 'girls', 'sen_students', 'g_t_students'] as $category)
            var chart = Highcharts.chart("{{$pagesType['pages_type_text']}}_{{$category}}_container_{{ $grade }}", {
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
                        @foreach($gradeYears as $year_key => $year_info)
                            '{{$year_key}}',
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
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_below}},
                        @endforeach],
                    color: chartColors.below

                }, {
                    name: '{{re('In line with curriculum expectations')}} ',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_inline}},
                        @endforeach],
                    color: chartColors.inline

                }, {
                    name: '{{re('Above curriculum expectations')}}',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_above}},
                        @endforeach],
                    color: chartColors.above

                }]
            });
            chart.setSize(null, 350);
            @endforeach
            @if($pagesType['pages_type'] != 0)
            @foreach(['citizen_students', 'citizen_boys_students', 'citizen_girls_students'] as $category)
            var chart = Highcharts.chart("{{$pagesType['pages_type_text']}}_{{$category}}_container_{{ $grade }}", {
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
                        @foreach($gradeYears as $year_key => $year_info)
                            '{{$year_key}}',
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
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_below}},
                        @endforeach],
                    color: chartColors.below

                }, {
                    name: '{{re('In line with curriculum expectations')}} ',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_inline}},
                        @endforeach],
                    color: chartColors.inline

                }, {
                    name: '{{re('Above curriculum expectations')}}',
                    data: [@foreach($gradeYears as $year_key => $year_info)
                        {{$year_info->{$category}->per_above}},
                        @endforeach],
                    color: chartColors.above

                }]
            });
            chart.setSize(null, 350);
            @endforeach
            @endif
            @endforeach
            @endforeach
        });
    </script>
@endpush
