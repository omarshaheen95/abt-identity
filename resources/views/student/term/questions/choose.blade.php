 <!-- Choose The Correct Answer -->
    <div class="row">
        <div class="col-lg-12">
            <div class="question-card" data-id="{{$question->id}}">

                <div class="answer-content bg-transparent border-0">
                    <div class="answer-card">
                        <div class="title">
                            <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                                    <p class="m-0 pb-1"> {{$question->content}}</p>
                            </div>

                        </div>
                    </div>
                    <div class="question-content my-2">
                        @if($question->image)
                            <div class="pic">
                                <img src="{{asset($question->image)}}" class="q-image">
                            </div>
                        @endif

                    </div>
                    <div class="answer-group d-flex justify-content-center align-items-start py-2">

                        @if(isset($correct_mode))
                            @php
                                $correct_option_id = $question->option_question
                                ->where('question_id',$question->id)
                                ->where('result',1)->pluck('id')->first();
                            @endphp
                            @if(isset($question->result))
                                <input type="hidden" name="questions[{{$question->id}}][question_result_id]" value="{{$question->result->id}}">
                            @endif
                            @foreach($question->option_question as $option)
                                <div class="form-check form-check-inline align-items-center">
                                    <input class="form-check-input" type="radio" name="questions[{{$question->id}}][answer_option_id]"
                                           id="choose-option-{{$option->id}}-{{auth()->id()}}" value="{{$option->id}}"

                                           @if(isset($question->result) && $question->result->option_id==$option->id)
                                               @if($correct_option_id == $option->id)
                                                   style="border-color: #24c07a;background-color: #24c07a"
                                           @else
                                               style="border-color: #ff2b32;background-color: #ff2b32"
                                           @endif

                                           @elseif($correct_option_id == $option->id)
                                               style="border-color: #24c07a;background-color: #24c07a"
                                        @endif

                                    @if(isset($question->result))
                                        {{$question->result->option_id==$option->id?'checked':''}}>
                                    @endif

                                        @if(!$option->image)
                                            <label class="form-check-label ms-2" for="choose-option-{{$option->id}}"> {{$option->content}} </label>
                                        @else
                                            <div class="option-pic">
                                                <img src="{{asset($option->image)}}" />
                                            </div>
                                        @endif

                                </div>
                            @endforeach
                        @else
                            @foreach($question->option_question as $option)
                                <div class="form-check form-check-inline align-items-center">
                                    <input class="form-check-input" type="radio" name="questions[{{$question->id}}][answer_option_id]"
                                           id="choose-option-{{$option->id}}{{auth()->id()}}" value="{{$option->id}}" onclick="saveResult()">

                                        @if(!$option->image)
                                            <label class="form-check-label ms-2" for="choose-option-{{$option->id}}"> {{$option->content}} </label>
                                        @else
                                            <div class="option-pic">
                                                <img src="{{asset($option->image)}}" />
                                            </div>
                                        @endif
                                </div>
                            @endforeach
                        @endif



                    </div>

                </div>

            </div>
        </div>
    </div>

