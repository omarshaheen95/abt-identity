<!--Article-->
<div class="form-group row">

    <input type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
    <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
    <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question->mark}}">

    <div class="col-lg-6">
        <label>Q {{$loop->index+1}} : {{t('Article Question')}}</label>
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
        <div class="w-100">
            <label>{{t('Question Standard')}} :</label>
            <input type="text" name="question_data[{{$question->id}}][question_standard]"
                   class="form-control" value="{{optional($question->question_standard)->standard}}">
        </div>
    </div>

</div>
<div class="separator my-5" style="border-color: #575757"></div>

<!--Article-->
