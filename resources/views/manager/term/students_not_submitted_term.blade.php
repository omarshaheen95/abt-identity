@extends('manager.layout.container')
@section('title')
    {{t('Not Started Yet')}}
@endsection

@section('actions')
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export students not submitted term')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.term.students-not-submitted-terms-export')}}')">{{t('Export')}}</a></li>
            @endcan
        </ul>
    </div>
@endsection


@section('filter')
    <div class="row">
        <div class="col-3 mb-2">
            <label>{{t('Student ID')}} :</label>
            <input type="text" id="student_id" name="id" class="form-control kt-input" placeholder="E.g: 4590">
        </div>

        <div class="col-3 mb-2">
            <label>{{t('Student Name')}} :</label>
            <input type="text" id="student_name" name="name" class="form-control direct-search" placeholder="{{t('Student Name')}}">
        </div>

        <div class="col-3 mb-2">
            <label>{{t('Username')}}:</label>
            <input type="text" id="email" name="email" class="form-control direct-search" placeholder="{{t('Username')}}">
        </div>
        <div class="col-3 mb-2">
            <div class="form-group">
                <label class="">{{t('School')}}:</label>
                <select class="form-select" data-control="select2" data-allow-clear="true" name="school_id" data-placeholder="{{t('Select School')}}">
                    <option></option>
                    @foreach($schools as $school)
                        <option
                            value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-3 mb-2">
            <label>{{t('Year')}} :</label>
            <select name="year_id" id="year_id" class="form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}">
                <option></option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 mb-2">
            <label>{{t('Level')}} :</label>
            <select name="level_id" id="levels_id" class="form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Level')}}">
                <option></option>
            </select>
        </div>
        <div class="col-lg-3 mb-2">
            <label>{{t('Round')}} :</label>
            <select name="round" class="form-select" data-control="select2" data-placeholder="{{t('Select Round')}}" data-allow-clear="true">
                <option></option>
                @foreach(['september','may','february'] as $round)
                    <option value="{{$round}}">{{ $round }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 mb-2">
            <label>{{t('Class')}} :</label>
            <select name="class" id="class" class="form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Class')}}">
                <option></option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>
        <div class="col-lg-2 mb-2">
            <label>{{t('Order By')}} :</label>
            <select name="orderBy" id="orderBy" class="form-select" data-control="select2" data-placeholder="{{t('Select Type')}}" >
                <option value="latest" selected>{{t('Latest')}}</option>
                <option value="name">{{t('Name')}}</option>
                <option value="level">{{t('Level')}}</option>
                <option value="section">{{t('Section')}}</option>
            </select>
        </div>
        <div class="col-2 mb-2">
            <label>{{t('G&T')}}:</label>
            <select class="form-control form-select" data-hide-search="true" data-control="select2" data-placeholder="{{t('G&T')}}" name="g_t" data-allow-clear="true">
                <option></option>
                <option value="1">{{t('Yes')}}</option>
                <option value="2">{{t('No')}}</option>
            </select>
        </div>
    </div>
@endsection


@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{t('Not Started Yet')}}</li>
@endpush


@section('content')
    <div class="row">

                    <table class="table table-row-bordered gy-5" id="datatable">
                        <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th class="text-start"></th>
                            <th class="text-center">{{t('ID')}}</th>
                            <th class="text-start">{{t('Name')}}</th>
                            <th class="text-start">{{t('School')}}</th>
                            <th class="text-start">{{t('Level')}}</th>
                            <th class="text-start">{{t('Class name')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
@endsection


@section('script')

    <script>
        var TABLE_URL = "{{route('manager.term.students-not-submitted-terms')}}";

        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'sid', name: 'student_id'},
            {data: 'name', name: 'name'},
            {data: 'school', name: 'school'},
            {data: 'level', name: 'level'},
            {data: 'grade_name', name: 'grade_name'},
        ];
        var CREATED_ROW = function(row, data, dataIndex) {
            if (data.sen) {
                $(row).css('background-color','#ffacac');
            }
            if (data.g_t) {
                $(row).css('background-color','#fff0b0');
            }
        }

    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>




@endsection


