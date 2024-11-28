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
        {{$title}} /
        {{ isset($term) ? $term->getTranslation('name', app()->getLocale()):''}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{isset($term) ? route('manager.term.update', $term->id):route('manager.term.store')}}"
          method="post">
        {{csrf_field()}}
        @isset($term)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="form-group row">
            @foreach(\Config::get('app.languages') as $locale)
                <div class="col-lg-4 mb-2">
                    <label class="mb-1">{{t('Assessment Name')}} : ({{$locale}})</label>
                    <input name="name[{{$locale}}]" type="text" placeholder="{{t('Assessment Name')}}"
                           class="form-control"
                           value="{{ isset($term) ? $term->getTranslation('name', $locale) : old("name[$locale]") }}"
                    />
                </div>
            @endforeach
            <div class="col-4 mb-2">
                <label class="mb-1">{{t('Round')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Round')}}" name="round">
                    <option></option>
                    <option
                        value="september" {{isset($term) && $term->round == 'september' ? 'selected':'' }}>
                        september
                    </option>
                    <option
                        value="february" {{isset($term) && $term->round == 'february' ? 'selected':'' }}>
                        february
                    </option>
                    <option value="may" {{isset($term) && $term->round == 'may' ? 'selected':'' }}>may
                    </option>

                </select>
            </div>
                <div class="col-4 mb-2">
                    <label class="mb-1">{{t('Year')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
                        <option></option>
                        @foreach($years as $year)
                            <option
                                {{isset($term) && $term->level->year_id == $year->id ? 'selected':'' }} value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label class="mb-1">{{t('Level')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="level_id" id="levels_id">
                        <option></option>
                        @isset($levels)
                            @foreach($levels as $level)
                                <option
                                    {{isset($term) && $term->level_id == $level->id ? 'selected':'' }} value="{{$level->id}}">{{$level->name}}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label class="mb-1">{{t('Duration by (h)')}} :</label>
                    <input name="duration" type="number" placeholder="{{t('Duration')}}"
                           class="form-control"
                           value="{{ isset($term) ? $term->duration : old("Duration") }}"
                    />
                </div>

                <div class="col-2 mt-3">
                    <div class="form-check form-check-custom form-check-solid me-10">
                        <input class="form-check-input" type="checkbox" value="1" name="active" {{ isset($term) && $term->active ? 'checked':'' }} id="flexCheckbox30"/>
                        <label class="form-check-label text-dark" for="flexCheckbox30">
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
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\TermRequest::class, '#form_data'); !!}
    <script type="text/javascript" src="{{ asset('assets_v1/js/manager/models/general.js')}}?v1"></script>

@endsection
