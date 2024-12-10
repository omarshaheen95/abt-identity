@extends('school.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('filter')
    <div class="col-lg-3 mb-2">
        <label>{{t('ID Number')}}:</label>
        <input type="text" name="id_number" class="form-control kt-input" placeholder="{{t('Student Id Number')}}">
    </div>

    <div class="col-lg-3  mb-2">
        <label>{{t('Student Name')}}:</label>
        <input type="text"  name="name" class="form-control direct-search" placeholder="{{t('Student Name')}}">
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Username')}}:</label>
        <input type="text"  name="email" class="form-control direct-search" placeholder="{{t('Username')}}">
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Registration Date')}} :</label>
        <input autocomplete="disabled" class="form-control form-control-solid" value="" placeholder="{{t('Pick date rage')}}" id="date_range_picker"/>
        <input type="hidden" name="start_registration_date" id="start_date_range_picker" />
        <input type="hidden" name="end_registration_date" id="end_date_range_picker" />
    </div>

    <div class="col-lg-3 mb-2">
        <label>{{t('Year')}} :</label>
        <select name="year_id" id="year_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
            <option></option>
            @foreach($years as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Levels')}} :</label>
        <select name="level_id[]" id="levels_id" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Level')}}" multiple data-allow-clear="true">
        </select>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Class Name')}} :</label>
        <select name="class_name[]" id="class_name" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Class Name')}}" multiple data-allow-clear="true">
        </select>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Order By')}} :</label>
        <select name="orderBy" id="orderBy" class="form-select" data-control="select2" data-placeholder="{{t('Select Type')}}" >
            <option value="latest" selected>{{t('Latest')}}</option>
            <option value="name">{{t('Name')}}</option>
            <option value="level">{{t('Level')}}</option>
            <option value="section">{{t('Section')}}</option>
            <option value="arab">{{t('Arab Status')}}</option>
        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Sen Student')}}:</label>
        <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="sen" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('SEN Student')}}</option>
            <option value="2">{{t('Normal Student')}}</option>
        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Citizen')}}:</label>
        <select class="form-control form-select " data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Type')}}" name="citizen" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Citizen')}}</option>
            <option value="2">{{t('Non-Citizen')}}</option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <label class="mb-1">{{t('Arabs Status')}}:</label>
        <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('Arabs Status')}}" name="arab_status" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Arabs')}}</option>
            <option value="2">{{t('Non-Arabs')}}</option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <label class="mb-1">{{t('G&T')}}:</label>
        <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('G&T')}}" name="g_t" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Yes')}}</option>
            <option value="2">{{t('No')}}</option>
        </select>
    </div>
@endsection

@section('actions')
    <a href="{{route('school.student.create')}}" role="button" class="btn btn-primary">
        {{t('Add new student')}}
    </a>

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">

                <li><a class="dropdown-item " href="#!" onclick="excelExport('{{route('school.student.student-export')}}')">{{t('Export Students')}}</a></li>

                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{route('school.student.student-marks-export')}}')">{{t('Export Student Marks')}}</a></li>
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="cardsExport()">{{t('Cards')}}</a></li>
            <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{ route("school.student.students-cards-by-section") }}')">{{t('Cards By Section')}}</a>

            <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>

        </ul>
    </div>

@endsection
@section('content')

    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Level')}}</th>
            <th class="text-start">{{t('Grade Name')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

@endsection
@section('script')
    <script>

        {{--var DELETE_URL = "{{route('school.student.delete-student')}}";--}}
        var TABLE_URL = "{{route('school.student.index')}}";

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'sid', name: 'student_id'},
            {data: 'name', name: 'name'},
            {data: 'level', name: 'level'},
            {data: 'grade_name', name: 'grade_name'},
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
        function cardsExport(withQR=false) {
            let filterForm = $("#filter");
            $('input[name="qr-code"]').remove() //remove old inputs
            $('input[name="row_id[]"]').remove()

            //to export card with QR
            if (withQR){
                filterForm.append('<input type="hidden" name="qr-code" value=""/>')
            }

            $("table input:checkbox:checked").each(function () {
                let id = $(this).val();
                filterForm.append('<input type="hidden" name="row_id[]" value="'+id+'"/>')
            });
            let url = "{{route('school.student.student-cards-export')}}"+'?'+filterForm.serialize();
             window.open(url, "_blank");
        }

        $('#year_id').on('change',function () {
            let year_id = $(this).val()
            $.ajax({
                url: '/school/get-sections',
                data: {
                    id: year_id,
                },
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#class_name').empty();
                    $.each(data, function (key, value) {
                        $('#class_name').append(value);
                    });
                }
            });
        })
    </script>

    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/school/general.js')}}"></script>

    <script>
        initializeDateRangePicker();
    </script>

@endsection
