<!--Matching-->
<div>
    <div class="form-group row">
        <div class="col-lg-6">
            <input type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
            <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
            <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question->mark}}">

            <label>Q {{$loop->index+1}}: {{t('Matching Question')}}</label>
            <input required class="form-control" name="question_data[{{$question->id}}][content]" type="text" value="{{$question->content}}">
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


        <div class="col-12 my-3">
            <label>{{t('Question Standard')}} :</label>
            <input type="text" name="question_data[{{$question->id}}][question_standard]"
                   class="form-control" value="{{optional($question->question_standard)->standard}}">
        </div>
    </div>

    <div id="matching_group{{$question->id}}_{{$loop->index}}">
        @if($question['match_question'] && count($question['match_question'])>0)
            @foreach($question['match_question'] as $item)
                <div class="form-group row mb-1 options-{{$question->id}}" id="question{{$question->id}}_option{{$item->id}}">
                    <input type="hidden" name="question_data[{{$question->id}}][options][{{$loop->index}}][id]" class="form-control" value="{{$item->id}}">

                    <div class="col-lg-2 ">
                        @include('manager.term.questions_types.file_input',
                       ['id'=>$item->id,'url' => $item->image,'type' => '3','title'=>'Image','delete_option' => true,
                       'name' => "question_data[$question->id][options][$loop->index][image]"])
                    </div>

                    <div class="col-lg-5">
                        <label class="mb-2">{{t('Option')}} {{$loop->index+1}}:</label>
                        <input required class="form-control" name="question_data[{{$question->id}}][options][{{$loop->index}}][content]" value="{{$item->content}}" type="text">
                    </div>
                    <div class="col-lg-4">
                        <label class="mb-2">{{t('Answer')}}:</label>
                        <input required class="form-control" name="question_data[{{$question->id}}][options][{{$loop->index}}][answer]" type="text" value="{{$item->result}}">
                    </div>
                    <button class="btn btn-icon btn-danger mt-8" type="button"
                            onclick="deleteOptionRequest('{{$question->id}}',{{$item->id}},3)">
                        <i class="la la-close la-2"></i>
                    </button>

                </div>
            @endforeach
        @else
            @foreach([0,1,2] as $item)
                <div class="form-group row options-{{$question->id}}" id="question{{$question->id}}_option{{$item}}">
                    <div class="col-lg-2">
                        <label>{{t('Image')}} :
                        </label>
                        <input type="file" name="question_data[{{$question->id}}][options][{{$item}}][image]" class="form-control">
                    </div>
                    <div class="col-lg-5">
                        <label>{{t('Option')}} {{$item+1}}:</label>
                        <input class="form-control" name="question_data[{{$question->id}}][options][{{$item}}][content]" value="" type="text">
                    </div>
                    <div class="col-lg-4">
                        <label>{{t('Answer')}}:</label>
                        <input required="" class="form-control" name="question_data[{{$question->id}}][options][{{$item}}][answer]" type="text">
                    </div>
                    <button class="btn btn-icon btn-danger mt-6" type="button"
                            onclick="deleteOptionElement('{{$question->id}}','{{$item}}')">
                        <i class="la la-close la-2"></i>
                    </button>

                </div>

            @endforeach
        @endif

    </div>
    <button class="btn btn-primary mt-3" type="button"
            onclick="addNewMatchOption({{count($question->match_question)>0?count($question->match_question):3}},
                                        'matching_group{{$question->id}}_{{$loop->index}}',{{$question->id}})">{{t('Add New Option')}}</button>

</div>
<div class="separator my-5" style="border-color: #575757"></div>
<!--Matching-->
