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
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{ route(getGuard().".reports.pdfReports") }}')">{{t('Students Bulk Reports')}}</a></li>
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{ route(getGuard().".reports.pdfReportsCards") }}')">{{t('Students Bulk Reports Cards')}}</a>
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.student.export_student_standards')}}')">{{t('Standards Excel')}}</a></li>
            @endcan
            @can('export students cards')
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="cardsExport()">{{t('Cards')}}</a></li>
                <li><a class="dropdown-item not-deleted-students" href="#!" onclick="excelExport('{{ route("manager.student.students-cards-by-section") }}')">{{t('Cards By Section')}}</a>
            @endcan
            <li><a class="dropdown-item d-none checked-visible" href="#!" onclick="autoOpenTime()">{{t('Open Time Assessment')}}</a></li>
            <li id="restore-students" class="d-none"><a class="dropdown-item" href="#!" onclick="restore()">{{t('Restore Students')}}</a></li>

            @can('transfer students')
                <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#transfer-modal">{{t('Transfer students')}}</a></li>
            @endcan

            @can('delete students')
                <li id="li_delete_rows"><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>


@endsection

@section('filter')
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('ID')}}:</label>
        <input type="text" name="id" class="form-control direct-search" placeholder="E.g: 4590"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Student Id Number')}}:</label>
        <input type="text" name="id_number" class="form-control" placeholder="{{t('Student Id Number')}}"
               data-col-index="0"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Name')}}:</label>
        <input type="text" name="name" class="form-control direct-search" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Email')}}:</label>
        <input type="text" name="email" class="form-control datatable-input"
               placeholder="{{t('Email')}}" data-col-index="1"/>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('School')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select School')}}" name="school_id">
            <option></option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach

        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Year')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}">{{$year->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Levels')}}:</label>
        <select class="form-select direct-value" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Level')}}" multiple name="level_id[]" id="levels_id">
        </select>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Class Name')}} :</label>
        <select  id="class_name" name="class_name[]" class="form-select direct-value" data-control="select2" data-placeholder="{{t('Select Class Name')}}" data-allow-clear="true" multiple="multiple">
        </select>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Registration Date')}} :</label>
        <input autocomplete="disabled" class="form-control form-control-solid" name="registration_date" value="" placeholder="{{t('Pick date rage')}}" id="registration_date"/>
        <input type="hidden" name="start_date" id="start_registration_date" />
        <input type="hidden" name="end_date" id="end_registration_date" />
    </div>


    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Order By')}} :</label>
        <select name="orderBy" id="orderBy" class="form-select" data-control="select2" data-placeholder="{{t('Select Type')}}" >
            <option value="latest" selected>{{t('Latest')}}</option>
            <option value="name">{{t('Name')}}</option>
            <option value="level">{{t('Level')}}</option>
            <option value="section">{{t('Section')}}</option>
        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Sen Student')}}:</label>
        <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="sen" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('SEN Student')}}</option>
            <option value="2">{{t('Normal Student')}}</option>
        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Citizen')}}:</label>
        <select class="form-control form-select " data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Type')}}" name="citizen" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Citizen')}}</option>
            <option value="2">{{t('Non-Citizen')}}</option>
        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Students Status')}}:</label>
        <select class="form-control form-select reset-no" data-hide-search="true" data-control="select2" data-placeholder="{{t('Select Student Status')}}" name="deleted_at" id="students_status">
            <option value="1" selected>{{t('Not Deleted Students')}}</option>
            @can('show deleted students')
                <option value="2">{{t('Deleted Students')}}</option>
            @endcan
        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
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
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('School')}}</th>
            <th class="text-start">{{t('Level')}}</th>
            <th class="text-start">{{t('Class Name')}}</th>
            <th class="text-start">{{t('Last Login')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

    @can('transfer students')
    <div class="modal fade" tabindex="-1" id="transfer-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Transfer Students')}}</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="col-12 mb-4">
                        <label class="form-label">{{t('School')}} :</label>
                        <select id="transfer_student_school" class="form-select" data-control="select2" data-placeholder="{{t('Select School')}}" data-allow-clear="true">
                            <option></option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-4">
                        <label class="form-label">{{t('Suffix for transferred students')}} :</label>
                        <input type="text" id="transfer_from_suffix" class="form-control" placeholder="{{t('e.g. AA')}}">
                        <span class="text-muted fs-7">{{t('Appended to class of students being transferred. e.g. 3G → 3G - AA')}}</span>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{t('Suffix for destination school students')}} :</label>
                        <input type="text" id="transfer_to_suffix" class="form-control" placeholder="{{t('e.g. BB')}}">
                        <span class="text-muted fs-7">{{t('Appended to class of students already in destination school. e.g. 3G → 3G - BB')}}</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{t('Close')}}</button>
                    <button type="button" class="btn btn-primary" onclick="transferStudents()">{{t('Transfer Students')}}</button>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection
@section('script')
    <script>

        var DELETE_URL = "{{route('manager.student.delete')}}";
        var TABLE_URL = "{{route('manager.student.index')}}";
        {{--var EXPORT_URL = '{{route('manager.student.student-export')}}';--}}
        {{--var STUDENT_MARKS_EXPORT_URL = '{{route('manager.student.student-marks-export')}}';--}}

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
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
                $('#restore-students').addClass('d-none')
            }else {
                //hide actions for not deleted students
                $('.not-deleted-students').addClass('d-none')
                // //hide delete button
                $('#li_delete_rows').addClass('d-none')
                $('#restore-students').removeClass('d-none')

            }
            table.DataTable().draw(true);
        })

        //restore students
        function restore(id = null) {
            let data = {
                '_token': '{{ csrf_token() }}'
            };

            if (!id) {
                id = [];
                $("table input:checkbox:checked").each(function () {
                    id.push($(this).val());
                });
                data['id'] = id;

                let school_id = $('select[name="school_id"]').val();
                if (id.length <= 0 && !school_id) {
                    toastr.error('{{ t('School is required') }}');
                    return;
                } else {
                    data['school_id'] = school_id;
                }

                let year_id = $('select[name="year_id"]').val();
                if (id.length <= 0 && !year_id) {
                    toastr.error('{{ t('Year is required') }}');
                    return;
                } else {
                    data['year_id'] = year_id;
                }
            }else {
                data['id']=id;
            }

            Swal.fire({
                title: '{{ t('Are you sure?') }}',
                text: '{{ t('Do you want to restore the selected students?') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ t('Yes, restore!') }}',
                cancelButtonText: '{{ t('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoadingModal()
                    $.ajax({
                        type: "POST",
                        url: '{{route('manager.student.restore')}}',
                        data: data,
                        success: function (result) {
                            hideLoadingModal()
                            Swal.fire({
                                icon: 'success',
                                title: '{{ t('Restored!') }}',
                                text: result.message,
                                //timer: 2000,
                                showConfirmButton: true
                            });
                            table.DataTable().draw(false);
                        },
                        error: function (error) {
                            hideLoadingModal()
                            Swal.fire({
                                icon: 'error',
                                title: '{{ t('Error') }}',
                                text: error.responseJSON?.message || '{{ t('Something went wrong') }}'
                            });
                        }
                    });
                }
            });
        }


        //open assessment
        function autoOpenTime() {
            var row_id = [];
            $("input:checkbox[name='rows[]']:checked").each(function () {
                row_id.push($(this).val());
            });

            $.ajax({
                type: "POST",
                url: '{{route('manager.student.open_term_time')}}', // get the route value
                data: {
                    '_token': '{{csrf_token()}}',
                    'row_id': row_id,
                },

                success: function (result) {
                    toastr.success(result.message);
                    table.DataTable().draw(false);

                },
                error: function (error) {
                    toastr.error(error.responseJSON.message)
                }
            })
        }

        //Transfer Students -----------------------------------------------
        let transferModal = $('#transfer-modal');
        transferModal.on('shown.bs.modal', function () {
            $('#transfer_student_school').select2({
                dropdownParent: transferModal,
                placeholder: '{{t('Select School')}}',
                allowClear: true
            });
        });
        function transferStudents() {
            let data = {}
            let frm_data = $('#filter').serializeArray();
            if (frm_data){
                $.each(frm_data, function (key, val) {
                    data[val.name] = val.value;
                });
            }
            let row_id = [];
            $("table input:checkbox:checked").each(function () {
                row_id.push($(this).val())
            });

            data['to_school_id'] = $('#transfer_student_school').val();
            data['row_id'] = row_id;
            data['_token'] = '{{csrf_token()}}';

            let fromSuffix = $('#transfer_from_suffix').val().trim();
            let toSuffix   = $('#transfer_to_suffix').val().trim();
            if (fromSuffix) data['from_suffix'] = fromSuffix;
            if (toSuffix)   data['to_suffix']   = toSuffix;

            if (data.to_school_id){
                transferModal.modal('hide');
                showLoadingModal()
                $.ajax({
                    type: "POST",
                    url: '{{route('manager.student.transfer')}}',
                    data: data,
                    success:function (result) {
                        hideLoadingModal()
                        $('#transfer_student_school').val('').trigger('change')
                        $('#transfer_from_suffix').val('')
                        $('#transfer_to_suffix').val('')
                        let d = result.data;
                        let msg = result.message + '<br>'
                            + '{{t('From')}}: ' + d.from_school + '<br>'
                            + '{{t('To')}}: '   + d.to_school   + '<br>'
                            + '{{t('Transferred')}}: ' + d.transferred + '<br>'
                            + '{{t('Updated in destination')}}: ' + d.updated_existing;
                        toastr.success(msg)
                        table.DataTable().draw(false);
                    },
                    error:function (error) {
                        hideLoadingModal()
                        transferModal.modal('show');
                        toastr.error(error.responseJSON.message)
                    }
                })
            }
        }
        //-----------------------------------------------------------------------
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/student.js')}}"></script>

@endsection
