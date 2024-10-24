@extends('manager.layout.container')

@section('title',$title)

@push('breadcrumb')
    <li class="breadcrumb-item">
        {{$title}}
    </li>
@endpush
{{--@section('actions')--}}
{{--    --}}
{{--    <div class="dropdown" id="actions_dropdown">--}}
{{--        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--            {{t('Actions')}}--}}
{{--        </button>--}}
{{--        <ul class="dropdown-menu">--}}
{{--            @can('delete session')--}}
{{--                <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>--}}
{{--            @endcan--}}
{{--        </ul>--}}
{{--    </div>--}}

{{--@endsection--}}

@section('filter')
    <div class="row">
        <div class="col-3 mb-2">
            <label>{{t('User ID')}}:</label>
            <input type="text"  name="model_id" class="form-control kt-input" placeholder="E.g: 45">
        </div>
        <div class="col-3 mb-2">
            <label>{{t('Name')}}:</label>
            <input type="text" name="name" class="form-control kt-input" placeholder="{{t('Name')}}">
        </div>

        <div class="col-3 mb-2">
            <label>{{t('Email')}}:</label>
            <input type="text" name="email" class="form-control kt-input" placeholder="{{t('Email')}}">
        </div>

        <div class="col-3 mb-2">
            <div class="form-group">
                <label class="">{{t('Type')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="model_type" data-placeholder="{{t('Select Type')}}">
                    <option></option>
                    @foreach(['Manager','School','Inspection','Student'] as $type)
                        <option value="{{$type}}">{{ t($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Date Range')}} :</label>
            <input autocomplete="disabled" class="form-control form-control-solid" id="date_range" name="date_range" value="" placeholder="{{t('Pick date range')}}" />
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
                            <th class="text-start">{{t('User')}}</th>
                            <th class="text-start">{{t('Type')}}</th>
                            <th class="text-start">{{t('Data')}}</th>
                            <th class="text-start">{{t('Time')}}</th>
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
            {data: 'data', name: 'data'},
            {data: 'created_at', name: 'created_at'},
        ];

        initializeDateRangePicker('date_range')
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection


