@extends('manager.layout.container')
@section('style')
    <script>
        window.onpageshow = function(event) {
            if (event.persisted) {
                // Code to be executed after the user navigates back
                // Add your script here
                window.location.reload();
            }
        };
    </script>
@endsection
@section('title',$title)


@section('actions')
    <a href="{{route('manager.abt-school-group-create')}}" class="btn btn-primary font-weight-bolder">
        <i class="la la-plus"></i>{{t('New School ABT Group')}}</a>
        <button onclick="assign()" class="btn btn-primary btn-elevate btn-icon-sm checked-visible d-none">
            <i class="la la-check"></i>{{t('Group Students')}}</button>
         <button onclick="deassign()" class="btn btn-warning btn-elevate btn-icon-sm checked-visible d-none">
              <i class="la la-times"></i>{{t('Ungroup Students')}}</button>

{{--    <div class="dropdown" id="actions_dropdown">--}}
{{--        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--            {{t('Actions')}}--}}
{{--        </button>--}}
{{--        <ul class="dropdown-menu">--}}
{{--            <li id="li_delete_rows"><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>--}}
{{--        </ul>--}}
{{--    </div>--}}
@endsection


@section('filter')
    <div class="row">
        <div class="col-lg-2 mb-2">
            <label>{{t('SID Num')}}:</label>
            <input type="text" name="id_number" class="form-control direct-search" placeholder="{{t('Student Id Number')}}">
        </div>
        <div class="col-2 mb-2">
            <label class="mb-1">{{t('ABT Id Number')}}:</label>
            <input type="text" name="abt_id" class="form-control" placeholder="{{t('ABT Id Number')}}"
                   data-col-index="0"/>
        </div>
        <div class="col-2 mb-2">
            <label class="mb-1">{{t('Has ABT')}}:</label>
            <select name="has_abt_id" id="has_abt_id" class="form-select" data-control="select2" data-placeholder="{{t('Select type')}}" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Has ABT ID')}}</option>
                <option value="2">{{t('Has Not ABT ID')}}</option>
            </select>
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
            <label>{{t('School')}} :</label>
            <select name="school_id" id="school_id" class="form-select" data-control="select2" data-placeholder="{{t('Select School')}}" data-allow-clear="true">
                <option></option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 mb-2">
            <label>{{t('Year')}} :</label>
            <select name="year_id" id="year_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
                <option></option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Levels')}} :</label>
            <select name="level_id[]" id="levels_id" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Level')}}" multiple data-allow-clear="true">
            </select>
        </div>
        <div class="col-lg-2 mb-2">
            <label>{{t('Class Name')}} :</label>
            <select name="grade_name[]" id="class_name" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Class Name')}}" multiple data-allow-clear="true">
            </select>
        </div>

        <div class="col-lg-2 mb-2">
            <label>{{t('Gender')}} :</label>
            <select name="gender" id="gender" class="form-select" data-control="select2" data-placeholder="{{t('Select Gender')}}" data-allow-clear="true">
                <option></option>
                <option value="boy">{{t('Boy')}}</option>
                <option value="girl">{{t('Girl')}}</option>
            </select>
        </div>

        <div class="col-2 mb-2">
            <label class="mb-1">{{t('Sen Student')}}:</label>
            <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="sen" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('SEN Student')}}</option>
                <option value="2">{{t('Normal Student')}}</option>
            </select>
        </div>
        <div class="col-2 mb-2">
            <label class="mb-1">{{t('Citizen Student')}}:</label>
            <select class="form-control form-select " data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="citizen" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Citizen')}}</option>
                <option value="2">{{t('NonCitizen')}}</option>
            </select>
        </div>
        <div class="col-2 mb-2">
            <label class="mb-1">{{t('Type Student')}}:</label>
            <select class="form-control form-select " data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Type')}}" name="section" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Arabs')}}</option>
                <option value="2">{{t('Non-Arabs')}}</option>
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Registration Date')}} :</label>
            <input autocomplete="disabled" class="form-control form-control-solid" name="registration_date" value="" placeholder="{{t('Pick date range')}}" id="registration_date"/>
            <input type="hidden" name="start_date" id="start_registration_date" />
            <input type="hidden" name="end_date" id="end_registration_date" />
        </div>
        <div class="col-lg-2 mb-2">
            <label>{{t('Order By')}} :</label>
            <select name="orderBy" id="orderBy" class="form-select" data-control="select2" data-placeholder="{{t('Select Type')}}" >
                <option value="latest" selected>{{t('Latest')}}</option>
                <option value="name">{{t('Name')}}</option>
                <option value="level">{{t('Level')}}</option>
                <option value="section">{{t('Section')}}</option>
                <option value="id_number">{{t('Student ID')}}</option>
            </select>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{$title}}</li>
@endpush


@section('content')
    <div class="row">
                    <table class="table table-row-bordered gy-5" id="datatable">
                        <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th class="text-start"></th>
                            <th class="text-start">#</th>
                            <th class="text-start">{{t('ABT ID')}}</th>
                            <th class="text-start">{{t('Name')}}</th>
                            <th class="text-start">{{t('School')}}</th>
                            <th class="text-start">{{t('Level')}}</th>
                            <th class="text-start">{{t('Class name')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
@endsection


@section('script')

    <script>
        var CONFIRM_MESSAGE = "{{t('Are you sure you want to group these students?')}}";
        var CONFIRM_UNGROUP_MESSAGE = "{{t('Are you sure you want to ungroup these students?')}}";
        var CONFIRM_SUB_MESSAGE = "{{t('You can ungroup them again')}}";
        var CONFIRM_UNGROUP_SUB_MESSAGE = "{{t('You can group them again')}}";
        var DELETE_URL = "{{route('manager.student.delete')}}";
        var TABLE_URL = "{{route('manager.student.abt_students')}}";
        var COLUMN_DEFS =  [
            {
                targets: 1,
                render: function (data, type, full, meta) {

                    return '<div class="student-box" style="text-align: start">\n' +
                        '                                    <div class="content">\n' +
                        '                                        <div class="student-name text-center">' + full.id + '</div>\n' +
                        '                                        <div class="student-username text-center"><span class=" badge badge-primary">' + full.student_terms_count + '</span></div>\n' +
                        '                                    </div>\n' +
                        '                                </div>';
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {

                    return '<div class="student-box" style="text-align: start">\n' +
                        '                                    <div class="content">\n' +
                        '                                        <div class="student-name">' + full.name + '</div>\n' +
                        '                                        <div class="student-username">' + full.email + '</div>\n' +
                        '                                    </div>\n' +
                        '                                </div>';
                },
            }
            ]

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id_number', name: 'id_number'},
            {data: 'abt_id', name: 'abt_id'},
            {data: 'name', name: 'name'},
            {data: 'school', name: 'school'},
            {data: 'level', name: 'level'},
            {data: 'grade_name', name: 'grade_name'},
        ];

        // Assign record
        function assign() {
            let csrf = $('meta[name="csrf-token"]').attr('content');
            var user_id = [];
            $("input:checkbox[name='rows[]']:checked").each(function () {
                user_id.push($(this).val());
            });
            // Check checkbox checked or not
            if (user_id.length > 0) {
                Swal.fire({
                    title: CONFIRM_MESSAGE,
                    text: CONFIRM_SUB_MESSAGE,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: CONFIRM_TEXT,
                    cancelButtonText: CANCEL_TEXT,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        let request_data = {
                            'user_id': user_id,
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            '_method': 'POST',
                        }

                        $.ajax({
                            type: "POST",
                            url: "{{route('manager.student.student-link-with-abt-id')}}",
                            data:request_data , //set data
                        }).done(function (data) {
                            if(data.success)
                            {
                                $('.group-checkable').prop('checked', false);
                                checkedVisible(false)
                                table.DataTable().draw(false);
                                Swal.fire(
                                    "",
                                    data.message,
                                    "success"
                                )
                            }else{
                                Swal.fire(
                                    "",
                                    data.message,
                                    "error"
                                )
                            }
                        }).fail(function (error){
                            var errorData = JSON.parse(error.responseText);
                            Swal.fire(
                                "",
                                errorData.message,
                                "error"
                            )
                        });
                    }

                });
            }
        };

        // Deassign record
         function deassign() {
            let csrf = $('meta[name="csrf-token"]').attr('content');
            var user_id = [];
            // Read all checked checkboxes
             $("input:checkbox[name='rows[]']:checked").each(function () {
                 user_id.push($(this).val());
             });

            // Check checkbox checked or not
             if (user_id.length > 0) {
                 Swal.fire({
                     title: CONFIRM_UNGROUP_MESSAGE,
                     text: CONFIRM_UNGROUP_SUB_MESSAGE,
                     icon: "warning",
                     showCancelButton: true,
                     confirmButtonText: CONFIRM_TEXT,
                     cancelButtonText: CANCEL_TEXT,
                 }).then(function (result) {
                     if (result.isConfirmed) {
                         let request_data = {
                             'user_id': user_id,
                             '_token': $('meta[name="csrf-token"]').attr('content'),
                             '_method': 'POST',
                         }

                         $.ajax({
                             type: "POST",
                             url: "{{route('manager.student.student-unlink-with-abt-id')}}",
                             data:request_data , //set data
                         }).done(function (data) {
                             if(data.success)
                             {
                                 $('.group-checkable').prop('checked', false);
                                 checkedVisible(false)
                                 table.DataTable().draw(false);
                                 Swal.fire(
                                     "",
                                     data.message,
                                     "success"
                                 )
                             }else{
                                 Swal.fire(
                                     "",
                                     data.message,
                                     "error"
                                 )
                             }
                         }).fail(function (error){
                             var errorData = JSON.parse(error.responseText);
                             Swal.fire(
                                 "",
                                 errorData.message,
                                 "error"
                             )
                         });
                     }

                 });
             }
         };
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>

    <script src="{{asset('assets_v1/js/manager/models/student.js')}}"></script>



@endsection


