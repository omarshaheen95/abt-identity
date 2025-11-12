@extends('manager.layout.container')

@section('title')
    {{t('Marking Requests')}}
@endsection

@section('actions')
    @can('add marking requests')
        <a href="{{ route('manager.marking_requests.create') }}" class="btn btn-primary btn-elevate btn-icon-sm me-2">
            <i class="la la-plus"></i>
            {{t('Add New Marking Request')}}
        </a>
    @endcan
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('delete marking requests')
                <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>
@endsection

@section('filter')
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-2">
            <label class="mb-1">{{t('ID')}}:</label>
            <input type="text"  name="id" class="form-control kt-input" placeholder="E.g: 45">
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{t('School')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="school_id" data-placeholder="{{t('Select School')}}">
                    <option></option>
                    @foreach($schools as $school)
                        <option
                            value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{t('Year')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="year_id" data-placeholder="{{t('Select Year')}}">
                    <option></option>
                    @foreach($years as $year)
                        <option
                            value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{t('Round')}}</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="round" data-placeholder="{{t('Select Round')}}">
                    <option></option>
                    @foreach(['september','february','may'] as $month)
                        <option value="{{$month}}">{{$month}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="form-group">
                <label class="mb-1">{{t('Status')}}:</label>
                <select class="form-select" data-control="select2" data-hide-search="true" data-allow-clear="true" name="status" data-placeholder="{{t('Select Section')}}">
                    <option></option>
                    <option value="Pending">{{t('Pending')}}</option>
                    <option value="Accepted">{{t('Accepted')}}</option>
                    <option value="In Progress">{{t('In Progress')}}</option>
                    <option value="Completed">{{t('Completed')}}</option>
                    <option value="Rejected" >{{t('Rejected')}}</option>

                </select>
            </div>
        </div>

    </div>
@endsection


@section('actions')
    <div class="dropdown d-none" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.level.export')}}',true)">{{t('Export')}}</a></li>
            <li><a class="dropdown-item text-danger" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
        </ul>
    </div>

@endsection
@push('breadcrumb')
    <li class="breadcrumb-item">
        {{t('Marking Requests')}}
    </li>
@endpush


@section('content')
    <div class="row">
        <table class="table table-row-bordered gy-5" id="datatable">
                        <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th class="text-start"></th>
                            <th class="text-start">{{t('School')}}</th>
                            <th class="text-start">{{t('Round')}}</th>
                            <th class="text-start">{{t('Year')}}</th>
                            <th class="text-start">{{t('Grades')}}</th>
                            <th class="text-start">{{t('Status')}}</th>
                            <th class="text-start">{{t('Actions')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>

@endsection


@section('script')

    <script>
        var DELETE_URL = '{{ route("manager.marking_requests.destroy")}}';
        var TABLE_URL = "{{route('manager.marking_requests.index')}}";

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'school', name: 'school'},
            {data: 'round', name: 'round'},
            {data: 'year', name: 'year'},
            {data: 'grades', name: 'grades'},
            {data: 'status', name: 'status'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection


