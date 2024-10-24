@extends('manager.layout.container')
@section('title')
    {{$title}} / {{$term->name}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.term.index')}}" class="text-muted">
            {{t('Terms')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$term->name}}
    </li>
@endpush
@section('content')
    <div class="d-flex flex-row align-items-center mb-4 gap-2">
        <strong class="fs-4">{{t('Questions')}}:</strong>
        <p id="questions_count" class="fs-5 m-0">0</p>
        <strong class="fs-4">{{t('Marks')}}: </strong>
        <p id="questions_marks" class="fs-5 m-0">0</p>
    </div>
    <form id="questions_form" method="POST" action="{{route('manager.term.save-questions-structure',['id'=>request()['id']])}}">
        @csrf
        @if(isset($questions) && $questions)
            @foreach($questions as $question)
                <div class="d-flex flex-row gap-2 pt-6 q-container" id="container_{{$loop->index}}">
                    @if(isset($question['id']) && $question['id'])
                        <input type="hidden" value="{{$question['id']}}" name="questions[{{$loop->index}}][id]"
                               id="question_id_{{$loop->index}}">
                    @endif
                    @if(isset($question['term_id']) && $question['term_id'])
                        <input type="hidden" value="{{$question['term_id']}}"
                               name="questions[{{$loop->index}}][term_id]" id="term_id_{{$loop->index}}">
                    @endif
                    <strong class="font-size-h6 align-self-end mb-3 mr-3">{{$loop->index+1}}-</strong>
                    <div class="d-flex flex-column w-25 mr-2 ">
                        <label for="question_type">{{t('Question type')}} :</label>
                        <select class="form-control form-select" data-control="select2" data-hide-search="true" id="select_{{$loop->index}}" name="questions[{{$loop->index}}][type]" data-placeholder="{{t('Choose the question type')}}">
                            <option></option>
                            @foreach($types as $type)
                                <option value="{{$type['value']}}" {{$question['type']==$type['value']?'selected':''}}>{{$type['name']}}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="d-flex flex-column w-300px mr-2 ">
                        <label for="question_type" class="font-weight-bold">{{t('Choose the subject')}} :</label>
                        <select  class="form-control subject_type form-select subject" data-control="select2" data-hide-search="true" id="select_subject_{{$loop->index}}" name="questions[{{$loop->index}}][subject_id]" data-placeholder="{{t('Choose the subject')}}">
                            <option></option>
                            @foreach($subjects as $subject)
                                <option value="{{$subject->id}}" {{$question->subject_id==$subject->id?'selected':''}}>{{$subject->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex flex-column w-100px">
                        <label for="question_mark">{{t('Mark')}} :</label>
                        <input class="form-control mark subject{{$question->subject->name}}-mark" id="marks_input_{{$loop->index}}" placeholder="Marks" type="number" name="questions[{{$loop->index}}][mark]" value="{{$question['mark']}}">
                    </div>

                    <a id="btn_delete" onclick="deleteQuestion('{{$loop->index}}')" class="btn btn-icon btn-danger align-self-end mb-2 ml-3" style="height: 30px;width: 30px">
                        <i class="fa fa-trash font-size-h6"></i>
                    </a>

                </div>
            @endforeach
        @endif
    </form>
    <div class="d-flex flex-row p-4 mt-5 bg-gray-100 rounded gap-2" >
        <div class="d-flex flex-column w-25 mr-2 ">
            <label for="question_type" class="font-weight-bold">{{t('Choose the question type')}} :</label>
            <select  class="form-control form-select" data-control="select2" data-hide-search="true" id="question_type" data-placeholder="{{t('Choose the question type')}}">
                <option></option>
                @foreach($types as $type)
                    <option value="{{$type['value']}}">{{$type['name']}}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex flex-column w-25 mr-2 ">
            <label for="question_subject" class="font-weight-bold">{{t('Choose the subject')}} :</label>
            <select  class="form-control form-select" data-control="select2" data-hide-search="true" id="question_subject" data-placeholder="{{t('Choose the subject')}}">
                <option></option>
                @foreach($subjects as $subject)
                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column w-100px">
            <label for="question_mark" class="font-weight-bold">{{t('Mark')}} :</label>
            <input class="form-control" id="question_mark" placeholder="{{t('Mark')}}" type="number">
        </div>

        <button id="btn_add_question" type="button" class="btn btn-primary d-flex justify-content-center align-items-center align-self-end" style="height: 40px">
            <i class="la la-plus"></i>{{t('Add Question')}}</button>

        <button id="btn_save_questions" type="button" class="btn btn-success d-flex justify-content-center align-items-center align-self-end" style="height: 40px">
            <i class="la la-check"></i>{{t('Save Questions')}}</button>
    </div>
@endsection
@section('script')
    <script>
        let ADD_QUESTION_MESSAGE_TITLE = '{{t('Save Questions')}}'
        let ADD_QUESTION_MESSAGE_BODY = '{{t(' Do you want to save questions?')}}'
        let ERROR_MESSAGE_BODY = '{{t('The marks sum must equal 100')}}'
        let OK_TEXT = '{{t('Ok')}}'

        let DELETE_QUESTION_URL = "{{route('manager.term.delete-question')}}"

        let QUESTION_MARK = "{{t('Mark')}}"
        let QUESTION_TYPE = "{{t('Question type')}}"
        let QUESTION_SUBJECT = "{{t('Question Subject')}}"

        let questionsTypes = []
        @foreach($types as $type)
        questionsTypes.push({name:'{{$type['name']}}','value':'{{$type['value']}}'})
        @endforeach

        let questionsSubjects = []
        @foreach($subjects as $subject)
        questionsSubjects.push({name:'{{t($subject->name)}}','value':'{{$subject->id}}','mark':'{{$subject->mark}}'});
        @endforeach

        let PLEASE_SELECT_TYPE = "{{t('Please select the question type')}}"
        let PLEASE_ADD_MARK = "{{t('Please add question mark from 0-100')}}"
        let MAX_MARKS = "{{t('The max marks sum must equal 100')}}"
        let SUBJECT_MARKS_SUM_MESSAGE = "{{t('The sum of the marks for each subject must equal 25')}}"


        let QUESTIONS_COUNT = {{$questions?count($questions):0}};
       // let LAST_QUESTION_INDEX  = {{$questions?(count($questions)-1):0}}; //last index for saved questions
    </script>

    <script src="{{asset('assets_v1/js/manager/questions_management/questions_structure.js')}}"></script>
@endsection
