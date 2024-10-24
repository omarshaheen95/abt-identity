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
                <div class="answer-content  py-4">

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
                                                                            @foreach($question->sort_question as $sort)
                                                                                <div data-question="{{$question->id}}" class="ui-state-default add-answer me-1"
                                                                                     data-id="{{$sort->uid}}">
                                                                                    <text>{{$sort->content}} </text>
                                                                                    <span class="float-right"></span>
                                                                                    <input type="hidden" name="sorting[{{$question->id}}][{{$sort->uid}}]" id="" value="">
                                                                                </div>
                                                                            @endforeach
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




