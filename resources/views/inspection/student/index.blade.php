@extends('inspection.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush

@section('actions')


    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
                <li><a class="dropdown-item " href="#!" onclick="excelExport('{{route('manager.student.student-export')}}')">{{t('Export Students')}}</a></li>

                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{route('manager.student.student-marks-export')}}')">{{t('Export Student Marks')}}</a></li>
{{--                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="cardsExport()">{{t('Cards')}}</a></li>--}}
{{--                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="cardsExport(true)">{{t('Cards QR')}}</a></li>--}}
        </ul>
    </div>


@endsection

@section('filter')
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Student Id Number')}}:</label>
        <input type="text" name="id_number" class="form-control" placeholder="{{t('Student Id Number')}}"
               data-col-index="0"/>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Name')}}:</label>
        <input type="text" name="name" class="form-control direct-search" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Email')}}:</label>
        <input type="text" name="email" class="form-control datatable-input"
               placeholder="{{t('Email')}}" data-col-index="1"/>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Grade Name')}}:</label>
        <input type="text" name="grade_name" class="form-control datatable-input"
               placeholder="{{t('Grade Name')}}" data-col-index="1"/>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('School')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select School')}}" name="school_id">
            <option></option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach

        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Year')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}">{{$year->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Levels')}}:</label>
        <select class="form-control form-select direct-value" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Levels')}}" multiple name="level_id[]" id="levels_id">
            <option></option>
        </select>
    </div>

@endsection
@section('content')

    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Username')}}</th>
            <th class="text-start">{{t('Level')}}</th>
            <th class="text-start">{{t('School')}}</th>
            <th class="text-start">{{t('Grade Name')}}</th>
            <th class="text-start">{{t('Year')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('inspection.student.delete-student')}}";
        var TABLE_URL = "{{route('inspection.student.index')}}";
        var EXPORT_URL = '{{route('inspection.student.student-export')}}';
        var STUDENT_MARKS_EXPORT_URL = '{{route('inspection.student.student-marks-export')}}';

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id_number', name: 'id_number'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'level', name: 'level'},
            {data: 'school', name: 'school'},
            {data: 'grade_name', name: 'grade'},
            {data: 'year', name: 'year'},
            {data: 'actions', name: 'actions'}
        ];
        var CREATED_ROW = function(row, data, dataIndex) {
            if (data.sen) {
                $(row).css('background-color','#ffacac');
            }
            if (data.g_t) {
                $(row).css('background-color','#fff0b0');
            }
        }
        $('#cards_export').click(function () {
            $("#search_form").attr("action",'{{ route('inspection.student.student-cards-export') }}')
            $('#search_form').submit();
        })

        $('#cards_qr_export').click(function () {
            $('#search_form').append('<input type="hidden" name="card-qr"/>')
            $("#search_form").attr("action",'{{ route('inspection.student.student-cards-export') }}')
            $('#search_form').submit();
        })

        $('#excel_export_student_marks').click(function (e) {
            e.preventDefault();
            var searchForm = $("#search_form");
            searchForm.attr("method", 'get');
            searchForm.attr("action", STUDENT_MARKS_EXPORT_URL)
            searchForm.submit();
            searchForm.attr("action", '');
        });

    </script>

    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>




@endsection
