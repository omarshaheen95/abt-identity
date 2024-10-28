@extends('manager.layout.container')

@section('title')
    {{$title}}

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
    <form id="questions_form" method="POST" action="{{route('manager.term.update-standards',['id'=>request()['id']])}}" enctype="multipart/form-data">
        @csrf
        <div class="mb-5">
            <div class="d-grid">
                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0 active"
                            data-bs-toggle="tab" href="#kt_tab_pane_0">{{$subjects[0]['name']}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                           data-bs-toggle="tab" href="#kt_tab_pane_1">{{$subjects[1]['name']}}</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                           data-bs-toggle="tab" href="#kt_tab_pane_2">{{$subjects[2]['name']}}</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                           data-bs-toggle="tab" href="#kt_tab_pane_3">{{$subjects[3]['name']}}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            @foreach($subjects as $subject)
                <div class="tab-pane fade {{$loop->index==0?'show active':''}}" id="kt_tab_pane_{{$loop->index}}" role="tabpanel">
                    @php
                      $questions_by_subject = $questions->where('subject',$subject['value'])
                     @endphp
                    @foreach($questions_by_subject as $question)
                        <input type="hidden" name="standards[{{ $question->id }}][mark]" value="{{$question->mark}}">
                        <div class="form-group row align-items-center">
                            <div class="col-lg-6 d-flex">
                                <label>{{t('Q')}} {{$loop->index+1}} : </label>
                                <p class="ms-1">{{$question->content}}</p>
                            </div>
                            <div class="col-lg-6">
                                <label>{{t('Standard')}} {{$loop->index+1}}:</label>
                                <input class="form-control" name="standards[{{ $question->id }}][standard]" value="{{ !is_null($question->question_standard) ? $question->question_standard->standard:'' }}" type="text">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>


        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
            </div>
        </div>

    </form>



@endsection
@section('script')
    <script>
        let IMAGE = '{{t('Image')}}';
        let OPTION = '{{t('Option')}}';
        let ANSWER = '{{t('Answer')}}';
        let DELETE = '{{t('Delete')}}';


        let DELETE_OPTION_URL = "{{route('manager.term.delete-question-option')}}"
        let DELETE_QUESTION_FILE_URL = "{{route('manager.term.delete-question-file')}}"
        let DELETE_OPTION_IMAGE_URL = "{{route('manager.term.delete-match-option-image')}}"

    </script>
    <script src="{{asset('assets_v1/js/manager/questions_management/questions.js')}}"></script>
@endsection
