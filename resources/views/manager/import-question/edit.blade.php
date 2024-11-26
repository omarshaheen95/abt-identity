@extends(getGuard().'.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route(getGuard().'.question-file.index')}}" class="text-muted">
            {{t('Student Import Files')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <div class="row">
        <form class="kt-form kt-form--fit" id="upload_students_file"
              method="POST" action="{{route(getGuard().'.question-file.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Select the file containing the students')}}</label>
                <div class="col-6">
                    <input type="file" class="form-control" name="file">
                </div>
            </div>

            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Year')}} : </label>
                <div class="col-6">
                    <select name="year_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select Year')}}">
                        <option></option>
                        @foreach(\App\Models\Year::get() as $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Level')}} : </label>
                <div class="col-6">
                    <select name="level_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select Level')}}">
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="form-group row mb-2">
                <label class="col-3 col-form-label">{{t('Assessment')}} : </label>
                <div class="col-6">
                    <select name="term_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select Assessment')}}">
                        <option></option>
                        @isset($terms)
                            @foreach($terms as $term)
                                <option
                                        value="{{$term->id}}">{{$term->name}}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <h5 class="m-0 mr-2">{{t('Example File')}} :
                </h5>
                <a target="_blank" href="{{asset('Examples Sheets/Questions Sample.xlsx')}}?v={{time()}}"
                   class="btn btn-link text-primary ms-1 text-decoration-underline">{{t('Download')}}</a>
            </div>

            <h5 class="m-0 mb-4">{{t('Subjects')}} :
            </h5>
            <table class="table table-separate table-bordered text-center">
                <thead class="bg-gray-300">
                <tr>
                    <td>{{t('Subject')}}</td>
                    <td>{{t('Subject ID')}}</td>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\Models\Subject::all() as $subject)
                 <tr>
                     <td>{{t($subject->name)}}</td>
                     <td>{{t($subject->id)}}</td>
                 </tr>
                @endforeach
                </tbody>
            </table>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button id="btn_submit" type="button" class="btn btn-primary mr-2">{{t('Import')}}</button>
                </div>
            </div>

        </form>


    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ImportQuestionFileRequest::class, '#upload_students_file'); !!}
    <script>
        $(document).ready(function () {
            getAndSetDataOnSelectChange('year_id','level_id','{{route('manager.get-levels-by-year',':id')}}')
            getAndSetDataOnSelectChange('level_id','term_id','{{route('manager.get-terms-by-level',':id')}}')

            let form = $('#upload_students_file');
            $('#btn_submit').on('click', function (e) {
                e.preventDefault()
                form.validate()
                if (form.valid()) {
                    showLoadingModal()
                    form.submit();
                }
            })
        })

    </script>
@endsection
