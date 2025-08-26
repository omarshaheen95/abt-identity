<!-- Article -->
    <div class="row">
        <div class="col-lg-12">

            <div class="question-card" data-id="{{$question->id}}">
                <div class="question-content">
                   <div class="d-flex flex-column">
                       <div class="d-flex align-items-center gap-2">
                           <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                           <p class="m-0 pb-1 content"> {{$question->content}}</p>
                       </div>

                   @if($question->image)
                           <div class="pic">
                               <img src="{{asset($question->image)}}" class="q-image" >
                           </div>
                       @endif
                       @if(!isset($correct_mode))
                           <div class="row">
                               <div class="col-2">
                                   <p class="ml-5">{{t('Answer type')}} :</p>
                               </div>
                               <div class="col-8">
                                   <div class="kt-radio-inline text-left">
                                       <label class="kt-radio me-2">
                                           <input type="radio" name="questions[{{$question->id}}][answer_type]" value="text"
                                                  onclick="showArticleQTextarea({{$question->id}})" checked> {{t('Text answer')}}
                                           <span></span>
                                       </label>
                                       <label class="kt-radio">
                                           <input type="radio"  name="questions[{{$question->id}}][answer_type]" value="file"
                                                  onclick="showArticleQFileInput({{$question->id}})" > {{t('Upload answer image')}}
                                           <span></span>
                                       </label>
                                   </div>

                               </div>
                           </div>
                       @else
                           <div class="d-flex align-items-center mb-3">
                               <h6 class="m-0 me-3">{{t('Mark')}} :</h6>
                               <input name="questions[{{$question->id}}][mark]" class="form-control" style="width: 100px" type="number" min="0" max="{{$question->mark}}" value="{{isset($question->result->mark)?$question->result->mark:''}}"
                                      onchange="
                                      let mark = '{{$question->mark}}'
                                      $(this).val()>mark?$(this).val('{{$question->mark}}'):$(this).val()
                                      "
                               >
                               <p class="m-0 ms-3">/{{$question->mark}}</p>
                           </div>

                       @endif

                   </div>

                    @if($question->image)
                        <div class="pic">
                            <img src="{{asset($question->image)}}" class="" alt="" style="height: 400px;width: 500px;object-fit: fill">
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <div class="col-lg-9">
                        <div class="answer-box">
                            @if(!isset($question->result))
                                <input type="file" class="form-control files-upload d-none" name="questions[{{$question->id}}][answer_file]" id="article_file_{{$question->id}}" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".jpeg,.png,.jpg" >

                                <textarea class="form-control" rows="3" name="questions[{{$question->id}}][answer_text]"
                                          id="article_text_{{$question->id}}" placeholder="{{t('Please enter your answer')}}"
                                          style="height: 120px;"
                                          onkeyup="saveResult()"></textarea>
                            @else
                                @if($question->result->text_answer)
                                    <textarea class="form-control" rows="3" name="questions[{{$question->id}}][answer_text]"
                                              id="article_text_{{$question->id}}" placeholder="{{t('Please enter your answer')}}"
                                              style="height: 120px;">{{$question->result->text_answer}}</textarea>
                                @elseif($question->result->answer_file_path)
                                    <div class="d-flex justify-content-center">
                                        <img src="{{asset($question->result->answer_file_path)}}" style="height: 350px;width: 450px"/>
                                    </div>
                                @else
                                    <h6 class="text-center">{{t('The student not answered for this question')}}</h6>
                                @endif
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


