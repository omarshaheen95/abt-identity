@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.students_files_import.index')}}" class="text-muted">
            {{t('Student Import Files')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <div class="row">
        <form class="kt-form kt-form--fit mb-15" id="upload_students_file"
              method="POST" action="{{route('manager.students_files_import.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Select School')}}</label>
                <div class="col-6">
                    <select name="school_id"  class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select School')}}">
                        <option value="" disabled selected>{{t('Select School')}}</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Select Year')}}</label>
                <div class="col-6">
                    <select name="year_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select Year')}}">
                        <option></option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Select the file containing the students')}}</label>
                <div class="col-6">
                    <input type="file" class="form-control" name="students_file">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Update Students in file')}}</label>
                <div class="col-6">
                    <div class="form-check form-check-custom form-check-solid form-check-lg mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="update" id="flexCheckboxLg"/>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <h5 class="m-0 mr-2">{{t('Example File')}} :
                </h5>
                <a target="_blank" href="{{asset('Examples Sheets/Import Users Example.xlsx')}}?v={{time()}}"
                   class="btn btn-link text-primary ms-1 text-decoration-underline">{{t('Download')}}</a>
            </div>
            <div class="d-flex align-items-center my-8">
                <h5 class="m-0 mr-2">{{t('Note')}}: </h5>
                <p class="m-0">{{$note}}</p>
            </div>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button onclick="$('#upload_students_file').submit()" type="submit" class="btn btn-primary mr-2">{{t('Import')}}</button>
                </div>
            </div>

        </form>



    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ImportStudentRequest::class, '#upload_students_file'); !!}
@endsection
