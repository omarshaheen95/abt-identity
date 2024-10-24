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
    @can('add inspections')
        <a href="{{route('manager.inspection.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create Inspection')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export inspections')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.export-inspections')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('delete inspections')
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
        <label class="mb-1">{{t('School')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select School')}}" name="school_id">
            <option></option>
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Image')}}</th>
            <th class="text-start">{{t('Email')}}</th>
            <th class="text-start">{{t('Schools')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>
@endsection

@section('script')
    <script>
        var DELETE_URL = "{{route('manager.delete-inspection')}}";
        var TABLE_URL = "{{route('manager.inspection.index')}}";

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'image', name: 'image'},
            {data: 'email', name: 'email'},
            {data: 'school', name: 'school'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection
