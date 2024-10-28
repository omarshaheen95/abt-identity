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
    @can('add terms')
        <a href="{{route('manager.term.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create Assessment')}}</a>
    @endcan

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export terms')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.term.export')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('add terms')
                <li><a class="dropdown-item text-primary" href="#!" data-bs-toggle="modal" data-bs-target="#add_terms_modal">{{t('Add General Assessment')}}</a></li>
            @endcan
            @can('terms activation')
                <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#activation-modal">{{t('Activation')}}</a></li>
            @endcan
            @can('delete terms')
                <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@section('filter')
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('ID')}}:</label>
        <input type="text" name="id" class="form-control" placeholder="{{t('ID')}}"/>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Name')}}:</label>
        <input type="text" name="name" class="form-control direct-search" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Round')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Round')}}" name="round">
            <option></option>
            <option value="september">september</option>
            <option value="february">february</option>
            <option value="may">may</option>

        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Year')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
            <option></option>
            @foreach($years as $year)
                <option value="{{$year->id}}">{{$year->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-3 mb-2">
        <label class="mb-1">{{t('Level')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Level')}}" name="level_id" id="levels_id">
            <option></option>
        </select>
    </div>
    <div class="col-lg-3 mb-2">
        <label>{{t('Status')}} :</label>
        <select name="active" class="form-select" data-control="select2" data-placeholder="{{t('Select status')}}" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Active')}}</option>
            <option value="2">{{t('Non-Active')}}</option>
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
             <th class="text-start">{{t('Round')}}</th>
             <th class="text-start">{{t('Level')}}</th>
             <th class="text-start">{{t('Active')}}</th>
             <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="modal fade" tabindex="-1" id="add_terms_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Add General Assessment')}}</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <form id="add_terms_form" method="POST" action="{{route('manager.term.addGeneralTerms')}}" >
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <label>{{t('Year')}}:</label>
                            <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                                    data-placeholder="{{t('Select Year')}}" name="year_id">
                                <option></option>
                                @foreach($years as $year)
                                    <option value="{{$year->id}}">{{$year->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mt-2">
                            <label>{{t('Round')}}:</label>
                            <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                                    data-placeholder="{{t('Select Round')}}" name="round">
                                <option></option>
                                @foreach(['september','february','may'] as $round)
                                    <option value="{{$round}}">{{$round}}</option>
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
    @can('terms activation')
        <div class="modal fade" tabindex="-1" id="activation-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{t('Assessments Activation')}}</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                             aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <form class="form-horizontal" id="activation-form"
                          action="{{route('manager.term.activation')}}" method="post">
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
                                <label class="control-label col-md-3">{{t('Round')}}</label>
                                <div class="col-md-6">
                                    <select class="form-select" data-control="select2"
                                            data-placeholder="{{t('Select Round')}}" data-allow-clear="true"
                                            name="round" required>
                                        <option></option>
                                        @foreach(getRounds() as $round)
                                            <option value="{{$round}}">{{$round}}</option>
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
        var DELETE_URL = "{{route('manager.term.delete')}}";
        var TABLE_URL = "{{route('manager.term.index')}}";
        var EXPORT_URL = null;
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'round', name: 'round'},
            {data: 'level', name: 'level'},
            {data: 'active', name: 'active'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script>
         onSelectAllClick('activation_grades')
        $(document).ready(function (){
            $('#add_terms_form').submit(function (event) {
                event.preventDefault()
                let data = new FormData(this)
                $('#add_terms_modal').modal('hide')
                //reset form
                $($('#'+$(this).attr('id')+' select')).val('').trigger('change');
                this.reset();
                showLoadingModal()
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: data,
                    processData: false,
                    contentType: false,
                    success:function (result) {
                        hideLoadingModal()
                        toastr.success(result.message)
                        table.DataTable().draw(false);
                    },
                    error:function (error) {
                        hideLoadingModal()
                        toastr.error(error.responseJSON.message)
                    }
                })
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
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>


@endsection
