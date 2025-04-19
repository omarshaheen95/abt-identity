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
        <link href="{{ asset('assets_v1/plugins/print/css/print.rtl.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.rtl.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{ asset('assets_v1/plugins/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/print.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @endif

    <link rel="shortcut icon"
          href="{{!settingCache('logo_min')? asset('assets_v1/media/svg/logo-min.svg'):asset(settingCache('logo_min'))}}"/>
    <title>{{re('Student Report')}} : {{$student->name}}</title>
    <script src="{{ asset('assets_v1/plugins/print/js/new_highcharts.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/rounded-corners.js') }}"></script>
    <style>
        .report-date {
            top: 64% !important;
            left: 27% !important;
        }

        .student-name {
            top: 50% !important;
            left: 20% !important;
            color: #EC2028
        }
    </style>
</head>
<body>
<div class="page p-0">
    <div class="subpage-w">
        <div class="position-relative">
            <img src="{{ asset("assets_v1/media/reports/covers/student_".$lang.".svg") }}?v1" class="w-100" alt="">
            <h2 class="position-absolute student-name m-3 text-black w-50 text-center fw-bold">
                {{ $student->name }}
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
        <div class="row justify-content-center">
            <div class="col-6 text-center">
                <div class="image-container">
                    <img src="{{asset($student->school->logo)}}" alt="">
                </div>
            </div>
        </div>
        <div class="row mt-4 justify-content-center">
            <div class="col-10 text-center">
                <div class="table-container">
                    <table class="table m-0">
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Student Name')}}</td>
                            <td class="">{{$student->name}}</td>
                        </tr>
                        <tr>
                            <td class="main-td w-25 py-2">{{re('Username')}}</td>
                            <td class="">{{$student->email}}</td>
                        </tr>
                        <tr>
                            <td class="main-td py-2">{{re('Nationality')}}</td>
                            <td class="">{{$student->nationality}}</td>
                        </tr>
                        <tr>
                            <td class="main-td py-2">{{re('School')}}</td>
                            <td class="">{{$student->school->name}}</td>
                        </tr>
                        <tr>
                            <td class="main-td py-2">{{re('Level')}}</td>
                            <td class="">{{$student->level->name}}</td>
                        </tr>
                        <tr>
                            <td class="main-td py-2">{{re('Section')}}</td>
                            <td class="">{{$student->grade_name}}</td>
                        </tr>
                        <tr>
                            <td class="main-td py-2">{{re('Gender')}}</td>
                            <td class="">{{re($student->gender)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <span class="number-page">3</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{ asset("assets_v1/media/reports/identity_domains.svg") }}?v=2" width="90%"/>
            </div>
        </div>
    </div>
    <span class="number-page">4</span>
</div>


<div class="page">
    <div class="subpage-w">
        <div class="row text-center justify-content-center">
            <div class="col-11">
                <h5 class="section-title">{{re('The Skills Results')}}</h5>
            </div>
        </div>
        <div class="row mb-5 mt-3">
            <div id="container" style="height: 380px"></div>
        </div>
        <div class="row text-center justify-content-center mt-5">
            <div class="col-12 ">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th"> {{re('The assessment')}}</th>
                                <th class="main-th"> {{re('Culture')}}</th>
                                <th class="main-th"> {{re('Values')}}</th>
                                <th class="main-th"> {{re('Citizenship')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data_term as $d_term)
                            <tr>
                                <td> {{ $d_term->term }} </td>
                                    <td class=""> {{ $d_term->skill1->mark }} / 30</td>
                                    <td class=""> {{ $d_term->skill2->mark }} / 30</td>
                                    <td class=""> {{ $d_term->skill3->mark }} / 40</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <span class="number-page">5</span>
</div>
<div class="page">
    <div class="subpage-w">
        <div class="row text-center justify-content-center">
            <div class="col-11">
                <h5 class="section-title">{{re('Overall Results')}}</h5>
            </div>
        </div>
        <div class="row mb-5 mt-3">
            <div id="container2" style="height: 380px"></div>
        </div>
        <div class="row mt-5 text-center justify-content-center">
            <div class="col-12 ">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{re('The assessment')}}</th>
                            <th class="main-th"> {{re('Mark')}}</th>
                            <th class="main-th"> {{re('Attainment')}}</th>
                            <th class="main-th"> {{re('Progress')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($full_data_term as $d_term)
                            <tr>
                                <td> {{ re(ucfirst($d_term->round_name)) }} {{re('Round')}}</td>
                                <td class=""> {{ $d_term->total }} / 100</td>
                                <td><span
                                        class="{{strtolower($d_term->expectation)}}-badge">{{ re($d_term->expectation) }}</span>
                                </td>
                                <td class=""><span
                                        class="{{ strtolower($d_term->progress_class) }}-badge">{{ re($d_term->progress) }}</span>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <span class="number-page">6</span>
</div>
@php
    $pageCounter = 7;
@endphp
@foreach($terms as $term)
        <div class="page">
            <div class="subpage-w">
                <div class="row text-center justify-content-center">
                    <div class="col-12">
                        <h5 class="section-title">{{re('The assessment Outcomes & Moderation (Culture)')}}</h5>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="table-container">
                            <table class="table table-standards">
                                <thead>
                                <tr>

                                    <th class="main-th standard" style="width:40px;"> {{re('Question Number')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Question score')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Student score')}}</th>
                                    <th class="main-th standard"> {{re('The assessment Outcomes')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Internal')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('External')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($standards as $standard)
                                    @if($standard->question->term_id == $term->term_id && $standard->question->subject_id == 1)
                                        <tr>
                                            <td class="standard">{{ $loop->iteration }}</td>
                                            <td class="standard">{{ $standard->mark }}</td>
                                            <td class="standard">{{ optional($standard->studentTermStandards->first())->mark ?? 0 }}</td>
                                            <td class="standard">{{ $standard->standard }}</td>
                                            <td class="standard">{{ $standard->student_in_level }} %</td>
                                            <td class="standard">{{$standard->student_in_system}} %</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br />
                <br />
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-12">
                        <h5 class="section-title">{{re('The assessment Outcomes & Moderation (Values)')}}</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-container">
                            <table class="table table-standards">
                                <thead>
                                <tr>

                                    <th class="main-th standard" style="width:40px;"> {{re('Question Number')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Question score')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Student score')}}</th>
                                    <th class="main-th standard"> {{re('The assessment Outcomes')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('Internal')}}</th>
                                    <th class="main-th standard" style="width:40px;"> {{re('External')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($standards as $standard)
                                    @if($standard->question->term_id == $term->term_id && $standard->question->subject_id == 2)
                                        <tr>
                                            <td class="standard">{{ $loop->iteration }}</td>
                                            <td class="standard">{{ $standard->mark }}</td>
                                            <td class="standard">{{ optional($standard->studentTermStandards->first())->mark ?? 0 }}</td>
                                            <td class="standard">{{ $standard->standard }}</td>
                                            <td class="standard">{{ $standard->student_in_level }} %</td>
                                            <td class="standard">{{$standard->student_in_system}} %</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <span class="number-page">{{$pageCounter++}}</span>
        </div>
        <div class="page">
            <div class="subpage-w">
                    <div class="row text-center justify-content-center">
                        <div class="col-12">
                            <h5 class="section-title">{{re('The assessment Outcomes & Moderation (Citizenship)')}}</h5>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="table-container">
                                <table class="table table-standards">
                                    <thead>
                                    <tr>

                                        <th class="main-th standard" style="width:40px;"> {{re('Question Number')}}</th>
                                        <th class="main-th standard" style="width:40px;"> {{re('Question score')}}</th>
                                        <th class="main-th standard" style="width:40px;"> {{re('Student score')}}</th>
                                        <th class="main-th standard"> {{re('The assessment Outcomes')}}</th>
                                        <th class="main-th standard" style="width:40px;"> {{re('Internal')}}</th>
                                        <th class="main-th standard" style="width:40px;"> {{re('External')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($standards as $standard)
                                        @if($standard->question->term_id == $term->term_id && $standard->question->subject_id == 3)
                                            <tr>
                                                <td class="standard">{{ $loop->iteration }}</td>
                                                <td class="standard">{{ $standard->mark }}</td>
                                                <td class="standard">{{ optional($standard->studentTermStandards->first())->mark ?? 0 }}</td>
                                                <td class="standard">{{ $standard->standard }}</td>
                                                <td class="standard">{{ $standard->student_in_level }} %</td>
                                                <td class="standard">{{$standard->student_in_system}} %</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
            <span class="number-page">{{$pageCounter++}}</span>
        </div>
@endforeach

<script src="{{ asset('assets_v1/plugins/global/jquery.min.js') }}" type="text/javascript"></script>
<script>
    $(function () {
        var colors = ['#3A98B9', '#BFBFBF', '#FF953A', '#FFC107'];

        Highcharts.setOptions({
            colors: ['#3A98B9', '#BFBFBF', '#FF953A', '#FFC107', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
        });
        var chart = new Highcharts.Chart({

            chart: {
                renderTo: 'container',
                type: 'column'
            },

            xAxis: {
                categories: [
                        '{{re('Culture')}}',
                        '{{re('Values')}} ',
                        '{{re('Citizenship')}}',
                ]@if(app()->getLocale()=='ar').reverse()@endif,
            },

            yAxis: {
                gridLineDashStyle: 'LongDash',
                title: {
                    text: ' ',
                    useHTML: Highcharts.hasBidiBug
                },
                tickInterval: 5,
                min: 0,
                max: 50,

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
                        format: '{point.y}',
                        color: '#000'
                    },
                    borderRadiusTopLeft: '15%',
                    borderRadiusTopRight: '15%'
                }
            },

            title: {
                text: ' ',
                useHTML: Highcharts.hasBidiBug
            },

            legend: {
                useHTML: Highcharts.hasBidiBug,
                itemMarginTop: 8,
            },

            tooltip: {
                useHTML: true
            },

            series: [
                    @foreach($full_data_term as $term_data)
                {
                    name: '{{ $term_data->term }}',
                    data: [
                            {{ $term_data->mark_step1->mark }},
                            {{ $term_data->mark_step2->mark }},
                            {{ $term_data->mark_step3->mark }},
                    ]@if(app()->getLocale()=='ar').reverse()@endif,
                },
                @endforeach
            ]@if(app()->getLocale()=='ar').reverse()@endif

        });
        var chart2 = new Highcharts.Chart({

            chart: {
                renderTo: 'container2',
                type: 'column'
            },

            xAxis: {
                categories: ['{{re('Total Exams Results')}}']

            },

            yAxis: {
                title: {
                    text: ' ',
                    useHTML: Highcharts.hasBidiBug
                },
                tickInterval: 20,
                gridLineDashStyle: 'LongDash',
                min: 0,
                max: 100,

            },

            plotOptions: {
                column: {
                    pointWidth: 60,
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
                    },
                    borderRadiusTopLeft: '10%',
                    borderRadiusTopRight: '10%'
                }
            },
            title: {
                text: ' ',
                useHTML: Highcharts.hasBidiBug
            },

            legend: {
                useHTML: Highcharts.hasBidiBug,
                itemMarginTop: 8,
            },

            tooltip: {
                useHTML: true
            },

            series: [
                    @foreach($full_data_term as $term_data)
                {
                    name: '{{ $term_data->term }}',
                    data: [
                        {{ $term_data->total }},
                    ]
                },
                @endforeach
            ]@if(app()->getLocale()=='ar').reverse()@endif


        });
    });
</script>

</body>
</html>
