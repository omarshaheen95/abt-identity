<!--True or False-->
<div class="form-group row">

    <input type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
    <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
    <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question->mark}}">

    <div class="col-lg-6">
        <label>Q {{$loop->index+1}} : {{t('True or False Question')}}</label>
        <input required class="form-control" name="question_data[{{$question->id}}][content]"
               type="text" value="{{$question->content}}">
    </div>

    <div class="col-6 row">
        <div class="col-lg-4 d-flex flex-column">
            @include('manager.term.questions_types.file_input',
            ['id'=>$question->id,'url' => $question->image,'type' => 'image','title'=>'Image','name' => "question_data[$question->id][image]"])
        </div>

        <div class="col-lg-4 d-flex flex-column">
            @include('manager.term.questions_types.file_input',
           ['id'=>$question->id,'url' => $question->audio,'type' => 'audio','title'=>'Audio','name' => "question_data[$question->id][audio]"])
        </div>

        <div class="col-lg-4 d-flex flex-column">
            @include('manager.term.questions_types.file_input',
           ['id'=>$question->id,'url' => $question->question_reader,'question_reader' => 'question_reader','title'=>'Question Reader','name' => "question_data[$question->id][question_reader]"])
        </div>
    </div>

    <div class="col-12 d-flex mt-4">
        <div class="w-25 mr-2">
            <label class="font-weight-bold">{{t('Correct Answer')}}:</label>
            <div class="d-flex gap-2 mt-3">
                @if(isset($question->tf_question))
                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                        <input class="form-check-input" required type="radio" value="1" name="question_data[{{$question->id}}][correct_answer_value]" id="flexRadioLg"
                            {{$question->tf_question['result']==1?'checked':''}}/>
                        <label class="form-check-label" for="flexRadioLg">
                            {{t('True')}}
                        </label>
                    </div>

                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                        <input class="form-check-input" required type="radio" value="0" name="question_data[{{$question->id}}][correct_answer_value]" id="flexRadioLg"
                            {{$question->tf_question['result']==0?'checked':''}} />
                        <label class="form-check-label" for="flexRadioLg">
                            {{t('False')}}
                        </label>
                    </div>
                @else
                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                        <input required class="form-check-input" type="radio" value="1" name="question_data[{{$question->id}}][correct_answer_value]" id="flexRadioLg"/>
                        <label class="form-check-label" for="flexRadioLg">
                            {{t('True')}}
                        </label>
                    </div>

                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                        <input required class="form-check-input" type="radio" value="0" name="question_data[{{$question->id}}][correct_answer_value]" id="flexRadioLg"/>
                        <label class="form-check-label" for="flexRadioLg">
                            {{t('False')}}
                        </label>
                    </div>
                @endif

            </div>
        </div>

        <div class="w-75">
            <label>{{t('Question Standard')}} :</label>
            <input type="text" name="question_data[{{$question->id}}][question_standard]"
                   class="form-control" value="{{optional($question->question_standard)->standard}}">
        </div>
    </div>

</div>
<div class="separator my-5" style="border-color: #575757"></div>
<!--True or False-->
