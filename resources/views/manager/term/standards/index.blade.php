@extends('manager.layout.container')
@section('title')
    {{t('Assessments Questions Standards')}}
@endsection

@section('actions')
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>

        <ul class="dropdown-menu">
            @can('export questions standards')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.term.standards-export')}}')">{{t('Export')}}</a></li>
            @endcan
        </ul>
    </div>
@endsection

@section('filter')
    <div class="row">
        <div class="col-lg-3 mb-2">
            <label>{{t('Standard')}}:</label>
            <input autocomplete="off" type="text"  name="standard" class="form-control direct-search" placeholder="{{t('Question Content')}}">
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Question Content')}}:</label>
            <input autocomplete="off" type="text"  name="question_content" class="form-control direct-search" placeholder="{{t('Question Content')}}">
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Assessments Name')}}:</label>
            <input type="text" id="term_name" name="term_name" class="form-control kt-input" placeholder="{{t('Assessments Name')}}">
        </div>


        <div class="col-lg-3 mb-2">
            <label>{{t('Year')}} :</label>
            <select name="year_id" id="year_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
                <option></option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Levels')}} :</label>
            <select name="level_id" id="levels_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Level')}}" data-allow-clear="true">
            </select>
        </div>


        <div class="col-lg-3 mb-2">
            <label>{{t('Assessments')}} :</label>
            <select name="term_id" id="terms" class="form-select" data-control="select2" data-placeholder="{{t('Select Assessment')}}" data-allow-clear="true">
                <option></option>
            </select>
        </div>

        <div class="col-lg-3 mb-2">
            <label>{{t('Subject')}} :</label>
            <select name="subject" id="subject" class="form-select" data-control="select2" data-placeholder="{{t('Select Subject')}}" data-allow-clear="true">
                <option></option>
                @for($i=1; $i<=4;$i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>

    </div>

@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{t('Assessments Questions Standards')}}</li>
@endpush


@section('content')



    <div class="row">

        <table class="table table-row-bordered gy-5" id="datatable">
            <thead>
            <tr class="fw-semibold fs-6 text-gray-800">
                <th class="text-start"></th>
                <th class="text-start">{{t('Assessment Name')}}</th>
                <th class="text-start">{{t('Standard')}}</th>
                <th class="text-start">{{t('Question Content')}}</th>
                <th class="text-start">{{t('Type')}}</th>
                <th class="text-start">{{t('Level')}}</th>
                <th class="text-start">{{t('Actions')}}</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection


@section('script')

    <script>
        {{--var DELETE_URL = "{{route('manager.term.delete')}}";--}}
        var TABLE_URL = "{{route('manager.term.standards')}}";

        var TABLE_COLUMNS = [
            {data: 'id'},
            {data: 'term_name'},
            {data: 'standard'},
            {data: 'question_content'},
            {data: 'type'},
            {data: 'level'},
            {data: 'actions'},
        ];

        $('#levels_id').on('change',function (){
            $.ajax({
                url: "{{route('manager.term.terms-names',['level'=>':level'])}}".replace(':level',$(this).val()),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#terms').empty();
                    $('#terms').append(data.html);
                }
            });
        })

    </script>

{{--    @can('edit questions standards')--}}
{{--        <script>--}}
{{--            var COLUMN_DEFS =  [--}}
{{--                {--}}
{{--                    targets:6,--}}
{{--                    render: function (data, type, full, meta) {--}}
{{--                        let url = "{{route('manager.term.standardsTerm',':id')}}".replace(':id',full.question.term_id)--}}
{{--                        return '<a class="btn btn-icon btn-sm btn-primary" href="'+url+'">' +--}}
{{--                            '<i class="la la-edit"></i>' +--}}
{{--                            '</a>'--}}
{{--                    },--}}
{{--                },]--}}
{{--        </script>--}}
{{--    @endcan--}}
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>


@endsection


