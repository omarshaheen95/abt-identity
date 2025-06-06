@extends('school.layout.container')
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
            @if(request('type')=='corrected')
            <li><a class="dropdown-item " href="#!" onclick="excelExport('{{route('school.reports.pdfCertificates')}}')">{{t('Export Certificates')}}</a></li>
            @endif
        </ul>
    </div>

@endsection
@section('filter')

        <div class="col-3 mb-2">
        <div class="form-group">
            <label class="mb-1">{{t('Student name')}}:</label>
            <input type="text" class="form-control direct-search" id="" value="" name="student_name" placeholder="{{t('Student name')}}">
        </div>
    </div>


        <div class="col-3 mb-2">
        <div class="form-group">
            <label class="mb-1">{{t('Student ID Number')}}:</label>
            <input type="text" class="form-control direct-search" id="" value="" name="student_id_number" placeholder="{{t('Student ID Number')}}" >
        </div>
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

        <div class="col-lg-3 mb-2">
            <label>{{t('Class Name')}} :</label>
            <select name="grade_name[]" id="class_name" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Class Name')}}" multiple data-allow-clear="true">
            </select>
        </div>

        <div class="col-3 mb-2">
            <label class="mb-1">{{t('Levels')}}:</label>
            <select class="form-control form-select direct-value" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select School')}}" multiple name="level_id" id="levels_id">
                <option></option>
            </select>
        </div>
        <div class="col-3 mb-2">
            <label class="mb-1">{{t('Round')}}:</label>
            <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select One')}}" name="round">
                <option></option>
                <option value="september">{{t('September')}}</option>
                <option value="february">{{t('February')}}</option>
                <option value="may">{{t('May')}}</option>
            </select>
        </div>
        <input type="hidden" name="corrected" value="{{request('type')=='corrected'?1:2}}">

@endsection
@section('content')

    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Username')}}</th>
            <th class="text-start">{{t('Grade Name')}}</th>
            <th class="text-start">{{t('Assessment')}}</th>
            <th class="text-start">{{t('Round')}}</th>
            <th class="text-start">{{t('Year')}}</th>
            @if(request('type')=='corrected')
                <th class="text-start">{{t('Total')}}</th>
            @endif
            <th class="text-start">{{t('Status')}}</th>
            @if(request('type')=='corrected')
            <th class="text-start">{{t('Actions')}}</th>
            @endif
        </tr>
        </thead>
    </table>
@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('school.student.delete-student')}}";
        var EXPORT_URL = '{{route('school.student.student-export')}}';
        var TABLE_URL =  "{{route('school.students-terms',['type'=>request()['type']])}}"


        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'student.id_number', name: 'student_id'},
            {data: 'student.name', name: 'name'},
            {data: 'student.email', name: 'email'},
            {data: 'student.grade_name', name: 'grade'},
            {data: 'term', name: 'term'},
            {data: 'round', name: 'round'},
            {data: 'year', name: 'year'},
            @if(request('type')=='corrected')
            {data: 'total', name: 'total'},
            @endif
            {data: 'status', name: 'status'},
            @if(request('type')=='corrected')
            {data: 'actions', name: 'actions'},
            @endif
        ];
        var CREATED_ROW = function(row, data, dataIndex) {
            if (data.student.sen) {
                $(row).css('background-color','#ffacac');
            }
            if (data.student.g_t) {
                $(row).css('background-color','#fff0b0');
            }
        }
        $('#year_id').on('change',function () {
            let year_id = $(this).val()
            $.ajax({
                url: '{{route('school.get-sections')}}',
                data: {
                    year_id: year_id,
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



@endsection
