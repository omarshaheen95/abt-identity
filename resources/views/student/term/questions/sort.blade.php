<!-- Sorting -->
    <div class="row">
        <div class="col-lg-12">
            <div class="question-card" data-id="{{$question->id}}">
                <div class="question-content">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                        <p class="m-0 pb-1 content"> {{$question->content}}</p>
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
                                                {{--    Start With Options Answers     --}}
                                                <div class="col-md-12 mb-3">
                                                    <div data-question="{{$question->id}}" id=""
                                                         class="sortOptions sortConnected list-unstyled font-bold text-center d-flex justify-content-around">
                                                        @if(isset($question->result))
                                                            @foreach($question->sort_question->whereNotIn('uid',$question->result->pluck('sort_question_uid')) as $sort)
                                                                <div data-question="{{$question->id}}" class="ui-state-default add-answer me-1"
                                                                     data-id="{{$sort->uid}}">
                                                                    <text>{{$sort->content}} </text>
                                                                    <span class="float-right"></span>
                                                                    <input type="hidden" name="questions[{{$question->id}}][options][{{$sort->uid}}]" id="" value="">
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            @foreach($question->sort_question()->inRandomOrder()->get() as $sort)
                                                                <div data-question="{{$question->id}}" class="ui-state-default add-answer me-1"
                                                                     data-id="{{$sort->uid}}">
                                                                    <text>{{$sort->content}} </text>
                                                                    <span class="float-right"></span>
                                                                    <input type="hidden" class="sort-answer-input" name="questions[{{$question->id}}][options][{{$sort->uid}}]" id="" value="">
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Start With Questions --}}
                                                <div class="col-md-12">
                                                    <div class="row item-container">
                                                        <div data-question="{{$question->id}}" class="list-unstyled m-0 font-bold active text-right m-0 p-0">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="item">
                                                                        <ul data-question="{{$question->id}}" data-index="{{$loop->iteration}}" class="d-flex align-items-center gap-3 px-3 sortAnswers sortWords sortConnected list-unstyled m-0 font-bold active textOnly text-center m-0 p-0">
                                                                           @if(isset($question->result))
                                                                                @foreach($question->result as $answer)
                                                                                    <div data-question="{{$question->id}}" class="ui-state-default add-answer me-1 ui-sortable-handle" data-id="{{$answer->sort_question_uid}}" style="">
                                                                                        <text>{{$question->sort_question->where('uid',$answer->sort_question_uid)->first()->content}} </text>
                                                                                        <span class="float-right"></span>
                                                                                        <input type="hidden" name="questions[{{$question->id}}][options][{{$answer->sort_question_uid}}]" id="" value="{{$loop->iteration}}">
                                                                                    </div>
                                                                                @endforeach
                                                                           @endif
                                                                        </ul>
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

            </div>
        </div>
    </div>




