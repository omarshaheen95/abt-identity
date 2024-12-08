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
                    <span class="text ms-2">{{t('Leave Term')}}</span>
                </a>
            </div>
        </div>
    </nav>
@endsection

@section('style')
    <link href="{{asset('web_assets/css/custom2.css')}}?v{{time()}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/exam_questions.css')}}?v{{time()}}" rel="stylesheet">
    <link href="{{asset('calculator_assets/css/style.css')}}?v={{time()}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/arabic-keyboard.css')}}?v={{time()}}">
        <style>
            #keyboardInputLayout {
                direction: ltr !important;
            }

            #keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td {
                font: normal 30px 'Lucida Console', monospace;
            }

            .keyboardInputInitiator {
                width: 50px
            }
        </style>

@endsection

@section('content')
    <section class="exam-view">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-card">
                        <div class="info">
                            <div class="exam-details ar">
                                <ul>
                                    <li>  عدد الأسئلة {{$questions_count}}  .</li>
                                    <li> يجب الإجابة على جميع الأسئلة. </li>
                                    <li> .مجموعة الدرجات {{$marks}} درجة</li>
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
                                @if(isset($term->show_calculator) && $term->show_calculator)
                                    <a href="#!" onclick="openCalculator()">
                                        <span>
                                            <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16 2H8C5.24 2 3 4.24 3 7V17C3 19.76 5.24 22 8 22H16C18.76 22 21 19.76 21 17V7C21 4.24 18.76 2 16 2ZM8.86 18.63C8.67 18.82 8.42 18.92 8.16 18.92C7.89 18.92 7.64 18.82 7.45 18.63C7.26 18.44 7.15 18.19 7.15 17.92C7.15 17.66 7.26 17.4 7.45 17.21C7.54 17.12 7.65 17.05 7.77 17C8.02 16.9 8.29 16.9 8.54 17C8.6 17.02 8.66 17.05 8.71 17.09C8.77 17.12 8.82 17.17 8.86 17.21C9.05 17.4 9.16 17.66 9.16 17.92C9.16 18.19 9.05 18.44 8.86 18.63ZM7.15 13.92C7.15 13.79 7.18 13.66 7.23 13.54C7.28 13.41 7.35 13.31 7.45 13.21C7.68 12.98 8.03 12.87 8.35 12.94C8.41 12.95 8.48 12.97 8.54 13C8.6 13.02 8.66 13.05 8.71 13.09C8.77 13.12 8.82 13.17 8.86 13.21C8.95 13.31 9.03 13.41 9.08 13.54C9.13 13.66 9.15 13.79 9.15 13.92C9.15 14.19 9.05 14.44 8.86 14.63C8.67 14.82 8.42 14.92 8.16 14.92C8.02 14.92 7.89 14.89 7.77 14.84C7.65 14.79 7.54 14.72 7.45 14.63C7.26 14.44 7.15 14.19 7.15 13.92ZM12.86 18.63C12.77 18.72 12.66 18.79 12.54 18.84C12.42 18.89 12.29 18.92 12.15 18.92C11.89 18.92 11.64 18.82 11.45 18.63C11.26 18.44 11.15 18.19 11.15 17.92C11.15 17.85 11.16 17.79 11.17 17.72C11.19 17.66 11.21 17.6 11.23 17.54C11.26 17.48 11.29 17.42 11.32 17.36C11.36 17.31 11.4 17.26 11.45 17.21C11.54 17.12 11.65 17.05 11.77 17C12.14 16.85 12.58 16.93 12.86 17.21C13.05 17.4 13.15 17.66 13.15 17.92C13.15 18.19 13.05 18.44 12.86 18.63ZM12.86 14.63C12.67 14.82 12.42 14.92 12.15 14.92C11.89 14.92 11.64 14.82 11.45 14.63C11.26 14.44 11.15 14.19 11.15 13.92C11.15 13.66 11.26 13.4 11.45 13.21C11.82 12.84 12.49 12.84 12.86 13.21C12.95 13.31 13.03 13.41 13.08 13.54C13.13 13.66 13.15 13.79 13.15 13.92C13.15 14.19 13.05 14.44 12.86 14.63ZM9 10.46C7.97 10.46 7.12 9.62 7.12 8.58V7.58C7.12 6.55 7.96 5.7 9 5.7H15C16.03 5.7 16.88 6.54 16.88 7.58V8.58C16.88 9.61 16.04 10.46 15 10.46H9ZM16.86 18.63C16.67 18.82 16.42 18.92 16.15 18.92C16.02 18.92 15.89 18.89 15.77 18.84C15.65 18.79 15.54 18.72 15.45 18.63C15.26 18.44 15.16 18.19 15.16 17.92C15.16 17.66 15.26 17.4 15.45 17.21C15.72 16.93 16.17 16.85 16.54 17C16.66 17.05 16.77 17.12 16.86 17.21C17.05 17.4 17.15 17.66 17.15 17.92C17.15 18.19 17.05 18.44 16.86 18.63ZM17.08 14.3C17.03 14.42 16.96 14.53 16.86 14.63C16.67 14.82 16.42 14.92 16.15 14.92C15.89 14.92 15.64 14.82 15.45 14.63C15.26 14.44 15.15 14.19 15.15 13.92C15.15 13.66 15.26 13.4 15.45 13.21C15.82 12.84 16.49 12.84 16.86 13.21C17.05 13.4 17.16 13.66 17.16 13.92C17.16 14.05 17.13 14.18 17.08 14.3Z" fill="#ffffff"></path> </g></svg>
                                        </span>
                                    </a>
                                @endif
                            </div>
                            <div class="exam-details en">
                                <ul>
                                    <li> No. of questions is {{$questions_count}}. </li>
                                    <li> All questions must be answered . </li>
                                    <li> The total marks is {{$marks}}. </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <form id="exams" action="{{route('student.term-save', ['id'=>$term->id])}}"  method="post" enctype="multipart/form-data">
                <input type="hidden" name="started_at" value="{{now()}}">
                @csrf
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
                                                        <div class="head">{{$term->level->arab != 1 ? 'True Or False Questions' : 'أسئلة صح أو خطأ :اجب بصح أم خطأ'}} </div>
                                                        @break
                                                    @case('multiple_choice')
                                                        <div class="head">{{$term->level->arab != 1 ? 'Multiple Choice Questions' : 'أسئلة اختر من متعدد : قم باختيار الإجابة الصحيحة'}} </div>
                                                        @break
                                                    @case('fill_blank')
                                                        <div class="head">{{$term->level->arab != 1 ? 'Fill blank Questions : Put the answer in the appropriate blank' : 'أسئلة أكمل الفراغ : ضع الإجابة المناسبة بالفراغ المناسب'}} </div>
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
@include('student.term.parts.submit-term-modal')
@include('student.term.parts.leave-term-modal')



@endsection

@section('script')
{{--    <script>--}}
{{--        @if($term->level->arab==1)--}}
{{--        $('html').attr('lang', 'ar').attr('dir', 'rtl');--}}
{{--        @else--}}
{{--        $('html').attr('lang', 'en').attr('dir', 'ltr');--}}
{{--        @endif--}}
{{--    </script>--}}
    <script src="{{asset('calculator_assets/js/script.js')}}"></script>
    <script src="{{asset('web_assets/js/student_term.js')}}?v={{time()}}"></script>
    <script src="{{asset('web_assets/js/questions/fill_blank.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/matching.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/sorting.js')}}"></script>
    <script>
        let TIME = "{{$term->duration}}";
        getAndSetResults() //cache results
    </script>

@endsection
