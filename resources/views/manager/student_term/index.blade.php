@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('filter')
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Name')}}:</label>
        <input type="text" name="student_name" class="form-control direct-search" placeholder="{{t('Name')}}"
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
                <option value="{{$school->id}}" @if(request('school_id') && request('school_id')==$school->id) selected @endif>{{$school->name}}</option>
            @endforeach

        </select>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Year')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}" @if(request('year_id') && request('year_id')==$year->id) selected @endif>{{$year->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Grade')}} :</label>
        <select name="grade[]" multiple class="form-select direct-value" data-control="select2" data-allow-clear="true"
                data-placeholder="{{t('Select Grade')}}">
            @foreach(range(1,12) as $grade)
                <option value="{{ $grade }}" @if(request('grades') && in_array($grade,explode(',',request('grades')))) selected @endif>{{ $grade }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Level')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Level')}}" name="level_id" id="levels_id">
            <option></option>
        </select>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Round')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select One')}}" name="round">
            <option></option>
            @foreach(['september','february','may'] as $round)
                <option value="{{$round}}" @if(request('round') && request('round')== $round) selected @endif>{{t($round)}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-3 mb-2">
        <label>{{t('Submission Date')}} :</label>
        <input autocomplete="disabled" class="form-control form-control-solid" name="submit_date" value=""
               placeholder="{{t('Pick date rage')}}" id="submit_date"/>
        <input type="hidden" name="start_date" id="start_submit_date"/>
        <input type="hidden" name="end_date" id="end_submit_date"/>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Order By')}} :</label>
        <select name="orderBy" id="orderBy" class="form-select" data-control="select2" data-placeholder="{{t('Select Type')}}" >
            <option value="latest" selected>{{t('Latest')}}</option>
            <option value="name">{{t('Name')}}</option>
            <option value="level">{{t('Level')}}</option>
            <option value="section">{{t('Section')}}</option>
        </select>
    </div>

    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Students Terms Status')}}:</label>
        <select class="form-control form-select reset-no" data-control="select2" data-hide-search="true" data-placeholder="{{t('Select Student Terms Status')}}" name="deleted_at" id="terms_status">
            <option value="1" selected>{{t('Not Deleted Terms')}}</option>
            @can('show deleted students terms')
                <option value="2">{{t('Deleted Terms')}}</option>
            @endcan
        </select>
    </div>
    <input type="hidden" name="corrected" value="{{request('status')=='corrected'?1:2}}">
    <div class="col-lg-2 mb-2">
        <label>{{t('Student Duplicated')}} :</label>
        <select name="duplicated" id="duplicated" class="form-select" data-control="select2" data-placeholder="{{t('Select Student Type')}}" data-allow-clear="true">
            <option></option>
            <option value="0">{{t('All')}}</option>
            <option value="1">{{t('Duplicated')}}</option>
        </select>
    </div>
@endsection

@section('actions')
    <a class="btn btn-primary" onclick="correcting()">{{t('Correcting')}}
        <i class="fa fa-share ms-2"></i>
    </a>
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export students terms')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.student-term.export')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('auto correct students terms')
                <li id="li_correct_rows"><a class="dropdown-item text-success " href="#!" onclick="autoCorrect()">{{t('Auto Correct')}}</a></li>
            @endcan
            @can('delete students terms')
                <li id="li_delete_rows"><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
             <th class="text-start"></th>
             <th class="text-start">{{t('Name')}}</th>
             <th class="text-start">{{t('Email')}}</th>
             <th class="text-start">{{t('School')}}</th>
             <th class="text-start">{{t('Grade Name')}}</th>
             <th class="text-start">{{t('Year')}}</th>
             <th class="text-start">{{t('Round')}}</th>
            @if(request('status')=='corrected')
                <th class="text-start">{{t('Total')}}</th>
            @endif
            <th class="text-start">{{t('Status')}}</th>
             <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('manager.student.delete-student-term')}}";
        var TABLE_URL = "{{route('manager.student_term.index',['status'=>request('status')])}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'school', name: 'school'},
            {data: 'grade_name', name: 'grade_name'},
            {data: 'year', name: 'year'},
            {data: 'round', name: 'round'},
            @if(request('status')=='corrected')
            {data: 'total', name: 'total'},
            @endif
            {data: 'corrected', name: 'corrected'},
            {data: 'actions', name: 'actions'}
        ];
        var CREATED_ROW = function(row, data, dataIndex) {
            if (data.student.sen) {
                $(row).css('background-color','#ffacac');
            }
            if (data.student.g_t) {
                $(row).css('background-color','#fff0b0');
            }
        }
        $('#terms_status').on('change',function () {
            let value = $(this).val();
            if (value==='1'){ //Not deleted student
                // //show delete button
                $('#li_delete_rows').removeClass('d-none')
                $('#li_correct_rows').removeClass('d-none')
                if (!$('#delete_rows').hasClass('d-none')){
                    $('#delete_rows').addClass('d-none')
                }
            }else {
                // //hide delete button
                $('#li_delete_rows').addClass('d-none');
                $('#li_correct_rows').addClass('d-none');
            }
            table.DataTable().draw(true);
        })

        //restore students
        function autoCorrect() {
            let data = {
                '_token': '{{csrf_token()}}',
            }
            let row_id = [];
            $("input:checkbox[name='rows[]']:checked").each(function () {
                row_id.push($(this).val());
            });

            var frm_data = $('#filter').serializeArray();
            if (frm_data){
                $.each(frm_data, function (key, val) {
                    data[val.name] = val.value;
                });
            }
            data['row_id'] = row_id

            showLoadingModal();
            $.ajax({
                type: "POST",
                url: '{{route('manager.auto-correct-student-term')}}', // get the route value
                data: data,
                success:function (result) {
                    hideLoadingModal()
                    toastr.success(result.message)
                    table.DataTable().draw(false);
                },
                error:function (error) {
                    hideLoadingModal()
                    toastr.error(error.responseJSON.message)
                }
            })
        }

        //restore students
        function restore(id) {
            $.ajax({
                type: "POST",
                url: '{{route('manager.student-term-restore',':id')}}'.replace(':id',id), // get the route value
                data: {
                    '_token':'{{csrf_token()}}',
                },
                success:function (result) {
                    console.log(result)
                    console.log('{{route('manager.student-term-restore',['id'=>':id'])}}'.replace(':id',id))
                    toastr.success(result.message)
                    table.DataTable().draw(false);
                },
                error:function (error) {
                    toastr.error(error.responseJSON.message)
                }
            })
        }

        //open many correcting pages in blank
        function correcting() {
            let ids = [];
            let route = "{{route('manager.student_term.edit',':id')}}";
            let checked_rows = [];
            $("input:checkbox[name='rows[]']:checked").each(function () {
                checked_rows.push($(this).val());
            });
            //check if some rows is selected
            if (checked_rows.length > 0) {
                ids = checked_rows;
            } else {
                //if not get all rows
                $("input:checkbox[name='rows[]']").each(function () {
                    ids.push($(this).val());
                });
            }
            ids.forEach((id,index) => {
                let url = route.replace(':id', id);
                let page = window.open(url, '_blank');
                if (!page || page.closed || typeof page.closed == 'undefined') {
                    alert('{{t('Pop-up is blocked! Please allow pop-ups and redirects in your browser settings to open all students assessments pages.')}}');
                    return false;
                }
            });
        }
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>
    <script>
        initializeDateRangePicker('submit_date');
    </script>

@endsection
