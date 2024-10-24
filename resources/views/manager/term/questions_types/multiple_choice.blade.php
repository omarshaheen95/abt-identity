<!--Options-->
<div class="pt-4">
    <div class="form-group row">
        <div class="col-lg-6">
            <input type="hidden" name="question_data[{{$question->id}}][id]" value="{{$question->id}}">
            <input type="hidden" name="question_data[{{$question->id}}][type]" value="{{$question->type}}">
            <input type="hidden" name="question_data[{{$question->id}}][mark]" value="{{$question['mark']}}">

            <label>Q {{$loop->index+1}}: {{t('Choose the correct answer question')}}</label>
            <input required class="form-control" name="question_data[{{$question->id}}][content]" type="text" value="{{$question['content']}}">

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


        <div class="col-12 my-3 pe-9">
            <label>{{t('Question Standard')}} :</label>
            <input type="text" name="question_data[{{$question->id}}][question_standard]"
                   class="form-control" value="{{optional($question->question_standard)->standard}}">
        </div>

    </div>
    <div class="row" id="options_group{{$question->id}}_{{$loop->index}}">

        @if($question->option_question && count($question->option_question)>0)

            @foreach($question->option_question as $option)
                <div class="col-12 row  mb-2 options-{{$question->id}}" id="question{{$question->id}}_option{{$option->id}}">
                    <input type="hidden" name="question_data[{{$question->id}}][options][{{$loop->index}}][id]" value="{{$option->id}}">
                    <div class="col-2">
                        @include('manager.term.questions_types.file_input',
                        ['id'=>$option->id,'url' => $option->image,'type' => '2','title'=>'Image','delete_option' => true,
                        'name' => "question_data[$question->id][options][$loop->index][image]"])
                    </div>
                    <div class="col-10">
                        <div class="d-flex flex-row align-items-center mb-2">
                            <label class="m-0 me-2">{{$loop->index+1}}:</label>
                            <div class="form-check form-check-custom form-check-solid form-check-sm">
                                <input required="" class="form-check-input" type="radio" class="ml-1"
                                       name="question_data[{{$question->id}}][correct_answer_index]"
                                       value="{{$loop->index}}" {{$option->result==1?'checked':''}}
                                />
                            </div>
                            <a class="cursor-pointer ms-auto"
                               style="font-size: 1rem;color: #ff0000"
                               onclick="deleteOptionRequest('{{$question->id}}',{{$option->id}},2)"
                            >{{t('Delete')}}</a>
                        </div>

                        <input required type="text" class="form-control"
                               name="question_data[{{$question->id}}][options][{{$loop->index}}][content]"
                               value="{{$option->content}}">
                    </div>
                </div>
            @endforeach

        @else
            @foreach([0,1,2] as $item)
                <div class="col-12 row mb-2 options-{{$question->id}}" id="question{{$question->id}}_option{{$loop->index}}">
                    <div class="col-2">
                        <label class="mb-2">{{t('Image')}} :</label>
                        <div class="d-flex flex-row align-items-center">
                            <input type="file" name="question_data[{{$question->id}}][options][{{$item}}][image]" class="form-control ">
                        </div>

                    </div>
                    <div class="col-10">
                        <div class="d-flex flex-row align-items-center mb-2">
                            <label class="m-0">{{$item+1}}:</label>
                            <div class="form-check form-check-custom form-check-solid form-check-sm mx-1">
                                <input required="" class="form-check-input" type="radio" value="{{$item}}" id="flexRadioLg"
                                       name="question_data[{{$question->id}}][correct_answer_index]"
                                />
                            </div>
                            <a class="ms-auto font-weight-bold cursor-pointer"
                               style="font-size: 1rem;color: #ff0000"
                               onclick="deleteOptionElement('{{$question->id}}','{{$loop->index}}')"
                            >{{t('Delete')}}</a>
                        </div>
                        <input required type="text" class="form-control"  name="question_data[{{$question->id}}][options][{{$item}}][content]" value="">
                    </div>

                </div>
            @endforeach


        @endif

    </div>
    <button class="btn btn-primary mt-3" type="button"
            onclick="addNewOption({{count($question->option_question)>0?count($question->option_question):3}},
                                        'options_group{{$question->id}}_{{$loop->index}}',{{$question->id}})">{{t('Add New Option')}}</button>
</div>
<div class="separator my-5" style="border-color: #575757"></div>
<!--Options-->
