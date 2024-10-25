@extends('manager.layout.container')

@section('title')
    {{$title}} / {{$term->name}}

@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.term.index')}}" class="text-muted">
            {{t('Assessments')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$term->name}}
    </li>
@endpush
@section('content')

    <div class="mb-5">
        <div class="d-grid">
            <ul class="nav nav-tabs flex-nowrap text-nowrap">
                @foreach($subjects as $subject)
                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0 @if($loop->index == 0) active @endif"
                           data-bs-toggle="tab" href="#kt_tab_pane_{{$subject->id}}">{{$subject->name}}</a>
                    </li>
                @endforeach

            </ul>
        </div>
    </div>

    <div class="tab-content" id="myTabContent">
        @foreach($subjects as $subject)
            <div class="tab-pane fade {{$loop->index==0?'show active':''}}" id="kt_tab_pane_{{$subject->id}}"
                     role="tabpanel">
                    <form id="questions_form{{$subject->id}}" class="questions-form" method="POST"
                          action="{{route('manager.term.update-questions',['id'=>request()['id']])}}"
                          enctype="multipart/form-data">
                        @csrf
                        @foreach($questions[$subject->id] as $question)
                            @include('manager.term.questions_types.'.$question->type)
                        @endforeach
                        <button type="submit" class="btn btn-primary">{{t('Save')}}</button>
                    </form>
                </div>


        @endforeach
    </div>

@endsection
@section('script')

    <script>
        let IMAGE = '{{t('Image')}}';
        let OPTION = '{{t('Option')}}';
        let ANSWER = '{{t('Answer')}}';
        let DELETE = '{{t('Delete')}}';
        let OK = '{{t('OK')}}';
        let DELETE_MESSAGE_TITLE = '{{t('Delete')}}';
        let DELETE_MESSAGE_BODY = '{{t('Do you want to delete this option?')}}';
        var T_blankFieldTitle = '{{t('Delete Blank Field')}}',
            T_blankFieldContent = '{{t('Do you want this field?')}}',
            T_blankFieldErrorMassage = '{{t('It cannot be deleted, because the number of blanks is equal to the number of blank input fields.')}}';


        {{--let SUBMIT_FORM_URL = "{{route('manager.term.delete-question-option')}}"--}}
        let DELETE_OPTION_URL = "{{route('manager.term.delete-question-option')}}"
        let DELETE_QUESTION_FILE_URL = "{{route('manager.term.delete-question-file')}}"
        let DELETE_OPTION_IMAGE_URL = "{{route('manager.term.delete-option-image')}}"

    </script>
    <script src="{{asset('assets_v1/js/manager/questions_management/questions.js')}}"></script>
    <script src="{{asset('assets_v1/js/jquery-validation/dist/jquery.validate.js')}}"></script>
    <script src="{{asset('assets_v1/js/jquery-validation/dist/additional-methods.js')}}"></script>
    @if(app()->getLocale()=='ar')
        <script src="{{asset('assets_v1/js/jquery-validation/dist/localization/messages_ar.js')}}"></script>
    @endif
    <script src="{{asset('assets_v1/js/manager/questions_management/fill_blank_question.js')}}"></script>

    <script>

    </script>
@endsection
