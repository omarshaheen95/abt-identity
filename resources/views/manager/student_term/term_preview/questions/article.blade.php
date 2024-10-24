<!-- Article -->
    <div class="row">
        <div class="col-lg-12">

            <div class="question-card">
                <div class="question-content">
                   <div class="d-flex flex-column">
                       <div class="d-flex align-items-center gap-2">
                           <h6 class="fw-bold m-0 pb-1 q-number">{{$index+1}}</h6>
                           <p class="m-0 pb-1"> {{$question->content}}</p>
                       </div>

                   @if($question->image)
                           <div class="pic">
                               <img src="{{asset($question->image)}}" class="q-image" >
                           </div>
                       @endif
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

                   </div>

                    @if($question->image)
                        <div class="pic">
                            <img src="{{asset($question->image)}}" class="" alt="" style="height: 400px;width: 500px;object-fit: fill">
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <div class="col-lg-9">
                        <div class="">
                            <input type="file" class="form-control d-none" name="questions[{{$question->id}}][answer_file]" id="article_file_{{$question->id}}" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".jpeg,.png,.jpg" >

                            <textarea class="form-control" rows="3" name="questions[{{$question->id}}][answer_text]"
                                      id="article_text_{{$question->id}}" placeholder="{{t('Please enter your answer')}}"
                                      style="height: 120px;"></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


