@extends('manager.layout.container')
@section('title', $title)

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush

@section('filter')
    <div class="row">
        <div class="col-3 mb-2">
            <label class="mb-1">{{t('Name')}}:</label>
            <input type="text" name="name" class="form-control" placeholder="{{t('Name')}}"/>
        </div>

        <div class="col-lg-3  mb-2">
            <label>{{t('School')}}:</label>
            <select name="school_id" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select School')}}" data-allow-clear="true">
                <option></option>
                @foreach($schools as $school)
                    <option value="{{$school->id}}">{{$school->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3  mb-2">
            <label>{{t('Process Type')}}:</label>
            <select name="process_type" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Type')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                @foreach($process_types as $type)
                    <option value="{{$type}}">{{t($type)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3  mb-2">
            <label>{{t('Status')}}:</label>
            <select name="status" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Status')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                @foreach($statuses as $status)
                    <option value="{{$status}}">{{t($status)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3  mb-2">
            <label>{{t('Has Logs')}}:</label>
            <select name="has_logs" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Type')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Yes')}}</option>
                <option value="0">{{t('No')}}</option>
            </select>
        </div>
        <div class="col-lg-3  mb-2">
            <label>{{t('Has Errors')}}:</label>
            <select name="has_errors" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Type')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Yes')}}</option>
                <option value="0">{{t('No')}}</option>
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Creation Date')}} :</label>
            <input autocomplete="disabled" class="form-control form-control-solid" name="create_date" value="" placeholder="{{t('Pick date rage')}}" id="create_date"/>
            <input type="hidden" name="start_date" id="start_create_date" />
            <input type="hidden" name="end_date" id="end_create_date" />
        </div>
    </div>

@endsection

@section('actions')
    @can('import students')
        <a href="{{route('manager.students_files_import.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Import File')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('delete students import')
                <li><a class="dropdown-item text-danger d-none checked-visible delete_row" href="#!">{{t('Delete')}}</a>
                </li>
            @endcan
        </ul>
    </div>

@endsection
@section('content')
    <table class="table table-row-bordered table-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">{{t('School')}}</th>
            <th class="text-start">{{t('Statistics')}}</th>
            <th class="text-start">{{t('Logs')}}</th>
            <th class="text-start">{{t('Process')}}</th>
            <th class="text-start">{{t('Creation Date')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>
    <div class="modal fade" tabindex="-1" id="delete_file_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Delete')}}</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                         aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <p>{{t('are sure of the deleting process ?')}}</p>
                    <div class="col-lg-12  d-flex align-items-center p-0">
                        <p class="m-0 p-0"
                           style="font-weight: normal;font-size: 14px">{{t('Delete students when deleting the import file')}}</p>
                        <div class="form-check form-check-custom form-check-solid mx-2">
                            <input id="delete_students" class="form-check-input" type="checkbox" value="1"
                                   name="delete_students"/>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btn_close" type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{t('Close')}}</button>
                    <button type="button" class="btn btn-danger" onclick="deleteRows()">{{t('Delete')}}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        var TABLE_URL = "{{route('manager.students_files_import.index')}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'file_info', name: 'file_info'},
            {data: 'statistics', name: 'statistics'},
            {data: 'logs_info', name: 'logs_info'},
            {data: 'process', name: 'process'},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions'}
        ];
        @can('delete students import')
        let id = null; //id for deleted row

        $(document).on('click', '.delete_row', (function (e) {
            e.preventDefault();
            id = $(this).data('id')
            $('#delete_file_modal').modal('show')
        }))

        function deleteRows() {
            let row_id = [];
            if (!id) {
                $("input:checkbox[name='rows[]']:checked").each(function () {
                    row_id.push($(this).val());
                });
            } else {
                row_id.push(id);
            }

            let request_data = {
                'row_id': row_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
                '_method': 'DELETE',
            }

            if ($('#delete_students').is(':checked')) {
                $('#delete_students').prop('checked', false)
                request_data['delete_students'] = true
            }

            $.ajax({
                type: "POST",
                url: "{{route('manager.students_files_import.delete')}}",
                data: request_data, //set data
                success: function (result) {
                    console.log(result)
                    $('.group-checkable').prop('checked', false);
                    checkedVisible(false)
                    table.DataTable().draw(false);
                    Swal.fire("", result.message, "success")
                    table.DataTable().draw(false);
                },
                error: function (error) {
                    Swal.fire("", data.message, "error")
                }
            })
            id = null
            $('#btn_close').trigger('click')
        }
        @endcan
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}"></script>
    <script>
        initializeDateRangePicker('create_date')
    </script>
@endsection
