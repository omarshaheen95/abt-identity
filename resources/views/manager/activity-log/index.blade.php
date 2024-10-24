@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection

@section('actions')

    <a class="btn btn-secondary  text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a>

@endsection
@section('filter')
    <div class="row">
        <div class="col-lg-3 mb-2">
            <input type="hidden" name="causedByManager" value="{{request()->get('causedByManager', false)}}">
            <input type="hidden" name="causedBySchool" value="{{request()->get('causedBySchool', false)}}">
            <label>{{t('Name')}}:</label>
            <input type="text"  name="name" class="form-control" placeholder="{{t('Name')}}">
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Email')}}:</label>
            <input type="text"  name="email" class="form-control" placeholder="{{t('Email')}}">
        </div>

        <div class="col-3 mb-2">
            <label>{{t('Type')}} :</label>
            <select name="type" class="form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Type')}}">
                <option></option>
                <option value="created">{{t('Created')}}</option>
                <option value="updated">{{t('Update')}}</option>
                <option value="deleted">{{t('Delete')}}</option>
            </select>
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Date Range')}} :</label>
            <input autocomplete="disabled" class="form-control form-control-solid" value="" placeholder="{{t('Pick date rage')}}" id="date_range_picker"/>
            <input type="hidden" name="date_start" id="start_date_range_picker" />
            <input type="hidden" name="date_end" id="end_date_range_picker" />
        </div>
        <!--Actions-->
    </div>
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item">
        {{t('Activity Logs')}}
    </li>
@endpush


@section('content')
    <div class="row">
        <table class="table table-row-bordered gy-5" id="datatable">
                        <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th class="text-start"></th>
                            <th class="text-start">{{t('Causer ')}}</th>
                            <th class="text-start">{{t('Subject')}}</th>
                            <th class="text-start">{{t('Type')}}</th>
                            <th class="text-start">{{t('Action Date')}}</th>
                            <th class="text-start">{{t('Actions')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>

@endsection


@section('script')

    <script>
        var DELETE_URL = '{{ route("manager.activity-log.delete")}}';
        var TABLE_URL = "{{route('manager.activity-log.index')}}";

        var TABLE_COLUMNS  = [
            {data: 'id', name: 'id'},
            {data: 'causer', name: 'causer'},
            {data: 'subject', name: 'subject'},
            {data: 'description', name: 'description'},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>
    <script>
        initializeDateRangePicker()
    </script>

@endsection


