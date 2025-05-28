@extends('manager.layout.container')

@section('title')
    {{ $title??'Unknown' }}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{ t('settings') }}</li>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <form class="form" id="form_data" action="{{ route('manager.settings.updateSettings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <!-- Group settings by their group -->
                    @php
                        $groupedSettings = $settings->groupBy('group');
                    @endphp

                    @foreach($groupedSettings as $group => $groupSettings)
                        <div class="mb-8">
                            <h3 class="fs-4 fw-bold mb-4 text-capitalize">{{ t('' . $group) }}</h3>

                            @foreach($groupSettings as $setting)
                                <div class="row mb-5">
                                    <div class="col-lg-4">
                                        <label class="fs-6 fw-semibold form-label">{{ $setting->name }}</label>
                                    </div>
                                    <div class="col-lg-8">
                                        @switch($setting->type)
                                            @case('text')
                                                <input type="text" class="form-control"
                                                       name="settings[{{ $setting->key }}]"
                                                       value="{{ $setting->value }}"
                                                       placeholder="{{ $setting->name }}">
                                                @break

                                            @case('password')
                                                <input type="password" class="form-control"
                                                       name="settings[{{ $setting->key }}]"
                                                       value="{{ $setting->value }}"
                                                       placeholder="{{ $setting->name }}">
                                                @break

                                            @case('number')
                                                <input type="number" class="form-control"
                                                       name="settings[{{ $setting->key }}]"
                                                       value="{{ $setting->value }}"
                                                       placeholder="{{ $setting->name }}">
                                                @break

                                            @case('textarea')
                                                <textarea class="form-control"
                                                          name="settings[{{ $setting->key }}]"
                                                          rows="4"
                                                          placeholder="{{ $setting->name }}">{{ $setting->value }}</textarea>
                                                @break

                                            @case('file')
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group">
                                                        <input type="file" class="form-control"
                                                               name="settings[{{ $setting->key }}]">
                                                    </div>

                                                    @if($setting->value)
                                                        <div class="ms-3">
                                                            @if(Str::contains($setting->value, ['.jpg', '.jpeg', '.png', '.gif', '.svg']))
                                                                <a href="{{ asset($setting->value) }}" target="_blank">
                                                                    <img src="{{ asset($setting->value) }}" alt="{{ $setting->name }}"
                                                                         class="img-thumbnail" style="max-height: 50px;">
                                                                </a>
                                                            @else
                                                                <a href="{{ asset($setting->value) }}" target="_blank" class="btn btn-sm btn-light-primary">
                                                                    <i class="ki-duotone ki-eye fs-5">
                                                                        <i class="path1"></i>
                                                                        <i class="path2"></i>
                                                                        <i class="path3"></i>
                                                                    </i>
                                                                    {{ t('view_file') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                @break

                                            @case('select')
                                                <select class="form-control form-select"
                                                        name="settings[{{ $setting->key }}]"
                                                        data-control="select2"
                                                        data-placeholder="{{ t('select') }}">
                                                    <option></option>
                                                    @foreach(json_decode($setting->options ?? '[]', true) as $optionKey => $optionValue)
                                                        <option value="{{ $optionKey }}" {{ $setting->value == $optionKey ? 'selected' : '' }}>
                                                            {{ $optionValue }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break

                                            @case('multi_select')
                                                <select class="form-control form-select"
                                                        name="settings[{{ $setting->key }}][]"
                                                        data-control="select2"
                                                        data-placeholder="{{ t('select') }}"
                                                        multiple>
                                                    @foreach(json_decode($setting->options ?? '[]', true) as $optionKey => $optionValue)
                                                        <option value="{{ $optionKey }}"
                                                            {{ in_array($optionKey, json_decode($setting->value ?? '[]', true) ?: []) ? 'selected' : '' }}>
                                                            {{ $optionValue }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break

                                            @case('radio')
                                                <div class="d-flex flex-wrap gap-5 mt-2">
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="radio"
                                                               name="settings[{{ $setting->key }}]"
                                                               value="1"
                                                               id="{{ $setting->key }}_enabled"
                                                            {{ $setting->value == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $setting->key }}_enabled">
                                                            {{ t('enabled') }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="radio"
                                                               name="settings[{{ $setting->key }}]"
                                                               value="0"
                                                               id="{{ $setting->key }}_disabled"
                                                            {{ $setting->value == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $setting->key }}_disabled">
                                                            {{ t('disabled') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @break

                                            @case('checkbox')
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="settings[{{ $setting->key }}]"
                                                           value="1"
                                                           id="{{ $setting->key }}_checkbox"
                                                        {{ $setting->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $setting->key }}_checkbox">
                                                        {{ t('enabled') }}
                                                    </label>
                                                </div>
                                                @break

                                            @case('color')
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color w-100px"
                                                           name="settings[{{ $setting->key }}]"
                                                           value="{{ $setting->value }}"
                                                           title="{{ t('choose_color') }}">
                                                </div>
                                                @break

                                            @case('date')
                                                <input type="date" class="form-control"
                                                       name="settings[{{ $setting->key }}]"
                                                       value="{{ $setting->value }}">
                                                @break

                                            @default
                                                <input type="text" class="form-control"
                                                       name="settings[{{ $setting->key }}]"
                                                       value="{{ $setting->value }}"
                                                       placeholder="{{ $setting->name }}">
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach

                            @if(!$loop->last)
                                <div class="separator separator-dashed my-8"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-3">{{ t('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ t('Save Changes') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize any select2 dropdowns
            $('select[data-control="select2"]').select2({
                minimumResultsForSearch: 10
            });

            // Show filename on file input change
            $('input[type="file"]').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    $(this).next('.custom-file-label').text(fileName);
                }
            });
        });
    </script>
@endsection
