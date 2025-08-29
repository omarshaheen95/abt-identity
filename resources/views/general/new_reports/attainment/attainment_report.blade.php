@extends('general.new_reports.layout')
@push('style')

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

                <h2 class="position-absolute report-title mx-3 text-black" style="margin-top: 90px;!important;">
                    {!! $reportTitleGroup['ar'] !!}
                    <br><br>
                    {!! $reportTitleGroup['en'] !!}

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
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="{{ asset("reports/identity_domains_".app()->getLocale().".svg") }}?v=1" width="90%"/>
                </div>
            </div>
        </div>
        <span class="number-page">3</span>
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
                    <img src="{{asset("reports/analysis_points_$lang.svg")}}" width="100%" alt="">
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
                            <img src="{{asset(auth()->guard('inspection')->user()->image)}}" alt="">
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
                                    <td class="">{{auth()->guard('inspection')->user()->name}}</td>
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
                            <h5 class="section-title"> <i class="fas fa-chart-bar section-title-icon"></i>{!! $page->general_title !!}</h5>
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
                            <h5 class="section-title"> <i class="fas fa-chart-line section-title-icon"></i>{!! $page->title !!}</h5>
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
            inline: "#f5d60b",
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
