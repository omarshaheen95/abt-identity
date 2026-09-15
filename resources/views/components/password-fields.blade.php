{{--
    Reusable password input(s) with a show/hide toggle, a policy aware
    "generate" button and a live rule checklist.

    Usage:
      @include('components.password-fields')                       full change password form fields
      @include('components.password-fields', ['confirm' => false])  single field (admin sets someone else's password)

    Options:
      name        field name, default "password"
      label       label text
      confirm     render the confirmation field, default true
      col         bootstrap column class for each field, default "col-lg-4"
      required    show the required marker, default true
      hint        show the policy hint under the fields, default true
      simple      temporary password mode: only the length rule applies, for
                  the admin side forms where the account is forced to change it
--}}
@php
    $name = $name ?? 'password';
    $label = $label ?? t('Password');
    $confirm = $confirm ?? true;
    $col = $col ?? 'col-lg-4';
    $required = $required ?? true;
    $hint = $hint ?? true;
    $simple = $simple ?? false;
    $min = $simple ? \App\Support\PasswordPolicy::MIN_LENGTH_TEMPORARY : \App\Support\PasswordPolicy::MIN_LENGTH;
    $group = 'pw_' . $name . '_' . uniqid();
@endphp

<div class="{{ $col }} mb-2" data-password-group="{{ $group }}">
    <label class="form-label mb-1">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <div class="input-group">
        <input name="{{ $name }}" type="password" placeholder="{{ $label }}"
               autocomplete="new-password"
               class="form-control" data-password-input data-password-min="{{ $min }}"/>
        <button class="btn btn-icon btn-light border" type="button" data-password-toggle
                title="{{ t('Show / Hide Password') }}">
            <i class="la la-eye"></i>
        </button>
        <button class="btn btn-light border" type="button" data-password-generate
                title="{{ t('Generate a password that matches the policy') }}">
            <i class="la la-magic"></i> {{ t('Generate') }}
        </button>
        <button class="btn btn-icon btn-light border d-none" type="button" data-password-copy
                title="{{ t('Copy') }}">
            <i class="la la-copy"></i>
        </button>
    </div>
    @if($hint)
        <ul class="list-unstyled mt-2 mb-0 fs-8 text-muted" data-password-rules>
            <li data-rule="length"><i class="la la-times-circle text-danger"></i> {{ t('At least :min characters', ['min' => $min]) }}</li>
            @unless($simple)
                <li data-rule="letter"><i class="la la-times-circle text-danger"></i> {{ t('Contains a letter') }}</li>
                <li data-rule="digit"><i class="la la-times-circle text-danger"></i> {{ t('Contains a number') }}</li>
                <li data-rule="symbol"><i class="la la-times-circle text-danger"></i> {{ t('Contains a symbol') }}</li>
            @endunless
        </ul>
        @if($simple)
            <div class="fs-8 text-muted mt-1">
                {{ t('A temporary password is enough here, as long as Force Password Change stays on.') }}
            </div>
        @endif
    @endif
</div>

@if($confirm)
    <div class="{{ $col }} mb-2">
        <label class="form-label mb-1">{{ t('Confirmed Password') }} @if($required)<span class="text-danger">*</span>@endif</label>
        <div class="input-group">
            <input name="{{ $name }}_confirmation" type="password" placeholder="{{ t('Confirmed Password') }}"
                   autocomplete="new-password"
                   class="form-control" data-password-confirm="{{ $group }}"/>
            <button class="btn btn-icon btn-light border" type="button" data-password-toggle
                    title="{{ t('Show / Hide Password') }}">
                <i class="la la-eye"></i>
            </button>
        </div>
    </div>
@endif

{{-- emitted once per page; the script uses event delegation so its position
     in the document does not matter and it needs no jQuery --}}
@once
    <script>
        window.PASSWORD_POLICY = {
            min: {{ \App\Support\PasswordPolicy::MIN_LENGTH }},
            symbols: @json(\App\Support\PasswordPolicy::SYMBOLS),
            copied: @json(t('Copied to clipboard'))
        };
    </script>
    <script src="{{ asset('assets_v1/js/password-policy.js') }}?v=1"></script>
@endonce
