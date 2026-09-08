@extends('manager.layout.container')

@section('title',$title)

@push('breadcrumb')
    <li class="breadcrumb-item">
        {{$title}}
    </li>
@endpush
@section('actions')

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{__('auth_log.Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export login sessions')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.login_sessions.export')}}')">{{__('auth_log.Export')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@section('filter')
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.ID')}}:</label>
            <input type="text" name="id" class="form-control direct-search" placeholder="E.g: 4590"/>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.User ID')}}:</label>
            <input type="text"  name="model_id" class="form-control kt-input" placeholder="E.g: 45">
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.Name')}}:</label>
            <input type="text" name="name" class="form-control kt-input" placeholder="{{__('auth_log.Name')}}">
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.Email')}}:</label>
            <input type="text" name="email" class="form-control kt-input" placeholder="{{__('auth_log.Email')}}">
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{__('auth_log.Type')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="model_type" data-placeholder="{{__('auth_log.Select Type')}}">
                    <option></option>
                    @foreach(['Manager', 'School', 'Student', 'Inspection', 'Unknown'] as $type)
                        <option value="{{$type}}">{{ __('auth_log.'.class_basename($type)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{__('auth_log.Status')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="status" data-placeholder="{{__('auth_log.Select Status')}}">
                    <option></option>
                    @foreach(['success' => __('auth_log.Success'), 'failed' => __('auth_log.Failed'), 'lockout' => __('auth_log.Lockout'), 'logout' => __('auth_log.Logout')] as $key => $label)
                        <option value="{{$key}}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{__('auth_log.Guard')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="login_guard" data-placeholder="{{__('auth_log.Select Guard')}}">
                    <option></option>
                    @foreach(['manager', 'school', 'student', 'inspection'] as $guard)
                        <option value="{{$guard}}">{{ __('auth_log.'.$guard) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.IP Address')}}:</label>
            <input type="text" name="ip" class="form-control kt-input" placeholder="E.g: 192.168.1.1">
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.Entered Identifier')}}:</label>
            <input type="text" name="identifier" class="form-control kt-input" placeholder="{{__('auth_log.Entered Identifier')}}">
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{__('auth_log.Date Range')}} :</label>
            <input autocomplete="disabled" class="form-control form-control-solid" name="date_range" value="" placeholder="{{__('auth_log.Pick date range')}}" id="date_range"/>
            <input type="hidden" name="start_date" id="start_date_range" />
            <input type="hidden" name="end_date" id="end_date_range" />
        </div>

    </div>
@endsection




@section('content')
    <div class="row">
        <table class="table table-row-bordered gy-5" id="datatable">
                        <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th class="text-start"></th>
                            <th class="text-start">{{__('auth_log.User')}}</th>
                            <th class="text-start">{{__('auth_log.Type')}}</th>
                            <th class="text-start">{{__('auth_log.Status')}}</th>
                            <th class="text-start">{{__('auth_log.IP Address')}}</th>
                            <th class="text-start">{{__('auth_log.Data')}}</th>
                            <th class="text-start">{{__('auth_log.Time')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>

@endsection


@section('script')
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v={{time()}}"></script>
    <script>
        var TABLE_URL = "{{route('manager.login_sessions.index')}}";

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'user', name: 'user'},
            {data: 'model_type', name: 'model_type'},
            {data: 'status', name: 'status'},
            {data: 'ip', name: 'ip'},
            {data: 'data', name: 'data'},
            {data: 'created_at', name: 'created_at'},
        ];

        initializeDateRangePicker('date_range')
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection
