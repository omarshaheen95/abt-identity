@extends('student.layout.container')
@section('title')
    {{$term->name}}
@endsection


@section('navbar')
    <nav class="navbard-top">
        <div class="container">
            <div class="navbar-container">
                <a href="#!" class="back-card">
                    <span class="text ms-2">{{$term->name}}</span>
                </a>

                <a href="#!" class="leave-exam">
                    <img src="{{asset('web_assets/img/leave.svg')}}" alt="">
                    <span class="text ms-2">{{$term->level->arab?'مغادرة الإختبار':'Leave Assessment'}}</span>
                </a>
            </div>
        </div>
    </nav>
@endsection

@section('style')
    <link href="{{asset('web_assets/css/custom2.css')}}?v{{time()}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/exam_questions.css')}}?v{{time()}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/arabic-keyboard.css')}}?v={{time()}}">
    <style>
        .exam-details li{
            font-size: 16px;
        }
        .q-number{
            width: 35px;
            height: 35px;
        }
        .question-content .content{
            font-size: 22px;
        }
        #keyboardInputLayout {
            direction: ltr !important;
        }

        #keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td {
            font: normal 30px 'Lucida Console', monospace;
        }

        .keyboardInputInitiator {
            width: 50px
        }

        /* Cache indicator styles */
        #cache-indicator {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0,118,164,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            display: none;
            transition: opacity 0.3s ease;
        }

        /* Recovery notice styles */
        .recovery-notice {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 4px;
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            border-right: none;
            font-size: 14px;
        }

        /* RTL support for recovery notice */
        [dir="rtl"] .recovery-notice {
            border-left: none;
            border-right: 4px solid #2196f3;
        }

        .recovery-notice p {
            margin-bottom: 0;
        }

        .recovery-notice strong {
            color: #0d47a1;
        }

        .alert.alert-info {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #0d47a1;
        }

        .alert.alert-info i {
            color: #1976d2;
            margin-right: 5px;
            margin-left: 0;
        }

        /* RTL support for alert icon */
        [dir="rtl"] .alert.alert-info i {
            margin-right: 0;
            margin-left: 5px;
        }

        /* Emergency save indicator */
        #emergency-save-indicator {
            position: fixed;
            bottom: 50px;
            right: 10px;
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            display: none;
        }
    </style>

@endsection

@section('content')
    <section class="exam-view">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-card">
                        <div class="info" dir="rtl">
                            <div class="exam-details ar">
                                <ul>
                                    <li>  عدد الأسئلة {{$questions_count}}  .</li>
                                    <li> يجب الإجابة على جميع الأسئلة. </li>
                                    <li> .مجموعة الدرجات {{$marks}} درجة</li>
                                    <li> سيتم حفط الإجابات بشكل تلقائي على المتصفح</li>

                                </ul>
                            </div>
                            <div class="exam-timer">
                                <div class="countdown mb-3">
                                    <div class="icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.75 13.25C20.75 14.9806 20.2368 16.6723 19.2754 18.1112C18.3139 19.5502 16.9473 20.6717 15.3485 21.3339C13.7496 21.9962 11.9903 22.1695 10.293 21.8319C8.59563 21.4943 7.03653 20.6609 5.81282 19.4372C4.58911 18.2135 3.75575 16.6544 3.41813 14.957C3.08051 13.2597 3.25379 11.5004 3.91606 9.90152C4.57832 8.30267 5.69983 6.9361 7.13876 5.97464C8.57769 5.01318 10.2694 4.5 12 4.5C14.3204 4.5008 16.5455 5.42292 18.1863 7.06369C19.8271 8.70446 20.7492 10.9296 20.75 13.25V13.25Z" stroke="#0076A4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M12 8V13" stroke="#0076A4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M9 2H15" stroke="#0076A4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div id="clock">00:59:49</div>
                                    <input type="hidden" id="timer-ago" value="00:59:49">
                                </div>

                            </div>
                            <div class="exam-details en">
                                <ul>
                                    <li> No. of questions is {{$questions_count}}. </li>
                                    <li> All questions must be answered . </li>
                                    <li> The total marks is {{$marks}}. </li>
                                    <li> Your answers are automatically saved in your browser. </li>

                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <form id="exams" action="{{route('student.term-save', ['id'=>$term->id])}}" data-term-id="{{$term->id}}" data-student-id="{{$student->id}}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="started_at" value="{{now()}}">
                @csrf
                @if(app()->getLocale()=='en')
                    <div class="recovery-notice mb-4" dir="ltr">
                        <p><strong>Note:</strong> Your answers are automatically saved in your browser. You can continue later from where you left off.</p>
                        <p class="mb-0"><small>For file uploads , you will need to upload again if you leave the assessment.</small></p>
                        <p class="mb-0"><small><strong>Emergency Save:</strong> Press to save your assessment without validation (use only if experiencing issues).</small> <a class="fw-bold" style="cursor: pointer; color:#ff0404" onclick=" $('#emergency-save-modal').modal('show')">( 👆 Press Here Emergency Save )</a></p>
                    </div>
                @else
                    <div class="recovery-notice mb-4" dir="rtl">
                        <p><strong>ملاحظة:</strong> سيتم حفظ إجاباتك تلقائيًا في متصفحك. يمكنك المتابعة لاحقًا من حيث توقفت.</p>
                        <p class="mb-0"><small>بالنسبة لرفع الملفات، ستحتاج إلى رفعها مرة أخرى إذا غادرت الاختبار.</small></p>
                        <p class="mb-0"><small><strong>حفظ طارئ: </strong> لحفظ اختبارك من دون  التحقق (استخدمه فقط في حالة مواجهة مشكلات).</small> <a class="fw-bold" style="cursor: pointer; color:#ff0404" onclick=" $('#emergency-save-modal').modal('show')">( 👆انقر هنا للحفظ الطارئ )</a></p>
                    </div>
                @endif
                <div class="mb-5">
                    <div class="">
                        <ul class="nav d-flex gap-4 justify-content-center">
                            @foreach($subjects as $subject)
                                <li class="nav-item">
                                    <a id="btn_nav_{{$subject->id}}" class="btn_nav btn_nav_active d-flex align-items-center justify-content-center {{$loop->index==0?'active':''}}"
                                       data-bs-toggle="tab" href="#kt_tab_pane_{{$subject->id}}">{{t($subject->name,[],$term->level->arab?'ar':'en')}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="tab-content" id="myTabContent">
                    @foreach($subjects as $subject)
                        <div class="tab-pane fade {{$loop->index==0?'show active':''}}" id="kt_tab_pane_{{$subject->id}}" role="tabpanel">

                            @isset($questions[$subject->id])
                                @foreach($questions[$subject->id] as $type => $questions_by_type)
                                        <div class="questions-group-card">
                                            <div class="d-flex justify-content-center">

                                                @switch($type)
                                                    @case('true_false')
                                                        <div class="head">{{$term->level->arab != 1 ? 'Put a (✔) for the correct statement and a (✘) for the incorrect one:' : 'ضَعْ عَلَامَةَ (✔) أَمَامَ ٱلْعِبَارَةِ ٱلصَّحِيحَةِ، وَعَلَامَةَ (✘) أَمَامَ ٱلْعِبَارَةِ ٱلْخَاطِئَةِ:'}} </div>
                                                        @break
                                                    @case('multiple_choice')
                                                        <div class="head">{{$term->level->arab != 1 ? 'Choose the correct answer from the options below :' : 'ٱخْتَرِ ٱلْإِجَابَةَ ٱلصَّحِيحَةَ مِمَّا يَأْتِي:'}} </div>
                                                        @break
                                                    @case('fill_blank')
                                                        <div class="head">{{$term->level->arab != 1 ? 'Complete the following blanks with the appropriate word.' : 'أكملِ الفراغاتِ الآتيةَ بالكلمةِ المناسبةِ.'}} </div>
                                                        @break
                                                @endswitch

                                            </div>
                                            <div class="card-body">
                                                @foreach(collect($questions_by_type)->shuffle() as $question)
                                                    @php
                                                        $index = $loop->index
                                                    @endphp

                                                    <input type="hidden" value="{{$question->type}}" name="questions[{{$question->id}}][type]">
                                                    <input type="hidden" value="{{$question->subject->id}}" name="questions[{{$question->id}}][subject]">

                                                    @if($question->type == 'true_false')
                                                        @include('student.term.questions.true_false', compact('question','index'))
                                                    @elseif($question->type == 'multiple_choice')
                                                        @include('student.term.questions.choose', compact('question','index'))
                                                    @elseif($question->type == 'matching' && isset($question->match_question) && count($question->match_question)>0)
                                                        @include('student.term.questions.match', compact('question','index'))
                                                    @elseif($question->type == 'sorting' && isset($question->sort_question) && count($question->sort_question)>0)
                                                        @include('student.term.questions.sort', compact('question','index'))
                                                    @elseif($question->type == 'article')
                                                        @include('student.term.questions.article', compact('question','index'))
                                                    @elseif($question->type == 'fill_blank')
                                                        @include('student.term.questions.fill_blank', compact('question','index'))
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>

                                @endforeach
                            @endisset
                            @include('student.term.parts.action_buttons',['tab_index' => $subject->id])

                        </div>
                    @endforeach
                </div>
            </form>


        </div>
    </section>

<!--exam confirm modal-->
@include('student.term.parts.submit-term-modal', compact('term'))
@include('student.term.parts.leave-term-modal', compact('term'))
@include('student.term.parts.emergency-save-modal')



@endsection

@section('script')
    <script>
        @if($term->level->arab)
        $('html').attr('lang', 'ar').attr('dir', 'rtl');
        @else
        $('html').attr('lang', 'en').attr('dir', 'ltr');
        @endif
    </script>
    <script>
        var MAIN_TIME = 40;
        var REMIND_TIME = 40;
        var studentIsDemo = {{ $student->demo }};
        var STORAGE_KEY = 'spent_time_{{$student->id}}_{{$term->id}}';
        var SUBMIT_ASSESSMENT_WHEN_TIMEOUT = {{settingCache('submit_when_timeout')??1}};

        @if(isset($assessment_opened) && !$assessment_opened)
        localStorage.removeItem(STORAGE_KEY);
        @endif
    </script>
    <script src="{{asset('web_assets/js/student_term.js')}}?v=10"></script>
    <script src="{{asset('web_assets/js/questions/fill_blank.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/matching.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/sorting.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/sorting.js')}}"></script>
    <script src="{{asset('web_assets/js/assessment_cache.js')}}"></script>
    <script>
        getAndSetResults() //cache results
    </script>
@endsection
