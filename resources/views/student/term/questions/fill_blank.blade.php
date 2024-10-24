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
            <div class="answer-box justify-content-center mt-2 p-4">
                <div class="w-100">
                    <div class="answers" data-question="{{$question->id}}">
                        <div class="row justify-content-center">
                            {{--    Start With Options Answers     --}}
                            <div class="col-md-12 mb-3">
                                <div data-question="{{$question->id}}" id=""
                                     class="fillBlankOptions fillBlankConnected list-unstyled font-bold text-center d-flex justify-content-around">
                                 @php
                                 if (isset($question->result)){
                                     $blanks = $question->fill_blank_question->whereNotIn('uid',$question->fill_blank_answer->pluck('answer_fill_blank_question_uid'));
                                 }else{
                                   $blanks =  $question->fill_blank_question;
                                 }
                                  @endphp

                                @foreach($blanks as $blank)
                                            <div data-question="{{$question->id}}" class="ui-state-default add-answer"
                                                 data-id="{{$blank->uid}}">
                                                <text>{{$blank->content}} </text>
                                                <span class="float-right"></span>
                                                <input type="hidden" name="questions[{{$question->id}}][blanks][{{$blank->uid}}]" id=""
                                                       value="">
                                            </div>
                                @endforeach

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

                                        @if(isset($question->result))
                                            @foreach ($question->fill_blank_question as $blank)

                                                @php
                                                    // Create the HTML for each blank
                                                    $answer = $question->result->where('fill_blank_question_id',$blank->id)->first();
                //                                    dd($answer);
                                                    if ($answer){
                                                     $answer_blank = $question->fill_blank_question->where('uid',$answer->answer_fill_blank_question_uid)->first();
                                                     if ($answer_blank){
                                                         $replacement = '<ul data-question="'.$question->id.'" data-index="'.$answer_blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0">'.
                                                     '<div data-question="'.$question->id.'" class="ui-state-default add-answer ui-sortable-handle" data-id="'.$answer_blank->uid.'" style="">'.
                                                        '<text>'.$answer_blank->content.'</text>'.
                                                        '<span class="float-right"></span>'.
                                                        '<input type="hidden" name="questions['.$question->id.'][blanks]['.$answer_blank->uid.']" id="" value="'.$blank->id.'">'.
                                                        '</div>'.
                                                     '</ul>';
                                                     }else{
                                                     $replacement = '<ul data-question="'.$question->id.'" data-index="'.$blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0"></ul>';
                                                     }
                                                    }else{
                                                     $replacement = '<ul data-question="'.$question->id.'" data-index="'.$blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0"></ul>';
                                                    }
                                                    // Replace one blank at a time
                                                    $content = preg_replace('/\[blank\]/', $replacement, $content, 1);
                                                    $index++;
                                                @endphp
                                            @endforeach


                                        @else
                                            @foreach ($question->fill_blank_question as $blank)
                                                @php
                                                    // Create the HTML for each blank
                                                    $replacement = '<ul data-question="'.$question->id.'" data-index="'.$blank->id.'" class="d-flex align-items-center fillBlankAnswers fillBlankWords fillBlankConnected list-unstyled m-0 mx-2 font-bold active textOnly text-center m-0 p-0"></ul>';
                                                    // Replace one blank at a time
                                                    $content = preg_replace('/\[blank\]/', $replacement, $content, 1);
                                                    $index++;
                                                @endphp
                                            @endforeach
                                        @endif

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

