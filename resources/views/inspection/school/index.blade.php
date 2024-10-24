@extends('inspection.layout.container')
@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item b text-muted">
        {{$title}}
    </li>
@endpush

@section('actions')

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('inspection.export-schools')}}')">{{t('Export')}}</a></li>
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
        <label class="mb-1">{{t('Mobile')}}:</label>
        <input type="text" name="mobile" class="form-control datatable-input"
               placeholder="{{t('Mobile')}}" data-col-index="1"/>
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
@endsection


@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Email')}}</th>
            <th class="text-start">{{t('Mobile')}}</th>
            <th class="text-start">{{t('Country')}}</th>
            <th class="text-start">{{t('Curriculum Type')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

@endsection
@section('script')
    <script>
        var TABLE_URL = "{{route('inspection.school.index')}}";
        var EXPORT_URL = '{{route('inspection.export-schools')}}';

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'mobile', name: 'mobile'},
            {data: 'country', name: 'country'},
            {data: 'curriculum_type', name: 'curriculum_type'},
            {data: 'actions', name: 'actions'}
        ];
    </script>

    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>



@endsection
