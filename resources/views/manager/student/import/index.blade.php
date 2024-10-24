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
    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('File Name')}}:</label>
        <input type="text" name="name" class="form-control" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('School')}}:</label>
        <select  class="form-control form-select" data-control="select2" data-allow-clear="true"  data-placeholder="{{t('Select School')}}" name="school_id">
            <option></option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach

        </select>
    </div>

    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('Status')}}:</label>
        <select  class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Status')}}" name="status">
            <option></option>
            @foreach($status as $s)
                <option value="{{$s['value']}}">{{$s['key']}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('Year')}}:</label>
        <select  class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}">{{$year->name}}</option>
            @endforeach
        </select>
    </div>

@endsection

@section('actions')
    @can('import students')
        <a href="{{route('manager.students_files_import.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Import Students')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
        <li><a class="dropdown-item text-danger checked-visible" href="{{asset('students example -math.xlsx')}}">{{t('Example File')}}</a></li>
        @can('delete students import')
                <li><a class="dropdown-item text-danger d-none checked-visible" data-bs-toggle="modal" data-bs-target="#delete_file_modal" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
        @endcan
        </ul>
    </div>

@endsection
@section('content')

    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('File Name')}}</th>
            <th class="text-start">{{t('Rows Count')}}</th>
            <th class="text-start">{{t('Updated Rows Count')}}</th>
            <th class="text-start">{{t('Failed Rows Count')}}</th>
            <th class="text-start">{{t('School Name')}}</th>
            <th class="text-start">{{t('Year')}}</th>
            <th class="text-start">{{t('Status')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

{{--    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_1">--}}
{{--        Launch demo modal--}}
{{--    </button>--}}

    <div class="modal fade" tabindex="-1" id="delete_file_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Delete')}}</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <p>{{t('are sure of the deleting process ?')}}</p>
                    <div class="col-lg-12  d-flex align-items-center p-0">
                        <p class="m-0 p-0" style="font-weight: normal;font-size: 14px">{{t('Delete students when deleting the import file')}}</p>
                        <div class="form-check form-check-custom form-check-solid mx-2">
                            <input id="delete_students" class="form-check-input" type="checkbox" value="1" name="delete_students"/>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btn_close" type="button" class="btn btn-light" data-bs-dismiss="modal">{{t('Close')}}</button>
                    <button type="button" class="btn btn-danger" onclick="deleteRows()">{{t('Delete')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>

        var TABLE_URL = "{{route('manager.students_files_import.index')}}";
        var TABLE_COLUMNS= [
            {data: 'id', name: 'id'},
            {data: 'id', name: 'id'},
            {data: 'original_file_name', name: 'original_file_name'},
            {data: 'row_count', name: 'row_count'},
            {data: 'updated_row_count', name: 'updated_row_count'},
            {data: 'failed_row_count', name: 'failed_row_count'},
            {data: 'school_name', name: 'school_name'},
            {data: 'year', name: 'year'},
            {data: 'status', name: 'status'},
            {data: 'actions', name: 'actions'}
        ];

        let id = null; //id for deleted row

        $(document).on('click', '.delete_row', (function () {
            id = $(this).data('id')
            $('#delete_file_modal').modal('show')
        }))


        function deleteRows() {
            let row_id=[];
            if (!id){
                $("input:checkbox[name='rows[]']:checked").each(function () {
                    row_id.push($(this).val());
                });
            }else {
                row_id.push(id);
            }

            let request_data = {
                'row_id': row_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
                '_method': 'DELETE',
            }

            if ($('#delete_students').is(':checked')){
                $('#delete_students').prop('checked',false)
                request_data['delete_students'] = true
            }

            $.ajax({
                type: "POST",
                url: "{{route('manager.students_files_import.delete')}}",
                data:request_data , //set data
                success:function (result) {
                    console.log(result)
                    $('.group-checkable').prop('checked', false);
                    checkedVisible(false)
                    table.DataTable().draw(false);
                    Swal.fire("", result.message,"success")
                    table.DataTable().draw(false);
                },
                error:function (error) {
                    Swal.fire("", data.message, "error")
                }
            })
            id= null
            $('#btn_close').trigger('click')
        }




    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection
