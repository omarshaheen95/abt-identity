@extends(getGuard().'.layout.container')
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
            <label>{{t('Year')}}:</label>
            <select name="year_id" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Year')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                @foreach(\App\Models\Year::get() as $year)
                    <option value="{{$year->id}}">{{$year->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3  mb-2">
            <label>{{t('Level')}}:</label>
            <select name="level_id" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Level')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
            </select>
        </div>

        <div class="col-lg-3  mb-2">
            <label>{{t('Status')}}:</label>
            <select name="status" class="form-control form-select" data-control="select2"
                    data-placeholder="{{t('Select Status')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                @foreach(['New', 'Uploading', 'Completed', 'Failed'] as $status)
                    <option value="{{$status}}">{{t($status)}}</option>
                @endforeach
            </select>
        </div>
    </div>

@endsection

@section('actions')
    @can('import questions')
        <a href="{{route(getGuard().'.question-file.create')}}" class="btn btn-primary btn-elevate btn-icon-sm me-2">
            <i class="la la-plus"></i>
            {{t('Import Questions')}}
        </a>
        @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('delete imported questions')
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
            <th class="text-start">{{t('Author')}}</th>
            <th class="text-start">{{t('Info')}}</th>
            <th class="text-start">{{t('Rows')}}</th>
            <th class="text-start">{{t('Status')}}</th>
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

                        <div class="form-check form-check-custom form-check-solid mx-2">
                            <input id="delete_file" class="form-check-input" type="radio" value="0" checked
                                   name="delete_with_rows"/>
                        </div>
                        <p class="m-0 p-0"
                           style="font-weight: normal;font-size: 14px">{{t('Delete just imported file')}}</p>
                    </div>
                    <div class="col-lg-12  d-flex align-items-center p-0 mt-2">
                        <div class="form-check form-check-custom form-check-solid mx-2">
                            <input id="delete_rows" class="form-check-input" type="radio" value="1"
                                   name="delete_with_rows"/>
                        </div>
                        <p class="m-0 p-0"
                           style="font-weight: normal;font-size: 14px">{{t('Delete questions when deleting imported file')}}</p>
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
        {{--var DELETE_URL = "{{route('teacher.student-data-files.destroy')}}";--}}
        var TABLE_URL = "{{route(getGuard().'.question-file.index')}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'author', name: 'author'},
            {data: 'info', name: 'info'},
            {data: 'rows', name: 'rows'},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions'}
        ];


        {{--        @can('delete students import')--}}
        let id = null; //id for deleted row

        $(document).on('click', '.delete_row', (function (e) {
            e.preventDefault();
            id = $(this).data('id')
            $('#delete_file_modal').modal('show')
        }))


        function deleteRows() {
            showLoadingModal();
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

            if ($("input[name='delete_with_rows']").is(':checked')) {
                request_data['delete_with_rows'] = $("input[name='delete_with_rows']:checked").val()
            }

            $.ajax({
                type: "POST",
                url: "{{route(getGuard().'.question-file.destroy')}}",
                data: request_data, //set data
                success: function (result) {
                    $('.group-checkable').prop('checked', false);
                    checkedVisible(false)
                    table.DataTable().draw(false);
                    hideLoadingModal()
                    Swal.fire("", result.message, "success")
                },
                error: function (error) {
                    hideLoadingModal()
                    let message = error.responseJSON.message
                    Swal.fire("", message, "error")
                }
            })
            id = null
            $('#btn_close').trigger('click')
        }
        {{--        @endcan--}}

    </script>
    <script>
        $(document).ready(function () {
            getAndSetDataOnSelectChange('year_id','level_id','{{route('manager.get-levels-by-year',':id')}}')
        })
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>

@endsection
