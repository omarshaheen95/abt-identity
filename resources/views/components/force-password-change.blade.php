{{--
    "Force password change on next sign in" switch for the admin side forms.

    The hidden input makes sure the key is always submitted, so clearing the
    switch actually clears the flag instead of falling back to the controller
    default. The script ticks it automatically as soon as a password is typed,
    because a password handed over by an administrator is always temporary.
--}}
@php $row = $row ?? null; @endphp
<div class="col-lg-3">
    <label>{{ t('Force Password Change') }} :</label>
    <div class="form-check form-switch form-check-custom form-check-solid mt-1">
        <input type="hidden" name="force_password_change" value="0"/>
        <input class="form-check-input" type="checkbox" value="1"
               id="force_password_change"
               name="force_password_change"
               data-force-password-change
               {{ !$row || $row->force_password_change ? 'checked' : '' }}/>
        <span class="form-check-label fs-8 text-muted ms-2">
            {{ t('The account will be locked on the change password page until it is changed.') }}
        </span>
    </div>
</div>

@once
    <script>
        // tick the switch as soon as a password is entered in this form
        document.addEventListener('input', function (event) {
            var input = event.target.closest('[data-password-input]');
            if (!input || !input.value) {
                return;
            }
            var form = input.closest('form');
            var toggle = form ? form.querySelector('[data-force-password-change]') : null;
            if (toggle && !toggle.dataset.touched) {
                toggle.checked = true;
            }
        });
        document.addEventListener('change', function (event) {
            var toggle = event.target.closest('[data-force-password-change]');
            if (toggle) {
                // remember that a human decided, and stop auto ticking it
                toggle.dataset.touched = '1';
            }
        });
    </script>
@endonce
