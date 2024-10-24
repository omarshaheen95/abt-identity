@extends('manager.layout.container', compact(['container_type']))
@section('style')
    <style>
        .error {
            color: red;
        }
    </style>
@endsection
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.students_files_import.index')}}" class="text-muted">
            {{t('Student Data Files')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('filter')
    <div class="row">
        <div class="col-3 mb-2">
            <label class="mb-1">{{t('Row Num')}}:</label>
            <input type="number" min="0" name="row_num" class="form-control direct-search"
                   placeholder="{{t('Row Num')}}"/>
        </div>
    </div>

@endsection
@section('actions')
    <button type="button" class="btn btn-success save_rows run-indicator" id="save-form-data">
        <span class="indicator-label">{{t('Save')}}</span>
        <span class="indicator-progress">{{t('Please wait...')}} <span
                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>

    <button type="button" class="btn btn-warning d-none checked-visible" id="delete_rows">
        <span>
            <i class="la la-trash"></i>
            <span>{{t('Delete')}}</span>
        </span>
    </button>

@endsection

@section('content')
    <form action="{{route('manager.students_files_import.save_logs')}}" method="POST" id="form-data">
        {{csrf_field()}}
        <input type="hidden" name="student_data_file_id" value="{{$studentDataFile->id}}">
        <input type="hidden" name="delegate_id" id="{{$studentDataFile->delegate_id}}">
        <table class="table table-row-bordered table-bordered gy-5" id="datatable">
            <thead>
            <tr class="fw-semibold fs-6 text-gray-800">
                <th class="text-start"></th>
                <th class="text-start">{{t('R N')}}</th>
                <th class="text-start">{{t('Data')}}</th>
                <th class="text-start">{{t('Errors')}}</th>
            </tr>
            </thead>
        </table>
        {{--        add action button to save the rows--}}
        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-primary save_rows run-indicator" id="save_rows">
                    <span class="indicator-label">
        {{t('Save')}}
    </span>
                    <span class="indicator-progress">
        {{t('Please wait...')}} <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
    </span>
                </button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    {{--    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>--}}
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\StudentImportLogDataRequest::class, '#form-data'); !!}

    <script>
        var DELETE_URL = "{{route('manager.students_files_import.delete_logs')}}";
        var TABLE_URL = "{{route('manager.students_files_import.show_logs', $studentDataFile->id)}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'row_number', name: 'row_number'},
            {data: 'data', name: 'data'},
            {data: 'errors', name: 'errors'},
        ];

        //add class data to 3rd column
        COLUMN_DEFS = [{className: 'data', targets: [2]}]

        const SAVE_URL = "{{route('manager.students_files_import.save_logs')}}";


    </script>
    <script src="{{asset('assets_v1/js/custom_datatable.js')}}?v={{time()}}"></script>



    <script>
        @php
            function translateMessage($value) {
                return t($value);
            }
        @endphp

        $(document).on('keyup','.remove_spaces',function () {
            let value = $(this).val()
            if (value){
                value = value.replace(/\u00A0/g, ' ').replace('  ', ' ').trim();
                $(this).val(value)
            }
        })

        $(document).ready(function () {

            //select2
            $('[data-control="select2"]').select2({
                allowClear: $(this).data('allow-clear') || false,
                placeholder: $(this).data('placeholder') || '',
            });

            $(document).on('click', '.save_rows', function () {
                var ele = $('.run-indicator');
                let form = $('#form-data');
                ele.attr("data-kt-indicator", "on");
                ele.attr('disabled', true);
                if (form.valid()) {
                    // Submit the form programmatically
                    form.submit()
                }else{
                    $('.save_rows').removeAttr('disabled');
                    $('.save_rows').removeAttr("data-kt-indicator");
                }
            });



        });

    </script>
@endsection
