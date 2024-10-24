@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.year.index')}}" class="text-muted">
            {{t('Years')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{isset($year) ? route('manager.year.update', $year->id):route('manager.year.store')}}"
          method="post">
        @csrf
        @isset($year)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="form-group row">
            @foreach(\Config::get('app.languages') as $locale)
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Year Name')}} : ({{$locale}})</label>
                    <input name="name[{{$locale}}]" type="text" placeholder="{{t('Year Name')}}"
                           class="form-control"
                           value="{{ isset($year) ? $year->getTranslation('name', $locale) : old("name[$locale]") }}"
                    />
                </div>
            @endforeach
        </div>
        <div class="col-md-6 mb-2 mt-2">
            <div class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" value="1" name="default" id="flexCheckDefault"
                    {{ isset($year) && $year->default == 1 ? 'checked':'' }}/>
                <label class="form-check-label" for="flexCheckDefault">
                    {{t('Default Year')}}
                </label>
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
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\YearRequest::class, '#form_data'); !!}
@endsection
