<!DOCTYPE html>
<html lang="{{app()->getlocale()}}" dir="{{app()->getLocale()=='ar'?'rtl':'ltr'}}">
@php
    $lang = app()->getLocale()=='ar'?'ar':'en';
@endphp
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="نظام اختبارات وتقييم للطلاب للهوية الوطنية Id.B.T – Identity Benchmark Test" name="description"/>
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
          href="{{!settingCache('logo_min')? asset('assets_v1/media/svg/logo-min.svg'):asset(settingCache('logo_min'))}}"/>
    <title>{{$title}}</title>
    <script src="{{ asset('assets_v1/plugins/print/js/new_highcharts.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/rounded-corners.js') }}"></script>
</head>
<body>
<div class="page">
    <div class="subpage-w">
        <div class="row align-items-center">
            <div class="col-6 justify-content-center text-left">
                <img src="{{asset('assets_v1/media/svg/abt-logo.svg')}}" width="75%"
                     alt="">

            </div>
            <div class="col-6  justify-content-center text-center">
                <img src="{{!settingCache('logo')? asset('assets_v1/media/svg/logo.svg?v1'):asset(settingCache('logo'))}}"
                     width="100%"
                     alt="">
            </div>
        </div>
        <div class="row text-center justify-content-center mt-5">
            <div class="col-11 mt-3">
                <h3 class="sub-color">The Attainment Combined Report</h3>
            </div>
            <div class="col-12">
                <h3 class="sub-color">تقرير التحصيل المجمع</h3>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <img src="{{asset('assets_v1/media/reports/attainment_report_1_page.svg')}}"
                     style="max-height: 350px; width: 50%" alt="">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12 text-center">
                <h4 class="main-color my-2">{{$school->name}} {{ count($sections) > 0 ? " - (".implode(',', $sections).")" . ' Sections':'' }}</h4>
                <h4 class="main-color my-2">{{$year->name}} </h4>
                <h5>{{t('Release Date')}} : {{now()->format('Y-m-d')}}</h5>
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
@php
    $pageNum = 3;
@endphp
    @if($arab_grades->total)
        <div class="page">
            <div class="subpage-w">
                <div class="row text-center justify-content-center">
                    <div class="col-11">
                        <h5 class="section-title">{{re('Attainment')}} {{ $arab_grades->title }}</h5>
                    </div>
                </div>
                <div class="row text-center justify-content-center">
                    <div class="col-11 ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Round')}}</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->rounds as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(!$include_sen && !$include_g_t)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the SEN and G & T students')}}</p>
                        @elseif(!$include_sen)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the SEN students')}}</p>
                        @elseif(!$include_g_t)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the G & T students')}}</p>
                        @endif
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11  mb-5">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Round')}}</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->rounds as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->below->percentage }} %</span>
                                        </td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->inline->percentage }} %</span>
                                        </td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->above->percentage }} %</span>
                                        </td>
                                        @php
                                            $rowData = judgement($round->below->percentage,$round->inline->percentage, $round->above->percentage);
                                        @endphp
                                        <td>
                                            {{ $round->total }} {{re('Students')}}
                                            / {{ $arab_grades->total }} {{re('Students')}}
                                            <br/>
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
                        <div id="arab_general_rounds"></div>
                    </div>
                </div>
            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
        @foreach($subjects->chunk(2)->values() as $skills)
            <div class="page">
                <div class="subpage-w">
                    @foreach($skills as $skill)
                        <div class="row text-center justify-content-center">
                            <div class="col-11 ">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"> {{re('Assessment')}} ({{$skill->name}})</th>
                                            <th class="below-td"> {{re('Below')}}</th>
                                            <th class="inline-td"> {{re('Inline')}}</th>
                                            <th class="above-td"> {{re('Above')}}</th>
                                            <th class="main-th"> {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($arab_grades->skills as $round => $skills_round)
                                            @foreach($skills_round as $skill_round)
                                                @if($skill_round->skill_id == $skill->id)
                                                    <tr class="text-center">
                                                        <td>{{ $round }}</td>
                                                        <td class="back-t">{{ $skill_round->below }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td class="back-a">{{ $skill_round->inline }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td class="back-m">{{ $skill_round->above }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td>{{ $skill_round->total }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div id="arab_skill_{{$skill->id}}_rounds"></div>
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
                                    <th class="main-th"> {{re('Assessment')}} ({{re('Boys')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->boys as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($arab_grades->boys as $round)
                        <div class="col-4">
                            <div id="arab_boys_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Assessment')}} ({{re('Girls')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->girls as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row text-center justify-content-center mt-2">
                    @foreach($arab_grades->girls as $round)
                        <div class="col-4">
                            <div id="arab_girls_{{strtolower($round->round)}}"></div>
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
                                    <th class="main-th"> {{re('Assessment')}} ({{re('SEN')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->sen as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($arab_grades->sen as $round)
                        <div class="col-4">
                            <div id="arab_sen_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11 ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Assessment')}} ({{re('G&T')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->g_t as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($arab_grades->g_t as $round)
                        <div class="col-4">
                            <div id="arab_g_t_{{strtolower($round->round)}}"></div>
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
                                    <th class="main-th"> {{re('Assessment')}} ({{re('Citizen')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arab_grades->local as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($arab_grades->local as $round)
                        <div class="col-4">
                            <div id="arab_local_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>

            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
    @endif
    @if($non_arab_grades->total)
        <div class="page">
            <div class="subpage-w">
                <div class="row text-center justify-content-center">
                    <div class="col-11">
                        <h5 class="section-title">{{re('Attainment')}} {{ $non_arab_grades->title }}</h5>
                    </div>
                </div>
                <div class="row text-center justify-content-center">
                    <div class="col-11 ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Round')}}</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->rounds as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(!$include_sen && !$include_g_t)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the SEN and G & T students')}}</p>
                        @elseif(!$include_sen)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the SEN students')}}</p>
                        @elseif(!$include_g_t)
                            <p class="text-danger fw-bold text-left">{{re('This report does NOT include the G & T students')}}</p>
                        @endif
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11  mb-5">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Round')}}</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->rounds as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->below->percentage }} %</span>
                                        </td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->inline->percentage }} %</span>
                                        </td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Students')}}
                                            <br/>
                                            <span class="text-danger">{{ $round->above->percentage }} %</span>
                                        </td>
                                        @php
                                            $rowData = judgement($round->below->percentage,$round->inline->percentage, $round->above->percentage);
                                        @endphp
                                        <td>
                                            {{ $round->total }} {{re('Students')}}
                                            / {{ $non_arab_grades->total }} {{re('Students')}}
                                            <br/>
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
                        <div id="non_arab_general_rounds"></div>
                    </div>
                </div>
            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
        @foreach($subjects->chunk(2)->values() as $skills)
            <div class="page">
                <div class="subpage-w">
                    @foreach($skills as $skill)
                        <div class="row text-center justify-content-center">
                            <div class="col-11 ">
                                <div class="table-container">
                                    <table class="table small m-0">
                                        <thead>
                                        <tr>
                                            <th class="main-th"> {{re('Assessment')}} ({{$skill->name}})</th>
                                            <th class="below-td"> {{re('Below')}}</th>
                                            <th class="inline-td"> {{re('Inline')}}</th>
                                            <th class="above-td"> {{re('Above')}}</th>
                                            <th class="main-th"> {{re('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($non_arab_grades->skills as $round => $skills_round)
                                            @foreach($skills_round as $skill_round)
                                                @if($skill_round->skill_id == $skill->id)
                                                    <tr class="text-center">
                                                        <td>{{ $round }}</td>
                                                        <td class="back-t">{{ $skill_round->below }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td class="back-a">{{ $skill_round->inline }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td class="back-m">{{ $skill_round->above }}
                                                            {{re('Student')}}
                                                        </td>
                                                        <td>{{ $skill_round->total }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center justify-content-center mt-2">
                            <div class="col-11">
                                <div id="non_arab_skill_{{$skill->id}}_rounds"></div>
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
                                    <th class="main-th"> {{re('Assessment')}} ({{re('Boys')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->boys as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($non_arab_grades->boys as $round)
                        <div class="col-4">
                            <div id="non_arab_boys_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Assessment')}} ({{re('Girls')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->girls as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row text-center justify-content-center mt-2">
                    @foreach($non_arab_grades->girls as $round)
                        <div class="col-4">
                            <div id="non_arab_girls_{{strtolower($round->round)}}"></div>
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
                                    <th class="main-th"> {{re('Assessment')}} ({{re('SEN')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->sen as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($non_arab_grades->sen as $round)
                        <div class="col-4">
                            <div id="non_arab_sen_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="row text-center justify-content-center mt-5">
                    <div class="col-11 ">
                        <div class="table-container">
                            <table class="table small m-0">
                                <thead>
                                <tr>
                                    <th class="main-th"> {{re('Assessment')}} ({{re('G&T')}})</th>
                                    <th class="below-td"> {{re('Below')}}</th>
                                    <th class="inline-td"> {{re('Inline')}}</th>
                                    <th class="above-td"> {{re('Above')}}</th>
                                    <th class="main-th"> {{re('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($non_arab_grades->g_t as $round)
                                    <tr class="text-center">
                                        <td>{{ $round->round }}</td>
                                        <td class="back-t">{{ $round->below->count }} {{re('Student')}}</td>
                                        <td class="back-a">{{ $round->inline->count }} {{re('Student')}}</td>
                                        <td class="back-m">{{ $round->above->count }} {{re('Student')}}</td>
                                        <td>{{ $round->total }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row text-center justify-content-center mt-3">
                    @foreach($non_arab_grades->g_t as $round)
                        <div class="col-4">
                            <div id="non_arab_g_t_{{strtolower($round->round)}}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <span class="number-page">{{$pageNum++}}</span>
        </div>
    @endif


<script src="{{ asset('assets_v1/plugins/global/jquery.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
<script type="text/javascript">
    @yield('script')
</script>

<script type="text/javascript">
    $(document).ready(function () {
        Highcharts.setOptions({
            colors: ['#d50000', '#ffc107', '#00c853', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
        });
        @if($non_arab_grades->total)
        Highcharts.chart("non_arab_general_rounds", {
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
                    @foreach($non_arab_grades->rounds as $round)
                        "{{$round->round}}",
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
                data: [@foreach($non_arab_grades->rounds as $round)
                    {{ $round->below->percentage}},
                    @endforeach ],
                color: "#EA5455"

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($non_arab_grades->rounds as $round)
                    {{ $round->inline->percentage }},
                    @endforeach],
                color: "#F0DE36"

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($non_arab_grades->rounds as $round)
                    {{ $round->above->percentage}},
                    @endforeach ],
                color: "#28C76F"
            }]
        });

        @foreach($subjects as $skill)
        var chart = Highcharts.chart("non_arab_skill_{{$skill->id}}_rounds", {
            chart: {
                type: 'bar',
            },
            title: {
                text: '{{re($skill->name)}}'
            },
            subtitle: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($non_arab_grades->skills as $round => $skills_round)
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
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [{
                name: '{{re('Below expecting')}}',
                data: [@foreach($non_arab_grades->skills as $round => $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->below_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#EA5455"

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($non_arab_grades->skills as $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->inline_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#F0DE36"

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($non_arab_grades->skills as $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->above_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#28C76F"

            }]
        });
        chart.setSize(null, 350);
        @endforeach


        @foreach($non_arab_grades->boys as $round)
        var chart = Highcharts.chart('non_arab_boys_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @foreach($non_arab_grades->girls as $round)
        var chart = Highcharts.chart('non_arab_girls_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @foreach($non_arab_grades->sen as $round)
        var chart = Highcharts.chart('non_arab_sen_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach
        @foreach($non_arab_grades->g_t as $round)
        var chart = Highcharts.chart('non_arab_g_t_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @endif
        @if($arab_grades->total)
        Highcharts.chart("arab_general_rounds", {
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
                    @foreach($arab_grades->rounds as $round)
                        "{{$round->round}}",
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
                data: [@foreach($arab_grades->rounds as $round)
                    {{ $round->below->percentage}},
                    @endforeach ],
                color: "#EA5455"

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($arab_grades->rounds as $round)
                    {{ $round->inline->percentage }},
                    @endforeach],
                color: "#F0DE36"

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($arab_grades->rounds as $round)
                    {{ $round->above->percentage}},
                    @endforeach ],
                color: "#28C76F"
            }]
        });

        @foreach($subjects as $skill)
        var chart = Highcharts.chart("arab_skill_{{$skill->id}}_rounds", {
            chart: {
                type: 'bar',
            },
            title: {
                text: '{{re($skill->name)}}'
            },
            subtitle: {
                text: ' '
            },
            xAxis: {
                categories: [
                    @foreach($arab_grades->skills as $round => $skills_round)
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
                    },
                    borderRadiusTopLeft: '20%',
                    borderRadiusTopRight: '20%'
                }
            },
            series: [{
                name: '{{re('Below expecting')}}',
                data: [@foreach($arab_grades->skills as $round => $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->below_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#EA5455"

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [@foreach($arab_grades->skills as $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->inline_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#F0DE36"

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [@foreach($arab_grades->skills as $skills_round)
                    @foreach($skills_round as $skill_round)
                    @if($skill_round->skill_id == $skill->id)
                    {{ $skill_round->above_percentage }},
                    @endif
                    @endforeach
                    @endforeach],
                color: "#28C76F"

            }]
        });
        chart.setSize(null, 350);
        @endforeach


        @foreach($arab_grades->boys as $round)
        var chart = Highcharts.chart('arab_boys_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @foreach($arab_grades->girls as $round)
        var chart = Highcharts.chart('arab_girls_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @foreach($arab_grades->sen as $round)
        var chart = Highcharts.chart('arab_sen_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach
        @foreach($arab_grades->local as $round)
        var chart = Highcharts.chart('arab_local_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach
        @foreach($arab_grades->g_t as $round)
        var chart = Highcharts.chart('arab_g_t_{{strtolower($round->round)}}', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '{{re($round->round)}}',
                align: 'center',
                verticalAlign: 'middle',
                y: 85,
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
                    {name: '{{re('below')}}', y: {{$round->below->percentage}}, color: "#EA5455"},
                    {name: '{{re('inline')}}', y: {{$round->inline->percentage}}, color: "#F0DE36"},
                    {name: '{{re('above')}}', y: {{$round->above->percentage}}, color: "#28C76F"},
                ]
            }]
        });
        chart.setSize(null, 250);
        @endforeach

        @endif
    });
</script>

</body>
</html>
