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
        <link href="{{ asset('assets_v1/plugins/print/css/print.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @endif

    <link rel="shortcut icon"
          href="{{!settingCache('logo_min')? asset('assets_v1/media/svg/logo-min.svg'):asset(settingCache('logo_min'))}}"/>
    <title>{{re('The School Progress Report')}} - {{$school->name}}</title>
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
                <img src="{{!settingCache('logo')? asset('assets_v1/media/svg/Identity Logo.svg?v1'):asset(settingCache('logo'))}}"
                     width="100%"
                     alt="">
            </div>
        </div>
        <div class="row text-center justify-content-center mt-5">
            <div class="col-11 mt-3">
                <h3 class="sub-color">The Attainment Report</h3>
            </div>
            <div class="col-12">
                <h3 class="sub-color">تقرير التحصيل</h3>
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
                <h4 class="main-color my-2">{{$school->name}}</h4>
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
<div class="page">
    <div class="subpage-w">
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{ asset("assets_v1/media/reports/progress_ranges_".app()->getLocale().".svg") }}" width="80%"/>
            </div>
        </div>
    </div>
    <span class="number-page">3</span>
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
                <h4 class="main-color">{{re('In this report, we analyse the below points')}}</h4>
            </div>
        </div>
        <div class="row justify-content-center  mt-4">
            <div class="col-11 text-center">
                <img src="{{asset("assets_v1/media/reports/analysis_points_$lang.svg")}}" width="100%" alt="">
            </div>
        </div>


    </div>
    <span class="number-page">4</span>
</div>


@php
    $pageNum = 5;
@endphp

@foreach($arab_pages as $key => $arab_page)
    <div class="page">
        <div class="subpage-w">
            <div class="row text-center justify-content-center">
                <div class="col-12">
                    <h5 class="section-title">{{$arab_page->title}}</h5>
                </div>
            </div>
            <div class="row text-center justify-content-center my-5">
                <div class="col-12  mb-5">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Rounds')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <td class="" > {{ $arab_page->septProgress['name'] }}</td>
                                <td class="">{{ $arab_page->septProgress['below'] }}
                                    {{re('Student')}} = {{ $arab_page->septProgress['below_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->septProgress['inline'] }}
                                    {{re('Student')}} = {{ $arab_page->septProgress['inline_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->septProgress['above'] }}
                                    {{re('Student')}} = {{ $arab_page->septProgress['above_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->septProgress['total'] }}
                                </td>
                                @php
                                    $data = judgement($arab_page->septProgress['below_ratio'],$arab_page->septProgress['inline_ratio'],$arab_page->septProgress['above_ratio']);
                                @endphp
                                <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                            </tr>
                            <tr class="text-center">
                                <td class="" > {{ $arab_page->febProgressData['name'] }}</td>
                                <td class="">{{ $arab_page->febProgressData['below'] }}
                                    {{re('Student')}} = {{ $arab_page->febProgressData['below_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->febProgressData['inline'] }}
                                    {{re('Student')}} = {{ $arab_page->febProgressData['inline_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->febProgressData['above'] }}
                                    {{re('Student')}} = {{ $arab_page->febProgressData['above_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->febProgressData['total'] }}
                                </td>
                                @php
                                    $data = judgement($arab_page->febProgressData['below_ratio'],$arab_page->febProgressData['inline_ratio'],$arab_page->febProgressData['above_ratio']);
                                @endphp
                                <td > <span class="{{str_replace(' ', '-', strtolower($data['level']))}}-badge">{{$data['level']}}</span></td>
                            </tr>
                            <tr class="text-center">
                                <td class="" > {{ $arab_page->mayProgressData['name'] }}</td>
                                <td class="">{{ $arab_page->mayProgressData['below'] }}
                                    {{re('Student')}} = {{ $arab_page->mayProgressData['below_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->mayProgressData['inline'] }}
                                    {{re('Student')}} = {{ $arab_page->mayProgressData['inline_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->mayProgressData['above'] }}
                                    {{re('Student')}} = {{ $arab_page->mayProgressData['above_ratio'] }}%
                                </td>
                                <td class="">{{ $arab_page->mayProgressData['total'] }}
                                </td>
                                @php
                                    $data = judgement($arab_page->mayProgressData['below_ratio'],$arab_page->mayProgressData['inline_ratio'],$arab_page->mayProgressData['above_ratio']);
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
                    <div id="arab_progress_{{$key}}"></div>
                </div>
            </div>
        </div>
        <span class="number-page">{{$pageNum++}}</span>
    </div>
    <div class="page">
        <div class="subpage-w">
            <div class="row text-center justify-content-center">
                <div class="col-12">
                    <h5 class="section-title">{{$arab_page->summary_title}} - {{$year->year}}</h5>
                </div>
            </div>
            <div class="row text-center justify-content-center mt-3">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Boys')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->maleProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12  ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Girls')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->femaleProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12  ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('SEN')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->senProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Emarati')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->uaeProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Emarati')}} {{re('Boys')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->uaeMaleProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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
            <div class="row text-center justify-content-center mt-5">
                <div class="col-12 ">
                    <div class="table-container">
                        <table class="table small m-0">
                            <thead>
                            <tr>
                                <th class="main-th"> {{re('Emarati')}} {{re('Girls')}}</th>
                                <th class="below-td"> {{re('Below expected')}}</th>
                                <th class="inline-td"> {{re('Expected progress')}}</th>
                                <th class="above-td"> {{re('Better than expected')}}</th>
                                <th class="main-th"> {{re('Total')}}</th>
                                <th class="main-th"> {{re('Judgement')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arab_page->uaeFemaleProgressData as $key => $round)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $round['below'] }} {{re('Student')}} = {{ $round['below_ratio'] }}%</td>
                                    <td>{{ $round['inline'] }} {{re('Student')}} = {{ $round['inline_ratio'] }}%</td>
                                    <td>{{ $round['above'] }} {{re('Student')}} = {{ $round['above_ratio'] }}%</td>
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

<script src="{{ asset('assets_v1/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets_panel/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->


<script type="text/javascript">
    $(document).ready(function () {
        @foreach($arab_pages as $key => $arab_page)
        var chart = Highcharts.chart("arab_progress_{{$key}}", {
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
                    @foreach($steps as $round)
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
                    {{$arab_page->septProgress['below_ratio']}},
                    {{$arab_page->febProgressData['below_ratio']}},
                    {{$arab_page->mayProgressData['below_ratio']}},
                ],
                color: "#EA5455"

            }, {
                name: '{{re('In line with curriculum expectations')}} ',
                data: [
                    {{$arab_page->septProgress['inline_ratio']}},
                    {{$arab_page->febProgressData['inline_ratio']}},
                    {{$arab_page->mayProgressData['inline_ratio']}},
                ],
                color: "#F0DE36"

            }, {
                name: '{{re('Above curriculum expectations')}}',
                data: [
                    {{$arab_page->septProgress['above_ratio']}},
                    {{$arab_page->febProgressData['above_ratio']}},
                    {{$arab_page->mayProgressData['above_ratio']}},
                ],
                color: "#28C76F"

            }]
        });
        @endforeach

    });
</script>

</body>
</html>
