@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.term.index')}}" class="text-muted">
            {{t('Assessments')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{route('manager.term.copy_term')}}"
          method="post">
        @csrf
        <div class="form-group row justify-content-center">
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('From Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Year')}}" name="from_year">
                    <option></option>
                    @foreach($years as $year)
                        <option
                            value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 mb-2">
                <label class="mb-1">{{t('From Round')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Round')}}" name="from_round">
                    <option></option>
                    <option
                        value="september">
                        september
                    </option>
                    <option
                        value="february">
                        february
                    </option>
                    <option value="may">may
                    </option>

                </select>
            </div>
        </div>
        <div class="form-group row  justify-content-center">
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('To Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Year')}}" name="to_year">
                    <option></option>
                    @foreach($years as $year)
                        <option
                            value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 mb-2">
                <label class="mb-1">{{t('To Round')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Round')}}" name="to_round">
                    <option></option>
                    <option
                        value="september">
                        september
                    </option>
                    <option
                        value="february">
                        february
                    </option>
                    <option value="may">may
                    </option>

                </select>
            </div>
        </div>
        <div class="form-group row  justify-content-center">

            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Grades')}}:</label>
                <select class="form-control form-select" multiple data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Grade')}}" name="grades[]">
                    <option></option>
                    @foreach($grades as $grade)
                        <option
                            value="{{$grade}}">{{$grade}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row  justify-content-center">
            <div class="col-lg-3 mt-8">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="with_paragraphs"
                           name="with_terms"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('With Term')}}
                    </label>
                </div>
            </div>
            <div class="col-lg-3 mt-8">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="with_questions"
                           checked name="with_questions"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('With Questions')}}
                    </label>
                </div>
            </div>
            <div class="col-lg-3 mt-8">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="with_standards"
                           checked name="with_standards"
                    />
                    <label class="form-check-label" for="flexSwitchDefault">
                        {{t('With Standards')}}
                    </label>
                </div>
            </div>



        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\CopyTermRequest::class, '#form_data'); !!}
@endsection
