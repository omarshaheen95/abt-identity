<!--FillBlank-->
<div>
    <div class="form-group row">
        <div class="col-lg-12">
            <input class="question-id" type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
            <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
            <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question->mark}}">

            <label>Q {{$loop->index+1}}: {{t('Fill Blank Question')}}</label>
            <div class="form-control blank-content" id="question_data[{{$question->id}}][content]" data-question-id="{{$question->id}}" contenteditable="true" style="border: 1px solid #ddd; padding: 10px;">
                @if(isset($question))
                    {!! $question->content !!}
                @endif
            </div>
            <input required class="form-control content" name="question_data[{{$question->id}}][content]"
                   type="hidden" value="{{ $question->content }}">
            <input type="hidden" name="question_data[{{$question->id}}][blanks_count]" class="blanks-count" value="{{isset($question) ? $question->fill_blank_question->count() : 0}}">

        </div>
        <div class="row mt-5 blank-fields">
            <input type="hidden" name="question_data[{{$question->id}}][fields_count]" class="fields-count" value="{{isset($question) ? $question->fill_blank_question->count() : 0}}">
            @if(isset($question))
                @foreach($question->fill_blank_question as $key => $blank)
                    <div class="col-4 form-group blank-field mb-2">
                        <div class="d-flex align-items-center mb-1">
                            <label class="counter">{{ $loop->index + 1 }}:</label>

                            <button type="button" class="btn btn-sm btn-icon ms-auto btn-danger delete_blank_field"
                                    style="height: 16px;width: 16px" data-id="{{$blank->id}}" data-status="old"><i class="fa fa-close"></i></button>
                        </div>
                        <input required class="form-control"
                               name="question_data[{{$question->id}}][old][{{$blank->id}}][content]"
                               type="text" value="{{ $blank->content}}">
                    </div>
                @endforeach
            @endif
        </div>
        <div class="separator content-separator my-2"></div>

        <div class="col-12 row">
            <div class="col-2 d-flex flex-column">
                @include('manager.term.questions_types.file_input',
                ['id'=>$question->id,'url' => $question->image,'type' => 'image','title'=>'Image','name' => "question_data[$question->id][image]"])
            </div>

            <div class="col-2 d-flex flex-column">
                @include('manager.term.questions_types.file_input',
               ['id'=>$question->id,'url' => $question->audio,'type' => 'audio','title'=>'Audio','name' => "question_data[$question->id][audio]"])
            </div>

            <div class="col-2 d-flex flex-column">
                @include('manager.term.questions_types.file_input',
               ['id'=>$question->id,'url' => $question->question_reader,'question_reader' => 'question_reader','title'=>'Question Reader','name' => "question_data[$question->id][question_reader]"])
            </div>

        </div>



        <div class="col-12 my-3">
            <label>{{t('Question Standard')}} :</label>
            <input type="text" name="question_data[{{$question->id}}][question_standard]"
                   class="form-control" value="{{optional($question->question_standard)->standard}}">
        </div>
    </div>

</div>
<div class="separator my-5" style="border-color: #575757"></div>
<!--Sorting-->
