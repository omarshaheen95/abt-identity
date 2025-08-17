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
    @if(app()->getLocale() == 'ar')
        <div class="page introduction-page">
            <div class="subpage-w">
                <div class="row text-center">
                    <h2 class="sub-color">اختبار الهوية الوطنية: تعزيز القيم والمواطنة والثقافة في دولة الإمارات العربية المتحدة</h2>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-10">
                        <ul>
                            <li class="mt-2 fw-500"><span class="main-color fw-bold">تُقدّم شركة اي بي تي للاختبارات المعيارية الدولية</span> اختبار الهوية الوطنية، وهو اختبار يهدف إلى ترسيخ الهوية الوطنية لدى طلاب المدارس الحكومية والخاصة في دولة الإمارات</li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">يُقدّم هذا الاختبار</span> بشكل مجاني تمامًا لجميع المدارس الحكومية والخاصة التي ترغب في تطبيق الاختبار، تقديراً منا لدولة الإمارات العربية المتحدة</li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">يسعى الاختبار</span> لتعزيز قيم المواطنة والارتقاء بثقافة الطلاب بما يتماشى مع رؤية الإمارات لبناء جيلٍ واعٍ بأصالته وهويته</li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">مدة الاختبار ومتطلباته التقنية:</span>
                                <ul class="mt-2">
                                    <li>يستغرق الاختبار 40 دقيقة، ويتم تقديمه عبر متصفح "جوجل كروم" لضمان أفضل تجربة تقنية للطلاب</li>
                                    <li>يتاح الاختبار بنسختين:
                                        <ul>
                                            <li>النسخة العربية: مخصصة للطلاب العرب</li>
                                            <li>النسخة الإنجليزية: مخصصة للطلاب غير الناطقين بالعربية</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">المحاور الأساسية للاختبار:</span>
                                <ul class="mt-2">
                                    <li>المحور الأول: (الثقافة)
                                        <ul>
                                            <li>المحاور الفرعية: اللغة العربية، التاريخ، التراث</li>
                                        </ul>
                                    </li>
                                    <li>المحور الثاني: (القيم)
                                        <ul>
                                            <li>المحاور الفرعية: الاحترام، التعاطف، التفاهم العالمي</li>
                                        </ul>
                                    </li>
                                    <li>المحور الثالث: (المواطنة)
                                        <ul>
                                            <li>المحاور الفرعية: الانتماء، التطوع، الحفاظ على البيئة</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">المجالات:</span>
                                <ul class="mt-2">
                                    <li>المناهج الدراسية: كيف يتم تعديل المناهج لدمج المحاور الفرعية؟</li>
                                    <li>التوفير: ما الفرص التي توفرها المدرسة لتعزيز معرفة الطلاب وفهمهم؟</li>
                                    <li>التأثير: مدى عمق معرفة الطلاب وفهمهم للمحاور الفرعية</li>
                                </ul>
                            </li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">متطلبات إضافية لتنفيذ الاختبار وتطبيق المحاور:</span>
                                <ul class="mt-2">
                                    <li>تُدرج في المناهج الدراسية أنشطة وتطبيقات مبتكرة تعزّز محاور الهوية الوطنية</li>
                                    <li>يتم توفير مصادر تعليمية مناسبة لكل المراحل الدراسية تتضمن:
                                        <ul>
                                            <li>الأنشطة الإثرائية كالبحوث والقراءات حول تاريخ الإمارات وشخصياتها البارزة</li>
                                            <li>تنظيم ورش عمل لربط الطلاب بالتراث الإماراتي</li>
                                            <li>تعزيز استخدام التطبيقات الرقمية لتعليم قيم الهوية الوطنية</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">أمثلة على تنفيذ محاور الهوية الوطنية:</span>
                                <ul class="mt-2">
                                    <li>في محور الثقافة - اللغة العربية: تفعيل دروس النحو والبلاغة، وتشجيع القراءة الحرة باستخدام كتب تدعم الهوية الإماراتية</li>
                                    <li>في محور الثقافة - التاريخ: إعداد مشروعات طلابية حول شخصيات قادة الإمارات، وإنتاج أعمال إبداعية كالرسم أو التمثيل المسرحي</li>
                                    <li>في محور المواطنة: حملات طلابية للتطوع وتنظيف البيئة المدرسية والمجتمعية</li>
                                </ul>
                            </li>

                            <li class="mt-2 fw-500"><span class="main-color fw-bold">ختاماً:</span> يمثل اختبار الهوية الوطنية خطوة محورية في تحقيق رؤية دولة الإمارات، حيث يدمج بين الأصالة والحداثة، ويعمل على إعداد أجيال تدرك أهمية القيم الوطنية وتمثلها في حياتها اليومية</li>
                        </ul>
                    </div>
                </div>
            </div>
            <span class="number-page">2</span>
        </div>
    @else
        <div class="page introduction-page">
            <div class="subpage-w">
                <div class="row text-center">
                    <h5 class="sub-color">National Identity Test: Promoting Values, Citizenship and Culture in the UAE</h5>
                </div>
                <div class="row mt-2 justify-content-center">
                    <div class="col-12">
                        <ul>
                            <li class="mt-1 fw-500"><span class="main-color fw-bold">ABT International Standard Testing</span> offers the National Identity Test, which aims to establish national identity among students of public and private schools in the UAE</li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">This test is offered</span> completely free of charge to all public and private schools that wish to apply the test, in appreciation of the UAE</li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">The test seeks</span> to promote the values of citizenship and raise the culture of students in line with the UAE's vision to build a generation aware of its originality and identity</li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">Test duration and technical requirements:</span>
                                <ul class="mt-2">
                                    <li>The test takes 40 minutes, served via browser "Google Chrome" To ensure the best technology experience for students</li>
                                    <li>The test is available in two versions:
                                        <ul>
                                            <li>Arabic Version: Dedicated to Arab Students</li>
                                            <li>English Version: Intended for non-Arabic speaking students</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">The main topics of the test:</span>
                                <ul class="mt-2">
                                    <li>The first topic: (Culture)
                                        <ul>
                                            <li>Sub-themes: Arabic, History, Heritage</li>
                                        </ul>
                                    </li>
                                    <li>The second aspect: (Values)
                                        <ul>
                                            <li>Sub-themes: Respect, empathy, global understanding</li>
                                        </ul>
                                    </li>
                                    <li>The third aspect: (Citizenship)
                                        <ul>
                                            <li>Sub-themes: Belonging, volunteering, preserving the environment</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">Domains:</span>
                                <ul class="mt-2">
                                    <li>Curriculum: How are curricula modified to merge sub-themes?</li>
                                    <li>Savings: What opportunities does the school provide to enhance students' knowledge and understanding?</li>
                                    <li>Impact: The depth of students' knowledge and understanding of the sub-themes</li>
                                </ul>
                            </li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">Additional requirements for implementation:</span>
                                <ul class="mt-2">
                                    <li>Innovative activities and applications that enhance the axes of national identity are included in the curriculum</li>
                                    <li>Appropriate educational resources are provided for all academic levels:
                                        <ul>
                                            <li>Enrichment activities such as research and readings on the history of the UAE and its prominent personalities</li>
                                            <li>Organizing workshops to connect students to Emirati heritage</li>
                                            <li>Promoting the use of digital applications to teach the values of national identity</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">Examples of implementation:</span>
                                <ul class="mt-2">
                                    <li>In the aspect of culture - Arabic Language: Activating grammar and rhetoric lessons, and encouraging free reading using books that support the Emirati identity</li>
                                    <li>In the aspect of culture - Date: Preparing student projects on the personalities of UAE leaders, and producing creative works such as painting or theatrical acting</li>
                                    <li>In the aspect of citizenship: Student campaigns to volunteer and clean the school and community environment</li>
                                </ul>
                            </li>

                            <li class="mt-1 fw-500"><span class="main-color fw-bold">Conclusion:</span> The national identity test represents a pivotal step in achieving the UAE's vision, as it merges tradition and modernity, and works to prepare generations to realize the importance of national values and represent them in their daily lives</li>
                        </ul>
                    </div>
                </div>
            </div>
            <span class="number-page">2</span>
        </div>
    @endif
    <div class="page">
        <div class="subpage-w">
            <div class="row justify-content-center  mt-2">
                <div class="col-10 text-center">
                    <h4 class="main-color">{{re('In this report, we analyse the below points')}}</h4>
                </div>
            </div>
            <div class="row justify-content-center  mt-4">
                <div class="col-11 text-center">
                    <img src="{{asset("reports/analysis_points_$lang.svg")}}" width="100%" alt="">
                </div>
            </div>


        </div>
        <span class="number-page">4</span>
    </div>

    @php
        $pageNum = 4;
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

        @foreach($pages['attainment'] as $grade => $gradeYears)
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

            @foreach($subjects->chunk(2) as $subjects_array)
               <div class="page">
                   <div class="subpage-w">
                       @foreach($subjects_array as $subject)
                           <div class="row text-center justify-content-center">
                               <div class="col-11 ">
                                   <div class="table-container">
                                       <table class="table small m-0">
                                           <thead>
                                           <tr>
                                               <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>
                                                   {{re('Assessment')}} ({{$subject->name}})
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
                                                   <td class="below-t-td">{{ $year_info->{"mark_step$subject->id"}->below }} {{re('Student')}}</td>
                                                   <td class="inline-t-td">{{ $year_info->{"mark_step$subject->id"}->inline }} {{re('Student')}}</td>
                                                   <td class="above-t-td">{{ $year_info->{"mark_step$subject->id"}->above }} {{re('Student')}}</td>
                                                   <td>{{ $year_info->{"mark_step$subject->id"}->total }}</td>
                                               </tr>
                                           @endforeach

                                           </tbody>
                                       </table>
                                   </div>
                               </div>
                           </div>
                           <div class="row text-center justify-content-center mt-2">
                               <div class="col-11">
                                   <div id="{{$pagesType['pages_type_text']}}_step_{{$subject->id}}_container_{{ $grade }}"></div>
                               </div>
                           </div>
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
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function () {
            const chartColors = {
                below: "#ef4444",
                inline: "#f59e0b",
                above: "#10b981"
            };

            @foreach($pages['attainment'] as $grade => $gradeYears)
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

            @foreach($subjects as $subject)
            @if($step)
            var chart = Highcharts.chart("{{$pagesType['pages_type_text']}}_step_{{$subject->id}}_container_{{ $grade }}", {
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
        });
    </script>
@endpush
