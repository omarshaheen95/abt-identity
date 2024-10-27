@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.level.index')}}" class="text-muted">
            {{t('Levels')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{isset($level) ? route('manager.level.update', $level->id):route('manager.level.store')}}"
          method="post">
        @csrf
        @isset($level)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="form-group row">
            @foreach(\Config::get('app.languages') as $locale)
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Level Name')}} : ({{$locale}})</label>
                    <input name="name[{{$locale}}]" type="text" placeholder="{{t('Level Name')}}"
                           class="form-control"
                           value="{{ isset($level) ? $level->getTranslation('name', $locale) : old("name[$locale]") }}"
                    />
                </div>
            @endforeach

            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Grade')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Grade')}}" name="grade">
                    <option></option>
                    @foreach($grades as $grade)
                        <option
                            {{isset($level) && $level->grade == $grade->id ? 'selected':'' }} value="{{$grade->id}}">{{$grade->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option
                            {{isset($level) && $level->year_id == $year->id ? 'selected':'' }} value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Section')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Section')}}" name="section">
                        <option></option>
                            <option {{isset($level) && $level->arab == 1 ? 'selected':'' }} value="1">{{t('Arabs')}}</option>
                            <option {{isset($level) && $level->arab == 0 ? 'selected':'' }} value="2">{{t('Non-Arabs')}}</option>
                    </select>
                </div>

                <div class="col-lg-4 mt-8">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                               {{ isset($level) && $level->active == 1 ? 'checked' :'' }} name="active"
                        />
                        <label class="form-check-label" for="flexSwitchDefault">
                            {{t('Activation')}}
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
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\LevelRequest::class, '#form_data'); !!}
@endsection
