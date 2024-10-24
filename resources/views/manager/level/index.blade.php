@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection

@section('actions')
    @can('add levels')
        <a href="{{route('manager.level.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create Level')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export levels')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.level.export')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('add levels')
                <li><a class="dropdown-item text-primary" href="#!" data-bs-toggle="modal" data-bs-target="#add_level_modal">{{t('Add General Level')}}</a></li>
            @endcan
            @can('levels activation')
                <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#activation-modal">{{t('Activation')}}</a></li>
            @endcan
            @can('delete levels')
                <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush


@section('filter')
    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('Name')}}:</label>
        <input type="text" name="name" class="form-control direct-search" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-lg-3 mb-lg-0 mb-6">
        <label>{{t('Year')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}">{{$year->name}}</option>
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


@endsection

@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">#</th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Year')}}</th>
            <th class="text-start">{{t('Active')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="modal fade" tabindex="-1" id="add_level_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Add General Assessment Grade')}}</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <form id="add_level_form" method="POST" action="{{route('manager.level.addGeneralLevels')}}" >
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <label>{{t('Year')}}:</label>
                            <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                                    data-placeholder="{{t('Select Year')}}" id="level_year_id" name="year_id">
                                <option></option>
                                @foreach($years as $year)
                                    <option value="{{$year->id}}">{{$year->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{t('Close')}}</button>
                        <button type="submit" class="btn btn-primary btn-add-level">{{t('Add')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @can('levels activation')
        <div class="modal fade" tabindex="-1" id="activation-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{t('Levels Activation')}}</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                             aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <form class="form-horizontal" id="activation-form"
                          action="{{route('manager.level.activation')}}" method="post">
                        @csrf
                        <div class="modal-body d-flex flex-column gap-4">
                            <div class="form-group row align-items-center">
                                <label class="control-label col-md-3">{{t('Year')}}</label>
                                <div class="col-md-6">
                                    <select class="form-select" data-control="select2"
                                            data-placeholder="{{t('Select Year')}}" data-allow-clear="true"
                                            name="year_id" required>
                                        <option></option>
                                        @foreach($years as $year)
                                            <option value="{{$year->id}}">{{$year->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="control-label col-md-3 ">{{t('Grades')}}</label>
                                <div class="col-md-6">
                                    <select id="activation_grades" class="form-select" data-control="select2"
                                            data-placeholder="{{t('Select Grade')}}" data-allow-clear="true"
                                            name="grades[]" required multiple>
                                        <option id="all" value="0">{{t('All')}}</option>
                                        @foreach(range(1,12) as $grade)
                                            <option value="{{$grade}}">{{t('Grade').' '.$grade}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-3 form-label mb-1">{{t('Status')}}</label>
                                <div class="col-6 d-flex gap-2">
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input" type="radio" value="1" name="active" id="flexRadioLg" checked/>
                                        <label class="form-check-label" for="flexRadioLg">
                                            {{t('Active')}}
                                        </label>
                                    </div>

                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input" type="radio" value="0" name="active" id="flexRadioLg"/>
                                        <label class="form-check-label" for="flexRadioLg">
                                            {{t('Non Active')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{t('Cancel')}}</button>
                            <button type="submit" class="btn btn-warning">{{t('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('manager.level.delete')}}";
        var TABLE_URL = "{{route('manager.level.index')}}";
        var EXPORT_URL = null;
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'year', name: 'year'},
            {data: 'active', name: 'active'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script>
        onSelectAllClick('activation_grades')
        $(document).ready(function (){
            $('#add_level_form').submit(function (event) {
                event.preventDefault()
                let data = new FormData(this)
                $('#add_level_modal').modal('hide')
                showLoadingModal()
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: data,
                    processData: false,
                    contentType: false,
                    success:function (result) {
                        hideLoadingModal()
                        console.log(result)
                        toastr.success(result.message)
                        table.DataTable().draw(false);
                    },
                    error:function (error) {
                        hideLoadingModal()
                        toastr.error(error.responseJSON.message)
                    }
                })
                $($('#'+$(this).attr('id')+' select')).val('').trigger('change'); //reset form
                this.reset()
            })
            $('#activation-form').submit(function (event) {
    event.preventDefault()
    let data = new FormData(this)
    $('#activation-modal').modal('hide')
    showLoadingModal()
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            hideLoadingModal()
            console.log(result)
            toastr.success(result.message)
            table.DataTable().draw(false);
        },
        error: function (error) {
            hideLoadingModal()
            toastr.error(error.responseJSON.message)
        }
    })
    $($('#' + $(this).attr('id') + ' select')).val('').trigger('change'); //reset form
    this.reset()
})
        })
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>


@endsection
