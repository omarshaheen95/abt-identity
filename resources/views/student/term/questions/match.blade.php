<!-- Matching -->
    <div class="row">
        <div class="col-lg-12">
            <div class="question-card" data-id="{{$question->id}}">
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
                <div class="answer-content  p-4">

                    <div class="answer-card flex-column align-items-start">

                        <div class="answer-group w-100">

                            <div class="complete-answer w-100">

                                <div class="question-box">
                                    <div class="w-100">
                                        <div class="answers ">
                                            <div class="row justify-content-center">

                                                {{-- Start With Options Answers --}}

                                                <div class="col-md-12">

                                                        <div data-question="{{$question->id}}" id=""
                                                             class="matchOptions matchConnected list-unstyled font-bold text-center d-flex justify-content-around">
                                                            @if(isset($question->result))
                                                                {{--When have answer in correcting mode--}}
                                                                @foreach($question->match_question->whereNotIn('uid',$question->result->pluck('match_question_answer_uid')) as $match)
                                                                    <div data-question="{{$question->id}}" class="ui-state-default add-answer"
                                                                         data-id="{{$match->uid}}">
                                                                        <text>{{$match->result}}</text>
                                                                        <span class="float-right"></span>
                                                                        <input type="hidden" name="questions[{{$question->id}}][options][{{$match->uid}}]" id="" value="">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                @foreach($question->match_question()->inRandomOrder()->get() as $match)
                                                                    <div data-question="{{$question->id}}" class="ui-state-default add-answer"
                                                                         data-id="{{$match->uid}}">
                                                                        <text>{{$match->result}}</text>
                                                                        <span class="float-right"></span>
                                                                        <input class="matching-answer-input" type="hidden" name="questions[{{$question->id}}][options][{{$match->uid}}]" id="" value="">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                </div>


                                                {{-- Start With Questions --}}
                                                <div class="col-md-12 mt-3">
                                                    <div class="row item-container">
                                                        <div data-question="{{$question->id}}" class="list-unstyled m-0 font-bold active text-right m-0 p-0">
                                                            @foreach($question->match_question as $match)
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="ui-state-default mb-2 question-option item">
                                                                            @if(!is_null($match->image))
                                                                                <div class="row justify-content-center">
                                                                                    <div class="col-md-12 text-center">
                                                                                        <img src="{{asset($match->image)}}" class="match-img" />
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <span class="ml-3"></span>
                                                                                <text class="fs-5 fw-bold">{{$match->content}}</text>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 mb-2">
                                                                        <div class="item match-item m-i-border">
                                                                            <ul data-question="{{$question->id}}" data-index="{{$match->id}}"
                                                                                class="matchAnswers matchWords matchConnected list-unstyled d-flex align-items-center justify-content-center" @if($match->image) style="height:200px" @endif>
                                                                               @if(isset($question->result))
                                                                                    {{--When have answer in correcting mode--}}
                                                                                    @php
                                                                                        $match_a = null;
                                                                                        $match_answer = $question->result->where('match_id',$match->id)->first();
                                                                                        if ($match_answer){
                                                                                         $match_a = $question->match_question->where('uid',$match_answer->match_question_answer_uid)->first();

                                                                                        }
                                                                                    @endphp
                                                                                    @if($match_a)
                                                                                        <div data-question="{{$question->id}}" class="ui-state-default add-answer ui-sortable-handle" data-id="{{$match_a->uid}}" style="">
                                                                                            <text>{{$match_a->result}}</text>
                                                                                            <span class="float-right"></span>
                                                                                            <input type="hidden" name="questions[{{$question->id}}][options][{{$match_a->uid}}]" id="" value="{{$match->id}}">
                                                                                        </div>
                                                                                    @endif
                                                                               @endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
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
                </div>

            </div>
        </div>
    </div>



