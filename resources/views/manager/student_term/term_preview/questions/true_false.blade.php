<!-- True & false -->
    <div class="row">
        <div class="col-lg-12">

            <div class="question-card">
                <div class="question-content ">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                        <p class="m-0 pb-1"> {{$question->content}}</p>
                    </div>
                    @if($question->image)
                            <div class="pic">
                                <img src="{{asset($question->image)}}" class="q-image">
                            </div>
                    @endif
                </div>

                <div class="answer-content bg-transparent border-0">
                    <div class="answer-card justify-content-center">
                        <div class="answer-group d-flex justify-content-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input d-none" type="radio" name="questions[{{$question->id}}][answer]"
                                           id="tf-option-{{$question->id}}-1" value="1"
                                        {{$question->result==1?'checked':''}}>
                                    <label class="form-check-label form-check-custom" for="tf-option-{{$question->id}}-1" >
                                        <span> <img src="{{asset('web_assets/img/true.svg')}}" alt=""></span>
                                        <span class="text"> {{$term->level->arab?'صح':'True'}} </span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline ">
                                    <input class="form-check-input d-none" type="radio" name="questions[{{$question->id}}][answer]"
                                           id="tf-option-{{$question->id}}-2" value="0"
                                        {{$question->result==0?'checked':''}}>

                                    <label class="form-check-label form-check-custom" for="tf-option-{{$question->id}}-2">
                                        <span> <img src="{{asset('web_assets/img/false.svg')}}" alt=""></span>
                                        <span class="text"> {{$term->level->arab?'خطأ':'False'}} </span>
                                    </label>
                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
