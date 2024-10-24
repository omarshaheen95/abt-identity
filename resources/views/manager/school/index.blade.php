@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item b text-muted">
        {{$title}}
    </li>
@endpush

@section('actions')
    @can('add schools')
        <a href="{{route('manager.school.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create School')}}</a>
    @endcan
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export schools')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.export-schools')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('schools general scheduling')
                    <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#update_general_scheduling">{{t('General Scheduling')}}</a></li>
            @endcan
            @can('delete schools')
            <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@section('filter')
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
            <label class="mb-1">{{t('Country')}}:</label>
            <select class="form-control form-select"  data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Country')}}" name="country">
                <option></option>
                @foreach(schoolsCountry() as $key => $type)
                    <option value="{{$key}}">{{$type}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-3 mb-2">
            <label class="mb-1">{{t('Curriculum Type')}}:</label>
            <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Curriculum Type')}}"
                    name="curriculum_type">
                <option></option>
                @foreach(schoolsType() as $key => $type)
                    <option value="{{$key}}">{{$type}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Status')}}:</label>
            <select name="active" class="form-select" data-control="select2" data-placeholder="{{t('Select Status')}}" data-hide-search="true" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Active')}}</option>
                <option value="2">{{t('Inactive')}}</option>
            </select>
        </div>
@endsection
@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Country')}}</th>
            <th class="text-start">{{t('Curriculum Type')}}</th>
            <th class="text-start">{{t('Logo')}}</th>
            <th class="text-start">{{t('Active Status')}}</th>
            <th class="text-start">{{t('Last Login')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>
    @can('schools general scheduling')
        <div class="modal fade" id="update_general_scheduling" tabindex="-1" role="dialog" aria-labelledby="updateModel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{t('General Scheduling')}}</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="form-horizontal" id="update_dorm"
                          action="{{route('manager.school.general-scheduling')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="control-label col-md-3">{{t('Round')}}</label>
                                <div class="col-md-6 mb-2">
                                    <select class="form-control form-select" required id="round" name="round" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="{{t('Select Round')}}">
                                        <option selected value="">{{t('Select Round')}}</option>
                                        <option value="september">{{t('September')}}</option>
                                        <option value="february">{{t('February')}}</option>
                                        <option value="may">{{t('May')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-3">{{t('Status')}}</label>
                                <div class="col-md-6">
                                    <select class="form-control form-select" required id="status" name="status" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="{{t('Select Status')}}">
                                        <option></option>
                                        <option value="1">{{t('Active')}}</option>
                                        <option value="2">{{t('Not-Active')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{t('Close')}}</button>
                            <button type="submit" class="btn btn-primary">{{t('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('manager.delete-school')}}";
        var TABLE_URL = "{{route('manager.school.index')}}";
        var EXPORT_URL = '{{route('manager.export-schools')}}';
        var COLUMN_DEFS =  [
            {
                targets: 1,
                render: function (data, type, full, meta) {

                    return '<div class="student-box" style="text-align: start">\n' +
                        '                                    <div class="content">\n' +
                        '                                        <div class="student-name">' + full.name + '</div>\n' +
                        '                                        <div class="student-username">' + full.email + '</div>\n' +
                        '                                    </div>\n' +
                        '                                </div>';
                },
            },]
        var TABLE_COLUMNS = [
            {data: 'id', name: 'name'},
            {data: 'name', name: 'name'},
            {data: 'country', name: 'country'},
            {data: 'curriculum_type', name: 'curriculum_type'},
            {data: 'logo', name: 'logo'},
            {data: 'active', name: 'active'},
            {data: 'last_login', name: 'last_login'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection
