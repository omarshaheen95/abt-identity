@extends('manager.student_term.term_correcting.container')
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

            </div>
        </div>
    </nav>
@endsection

@section('style')
    <link href="{{asset('web_assets/css/exam_questions.css')}}?v1" rel="stylesheet">
    <link href="{{asset('calculator_assets/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/arabic-keyboard.css')}}">
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

            <form id="exams" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="started_at" value="{{now()}}">
                @csrf
                <div class="mb-5">
                    <div class="">
                        <ul class="nav d-flex gap-4 justify-content-center">
                            @foreach($subjects as $subject)
                                <li class="nav-item">
                                    <a id="btn_nav_{{$subject->id}}" class="btn_nav btn_nav_active d-flex align-items-center justify-content-center {{$loop->index==0?'active':''}}"
                                       data-bs-toggle="tab" href="#kt_tab_pane_{{$subject->id}}">{{$subject->name}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="tab-content" id="myTabContent">
                    @foreach($subjects as $subject)
                        <div class="tab-pane fade {{$loop->index==0?'show active':''}}" id="kt_tab_pane_{{$subject->id}}" role="tabpanel">
                            @foreach($questions[$subject->id] as $question)
                                @php
                                    $index = $loop->index
                                @endphp

                                <input type="hidden" value="{{$question->type}}" name="questions[{{$question->id}}][type]">
                                <input type="hidden" value="{{$question->subject}}" name="questions[{{$question->id}}][subject]">

                                @if($question->type == 'true_false')
                                    @include('manager.student_term.term_preview.questions.true_false', compact('question','index'))
                                @elseif($question->type == 'multiple_choice')
                                    @include('manager.student_term.term_preview.questions.choose', compact('question','index'))
                                @elseif($question->type == 'matching' && isset($question->match_question) && count($question->match_question)>0)
                                    @include('manager.student_term.term_preview.questions.match', compact('question','index'))
                                @elseif($question->type == 'sorting' && isset($question->sort_question) && count($question->sort_question)>0)
                                    @include('manager.student_term.term_preview.questions.sort', compact('question','index'))
                                @elseif($question->type == 'article')
                                    @include('manager.student_term.term_preview.questions.article', compact('question','index'))
                                @elseif($question->type == 'fill_blank')
                                    @include('manager.student_term.term_preview.questions.fill_blank', compact('question','index'))
                                @endif
                            @endforeach

                                @include('student.term.parts.action_buttons',['tab_index' => $subject->id])

                        </div>
                    @endforeach

                </div>
            </form>


        </div>
    </section>

@endsection

@section('script')
    <script src="{{asset('web_assets/js/questions/fill_blank.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/matching.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/sorting.js')}}"></script>
@endsection
