@extends('school.layout.container')
@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form id="grades_form" method="POST"
          action="{{route('school.scheduling.update',Auth::guard('school')->user()->id)}}">
        @csrf
        <div class="row mb-3">
            <div class="col-3 ">
                <label>{{t('Years')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{$year->id}}" {{$school->available_year_id==$year->id?'selected':''}}>{{$year->name}}</option>
                    @endforeach

                </select>
            </div>

            <div class="col-lg-3 d-flex align-items-end">
                <button class="btn btn-primary" type="button" id="update_grades_btn">
                            <span>
                                {{t('Save')}}
                            </span>
                </button>
            </div>


        </div>
        <div class="row mb-8">

        </div>
        <!--begin: Datatable-->

        <table class="table table-separate table-bordered text-center table-checkable" id="kt_datatable_2">
            <thead>
            <tr>
                <th>{{t('Grade')}}</th>
                <th>{{t('Arab')}}</th>
                <th>
                    <div class="d-flex flex-column align-items-center">
                        {{t('September')}}
                        <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                            <input class="form-check-input september_select_all " type="checkbox" value="" id="flexRadioLg"
                                   @if(count($grades)>0 && count($grades->where('september',0))==0) checked @elseif(count($grades)==0) checked @endif />
                        </div>
                    </div>

                </th>
                <th>
                    <div class="d-flex flex-column align-items-center">
                        {{t('February')}}
                        <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                            <input class="form-check-input february_select_all" type="checkbox" value="" id="flexRadioLg"
                                   @if(count($grades)>0 && count($grades->where('february',0))==0) checked @elseif(count($grades)==0) checked @endif />
                        </div>
                    </div>

                </th>
                <th>
                    <div class="d-flex flex-column align-items-center">
                        {{t('May')}}
                        <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                            <input class="form-check-input may_select_all" type="checkbox" value="" id="flexRadioLg"
                                   @if(count($grades)>0 && count($grades->where('may',0))==0) checked @elseif(count($grades)==0) checked @endif />

                        </div>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            @if(count($grades)>0)
                @foreach($grades as $grade)
                    <tr>
                        <td>
                            {{$grade->grade}}
                            <input type="hidden" name="grades[{{$grade->id}}][grade]" value="{{$grade->grade}}">
                        </td>
                        <td>
                            <input type="hidden" name="grades[{{$grade->id}}][arab]" value="{{$grade->arab}}">
                            <span class="badge {{$grade->arab?'badge-primary':'badge-secondary'}}">{{$grade->arab?t('Arab'):t('Non-Arab')}}</span>
                        </td>
                        <td>
                            <input type="hidden" name="grades[{{$grade->id}}][id]" value="{{$grade->id}}">
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                                    <input class="form-check-input september_checkbox" name="grades[{{$grade->id}}][september]" type="checkbox" value="september" id="flexRadioLg" {{$grade->september?'checked':''}}/>
                                </div>
                            </div>

                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                                    <input class="form-check-input february_checkbox" name="grades[{{$grade->id}}][february]" type="checkbox" value="february" id="flexRadioLg" {{$grade->february?'checked':''}}/>
                                </div>
                            </div>

                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-solid form-check-sm mt-2">
                                    <input class="form-check-input may_checkbox" name="grades[{{$grade->id}}][may]" type="checkbox" value="may" id="flexRadioLg" {{$grade->may?'checked':''}}/>
                                </div>
                            </div>

                        </td>
                    </tr>
                @endforeach
            @endif

            </tbody>
        </table>
        <!--end: Datatable-->
    </form>
@endsection

@section('script')
    <script>

        $('#update_grades_btn').on('click',function () {
            $('#grades_form').submit()
        })

        $('.september_select_all').on('click',function (){
            checked('september')
        })

        $('.february_select_all').on('click',function (){
            checked('february')
        })

        $('.may_select_all').on('click',function (){
            checked('may')
        })

        function checked(month) {
            let status = $('.'+month+'_select_all').prop('checked')
            $('.'+month+'_checkbox').each(function (index,item) {
                $(item).prop('checked', status);
            })
        }

    </script>
@endsection
