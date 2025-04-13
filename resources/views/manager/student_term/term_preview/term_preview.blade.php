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
    <link href="{{asset('web_assets/css/exam_questions.css')}}?v={{time()}}" rel="stylesheet">
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
                                       data-bs-toggle="tab" href="#kt_tab_pane_{{$subject->id}}">{{t($subject->name,[],$term->level->arab?'ar':'en')}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="tab-content" id="myTabContent">
                    @foreach($subjects as $subject)
                        <div class="tab-pane fade {{$loop->index==0?'show active':''}}" id="kt_tab_pane_{{$subject->id}}" role="tabpanel">
                            @if(isset($questions[$subject->id]))

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
                                                <input type="hidden" value="{{$question->subject->name}}" name="questions[{{$question->id}}][subject]">

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
                                        </div>
                                    </div>

                                @endforeach

                            @else
                                <h4 class="text-center">{{t('The subject not have any questions!')}}</h4>
                            @endif


                                @include('student.term.parts.action_buttons',['tab_index' => $subject->id])

                        </div>
                    @endforeach

                </div>
            </form>


        </div>
    </section>

@endsection

@section('script')
{{--    <script>--}}
{{--        @if($term->level->arab==1)--}}
{{--        $('html').attr('lang', 'ar').attr('dir', 'rtl');--}}
{{--        @else--}}
{{--        $('html').attr('lang', 'en').attr('dir', 'ltr');--}}
{{--        @endif--}}
{{--    </script>--}}
    <script src="{{asset('web_assets/js/questions/fill_blank.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/matching.js')}}"></script>
    <script src="{{asset('web_assets/js/questions/sorting.js')}}"></script>
@endsection
