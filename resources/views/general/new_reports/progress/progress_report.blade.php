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
            color: #EC2028;
            width: 485px;
            font-size:2.2rem;
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
            color: #EC2028;
            width: 485px;
            font-size:2.2rem;
        }

        @endif
        .std_title{
            font-size: 12px;
        }
        .report-ranges {
            color: #E37425;
            font-weight: 500;
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
                <img src="{{ asset("reports/covers/progress_".$lang.".svg").'?v=1' }}"
                     class="w-100" alt="">


                <h2 class="position-absolute report-title mx-3 text-black" style="margin-top: 50px;!important;">
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
                    <img src="{{ asset("reports/progress_ranges_".app()->getLocale().".svg") }}" width="80%"/>
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
                                <td class="">{{re($report_info['student_type'])}}</td>
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
                        <h5 class="section-title"> <i class="fas fa-chart-bar section-title-icon me-2"></i>{!! $page->general_title !!}</h5>
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
                        <h5 class="section-title"><i class="fas fa-chart-line section-title-icon me-2"></i>{!! $page->title !!}</h5>
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
                                        <th class="main-th"><i class="fas fa-flag me-1"></i> {{re(sysNationality())}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
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
                                        <th class="main-th"><i class="fas fa-male me-1"></i> {{re(sysNationality())}} {{re('Boys')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
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
                                        <th class="main-th"><i class="fas fa-female me-1"></i> {{re(sysNationality())}} {{re('Girls')}} <br /><i class="fas fa-calendar-alt me-1"></i></th>
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
                inline: "#f5d60b",
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
