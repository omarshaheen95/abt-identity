@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush

@section('actions')
    @can('add students')
        <a href="{{route('manager.student.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create Student')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export students')
                <li><a class="dropdown-item " href="#!" onclick="excelExport('{{route('manager.student.student-export')}}')">{{t('Export Students')}}</a></li>
            @endcan

            @can('export students marks')
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{route('manager.student.student-marks-export')}}')">{{t('Export Student Marks')}}</a></li>
            @endcan
            @can('export students cards')
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="cardsExport()">{{t('Cards')}}</a></li>
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{ route("manager.student.students-cards-by-section") }}')">{{t('Cards By Section')}}</a>
            @endcan

            @can('delete students')
                <li id="li_delete_rows"><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
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
        <select class="form-select direct-value" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Level')}}" multiple name="level_id[]" id="levels_id">
        </select>
    </div>

    <div class="col-lg-3 mb-2">
        <label>{{t('Class Name')}} :</label>
        <select  id="class_name" name="class_name[]" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Class Name')}}" data-allow-clear="true" multiple="multiple">
        </select>
    </div>

    <div class="col-lg-3 mb-2">
        <label>{{t('Registration Date')}} :</label>
        <input autocomplete="disabled" class="form-control form-control-solid" name="registration_date" value="" placeholder="{{t('Pick date rage')}}" id="registration_date"/>
        <input type="hidden" name="start_date" id="start_registration_date" />
        <input type="hidden" name="end_date" id="end_registration_date" />
    </div>


    <div class="col-2 mb-2">
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
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Students Status')}}:</label>
        <select class="form-control form-select reset-no" data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="deleted_at" id="students_status">
            <option value="1" selected>{{t('Not Deleted Students')}}</option>
            @can('show deleted students')
                <option value="2">{{t('Deleted Students')}}</option>
            @endcan
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


@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('School')}}</th>
            <th class="text-start">{{t('Level')}}</th>
            <th class="text-start">{{t('Class Name')}}</th>
            <th class="text-start">{{t('Last Login')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

@endsection
@section('script')
    <script>

        var DELETE_URL = "{{route('manager.student.delete')}}";
        var TABLE_URL = "{{route('manager.student.index')}}";
        {{--var EXPORT_URL = '{{route('manager.student.student-export')}}';--}}
        {{--var STUDENT_MARKS_EXPORT_URL = '{{route('manager.student.student-marks-export')}}';--}}

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'sid', name: 'student_id'},
            {data: 'name', name: 'name'},
            {data: 'school', name: 'school'},
            {data: 'level', name: 'level'},
            {data: 'grade_name', name: 'grade_name'},
            {data: 'last_login', name: 'last_login'},
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


            let url = "{{route('manager.student.student-cards-export')}}"+'?'+filterForm.serialize();
            window.open(url, "_blank");
        }

        $('#students_status').on('change',function () {
            let value = $(this).val();
            if (value==='1'){ //Not deleted student
                //show actions for not deleted students
                $('.not-deleted-students').removeClass('d-none')
                // //show delete button
                $('#li_delete_rows').removeClass('d-none')
            }else {
                //hide actions for not deleted students
                $('.not-deleted-students').addClass('d-none')
                // //hide delete button
                $('#li_delete_rows').addClass('d-none')

            }
            table.DataTable().draw(true);
        })

        //restore students
       function restore(id) {
           $.ajax({
               type: "POST", //we are using GET method to get data from server side
               url: '{{route('manager.student.student-restore',':id')}}'.replace(':id',id), // get the route value
               data: {
                   '_token':'{{csrf_token()}}'
               },
               success:function (result) {
                   toastr.success(result.message)
                   table.DataTable().draw(false);
               },
               error:function (error) {
                   toastr.error(error.responseJSON.message)
               }
           })
       }

    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/student.js')}}"></script>

@endsection
