<!--Sorting-->
<div>
    <div class="form-group row">
        <div class="col-lg-6">
            <input type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
            <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
            <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question->mark}}">

            <label>Q {{$loop->index+1}}: {{t('Sorting Question')}}</label>
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

    <div class="form-group row" id="sort_group{{$question->id}}_{{$loop->index}}">

        @if($question->sort_question && count($question->sort_question)>0)

            @foreach($question['sort_question'] as $option)

                <div class="col-12 row mb-2 options-{{$question->id}}" id="question{{$question->id}}_option{{$option->id}}">
                    <input type="hidden" name="question_data[{{$question->id}}][options][{{$loop->index}}][id]" value="{{$option->id}}">
                    <div class="col-2">
                        @include('manager.term.questions_types.file_input',
                       ['id'=>$option->id,'url' => $option->image,'type' => '4','title'=>'Image','delete_option' => true,
                       'name' => "question_data[$question->id][options][$loop->index][image]"])
                    </div>

                    <div class="col-10">
                        <div class="d-flex flex-row align-items-center mb-2">
                            <label class="m-0 align-self-start">{{$loop->index+1}}:</label>
                            <a class="ms-auto font-weight-bold cursor-pointer"
                               style="font-size: 1rem;color: #ff0000"
                               onclick="deleteOptionRequest('{{$question->id}}',{{$option->id}},4)"
                            >{{t('Delete')}}</a>
                        </div>
                        <input required type="text" class="form-control"
                               name="question_data[{{$question->id}}][options][{{$loop->index}}][content]"
                               value="{{$option['content']}}">
                    </div>
                </div>

            @endforeach

        @else

            @foreach([0,1,2] as $item)
                <div class="col-12 row options-{{$question->id}}" id="question{{$question->id}}_option{{$item}}">
                    <div class="col-2">
                        <label class="mb-2">{{t('Image')}} :</label>
                        <div class="d-flex flex-row align-items-center">
                            <input type="file" name="question_data[{{$question->id}}][options][{{$item}}][image]" class="form-control ">
                        </div>
                    </div>
                  <div class="col-10">
                      <div class="d-flex flex-row align-items-center mb-2">
                        <label class="m-0 align-self-start">{{$item+1}}:</label>
                        <a class="ms-auto font-weight-bold cursor-pointer"
                           style="font-size: 1rem;color: #ff0000"
                           onclick="deleteOptionElement('{{$question->id}}','{{$item}}')"
                        >{{t('Delete')}}</a>
                    </div>
                      <input required type="text" class="form-control"  name="question_data[{{$question->id}}][options][{{$item}}][content]" value="">
                   </div>


                </div>
            @endforeach


        @endif

    </div>
    <button class="btn btn-primary mt-3" type="button"
            onclick="addNewSortOption({{count($question->sort_question)>0?count($question->sort_question):3}},
                                        'sort_group{{$question->id}}_{{$loop->index}}',{{$question->id}})">{{t('Add New Option')}}</button>
</div>
<div class="separator my-5" style="border-color: #575757"></div>
<!--Sorting-->
