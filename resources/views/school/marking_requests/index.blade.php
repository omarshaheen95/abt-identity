@extends('school.layout.container')

@section('title')
    {{t('Marking Requests')}}
@endsection

@section('actions')
    <a href="{{ route('school.marking_requests.create') }}" class="btn btn-primary btn-elevate btn-icon-sm me-2">
        <i class="la la-plus"></i>
        {{t('Add Marking Request')}}
    </a>
    <div class="dropdown with-filter">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
        </ul>
    </div>

@endsection

@section('filter')
    <div class="row">

        <div class="col-lg-3 mb-2">
            <label>{{t('Year')}} :</label>
            <select name="year_id" id="year_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
                <option></option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 mb-2">
            <label class="">{{t('Round')}}:</label>
            <select class="form-select" required name="round" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Round')}}">
                <option></option>
                @foreach(['september','may','february'] as $month)
                    <option value="{{$month}}">{{$month}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Status')}} :</label>
            <select name="status" class="form-select" data-control="select2" data-placeholder="{{t('Select Status')}}" data-allow-clear="true">
                <option></option>
                <option value="Pending">{{t('Pending')}}</option>
                <option value="Accepted">{{t('Accepted')}}</option>
                <option value="In Progress">{{t('In Progress')}}</option>
                <option value="Completed">{{t('Completed')}}</option>
                <option value="Rejected">{{t('Rejected')}}</option>
            </select>
        </div>

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
                <th class="text-start">{{t('ID')}}</th>
                <th class="text-start">{{t('Year')}}</th>
                <th class="text-start">{{t('Grades')}}</th>
                <th class="text-start">{{t('Round')}}</th>
                <th class="text-start">{{t('Status')}}</th>
                <th class="text-start">{{t('Submitted At')}}</th>
                <th class="text-start">{{t('Actions')}}</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection


@section('script')

    <script>
        var DELETE_URL = '{{ route("school.marking_requests.destroy")}}';
        var TABLE_URL = "{{route('school.marking_requests.index')}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id', name: 'id'},
            {data: 'year', name: 'year'},
            {data: 'grades', name: 'grades'},
            {data: 'round', name: 'round'},
            {data: 'status', name: 'status'},
            {data: 'submitted_at', name: 'submitted_at'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection


