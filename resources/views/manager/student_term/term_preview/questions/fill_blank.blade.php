<!-- Sorting -->
<div class="row">
    <div class="col-lg-12">
        <div class="question-card">
            <div class="question-content">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                    <p class="m-0 pb-1"> {{$question->content}}</p>
                </div>


                @if($question->image)
                    <div class="pic">
                        <img src="{{asset($question->image)}}" class="q-image">
                    </div>
                @endif
            </div>
            <div class="answer-box justify-content-center mt-2 py-4">
                <div class="w-100">
                    <div class="answers" data-question="{{$question->id}}">
                        <div class="row justify-content-center">
                            {{--    Start With Options Answers     --}}
                            <div class="col-md-12 mb-3">
                                <div data-question="{{$question->id}}" id=""
                                     class="fillBlankOptions fillBlankConnected list-unstyled font-bold text-center d-flex justify-content-around">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="question-content">
                                <div class="d-flex flex-column">
                                    <div class="row align-items-center">
                                        @php
                                            $content = $question->content;
                                            $index = 1;
                                        @endphp

                                        @foreach ($question->fill_blank_question as $blank)
                                            @php
                                                $replacement = '<ul data-question="'.$question->id.'" data-index="'.$blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0">'.
                                                '<div data-question="'.$question->id.'" class="ui-state-default add-answer ui-sortable-handle" data-id="'.$blank->uid.'" style="">'.
                                                   '<text>'.$blank->content.'</text>'.
                                                   '<span class="float-right"></span>'.
                                                   '<input type="hidden" name="fill_blank['.$question->id.']['.$blank->uid.']" id="" value="'.$blank->id.'">'.
                                                   '</div>'.
                                                '</ul>';
                                               // Replace one blank at a time
                                               $content = preg_replace('/\[blank\]/', $replacement, $content, 1);
                                               $index++;
                                            @endphp
                                        @endforeach

                                        {!! $content !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>






{{--<div class="question question-card form-group row card" data-id = "{{$question->id}}">--}}
{{--    <div class="content">--}}
{{--        <div class="question-content mb-3">--}}
{{--            <div class="row">--}}
{{--                <div class="col-12 @if(isset($question->media) && $question->media->count()>0) col-md-9 @endif row">--}}
{{--                    <div class="col-1 q-number">{{$loop->iteration}}</div>--}}
{{--                    <div class="col-11"> {!! $question->content !!}</div>--}}
{{--                </div>--}}
{{--                --}}{{--            @include('student.exam.parts.question_media',['media_files' => $question->media])--}}
{{--            </div>--}}
{{--        </div>--}}


{{--        <div class="answer-box justify-content-center mt-2">--}}
{{--            <div class="answers" data-question="{{$question->id}}">--}}
{{--                <div class="row justify-content-center">--}}
{{--                    --}}{{--    Start With Options Answers     --}}
{{--                    <div class="col-md-12 mb-3">--}}
{{--                        <div data-question="{{$question->id}}" id=""--}}
{{--                             class="fillBlankOptions fillBlankConnected list-unstyled font-bold text-center d-flex justify-content-around">--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-12">--}}
{{--                    <div class="question-content">--}}
{{--                        <div class="d-flex flex-column">--}}
{{--                            <div class="row align-items-center">--}}
{{--                                @php--}}
{{--                                    $content = $question->content;--}}
{{--                                    $index = 1;--}}
{{--                                @endphp--}}

{{--                                @foreach ($question->fill_blank_question as $blank)--}}
{{--                                    @php--}}
{{--                                        $replacement = '<ul data-question="'.$question->id.'" data-index="'.$blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0">'.--}}
{{--                                        '<div data-question="'.$question->id.'" class="ui-state-default add-answer ui-sortable-handle" data-id="'.$blank->uid.'" style="">'.--}}
{{--                                           '<text>'.$blank->content.'</text>'.--}}
{{--                                           '<span class="float-right"></span>'.--}}
{{--                                           '<input type="hidden" name="fill_blank['.$question->id.']['.$blank->uid.']" id="" value="'.$blank->id.'">'.--}}
{{--                                           '</div>'.--}}
{{--                                        '</ul>';--}}
{{--                                       // Replace one blank at a time--}}
{{--                                       $content = preg_replace('/\[blank\]/', $replacement, $content, 1);--}}
{{--                                       $index++;--}}
{{--                                    @endphp--}}
{{--                                @endforeach--}}

{{--                                {!! $content !!}--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}







