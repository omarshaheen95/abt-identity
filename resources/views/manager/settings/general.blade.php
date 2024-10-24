@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data" enctype="multipart/form-data"
          action="{{route('manager.settings.updateSettings')}}"
          method="post">
        {{csrf_field()}}
        <div class="d-flex flex-column gap-3">
            @foreach ($settings as $setting)
                @if ($setting->type == 'text')
                    <div class="form-group row">
                        <label class="col-3 col-form-label">{{ t($setting->name) }}</label>
                        <div class="col-6">
                            <input class="form-control" type="text"
                                   @if(!in_array($setting->key , ['SOFTWARE_CLIENT_ID' , 'SOFTWARE_LICENSE_CODE'])) name="settings[{{ $setting->key }}]" @else readonly disabled @endif
                                   value="{{ $setting->value }}" placeholder="{{ t($setting->name) }}" />
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @elseif($setting->type == 'color')
                    <div class="form-group row">
                        <label class="col-3 col-form-label text-uppercase">{{ t($setting->name) }} </label>
                        <div class="col-6">
                            <input class="form-control" name="settings[{{ $setting->key }}]" type="color" value="{{ $setting->value }}"
                            />
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @elseif($setting->type == 'file')
                    <div class="form-group row">
                        <label class="col-3 col-form-label">{{ t($setting->name) }} @if(isset($setting) && $setting->value) <a
                                href="{{asset($setting->value)}}">{{t('Browse')}}</a> @endif</label>
                        <div class="col-6">
                            <input class="form-control" accept="image/*" name="settings[{{ $setting->key }}]" type="file"
                            />
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @elseif($setting->type == 'password')
                    <div class="form-group row">
                        <label class="col-3 col-form-label">{{ t($setting->name) }}</label>
                        <div class="col-6">
                            <input class="form-control" name="settings[{{ $setting->key }}]" type="password"
                                   value="{{ $setting->value }}" placeholder="{{ t($setting->name) }}" />
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @elseif($setting->type == 'checkbox')
                    <div class="form-group row">
                        <label class="col-3 col-form-label">{{ t($setting->name) }}</label>
                        <div class="col-6 col-form-label">
                            <div class="radio-inline">
                                <label class="radio radio-primary">
                                    <input type="radio" value="1" name="settings[{{ $setting->key }}]"
                                           @if ($setting->value) checked @endif />
                                    <span></span>
                                    {{ t('Enable') }}
                                </label>
                                <label class="radio radio-primary">
                                    <input type="radio" value="0" name="settings[{{ $setting->key }}]"
                                           @if ($setting->value != 1) checked @endif />
                                    <span></span>
                                    {{ t('Disable') }}
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
            </div>
        </div>
    </form>
@endsection
